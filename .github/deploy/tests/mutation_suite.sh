#!/usr/bin/env bash
# Mutation suite for .github/deploy/deploy.sh selftest: every mutant MUST be caught (non-zero exit).
# Local only, no network. Work directories go to a temp dir, never into the repository.
# shellcheck disable=SC2016  # the sed expressions below are deliberately single-quoted
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC="$(cd "$HERE/.." && pwd)"
SCR="$(mktemp -d "${TMPDIR:-/tmp}/deploy-mutants.XXXXXX")"
trap 'rm -rf "$SCR"' EXIT
survivors=0

run_mutant() {  # name, sed -E expr for deploy.sh ('' = none), excludes-rule-to-delete ('' = none)
  local name="$1" sh_expr="$2" drop_rule="${3:-}"
  local d="$SCR/mut_$name"; rm -rf "$d"; mkdir -p "$d"
  cp "$SRC/deploy.sh" "$SRC/rsync-excludes.txt" "$d/"
  [ -n "$sh_expr" ] && sed -i -E "$sh_expr" "$d/deploy.sh"
  if [ -n "$drop_rule" ]; then
    grep -vxF -- "$drop_rule" "$SRC/rsync-excludes.txt" >"$d/rsync-excludes.txt"
    # keep the exclude-file guard from short-circuiting: we want rsync-behaviour assertions to catch it
    sed -i -E 's/^  verify_exclude_file$/  :/' "$d/deploy.sh"
    if cmp -s "$SRC/rsync-excludes.txt" "$d/rsync-excludes.txt"; then echo "$name: INVALID MUTANT (nothing removed)"; survivors=$((survivors+1)); return; fi
  fi
  if [ -z "$drop_rule" ] && cmp -s "$SRC/deploy.sh" "$d/deploy.sh"; then echo "$name: INVALID MUTANT (sed changed nothing)"; survivors=$((survivors+1)); return; fi
  local out rc
  out=$(bash "$d/deploy.sh" selftest 2>&1); rc=$?
  printf "%-30s exit=%s  " "$name" "$rc"
  if [ $rc -eq 0 ]; then echo "!!! SURVIVED"; survivors=$((survivors+1))
  else echo "caught: $(echo "$out" | grep -E 'FAIL|::error::' | head -2 | tr '\n' '|' | cut -c1-170)"; fi
}

echo "--- baseline (must pass) ---"
if bash "$SRC/deploy.sh" selftest >/dev/null 2>&1; then echo "baseline selftest: PASS"; else echo "baseline selftest: FAIL"; survivors=$((survivors+1)); fi

echo "--- rule-removal mutants (guard bypassed; one per mandatory rule) ---"
for rule in '.env' '.env.*' '.htaccess' '/vendor/' '/public/' '/writable/' '/.user.ini' '/php.ini' '.git' '/.github/' \
  '/aisonode/' '/app_old/' '/erp3-accounts-xml-export/' '/erp3-voucher-verification/' '/app/Views/grpcomp/'; do
  run_mutant "drop_rule_$(echo "$rule" | tr -c 'A-Za-z0-9\n' '_')" '' "$rule"
done

echo "--- same removals with the guard ON (guard itself must reject) ---"
for rule in '.env' '/vendor/' '/writable/' '/aisonode/' '/app_old/' '/erp3-accounts-xml-export/' '/erp3-voucher-verification/' '/app/Views/grpcomp/'; do
  d="$SCR/mut_guard"; rm -rf "$d"; mkdir -p "$d"; cp "$SRC/deploy.sh" "$d/"; grep -vxF -- "$rule" "$SRC/rsync-excludes.txt" >"$d/rsync-excludes.txt"
  bash "$d/deploy.sh" selftest >/dev/null 2>&1 && { echo "guard let '$rule' removal through"; survivors=$((survivors+1)); } || echo "guard rejects removal of '$rule': ok"
done

