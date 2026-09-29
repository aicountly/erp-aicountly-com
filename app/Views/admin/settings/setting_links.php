<div class="">
      <?php
$current_url = current_url(false); // without query string
?>

<a class="btn btn-outline-success btn-sm showinline-md <?= ($current_url == base_url('admin/settings/general')) ? 'themeactive' : '' ?>" 
   href="<?= base_url('admin/settings/general') ?>">General</a>

<a class="btn btn-outline-success btn-sm showinline-md <?= ($current_url == base_url('admin/settings/access_profile')) ? 'themeactive' : '' ?>" 
   href="<?= base_url('admin/settings/access_profile') ?>">Access Profile</a>

<a class="btn btn-outline-success btn-sm showinline-md <?= ($current_url == base_url('admin/settings/inventory')) ? 'themeactive' : '' ?>" 
   href="<?= base_url('admin/settings/inventory') ?>">Inventory</a>

<a class="btn btn-outline-success btn-sm showinline-md <?= ($current_url == base_url('admin/settings/advanced_settings')) ? 'themeactive' : '' ?>" 
   href="<?= base_url('admin/settings/advanced_settings') ?>">Advanced Settings</a>

<a class="btn btn-outline-success btn-sm showinline-md <?= ($current_url == base_url('admin/settings/transaction_limit')) ? 'themeactive' : '' ?>" 
   href="<?= base_url('admin/settings/transaction_limit') ?>">Transaction Limit</a>
      <a href="<?php echo history_back();?>"  class="btn btn-outline-success btn-sm showinline-md float-end">« Back</a> 
  </div>