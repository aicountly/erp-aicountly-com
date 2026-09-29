<?php $header = ['title' => 'Bulk Updation Data']; ?>
<?php echo view('includes/header', $header); ?>

<?php
    $indiaId = '';
    if (!empty($CountryDropdown) && is_array($CountryDropdown)) {
        foreach ($CountryDropdown as $cid => $cname) {
            if (strtolower(trim($cname)) === 'india') {
                $indiaId = $cid;
                break;
            }
        }
    }

    if (isset($StatesDropdown)) {
        $tmp = $StatesDropdown;
        $tmp = str_replace(['name="state_id"', 'id="state_id"'], ['name="state_id[]"', ''], $tmp);
        $tmp = preg_replace('/\srequired\b/i', '', $tmp);
        $stateSelectTemplate = $tmp;
    } else {
        $stateSelectTemplate = '<select name="state_id[]" class="form-control"><option value="">Choose State</option></select>';
    }

    $countrySelectTemplate = '<select name="country_id[]" class="form-select form-select-sm">';
    if (!empty($CountryDropdown) && is_array($CountryDropdown)) {
        foreach ($CountryDropdown as $cid => $cname) {
            $sel = ($indiaId !== '' && $cid == $indiaId) ? ' selected' : '';
            $countrySelectTemplate .= '<option value="'.esc($cid).'"'.$sel.'>'.esc($cname).'</option>';
        }
    } else {
        $countrySelectTemplate .= '<option value="">Choose</option>';
    }
    $countrySelectTemplate .= '</select>';
?>

<style>
    .table thead th { white-space: nowrap; }
    .sticky-head { position: sticky; top: 0; z-index: 2; }
    .pager button { margin: 0 2px; }
    .pager .active { font-weight: bold; }
    .row-hidden { display: none !important; }
    .tbl-container { max-height: 70vh; overflow: auto; }
    .blocked-row { background: #f9f5e7; }

    .table td input.form-control-sm,
    .table td select.form-select-sm {
        min-width: 140px;
    }
    .table td input.form-control-sm.wide {
        min-width: 170px;
    }
    .table td select.state-select {
        min-width: 170px;
    }
    #table-search {
        max-width: 320px;
    }

    #accounts-table {
        table-layout: fixed;
    }
    #accounts-table th:nth-child(1),
    #accounts-table td:nth-child(1) {
        position: sticky;
        left: 0;
        width: 120px;
        min-width: 120px;
        max-width: 120px;
        background: #fff;
        z-index: 3;
    }
    #accounts-table th:nth-child(2),
    #accounts-table td:nth-child(2) {
        position: sticky;
        left: 120px;
        width: 100px;
        min-width: 100px;
        max-width: 100px;
        background: #fff;
        z-index: 3;
    }
    #accounts-table thead th:nth-child(1),
    #accounts-table thead th:nth-child(2) {
        background: #f8f9fa;
        z-index: 4;
        box-shadow: 2px 0 0 #dee2e6;
    }
    .table td input.form-control-sm,
    .table td select.form-select-sm,
    .table td select.state-select {
        min-width: 100% !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box;
        height: 30px;
        padding: 2px 6px;
        font-size: 12px;
    }
    .table td {
        padding: 6px;
    }
</style>

<h3 class="pb-3">Bulk Updation</h3>

<div class="col-12">
  <div class="tab-content border-0 accordion form-outline mb-4" id="myTabContent">
    <div class="tab-pane border-0 fade accordion-item active show" id="acess1-panel" role="tabpanel" aria-labelledby="acess1-tab" tabindex="0">
      <div class="card p-4 mt-2">
        <h4>Update Account Address</h4>

        <div class="d-flex align-items-center gap-3 mb-3">
          <input type="text" id="table-search" class="form-control form-control-sm" placeholder="Search account / group / email / mobile">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="state-empty-only">
            <label class="form-check-label small" for="state-empty-only">Show only rows with empty state</label>
          </div>
        </div>

        <form id="create_mst_form" action="<?= base_url() ?>admin/bulk_updation/UpdateAccountAddress" method="post">
          <div class="col-md-12 myform pt-4">
            <div id="crt_mst_val_err"></div>

            <template id="tpl-state-select"><?= $stateSelectTemplate ?></template>
            <template id="tpl-country-select"><?= $countrySelectTemplate ?></template>

            <div class="tbl-container border rounded">
              <table id="accounts-table" class="table table-striped table-bordered align-middle mb-0">
                <thead class="table-light sticky-head">
                  <tr>
                    <th>Account</th>
                    <th>Group</th>
                    <th>Email</th>
                    <th>Mobile No.</th>
                    <th>Whatsapp No.</th>
                    <th>Address 1</th>
                    <th>Address 2</th>
                    <th>City</th>
                    <th>Pin Code</th>
                    <th>GSTIN</th>
                    <th>State</th>
                    <th>Country</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-2">
              <div class="small text-muted" style="display:none;">Showing 10 per page (client-side pagination; data is fully loaded).</div>
              <div class="pager btn-group" id="pager"></div>
            </div>

            <p class="col-12 text-end mt-3">
              <button type="submit" class="btn btn-success">Submit</button>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo view('includes/footer_scripts'); ?>