echo "--- include/reset line injection ---"
d="$SCR/mut_inject"; rm -rf "$d"; mkdir -p "$d"; cp "$SRC/deploy.sh" "$d/"; { echo '+ /vendor/keepme'; cat "$SRC/rsync-excludes.txt"; } >"$d/rsync-excludes.txt"
bash "$d/deploy.sh" selftest >/dev/null 2>&1 && { echo "include line let through"; survivors=$((survivors+1)); } || echo "'+' include line rejected: ok"
d="$SCR/mut_inject2"; rm -rf "$d"; mkdir -p "$d"; cp "$SRC/deploy.sh" "$d/"; { cat "$SRC/rsync-excludes.txt"; echo '!'; } >"$d/rsync-excludes.txt"
bash "$d/deploy.sh" selftest >/dev/null 2>&1 && { echo "reset line let through"; survivors=$((survivors+1)); } || echo "'!' reset line rejected: ok"

echo "--- option mutants ---"
# unanchored vendor needs an excludes edit, do it by hand:
d="$SCR/mut_unanchored_vendor2"; rm -rf "$d"; mkdir -p "$d"; cp "$SRC/deploy.sh" "$d/"; sed 's#^/vendor/$#vendor/#' "$SRC/rsync-excludes.txt" >"$d/rsync-excludes.txt"
sed -i -E 's/^  verify_exclude_file$/  :/' "$d/deploy.sh"
bash "$d/deploy.sh" selftest >/dev/null 2>&1 && { echo "unanchored_vendor2: !!! SURVIVED"; survivors=$((survivors+1)); } || echo "unanchored_vendor2: caught"
# the same for each other-project folder: unanchored, a rule would also swallow ERP code that shares the name
for od in aisonode app_old erp3-accounts-xml-export erp3-voucher-verification; do
  d="$SCR/mut_unanchored_$od"; rm -rf "$d"; mkdir -p "$d"; cp "$SRC/deploy.sh" "$d/"; sed "s#^/$od/\$#$od/#" "$SRC/rsync-excludes.txt" >"$d/rsync-excludes.txt"
  sed -i -E 's/^  verify_exclude_file$/  :/' "$d/deploy.sh"
  if cmp -s "$SRC/rsync-excludes.txt" "$d/rsync-excludes.txt"; then echo "unanchored_$od: INVALID MUTANT (nothing changed)"; survivors=$((survivors+1)); continue; fi
  bash "$d/deploy.sh" selftest >/dev/null 2>&1 && { echo "unanchored_$od: !!! SURVIVED"; survivors=$((survivors+1)); } || echo "unanchored_$od: caught"
done
# app/Views/grpcomp must stay exactly that path. Two ways to get it wrong: a rule so short it swallows every
# folder of that name (or all views), and a rule that swallows the neighbours' folders too.
for variant in 'unanchored:grpcomp/' 'all_views:/app/Views/' 'any_depth_under_views:app/Views/**/grpcomp/'; do
  name="${variant%%:*}"; repl="${variant#*:}"
  d="$SCR/mut_grpcomp_$name"; rm -rf "$d"; mkdir -p "$d"; cp "$SRC/deploy.sh" "$d/"
  awk -v r="$repl" '$0 == "/app/Views/grpcomp/" { print r; next } { print }' "$SRC/rsync-excludes.txt" >"$d/rsync-excludes.txt"
  sed -i -E 's/^  verify_exclude_file$/  :/' "$d/deploy.sh"
  if cmp -s "$SRC/rsync-excludes.txt" "$d/rsync-excludes.txt"; then echo "grpcomp_$name: INVALID MUTANT (nothing changed)"; survivors=$((survivors+1)); continue; fi
  bash "$d/deploy.sh" selftest >/dev/null 2>&1 && { echo "grpcomp_$name: !!! SURVIVED"; survivors=$((survivors+1)); } || echo "grpcomp_$name: caught"
