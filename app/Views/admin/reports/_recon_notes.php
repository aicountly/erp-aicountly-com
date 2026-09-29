<?php
/**
 * Notes above a Balance Sheet / Trial Balance / Profit & Loss, taken from the ledger (ReportingPresenters::reportNotes):
 * why the report does not tally, and facts about the stored data that explain lines of it.
 * Renders nothing when there is nothing to say.
 *
 * @var array<int,array{level:string,text:string}> $notes
 */
$warn = []; $info = [];
foreach (($notes ?? []) as $n) {
    $n = is_array($n) ? $n : ['level' => 'warn', 'text' => (string)$n];
    if ((string)($n['text'] ?? '') === '') { continue; }
    if (($n['level'] ?? 'warn') === 'warn') { $warn[] = $n['text']; } else { $info[] = $n['text']; }
}
?>
<?php if ($warn): ?>
<div class="alert alert-warning py-2 mb-2" role="alert" id="recon_notes">
  <strong><?= esc($warn[0]) ?></strong>
  <?php foreach (array_slice($warn, 1) as $note): ?>
    <div class="small"><?= esc($note) ?></div>
  <?php endforeach; ?>
  <div class="small text-muted">The figures below are exactly what the ledger holds; nothing has been adjusted to make the report tally.</div>
</div>
<?php endif; ?>
<?php if ($info): ?>
<div class="alert alert-info py-2 mb-2" role="note" id="recon_info">
  <?php foreach ($info as $note): ?>
    <div class="small"><?= esc($note) ?></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
