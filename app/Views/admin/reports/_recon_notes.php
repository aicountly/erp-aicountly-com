<?php
/**
 * Banner above a Balance Sheet / Trial Balance that does not tally: says why, using figures taken from the
 * ledger (ReportingPresenters::reconciliationNotes). Renders nothing when the books balance.
 *
 * @var string[] $notes
 */
$notes = array_values(array_filter($notes ?? [], static fn($n) => (string)$n !== ''));
if ($notes): ?>
<div class="alert alert-warning py-2 mb-2" role="alert" id="recon_notes">
  <strong><?= esc($notes[0]) ?></strong>
  <?php foreach (array_slice($notes, 1) as $note): ?>
    <div class="small"><?= esc($note) ?></div>
  <?php endforeach; ?>
  <div class="small text-muted">The figures below are exactly what the ledger holds; nothing has been adjusted to make the report tally.</div>
</div>
<?php endif; ?>