<script>
(function() {
  const pageSize = 500;
  const blockedGroups = [];
  const indiaId = "<?= esc($indiaId) ?>";
  const chunkSize = 100;
  const saveUrl = "<?= base_url() ?>admin/bulk_updation/UpdateAccountAddress";

  const $tbody = $('#accounts-table tbody');
  const $pager = $('#pager');
  const $search = $('#table-search');
  const $stateEmptyOnly = $('#state-empty-only');
  const $form = $('#create_mst_form');

  const stateSelectHTML   = document.getElementById('tpl-state-select').innerHTML.trim();
  const countrySelectHTML = document.getElementById('tpl-country-select').innerHTML.trim();

  let rows = [];

  function isBlocked(groupName) {
    const g = (groupName || '').toString().trim().toUpperCase();
    return blockedGroups.includes(g);
  }

  function renderRows(data) {
    $tbody.empty();
    const fragments = document.createDocumentFragment();

    data.forEach((item, idx) => {
      const tr = document.createElement('tr');
      tr.dataset.index = idx;

      const blocked = isBlocked(item.group_name);
      if (blocked) tr.classList.add('blocked-row');

      const disabledAttr = blocked ? 'disabled aria-disabled="true"' : '';

      const stateSelect = stateSelectHTML
        .replace(/selected/gi, '')
        .replace('name="state_id[]"', 'data-name="state_id"')
        .replace('<select ', `<select ${disabledAttr} `)
        .replace('class="', 'class="state-select ');

      const countrySelect = countrySelectHTML
        .replace('name="country_id[]"', 'data-name="country_id"')
        .replace('<select ', `<select ${disabledAttr} `);

      const addr1    = item.addr1 ?? '';
      const addr2    = item.addr2 ?? '';
      const city     = item.city ?? '';
      const pincode  = item.pincode ?? '';
      const email    = item.email ?? '';
      const mobile   = item.mobile ?? '';
      const wamobile = item.wamobile ?? '';
      const accName  = item.account_name ?? '';
      const gstin    = item.gstin ?? '';

      const stateId   = item.state_id ?? '';
      const countryId = item.country_id ?? '';

      tr.innerHTML = `
<td>${accName}</td>
<td>${item.group_name ?? ''}</td>
<td><input type="text" name="email[]" class="form-control form-control-sm wide" value="${email}" ${disabledAttr}></td>
<td><input type="text" name="mobile[]" class="form-control form-control-sm" value="${mobile}" ${disabledAttr}></td>
<td><input type="text" name="wamobile[]" class="form-control form-control-sm" value="${wamobile}" ${disabledAttr}></td>
<td><input type="text" name="addr1[]" class="form-control form-control-sm wide" value="${addr1}" ${disabledAttr}></td>
<td><input type="text" name="addr2[]" class="form-control form-control-sm wide" value="${addr2}" ${disabledAttr}></td>
<td><input type="text" name="city[]" class="form-control form-control-sm" value="${city}" ${disabledAttr}></td>
<td><input type="text" name="pincode[]" class="form-control form-control-sm" value="${pincode}" ${disabledAttr}></td>
<td><input type="text" name="gstin[]" class="form-control form-control-sm wide" value="${gstin}" ${disabledAttr}></td>
<td>${stateSelect}</td>
<td>
${countrySelect}
<input type="hidden" name="state_id[]" value="${stateId}">
<input type="hidden" name="country_id[]" value="${countryId}">
<input type="hidden" name="account_id[]" value="${item.acc_id}">
<input type="hidden" name="account_name[]" value="${accName}">
</td>
`;

      tr.dataset.stateId   = stateId;
      tr.dataset.countryId = countryId;
      tr.dataset.accId     = item.acc_id || '';
      tr.dataset.accName   = accName || '';
      tr.dataset.groupName = item.group_name || '';

      fragments.appendChild(tr);
    });

    $tbody.append(fragments);
    rows = Array.from($tbody.children());
  }

  $tbody.on('change', 'select.state-select', function() {
    const row = this.closest('tr');
    const hidden = row.querySelector('input[name="state_id[]"]');
    if (hidden) hidden.value = this.value || '';
    row.dataset.stateId = this.value || '';
    refreshView();
  });

  $tbody.on('change', 'select[data-name="country_id"]', function() {
    const row = this.closest('tr');
    const hidden = row.querySelector('input[name="country_id[]"]');
    if (hidden) hidden.value = this.value || '';
    row.dataset.countryId = this.value || '';
  });

  function applySelectValues() {
    rows.forEach((row) => {
      const stateVal   = row.dataset.stateId || '';
      const countryVal = row.dataset.countryId || '';
      const $row = $(row);
      const stateSel   = $row.find('select.state-select')[0];
      const countrySel = $row.find('select[data-name="country_id"]')[0];

      if (stateSel) {
        var firstOpt = stateSel.options[0];
        if (!firstOpt || (firstOpt.value !== '' && firstOpt.value !== '0')) {
          var blankOpt = document.createElement('option');
          blankOpt.value = '';
          blankOpt.textContent = 'Choose State';
          stateSel.prepend(blankOpt);
        } else {
          firstOpt.value = '';
          firstOpt.textContent = 'Choose State';
        }

        if (stateVal) {
          stateSel.value = stateVal;
          if (stateSel.value !== stateVal) stateSel.value = '';
        } else {
          stateSel.value = '';
        }

        const hidden = row.querySelector('input[name="state_id[]"]');
        if (hidden) hidden.value = stateVal || '';
        row.dataset.stateId = stateVal || '';
      }

      if (countrySel) {
        if (countryVal) {
          countrySel.value = countryVal;
        } else if (indiaId) {
          countrySel.value = indiaId;
        }

        const hiddenC = row.querySelector('input[name="country_id[]"]');
        if (hiddenC) hiddenC.value = countrySel.value || '';
      }
    });
  }

  function renderPager(totalPages, currentPage) {
    $pager.empty();
    if (totalPages <= 1) return;

    const btn = (label, page, disabled = false, active = false) =>
      `<button type="button" class="btn btn-sm ${active ? 'btn-primary' : 'btn-outline-secondary'}" data-page="${page}" ${disabled ? 'disabled' : ''}>${label}</button>`;

    $pager.append(btn('Prev', currentPage - 1, currentPage === 1, false));
    for (let p = 1; p <= totalPages; p++) {
      $pager.append(btn(p, p, false, p === currentPage));
    }
    $pager.append(btn('Next', currentPage + 1, currentPage === totalPages, false));
  }

  function showPage(page, filteredRows) {
    const activeRows = filteredRows || rows;
    const totalPages = Math.ceil(activeRows.length / pageSize) || 1;
    const current = Math.min(Math.max(1, page), totalPages);
    rows.forEach(r => r.classList.add('row-hidden'));
    activeRows.forEach((row, idx) => {
      const start = (current - 1) * pageSize;
      const end   = start + pageSize;
      if (idx >= start && idx < end) row.classList.remove('row-hidden');
    });
    renderPager(totalPages, current);
    $pager.find('button[data-page]').off('click').on('click', function() {
      const p = Number(this.dataset.page);
      showPage(p, activeRows);
    });
  }

  function isStateEmpty(row) {
    var dataVal = (row.dataset.stateId || '').trim();
    if (dataVal !== '' && dataVal !== '0') return false;

    var hidden = row.querySelector('input[name="state_id[]"]');
    var hiddenVal = (hidden ? hidden.value : '').trim();
    if (hiddenVal !== '' && hiddenVal !== '0') return false;

    var select = row.querySelector('select.state-select');
    if (select) {
      var selectVal = (select.value || '').trim();
      if (selectVal !== '' && selectVal !== '0') return false;

      var selOpt = select.options[select.selectedIndex];
      if (selOpt) {
        var optVal = (selOpt.value || '').trim();
        var optText = (selOpt.textContent || '').trim().toLowerCase();
        if (optVal === '' || optVal === '0' || optText === 'choose state' || optText === 'choose' || optText === 'select state' || optText === '-- select --') {
          return true;
        }
        return false;
      }
    }

    return true;
  }

  function filterRows(term) {
    const t = (term || '').toLowerCase();
    const emptyOnly = $stateEmptyOnly.is(':checked');

    return rows.filter(row => {
      const matchesText = !t || Array.from(row.querySelectorAll('td'))
        .map(td => td.innerText.toLowerCase())
        .some(txt => txt.includes(t));

      if (!matchesText) return false;
      if (emptyOnly) return isStateEmpty(row);
      return true;
    });
  }

  function refreshView() {
    const term = $search.val().trim();
    const filtered = filterRows(term);
    showPage(1, filtered);
  }

  function collectAllRows() {
    return rows
      .filter(row => !isBlocked(row.dataset.groupName || ''))
      .map(row => {
        const $row = $(row);

        return {
          account_id: (row.dataset.accId || '').trim(),
          account_name: (row.dataset.accName || '').trim(),
          email: ($row.find('input[name="email[]"]').val() || '').trim(),
          mobile: ($row.find('input[name="mobile[]"]').val() || '').trim(),
          wamobile: ($row.find('input[name="wamobile[]"]').val() || '').trim(),
          addr1: ($row.find('input[name="addr1[]"]').val() || '').trim(),
          addr2: ($row.find('input[name="addr2[]"]').val() || '').trim(),
          city: ($row.find('input[name="city[]"]').val() || '').trim(),
          pincode: ($row.find('input[name="pincode[]"]').val() || '').trim(),
          gstin: ($row.find('input[name="gstin[]"]').val() || '').trim(),
          state_id: ($row.find('select.state-select').val() || '').trim(),
          country_id: ($row.find('select[data-name="country_id"]').val() || '').trim(),
          acc_aadhaar: '',
          acc_pan: '',
          acc_tan: '',
          acc_it_jurisd: '0',
          acc_sac: '',
          acc_is_sys_acc: '0',
          acc_is_sez: '0'
        };
      });
  }

  function validateRowsForSave(items) {
    for (const item of items) {
      const anyAddress = item.addr1 || item.addr2 || item.city || item.pincode;
      if (anyAddress && (!item.state_id || !item.country_id)) {
        return 'State and Country are required when address details are filled.';
      }
    }
    return '';
  }

  async function postChunk(items, chunkIndex, totalChunks) {
    const response = await fetch(saveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        items: items,
        chunk_index: chunkIndex,
        total_chunks: totalChunks
      })
    });

    const text = await response.text();
    let data = {};
    try {
      data = text ? JSON.parse(text) : {};
    } catch (e) {
      throw new Error(`Invalid JSON response: ${text}`);
    }

    if (!response.ok || data.success === false) {
      throw new Error(data.message || `Chunk ${chunkIndex} failed`);
    }

    return data;
  }

  async function saveInChunks(items) {
    const totalChunks = Math.ceil(items.length / chunkSize) || 1;
    let saved = 0;

    for (let i = 0; i < totalChunks; i++) {
      const start = i * chunkSize;
      const end = start + chunkSize;
      const chunk = items.slice(start, end);

      $('#crt_mst_val_err').html(
        `<div class="alert alert-info">Saving chunk ${i + 1} of ${totalChunks}...</div>`
      );

      const result = await postChunk(chunk, i + 1, totalChunks);
      saved += Number(result.updated || 0);
    }

    return saved;
  }

  function wireSearch() {
    $search.on('input', refreshView);
    $stateEmptyOnly.on('change', refreshView);
  }

  function wireSubmitLoader() {
    $form.on('submit', async function(e) {
      e.preventDefault();

      const $btn = $(this).find('button[type="submit"]');
      $btn.prop('disabled', true).text('Submitting...');
      show_loader(0);
      $('#crt_mst_val_err').empty();

      try {
        const items = collectAllRows();
        const validationError = validateRowsForSave(items);

        if (validationError) {
          throw new Error(validationError);
        }

        const saved = await saveInChunks(items);

        $('#crt_mst_val_err').html(
          `<div class="alert alert-success">${saved} record(s) updated successfully. Reloading...</div>`
        );

        setTimeout(() => {
          window.location.reload();
        }, 1200);

      } catch (err) {
        $('#crt_mst_val_err').html(
          `<div class="alert alert-danger">${err.message || err}</div>`
        );
      } finally {
        stop_loader();
        $btn.prop('disabled', false).text('Submit');
      }
    });
  }

  show_loader(0);
  $.getJSON("<?php echo base_url();?>admin/bulk_updation/load_accounts")
    .done(function(json) {
      const data = json.data || [];
      renderRows(data);
      applySelectValues();
      refreshView();
      wireSearch();
      wireSubmitLoader();
      stop_loader();
    })
    .fail(function() {
      $('#crt_mst_val_err').html('<div class="alert alert-danger">Failed to load accounts.</div>');
      stop_loader();
    });

})();
</script>
</body>
</html>