done
run_mutant delete_enabled       's/^  --max-delete=0$/  --delete/' ''
run_mutant delete_with_guard    's/^  --max-delete=0$/  --delete --max-delete=0/' ''
run_mutant perms_and_times      's/--no-perms --no-owner --no-group --no-times/--perms --times/' ''
run_mutant perms_only           's/--no-perms /--perms /' ''
run_mutant times_only           's/ --no-times$/ --times/' ''
run_mutant no_checksum          's/^  --checksum$/  --ignore-times/' ''
run_mutant no_backup            's/--delay-updates --backup "--backup-dir=\$backup_dir"/--delay-updates/' ''
run_mutant chmod_777            "s/'--chmod=D755,F644'/'--chmod=D777,F666'/" ''
run_mutant no_keep_dirlinks 's/ --keep-dirlinks//' ''
run_mutant counter_only_local_fmt 's/\^\[<>\]f/^>f/g' ''
run_mutant checker_ignores_push_fmt 's/\[<>ch\.\]\[fdLDS\]/[>ch.][fdLDS]/' ''
run_mutant checker_neutered     's/^    if is_protected_path "\$path"; then$/    if false; then/' ''
# the checker's regex for the other projects' folders, loosened in the two ways that would matter:
run_mutant checker_other_no_terminator  '/aisonode\|app_old/ s/\)\(\/\|\$\)'"'"'/)'"'"'/' ''      # 'aisonode2/' would be flagged
run_mutant checker_other_unanchored     '/aisonode\|app_old/ s/'"'"'\^\(aisonode/'"'"'(^|\/)(aisonode/' ''  # nested ERP code would be flagged
# ...and the one for app/Views/grpcomp: without the terminator a look-alike (grpcomp_new) is flagged; widened to any
# depth, app/Views/includes/grpcomp and app/Views/admin/grpcomp are flagged.
run_mutant checker_grpcomp_no_terminator 's/'"'"'\^app\/Views\/grpcomp\(\/\|\$\)'"'"'/'"'"'^app\/Views\/grpcomp'"'"'/' ''
run_mutant checker_grpcomp_any_depth     's/'"'"'\^app\/Views\/grpcomp\(\/\|\$\)'"'"'/'"'"'(^|\/)grpcomp(\/|$)'"'"'/' ''

echo "--- checker-regex mutants (each protected_re entry removed; negative tests must notice) ---"
for entry in \
  "'(^|/)\\.env(\\.[^/]*)?(/|\$)'" \
  "'(^|/)\\.htaccess(/|\$)'" \
  "'(^|/)\\.git(/|\$)'" \
  "'^(vendor|public|writable|\\.github)(/|\$)'" \
  "'^(\\.user\\.ini|php\\.ini)\$'" \
  "'^(aisonode|app_old|erp3-accounts-xml-export|erp3-voucher-verification)(/|\$)'" \
  "'^app/Views/grpcomp(/|\$)'"; do
  d="$SCR/mut_regex"; rm -rf "$d"; mkdir -p "$d"; cp "$SRC/rsync-excludes.txt" "$d/"
  grep -vF -- "  $entry" "$SRC/deploy.sh" >"$d/deploy.sh"
  if cmp -s "$SRC/deploy.sh" "$d/deploy.sh"; then echo "regex mutant $entry: INVALID (nothing removed)"; survivors=$((survivors+1)); continue; fi
  bash "$d/deploy.sh" selftest >/dev/null 2>&1 && { echo "regex mutant $entry: !!! SURVIVED"; survivors=$((survivors+1)); } || echo "regex mutant $entry: caught"
done

echo "--- workflow mutants (workflow_steps_test.sh must fail for every broken copy of the workflow) ---"
WFT="$SRC/tests/workflow_steps_test.sh"
WFM="$SRC/tests/workflow_mutants.py"
out=$(bash "$WFT" 2>&1); rc=$?
if [ $rc -eq 0 ]; then echo "workflow baseline: PASS"; else echo "workflow baseline: FAIL"; echo "$out" | grep FAIL | head -5; survivors=$((survivors+1)); fi
while read -r name; do
  r="$SCR/wf_$name"; rm -rf "$r"; mkdir -p "$r/.github/deploy/tests" "$r/.github/workflows"
  cp "$WFT" "$r/.github/deploy/tests/"
  if ! python3 "$WFM" make "$name" "$SRC/../workflows/deploy-production.yml" "$r/.github/workflows/deploy-production.yml"; then
    echo "$name: INVALID MUTANT (edit changed nothing)"; survivors=$((survivors+1)); continue
  fi
  out=$(bash "$r/.github/deploy/tests/workflow_steps_test.sh" 2>&1); rc=$?
  printf "%-30s exit=%s  " "$name" "$rc"
  if [ $rc -eq 0 ]; then echo "!!! SURVIVED"; survivors=$((survivors+1))
  else echo "caught: $(echo "$out" | grep -m1 'FAIL' | cut -c1-120)"; fi
done < <(python3 "$WFM" list)

echo
[ "$survivors" -eq 0 ] && echo "ALL MUTANTS CAUGHT" || echo "PROBLEMS: $survivors"
exit "$survivors"
