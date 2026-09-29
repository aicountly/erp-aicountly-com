<?php
use CodeIgniter\Router\RouteCollection;
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Login');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();


/**
 * @var RouteCollection $routes
 */
/* $routes->options('(:any)', function () {
    return service('response')
        ->setStatusCode(200);
}); 
  */
$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function ($routes) {
    $routes->get('userprofile', 'CompanyController::userprofile'); 
	$routes->get('companyinfo', 'CompanyController::companyinfo');
	$routes->get('businessid', 'CompanyController::businessid'); 
	$routes->post('logout', 'CompanyController::logout'); 
     $routes->post('login', 'LoginRegisterController::login');      	
	$routes->get('companies', 'CompanyController::index');                        // filter=all|my|shared|recycle
    $routes->get('companies/activities', 'CompanyController::activities');
	$routes->post('companies/create', 'CompanyController::create_company');                      // create
    $routes->post('seskey', 'CompanyController::createSesKey');
	$routes->post('refresh_authtoken', 'CompanyController::refresh_authtoken');
	$routes->post('ResaveVouchers', 'ResaveVouchersController::resave');
	$routes->post('ResaveVouchersStream', 'ResaveVouchersController::resaveStream');
	$routes->post('InventoryStatus', 'ReportsController::inventory_status');
	$routes->post('AccountLedger', 'ReportsController::account_ledger');
	$routes->post('StockLedger', 'ReportsController::stock_ledger');
	$routes->post('StockSummary', 'ReportsController::stock_summary');
	$routes->post('GSTSummary', 'ReportsController::gst_summary');
	$routes->post('DayBook', 'ReportsController::daybook');
	$routes->put('companies/(:num)', 'CompanyController::modify/$1');             // edit
    $routes->delete('companies/delete/(:num)', 'CompanyController::delete/$1');          // soft delete
    $routes->post('companies/(:num)/restore', 'CompanyController::restore/$1');   // restore
    $routes->delete('companies/(:num)/destroy', 'CompanyController::destroy/$1'); // hard delete
});
 
 /*
 POST routes
 */

$routes->get('/admin/load_recent_items', 'Admin\Vouchers::load_recent_items');
$routes->get('/admin/load_recent_accounts', 'Admin\Vouchers::load_recent_accounts');

$routes->post('/admin/bulk_updation/UpdateAccountAddress', 'Admin\Bulk_updation::UpdateAccountAddress');
$routes->post('/admin/etaxes/ajax_einvoice_transactions', 'Admin\Etaxes::ajax_einvoice_transactions');
$routes->post('/admin/etaxes/ajax_eway_transactions', 'Admin\Etaxes::ajax_eway_transactions');
$routes->post('/admin/etaxapis/generate_eway', 'Admin\Etaxapis::generate_eway');
$routes->post('/admin/etaxapis/generate_einvoice', 'Admin\Etaxapis::generate_einvoice');
$routes->post('/home/ajax_all_companies/', 'Home::ajax_all_companies');
$routes->post('/sharedwithme/ajax_shared_companies/', 'Sharedwithme::ajax_shared_companies');
$routes->post('/home/ajax_my_companies/', 'Home::ajax_my_companies');
$routes->post('/admin/company/new_fy', 'Admin\Company::new_fy');
$routes->post('/home/get_comp_size', 'Home::get_comp_size');
$routes->post('/company_access/get_company_access_users', 'Company_access::get_company_access_users');
$routes->post('/company_access/remove_access', 'Company_access::remove_access');
$routes->post('/admin/company/modify/(:any)', 'Admin\Company::modify/$1');
$routes->post('/home/add_company', 'Home::add_company');
$routes->post('/admin/company/modify', 'Admin\Company::modify');
$routes->post('/admin/company/modify/(:any)', 'Admin\Company::modify/$1');
$routes->post('/company_access/contacts', 'Company_access::contacts');
$routes->post('/company_access/add_access', 'Company_access::add_access');
$routes->post('/user/upload_file', 'Home::upload_file');
$routes->post('/admin/branches/add', 'Admin\Branches::add');
$routes->post('/admin/branches/modify/(:any)', 'Admin\Branches::modify/$1');
$routes->post('/admin/branches/ajax_branches_view', 'Admin\Branches::ajax_branches_view');
$routes->post('/admin/branches/mark_branch_ho', 'Admin\Branches::mark_branch_ho');
$routes->post('/admin/branches/remove_branches', 'Admin\Branches::remove_branches');
$routes->post('/admin/banks/ajax_banks_view', 'Admin\Banks::ajax_banks_view');
$routes->post('/admin/banks/add', 'Admin\Banks::add');
$routes->post('/admin/banks/modify/(:any)', 'Admin\Banks::modify/$1');
$routes->post('/admin/banks/remove_banks', 'Admin\Banks::remove_banks');
$routes->post('/admin/voucher_series/ajax_series_list', 'Admin\Voucher_series::ajax_series_list');
$routes->post('/admin/voucher_series/add', 'Admin\Voucher_series::add');
$routes->post('/admin/voucher_series/edit/(:any)', 'Admin\Voucher_series::edit/$1');
$routes->post('/admin/voucher_series/AutoSeriesExists', 'Admin\Voucher_series::AutoSeriesExists'); 
$routes->post('/admin/accounts/ajax_accounts_view', 'Admin\Accounts::ajax_accounts_view');
$routes->post('/admin/accounts/add_group', 'Admin\Accounts::add_group');
$routes->post('/admin/accounts/ajax_list_groups', 'Admin\Accounts::ajax_list_groups');
$routes->post('/admin/accounts/modify_group/(:any)', 'Admin\Accounts::modify_group/$1');
$routes->post('/admin/accounts/add', 'Admin\Accounts::add');
$routes->post('/admin/accounts/modify/(:any)', 'Admin\Accounts::modify/$1');  
$routes->post('/admin/vouchers/invoice/(:any)', 'Admin\Vouchers::invoice/$1'); 
$routes->post('/admin/registerlog/ajax_voucher_register', 'Admin\Registerlog::ajax_voucher_register');
$routes->post('/admin/vouchers/edit/(:any)/(:any)', 'Admin\Vouchers::edit/$1/$2');
$routes->post('/admin/reports/ajax_day_book', 'Admin\Reportsbooks::ajax_day_book');
$routes->post('/admin/reports/account_ledger', 'Admin\Reportsaccounts::ledger');
$routes->post('/admin/accounts/ajax_ledger_detail', 'Admin\Accounts::ajax_ledger_detail');
$routes->post('/admin/reports/account_summary', 'Admin\Reportsaccounts::summary');
$routes->post('/admin/registerlog/ajax_draft_vouchers', 'Admin\Registerlog::ajax_draft_vouchers');
$routes->post('/admin/vouchers/draft_edit/(:any)', 'Admin\Vouchers::draft_edit/$1');
$routes->post('/admin/vouchers/finalize_draft/(:any)', 'Admin\Vouchers::finalize_draft/$1');
$routes->post('/admin/vouchers/memorandum/(:any)', 'Admin\Vouchers::memorandum/$1');
$routes->post('/admin/memorandum/edit/(:any)', 'Admin\Vouchers::memorandum_edit/$1');
$routes->post('/admin/registerlog/other_register', 'Admin\Registerlog::other_register');
$routes->post('/admin/office_tools/stickyNote', 'Admin\Office_tools::stickyNote');
$routes->post('/admin/office_tools/saveNote', 'Admin\Office_tools::saveNote');
$routes->post('/admin/office_tools/fetchNotes', 'Admin\Office_tools::fetchNotes');
$routes->post('/admin/office_tools/updateNoteOrder', 'Admin\Office_tools::updateNoteOrder');
$routes->post('/admin/office_tools/saveEvent', 'Admin\Office_tools::saveEvent');
$routes->post('/admin/material_centres/add_group', 'Admin\Material_centres::add_group');
$routes->post('/admin/material_centres/modify_group/(:any)', 'Admin\Material_centres::modify_group/$1');
$routes->post('/admin/material_centres/add_centres', 'Admin\Material_centres::add_centres');
$routes->post('/admin/material_centres/modify_centre/(:any)', 'Admin\Material_centres::modify_centre/$1');
$routes->post('/admin/items/add_group', 'Admin\Items::add_group');
$routes->post('/admin/items/modify_group/(:any)', 'Admin\Items::modify_group/$1');
$routes->post('/admin/items/add_category', 'Admin\Items::add_category');
$routes->post('/admin/items/modify_category/(:any)', 'Admin\Items::modify_category/$1');
$routes->post('/admin/units/add', 'Admin\Units::add');
$routes->post('/admin/units/modify/(:any)', 'Admin\Units::modify/$1');
$routes->post('/admin/items/add_item', 'Admin\Items::add_item');
$routes->post('/admin/items/ajax_items', 'Admin\Items::ajax_items');
$routes->post('/admin/items/modify_item/(:any)', 'Admin\Items::modify_item/$1');
$routes->post('/admin/item/get_last_qty_balance', 'Admin\Items::get_last_qty_balance');

$routes->get('/admin/export/balance_sheet', 'Admin\Export::balance_sheet');
$routes->get('/admin/export/profit_loss', 'Admin\Export::profit_loss');
$routes->get('/admin/export/gstrsummary_report_detail', 'Admin\Export::gstrsummary_report_detail');

$routes->get('/admin/export/accounts_trial', 'Admin\Export::accounts_trial');

 
 
 

$routes->post('/admin/cost_centres/add_group', 'Admin\Cost_centres::add_group');
$routes->post('/admin/cost_centres/modify_group/(:any)', 'Admin\Cost_centres::modify_group/$1');
$routes->post('/admin/cost_centres/remove_groups/(:any)', 'Admin\Cost_centres::remove_groups/$1');
$routes->post('/admin/cost_centres/ajax_cc', 'Admin\Cost_centres::ajax_cc');
$routes->post('/admin/cost_centres/modify_group/(:any)', 'Admin\Cost_centres::modify_group/$1');
$routes->post('/admin/cost_centres/add_cc', 'Admin\Cost_centres::add_cc');
$routes->post('/admin/cost_centres/modify_cc/(:any)', 'Admin\Cost_centres::modify_cc/$1');
$routes->post('/admin/cost_centres/ajax_cc', 'Admin\Cost_centres::ajax_cc');
$routes->post('/admin/project/add_group', 'Admin\Project::add_group');
$routes->post('/admin/project/ajax_group_list', 'Admin\Project::ajax_group_list');
$routes->post('/admin/project/edit_group/(:any)', 'Admin\Project::edit_group/$1');
$routes->post('/admin/project/add', 'Admin\Project::add');
$routes->post('/admin/project/ajax_list', 'Admin\Project::ajax_list');
$routes->post('/admin/project/edit/(:any)', 'Admin\Project::edit/$1');
$routes->post('/admin/project/delete', 'Admin\Project::delete');
$routes->post('/admin/accounts/changestatus', 'Admin\Accounts::change_status');
$routes->post('/admin/accounts/changestatus_group', 'Admin\Accounts::group_change_status');
$routes->post('/admin/items/changestatus', 'Admin\Items::change_status');
$routes->post('/admin/items/changestatus_group', 'Admin\Items::group_change_status');
$routes->post('/admin/items/changestatus_category', 'Admin\Items::category_change_status');
$routes->post('/admin/material_centres/changestatus_mc', 'Admin\Material_centres::mc_change_status');
$routes->post('/admin/material_centres/changestatus_group', 'Admin\Material_centres::group_change_status');
$routes->post('/admin/cost_centres/changestatus', 'Admin\Cost_centres::change_status');
$routes->post('/admin/cost_centres/changestatus_group', 'Admin\Cost_centres::group_change_status');
$routes->post('/admin/project/changestatus', 'Admin\Project::change_status');
$routes->post('/admin/project/changestatus_group', 'Admin\Project::group_change_status');
$routes->post('/admin/currency/ajax_list', 'Admin\Currency::ajax_list');
$routes->post('/admin/currency/add', 'Admin\Currency::add');
$routes->post('/admin/currency/edit/(:any)/(:any)', 'Admin\Currency::edit/$1/$2');
$routes->post('/admin/vouchers/getAccountBillRefs', 'Admin\Vouchers::getAccountBillRefs');
$routes->post('/admin/vouchers/getCc', 'Admin\Vouchers::getCc');
$routes->post('/admin/vouchers/getPurchaseCc', 'Admin\Vouchers::getPurchaseCc');
$routes->post('/admin/vouchers/getPr', 'Admin\Vouchers::getPr');
$routes->post('/admin/vouchers/getAccountSblgrRefs', 'Admin\Vouchers::getAccountSblgrRefs');
$routes->post('/admin/billbybill/ajax_bbb', 'Admin\Billbybill::ajax_bbb');
$routes->post('/admin/billbybill/changestatus', 'Admin\Billbybill::change_status');
$routes->post('/admin/subledger/changestatus', 'Admin\Subledger::change_status');
$routes->post('/admin/subledger/ajax_subledger', 'Admin\Subledger::ajax_subledger');
$routes->post('/admin/company/upload_file', 'Admin\Company::upload_file');

$routes->post('/admin/branches/add_more_gstin/(:num)', 'Admin\Branches::add_more_gstin/$1');
$routes->post('/admin/branches/modify_gstin/(:num)/(:num)', 'Admin\Branches::modify_gstin/$1/$2');



$routes->post('/admin/subledger_masters_dropdown', 'Admin\Reportsaccounts::ajax_subledger_masters_dropdown');
$routes->post('/admin/accounts/ajax_subledger_detail', 'Admin\Accounts::ajax_subledger_detail');
$routes->post('/admin/accounts/getAccountBills', 'Admin\Accounts::getAccountBills');
$routes->post('/admin/accounts/getAccountSblgrRefs', 'Admin\Accounts::getAccountSblgrRefs');
$routes->post('/admin/accounts/ValidateAccountBillRefs', 'Admin\Accounts::ValidateAccountBillRefs');
$routes->post('/admin/accounts/ValidateAccountSblgrRefs', 'Admin\Accounts::ValidateAccountSblgrRefs');
$routes->post('/admin/reports/project_reporting', 'Admin\Reportsprojects::index');
$routes->post('/admin/reports/ajax_project_account_trial', 'Admin\Reportsprojects::ajax_project_account_trial');
$routes->post('/admin/reports/ajax_project_account_ledger', 'Admin\Reportsprojects::ajax_project_account_ledger');
$routes->post('/admin/reports/cost_centre', 'Admin\Reportscc::index');
$routes->post('/admin/reports/ajax_cost_centre_account_ledger', 'Admin\Reportscc::ajax_cost_centre_account_ledger');
$routes->post('/admin/reports/ajax_cost_centre_account_wise', 'Admin\Reportscc::ajax_cost_centre_account_wise');
$routes->post('/admin/reports/ajax_cost_centre_wise', 'Admin\Reportscc::ajax_cost_centre_wise');
$routes->post('/admin/reports/bills_management', 'Admin\Reportsbills::index');
$routes->post('/admin/reports/ajax_billwise_acc_statement', 'Admin\Reportsbills::ajax_billwise_acc_statement');
$routes->post('/admin/reports/ajax_billwise_statement', 'Admin\Reportsbills::ajax_billwise_statement');
$routes->post('/admin/reports/ajax_bills_management_one_account', 'Admin\Reportsbills::ajax_bills_management_one_account');
$routes->post('/admin/reports/ajax_bills_management_details', 'Admin\Reportsbills::ajax_bills_management_details');
$routes->post('/admin/settings/load_default_profiles', 'Admin\Settings::load_default_profiles');
$routes->post('/admin/settings/ajax_major_access_profile', 'Admin\Settings::ajax_major_access_profile');
$routes->post('/admin/settings/ajax_major_level_3', 'Admin\Settings::ajax_major_level_3');
$routes->post('/admin/settings/save_access_permissions', 'Admin\Settings::save_access_permissions');
$routes->post('/admin/settings/add_access_profile', 'Admin\Settings::add_access_profile');
$routes->post('/company_access/get_company_profiles', 'Company_access::get_company_profiles');
$routes->post('/admin/settings/rights', 'Admin\Settings::checkRights');
$routes->post('/admin/settings/save_backdate_entry', 'Admin\Settings::save_backdate_entry');
$routes->post('/admin/vouchers/ValidateDateEntry', 'Admin\Vouchers::ValidateDateEntry');
$routes->post('/company_access/change_access', 'Company_access::change_user_profile');
$routes->post('/admin/settings/save_txnaprvl', 'Admin\Settings::save_txnaprvl');
$routes->post('/admin/approvals/ajax_voucher_approvals', 'Admin\Reportings::ajax_voucher_approvals');
$routes->post('/admin/taxcategory/ajax_category_view', 'Admin\Taxcategory::ajax_category_view');
$routes->post('/admin/taxcategory/add', 'Admin\Taxcategory::add');
$routes->post('/admin/taxcategory/modify/(:any)', 'Admin\Taxcategory::modify/$1');
$routes->post('/admin/taxcategory/changestatus', 'Admin\Taxcategory::change_status');
$routes->post('/admin/billsundry/add', 'Admin\Billsundry::add');
$routes->post('/admin/billsundry/ajax_billsundry', 'Admin\Billsundry::ajax_billsundry');
$routes->post('/admin/billsundry/modify/(:any)', 'Admin\Billsundry::modify/$1');
$routes->post('/admin/billsundry/changestatus', 'Admin\Billsundry::change_status');
$routes->post('/admin/ajax/GetAutoBillNo', 'Admin\Vouchers::GetAutoBillNo');
$routes->post('/admin/ajax/GetTax', 'Admin\Vouchers::GetTaxInfo');
$routes->post('/admin/sales/non_item', 'Admin\Sales::non_item');
$routes->post('/admin/ajax/checkTaxMasters', 'Admin\Billsundry::checkTaxMasters');
$routes->post('/admin/ajax/createDefaultTaxMasters', 'Admin\Billsundry::createDefaultTaxMasters');
$routes->post('/admin/registerlog/ajax_sale_register', 'Admin\Registerlog::ajax_sale_register');
$routes->post('/admin/registerlog/ajax_sale_return_register', 'Admin\Registerlog::ajax_sale_return_register');
$routes->post('/admin/registerlog/ajax_purchase_register', 'Admin\Registerlog::ajax_purchase_register');
$routes->post('/admin/registerlog/ajax_purchase_return_register', 'Admin\Registerlog::ajax_purchase_return_register');

$routes->post('/admin/sales/edit/(:any)', 'Admin\Sales::edit/$1');
$routes->post('/admin/bulk_updation/createMaster', 'Admin\Bulk_updation::createMaster');
$routes->post('/admin/bulk_updation/ajax_sales_transactions', 'Admin\Bulk_updation::ajax_sales_transactions');
$routes->post('/admin/sales/resave/(:any)', 'Admin\Sales::sales_voucher_resave/$1');
$routes->post('admin/sales/delete', 'Admin\Sales::delete_sale');
$routes->post('/admin/purchase/non_item', 'Admin\Purchase::non_item');
$routes->post('/admin/purchase/edit/(:any)', 'Admin\Purchase::edit/$1');
$routes->post('/admin/purchase/resave/(:any)', 'Admin\Purchase::purchase_voucher_resave/$1');
$routes->post('admin/purchase/delete', 'Admin\Purchase::delete_purchase');
$routes->post('/admin/stock_journal/invoice/(:any)', 'Admin\Stock_journal::invoice/$1');
$routes->post('/admin/registerlog/ajax_stock_journal_register', 'Admin\Registerlog::ajax_stock_journal_register');
$routes->post('/admin/stock_journal/edit/(:any)', 'Admin\Stock_journal::edit/$1');
$routes->post('admin/stock_journal/delete', 'Admin\Stock_journal::delete_stock');
$routes->post('/admin/vouchers/item', 'Admin\Vouchers::withitem');
$routes->post('/admin/get_item_units_list', 'Admin\Items::GetItemUnits'); 
$routes->post('/admin/reports/stock_ledger', 'Admin\ReportsStock::ledger'); 
$routes->post('/admin/items/ajax_item_ledger', 'Admin\Items::ajax_item_ledger');
$routes->post('/admin/items/ajax_get_item_units_list', 'Admin\Items::ajax_get_item_units_list');
$routes->post('/admin/sales/item', 'Admin\Sales::item');
$routes->post('/admin/purchase/item', 'Admin\Purchase::item');
$routes->post('/admin/credit_note/item', 'Admin\Credit_note::item');
$routes->post('/admin/credit_note/non_item', 'Admin\Credit_note::non_item');
$routes->post('/admin/credit_note/edit/(:any)', 'Admin\Credit_note::edit/$1');
$routes->post('admin/debit_note/delete', 'Admin\Debit_note::delete_debitnote');
$routes->post('admin/credit_note/delete', 'Admin\Credit_note::delete_creditnote');
$routes->post('/admin/debit_note/item', 'Admin\Debit_note::item');
$routes->post('/admin/debit_note/non_item', 'Admin\Debit_note::non_item');
$routes->post('/admin/debit_note/edit/(:any)', 'Admin\Debit_note::edit/$1');
$routes->post('/admin/vouchers/getItemBatch', 'Admin\Vouchers::getItemBatch');
$routes->post('/admin/reports/other_stock_report', 'Admin\ReportsStock::other_stock_report');
$routes->post('/admin/reports/ajax_batch_ledger_detail', 'Admin\ReportsStock::ajax_batches_report');
$routes->post('/admin/reports/ajax_batchwise_ledger_detail', 'Admin\ReportsStock::ajax_batchwise_report');
$routes->post('/admin/reports/load_stock_status', 'Admin\ReportsStock::ajax_load_stock_status');
$routes->post('/admin/company/sending_delfy_otp', 'Admin\Company::sending_delfy_otp');
$routes->post('/admin/company/confirmotp', 'Admin\Company::confirmotp');
$routes->post('/admin/rewritebooks/update_fy_balance', 'Admin\Rewritebooks::update_fy_balance');
$routes->post('/admin/rewritebooks/load_details', 'Admin\Rewritebooks::load_details');
$routes->post('/admin/rewritebooks/update_item_batch', 'Admin\Rewritebooks::update_item_batch');
$routes->post('/admin/rewritebooks/update_item_valuation_batch', 'Admin\Rewritebooks::update_item_valuation_batch');
$routes->post('/admin/office_tools/updateEvent', 'Admin\Office_tools::updateEvent');
$routes->post('/admin/office_tools/deleteEvent', 'Admin\Office_tools::deleteEvent');
$routes->post('/admin/office_tools/saveCalendar', 'Admin\Office_tools::saveCalendar');
$routes->post('/admin/office_tools/selectedCalendars', 'Admin\Office_tools::selectedCalendars');
$routes->post('/admin/stock_summary', 'Admin\Stock_summary::index');
$routes->post('preferences/save', 'Home::save_preferences');
$routes->post('/admin/gst/gstsummary', 'Admin\Gst::index');
$routes->post('/admin/gst/ajax_hsn_summary_list', 'Admin\Gst::ajax_hsn_summary_list');
$routes->post('/admin/gst/ajax_gstr_transactions', 'Admin\Gst::ajax_gstr_transactions');
$routes->post('/admin/my_credentials/add', 'Admin\My_credentials::add');
$routes->post('/admin/my_credentials/ajax_credentials_view', 'Admin\My_credentials::ajax_credentials_view');
$routes->post('/admin/my_credentials/modify/(:any)',  'Admin\My_credentials::modify/$1');
/*
GET routes
*/ 


$routes->get('/admin/eGSTR1Print', 'Admin\eGSTR1Print::index');
$routes->get('/admin/CreditNotePrint/(:num)', 'Admin\CreditNotePrint::index/$1');
$routes->get('/admin/AccountLedgerPrint/(:num)', 'Admin\AccountLedgerPrint::index/$1');
$routes->get('/admin/SalePrint/(:num)', 'Admin\SalePrint::index/$1');
$routes->get('/admin/TrialBalancePrint/(:num)', 'Admin\TrialBalancePrint::index/$1');
$routes->get('/admin/VoucherPrint/(:num)', 'Admin\VoucherPrint::index/$1');
$routes->get('/admin/BalanceSheetPrint', 'Admin\BalanceSheetPrint::index');
$routes->get('/admin/ajax/party_gst_info/(:any)', 'Admin\Accounts::party_gst_info/$1');
$routes->get('/admin/ajax/mc_info/(:any)', 'Admin\Accounts::mc_info/$1');
$routes->get('/admin/etaxes/eway', 'Admin\Etaxes::eway');
$routes->get('/admin/etaxes/eway_summary', 'Admin\Etaxes::eway_summary');
$routes->get('/admin/etaxes/einvoice', 'Admin\Etaxes::einvoice');
$routes->get('/admin/etaxes/einvoice_summary', 'Admin\Etaxes::einvoice_summary');

$routes->post('/admin/etaxes/download_gstr1_json', 'Admin\Etaxes::download_gstr1_json');
$routes->post('/admin/etaxes/download_gstr1iff_json', 'Admin\Etaxes::download_gstr1iff_json');


$routes->get('/admin/office_tools/allCalendars', 'Admin\Office_tools::allCalendars');
$routes->get('/', 'Login::index');
$routes->get('/login', 'Login::index');
$routes->post('/login/checkUser', 'Login::checkUser');
$routes->post('/login/checkPassword', 'Login::checkPassword');

$routes->get('home/companies', 'Home::companies');

$routes->get('/login/authentication/(:any)', 'Login::authentication/$1');
$routes->get('/companies', 'Home::companies');
$routes->get('/home/ajax_states_list/(:any)', 'Home::ajax_states_list/$1');
$routes->get('/sharedwithme', 'Sharedwithme::index');
$routes->get('/my_companies', 'Home::my_companies');
$routes->get('/archivecompany', 'Archivecompany::index');
$routes->get('/groupcompany', 'Groupcompany::index');
$routes->get('/admin/dashboard', 'Admin\Dashboard::index');
$routes->get('/admin/dashboard/makeactive/(:any)', 'Admin\Dashboard::makeactive/$1');
$routes->get('/admin/company/modify/(:any)', 'Admin\Company::modify/$1');
$routes->get('/ajax_select_company/(:any)', 'Home::ajax_select_company/$1');
$routes->get('/admin/ajax/accountbalance/(:any)/(:any)', 'Admin\Accounts::account_balance_info/$1/$2');
$routes->get('/admin/company/modify', 'Admin\Company::modify');
$routes->get('/admin/company/logo', 'Admin\Company::logo');
$routes->get('/admin/company/logo/(:any)', 'Admin\Company::logo/$1');
$routes->get('/remove_comp/(:any)', 'Home::remove_comp/$1');
$routes->get('/home/company_backup', 'Home::company_backup');
$routes->get('/home/recyclebin', 'Home::recyclebin');
$routes->get('/home/finally_remove_comp/(:any)', 'Home::finally_remove_comp/$1');
$routes->get('/home/restore_company/(:any)', 'Home::restore_company/$1');
$routes->get('/company_access', 'Company_access::index');
$routes->get('/home/business(:any)', 'Home::business');
$routes->get('/user/logo', 'Home::logo');
$routes->get('/admin/', 'Admin\Dashboard::index');
$routes->get('/admin/my_credentials/gst', 'Admin\My_credentials::index/1');
$routes->get('/admin/my_credentials', 'Admin\My_credentials::index');
$routes->get('/admin/my_credentials/add', 'Admin\My_credentials::add');
$routes->get('/admin/settings', 'Admin\Dashboard::settings');
$routes->get('/admin/signout', 'Login::logout'); 
$routes->get('/admin/dashboard/close_company?(:any)', 'Admin\Dashboard::close_company');
$routes->get('/admin/choose_company/(:any)', 'Home::ajax_select_company/$1');
$routes->get('/admin/change_company_fy/(:any)', 'Admin\Company::ajax_change_company_fy/$1');
$routes->get('/admin/choose_branch/(:any)', 'Home::ajax_select_branch/$1');
$routes->get('/home/add_company', 'Home::add_company');
$routes->get('/admin/trial_balance', 'Admin\Trial_balance::view');
$routes->get('/admin/trial_balance/account_ledger', 'Admin\Trial_balance::view');
$routes->get('/admin/branches', 'Admin\Branches::index');
$routes->get('/admin/branches/modify/(:any)', 'Admin\Branches::modify/$1');
$routes->get('/admin/branches/add', 'Admin\Branches::add');
$routes->get('/admin/banks', 'Admin\Banks::index');
$routes->get('/admin/banks/add', 'Admin\Banks::add');
$routes->get('/admin/banks/modify/(:any)', 'Admin\Banks::modify/$1');
$routes->get('/admin/voucher_series', 'Admin\Voucher_series::index');
$routes->get('/admin/voucher_series/add', 'Admin\Voucher_series::add');
$routes->get('/admin/voucher_series/edit/(:any)', 'Admin\Voucher_series::edit/$1');
$routes->get('/admin/voucher_series/delete/(:any)', 'Admin\Voucher_series::delete/$1');
$routes->get('/admin/accounts/list', 'Admin\Accounts::list');
$routes->get('/admin/accounts/list_group', 'Admin\Accounts::list_group');
$routes->get('/admin/accounts/add', 'Admin\Accounts::add');
$routes->get('/admin/accounts/add_group', 'Admin\Accounts::add_group');
$routes->get('/admin/accounts/modify_group/(:any)', 'Admin\Accounts::modify_group/$1');
$routes->get('/admin/accounts/remove_groups/(:any)', 'Admin\Accounts::remove_groups/$1');
$routes->get('/admin/accounts/modify/(:any)', 'Admin\Accounts::modify/$1');
$routes->get('/admin/accounts/remove_accounts/(:any)', 'Admin\Accounts::remove_accounts/$1');
$routes->get('/admin/vouchers/invoice/(:any)', 'Admin\Vouchers::invoice/$1');
$routes->get('/admin/registerlog/payment_register', 'Admin\Registerlog::payment_register');
$routes->get('/admin/registerlog/purchase_register', 'Admin\Registerlog::purchase_register');
$routes->get('/admin/registerlog/purchase_register_script', 'Admin\Registerlog::purchase_register_script');
$routes->get('/admin/registerlog/purchase_return_register', 'Admin\Registerlog::purchase_return_register');
$routes->get('/admin/branches/modify_gstin/(:num)/(:num)', 'Admin\Branches::modify_gstin/$1/$2');

$routes->get('/admin/branches/validate_remove_gstin/(:any)/(:any)', 'Admin\Branches::validate_remove_gstin/$1/$2');

$routes->get('/admin/vouchers/edit/(:any)/(:any)', 'Admin\Vouchers::edit/$1/$2');
$routes->get('/admin/vouchers/edit/(:any)/(:any)/(:any)', 'Admin\Vouchers::edit/$1/$2/$3');
$routes->get('/admin/registerlog/receipt_register', 'Admin\Registerlog::receipt_register');
$routes->get('/admin/registerlog/systemjournal_register', 'Admin\Registerlog::system_journal_register');
$routes->get('/admin/reports/day_book', 'Admin\Reportsbooks::day_book');
$routes->get('/admin/registerlog/contra_register', 'Admin\Registerlog::contra_register');
$routes->get('/admin/registerlog/journal_register', 'Admin\Registerlog::journal_register');
$routes->get('/admin/reports/account_ledger', 'Admin\Reportsaccounts::ledger');
$routes->get('/admin/ajax/accounts_search', 'Admin\Accounts::ajax_search_accounts');
$routes->get('/admin/accounts/ledger_detail/(:any)', 'Admin\Accounts::ledger_detail/$1');
$routes->get('/admin/accounts/LoadAccountTotals', 'Admin\Accounts::LoadAccountTotals');
$routes->get('/admin/reports/account_summary', 'Admin\Reportsaccounts::summary');
$routes->get('/admin/accounts/monthly_detail/(:num)', 'Admin\Accounts::monthly_detail/$1');
$routes->get('/admin/registerlog/ajax_draft_vouchers', 'Admin\Registerlog::ajax_draft_vouchers');
$routes->get('/admin/vouchers/draft_edit/(:any)', 'Admin\Vouchers::draft_edit/$1');
$routes->get('/admin/vouchers/delete/(:any)/(:any)', 'Admin\Vouchers::delete/$1/$2');
$routes->get('/admin/vouchers/delete_draft/(:any)', 'Admin\Vouchers::delete_draft/$1');
$routes->get('/admin/vouchers/memorandum/(:any)', 'Admin\Vouchers::memorandum/$1');
$routes->get('/admin/registerlog/other_register', 'Admin\Registerlog::other_register');
$routes->get('/admin/registerlog/memorandum_register', 'Admin\Registerlog::memorandum_register');
$routes->get('/admin/registerlog/optional_register', 'Admin\Registerlog::optional_register');
$routes->get('/admin/memorandum/edit/(:any)', 'Admin\Vouchers::memorandum_edit/$1');
$routes->get('/admin/reports/balance_sheet', 'Admin\Reportings::balance_sheet');
$routes->get('/admin/reports/trial_balance', 'Admin\Reportings::trial_balance');
$routes->get('/admin/reports/profit_loss', 'Admin\Reportings::profit_loss');
$routes->get('/admin/accounts/accounts_trial/(:any)', 'Admin\Accounts::accounts_trial/$1');
$routes->get('/admin/material_centres', 'Admin\Material_centres::list_centres');
$routes->get('/admin/material_centres/list_centres', 'Admin\Material_centres::list_centres');
$routes->get('/admin/material_centres/group_list', 'Admin\Material_centres::group_list');
$routes->get('/admin/material_centres/add_group', 'Admin\Material_centres::add_group');
$routes->get('/admin/material_centres/modify_group/(:any)', 'Admin\Material_centres::modify_group/$1');
$routes->get('/admin/material_centres/remove_centre_grps/(:any)', 'Admin\Material_centres::remove_centre_grps/$1');
$routes->get('/admin/material_centres/add_centres', 'Admin\Material_centres::add_centres');
$routes->get('/admin/material_centres/modify_centre/(:any)', 'Admin\Material_centres::modify_centre/$1');
$routes->get('/admin/material_centres/remove_centres/(:any)', 'Admin\Material_centres::remove_centres/$1');
$routes->get('/admin/items/list_items', 'Admin\Items::list_items');
$routes->get('/admin/items', 'Admin\Items::list_items');
$routes->get('/admin/items/list_group', 'Admin\Items::list_group');
$routes->get('/admin/items/add_group', 'Admin\Items::add_group');
$routes->get('/admin/items/modify_group/(:any)', 'Admin\Items::modify_group/$1');
$routes->get('/admin/items/remove_groups/(:any)', 'Admin\Items::remove_groups/$1');
$routes->get('/admin/items/stock_category', 'Admin\Items::stock_category');
$routes->get('/admin/items/add_category', 'Admin\Items::add_category');
$routes->get('/admin/items/modify_category/(:any)', 'Admin\Items::modify_category/$1');
$routes->get('/admin/items/remove_catgeory/(:any)', 'Admin\Items::remove_catgeory/$1');
$routes->get('/admin/items/remove_items/(:any)', 'Admin\Items::remove_items/$1');
$routes->get('/admin/units/list', 'Admin\Units::list');
$routes->get('/admin/units/add', 'Admin\Units::add');
$routes->get('/admin/units/modify/(:any)', 'Admin\Units::modify/$1');
$routes->get('/admin/units/remove_units/(:any)', 'Admin\Units::remove_units/$1');
$routes->get('/admin/items/add_item', 'Admin\Items::add_item');
$routes->get('/admin/items/modify_item/(:any)', 'Admin\Items::modify_item/$1');
$routes->get('/admin/cost_centres/list', 'Admin\Cost_centres::list');
$routes->get('/admin/cost_centres', 'Admin\Cost_centres::list');
$routes->get('/admin/cost_centres/list_group', 'Admin\Cost_centres::list_group');
$routes->get('/admin/cost_centres/add_group', 'Admin\Cost_centres::add_group');
$routes->get('/admin/cost_centres/modify_group/(:any)', 'Admin\Cost_centres::modify_group/$1');
$routes->get('/admin/cost_centres/remove_groups/(:any)', 'Admin\Cost_centres::remove_groups/$1');
$routes->get('/admin/cost_centres/add_cc', 'Admin\Cost_centres::add_cc');
$routes->get('/admin/cost_centres/modify_cc/(:any)', 'Admin\Cost_centres::modify_cc/$1');
$routes->get('/admin/cost_centres/remove_cc/(:any)', 'Admin\Cost_centres::remove_cc/$1');
$routes->get('/admin/project', 'Admin\Project::index');
$routes->get('/admin/project/groups', 'Admin\Project::groups');
$routes->get('/admin/project/add_group', 'Admin\Project::add_group');
$routes->get('/admin/project/edit_group/(:any)', 'Admin\Project::edit_group/$1');
$routes->get('/admin/project/add', 'Admin\Project::add');
$routes->get('/admin/project/edit/(:any)', 'Admin\Project::edit/$1');
$routes->get('/admin/currency', 'Admin\Currency::index');
$routes->get('/admin/currency/add', 'Admin\Currency::add');
$routes->get('/admin/currency/edit/(:any)/(:any)', 'Admin\Currency::edit/$1/$2');
$routes->get('/admin/currency/delete/(:any)', 'Admin\Currency::delete_currency/$1');
$routes->get('/admin/billbybill', 'Admin\Billbybill::list');
$routes->get('/admin/subledger', 'Admin\Subledger::list');
$routes->get('/admin/dashboard/pieChart', 'Admin\Dashboard::pieChart');
$routes->get('/admin/dashboard/revenueChart', 'Admin\Dashboard::revenueChart');
$routes->get('/admin/dashboard/quickAssets', 'Admin\Dashboard::quickAssets');
$routes->get('/admin/dashboard/cashEquivalentChart', 'Admin\Dashboard::cashEquivalentChart');
$routes->get('/admin/dashboard/profitChart', 'Admin\Dashboard::profitChart');
$routes->get('/admin/dashboard/netWorthChart', 'Admin\Dashboard::netWorthChart');
$routes->get('/admin/dashboard/cashFlowChart', 'Admin\Dashboard::cashFlowChart');
$routes->get('/admin/MasterExport/accounts', 'Admin\MasterExport::accounts');
$routes->get('/admin/MasterExport/account_groups', 'Admin\MasterExport::account_groups');

$routes->get('/admin/MasterExport/items', 'Admin\MasterExport::items');
$routes->get('/admin/MasterExport/item_groups', 'Admin\MasterExport::item_groups');

$routes->get('/admin/accounts/subledger_detail/(:any)', 'Admin\Accounts::acc_subledger_detail/$1');
$routes->get('/admin/accounts/LoadAccountSubLedgerTotals', 'Admin\Accounts::LoadAccountSubLedgerTotals');
$routes->get('/admin/reports/project_reporting', 'Admin\Reportsprojects::index');
$routes->get('/admin/reports/project_trial', 'Admin\Reportsprojects::project_account_trial');
$routes->get('/admin/reports/project_account_ledger/(:any)', 'Admin\Reportsprojects::project_account_ledger/$1');
$routes->get('/admin/reports/cost_centre', 'Admin\Reportscc::index');
$routes->get('/admin/reports/cost_centre_account_ledger/(:any)', 'Admin\Reportscc::cost_centre_account_ledger/$1');
$routes->get('/admin/reports/cost_centre_account_wise', 'Admin\Reportscc::cost_centre_account_wise');
$routes->get('/admin/reports/cost_centre_wise', 'Admin\Reportscc::cost_centre_wise');
$routes->get('/admin/reports/bills_management', 'Admin\Reportsbills::index');
$routes->get('/admin/reports/bills_management_accounts/(:any)', 'Admin\Reportsbills::bills_management_accounts/$1');
$routes->get('/admin/reports/bills_management_statement/(:any)', 'Admin\Reportsbills::bills_management_statement/$1');
$routes->get('/admin/reports/bills_management_one_account/(:any)/(:any)', 'Admin\Reportsbills::bills_management_one_account/$1/$2');
$routes->get('/admin/reports/bills_management_details/(:any)/(:any)', 'Admin\Reportsbills::bills_management_details/$1/$2');
$routes->get('/admin/dashboard/logs', 'Admin\Dashboard::logs');
$routes->get('/admin/settings/general', 'Admin\Settings::general');
$routes->get('/admin/settings/access_profile', 'Admin\Settings::access_profile');
$routes->get('/home/open_company', 'Home::companies');
$routes->get('/admin/approvals/txn', 'Admin\Reportings::txn_approvals');
$routes->get('/admin/taxcategory', 'Admin\Taxcategory::index');
$routes->get('/admin/taxcategory/add', 'Admin\Taxcategory::add');
$routes->get('/admin/taxcategory/modify/(:any)', 'Admin\Taxcategory::modify/$1');
$routes->get('/admin/taxcategory/remove_category/(:any)', 'Admin\Taxcategory::remove_category/$1');
$routes->get('/admin/billsundry', 'Admin\Billsundry::index');
$routes->get('/admin/billsundry/add', 'Admin\Billsundry::add');
$routes->get('/admin/billsundry/modify/(:any)', 'Admin\Billsundry::modify/$1');
$routes->get('/admin/billsundry/remove_billsundry/(:any)', 'Admin\Billsundry::remove/$1');
$routes->get('/admin/billsundry/ledger_detail/(:any)', 'Admin\Accounts::bsd_ledger_detail/$1');
$routes->get('/admin/billsundry/subledger_detail/(:any)', 'Admin\Accounts::acc_subledger_detail/$1');
$routes->get('/admin/billsundry/LoadAccountSubLedgerTotals', 'Admin\Accounts::LoadAccountSubLedgerTotals');
$routes->get('/office_tools', 'Home::office_tools');
$routes->get('/admin/sales/non_item', 'Admin\Sales::non_item');
$routes->get('/admin/sales/edit/(:any)', 'Admin\Sales::edit/$1');
$routes->get('/admin/sales/edit/(:any)/(:any)', 'Admin\Sales::edit/$1/$2');
$routes->get('/admin/registerlog/sale_register', 'Admin\Registerlog::sale_register');
$routes->get('/admin/registerlog/sale_register_script', 'Admin\Registerlog::sale_register_script');
$routes->get('/admin/registerlog/sale_return_register_script', 'Admin\Registerlog::sale_return_register_script');
$routes->get('/admin/registerlog/purchase_return_register_script', 'Admin\Registerlog::purchase_return_register_script');
$routes->get('/admin/registerlog/sale_return_register', 'Admin\Registerlog::sale_return_register');
$routes->get('/admin/bulk_updation', 'Admin\Bulk_updation::index');
$routes->get('/admin/bulk_updation/sales_voucher_resave', 'Admin\Bulk_updation::sales_voucher_resave');
$routes->get('/admin/purchase/non_item', 'Admin\Purchase::non_item');
$routes->get('/admin/purchase/edit/(:any)', 'Admin\Purchase::edit/$1');
$routes->get('/admin/purchase/edit/(:any)/(:any)', 'Admin\Purchase::edit/$1/$2');
$routes->get('/admin/stock_journal/invoice/(:any)', 'Admin\Stock_journal::invoice/$1');
$routes->get('/admin/stock_journal/edit/(:any)', 'Admin\Stock_journal::edit/$1');
$routes->get('/admin/registerlog/others', 'Admin\Registerlog::others');
$routes->get('/admin/vouchers/item', 'Admin\Vouchers::withitem');
$routes->get('/admin/reports/stock_ledger', 'Admin\ReportsStock::ledger');
$routes->get('/admin/items/ledger_detail/(:any)', 'Admin\Items::ledger_detail/$1');
$routes->get('/admin/items/get_item_summary_totals/(:any)', 'Admin\Items::get_item_summary_totals/$1');
$routes->get('/admin/items/get_item_closing_totals/(:any)', 'Admin\Items::get_item_closing_balances/$1');
$routes->get('/admin/items/get_item_opening_totals/(:any)', 'Admin\Items::get_item_opening_balances/$1');
$routes->get('/admin/sales/item', 'Admin\Sales::item');
$routes->get('/admin/purchase/item', 'Admin\Purchase::item');
$routes->get('/admin/credit_note/item', 'Admin\Credit_note::item');
$routes->get('/admin/credit_note/non_item', 'Admin\Credit_note::non_item');
$routes->get('/admin/credit_note/edit/(:any)', 'Admin\Credit_note::edit/$1');
$routes->get('/ErpCronJob', 'ErpCronJob::index');
$routes->get('/admin/debit_note/item', 'Admin\Debit_note::item');
$routes->get('/admin/debit_note/non_item', 'Admin\Debit_note::non_item');
$routes->get('/admin/debit_note/edit/(:any)', 'Admin\Debit_note::edit/$1');
$routes->get('/admin/reports/other_stock_report', 'Admin\ReportsStock::other_stock_report');
$routes->get('/admin/reports/allbatches', 'Admin\ReportsStock::all_batch_report');
$routes->get('/admin/reports/batchwise/(:any)', 'Admin\ReportsStock::batch_wise_report/$1');
$routes->get('/admin/reports/stock_status', 'Admin\ReportsStock::stock_status');
$routes->get('/ErpCronJob', 'ErpCronJob::index');
$routes->get('/crondashboard', 'ErpCronJobDashboard::index');
$routes->get('/admin/company/remove_company_fy', 'Admin\Company::remove_company_fy');
$routes->get('/admin/rewritebooks', 'Admin\Rewritebooks::index');
$routes->post('/admin/rewritebooks/update_account_batch', 'Admin\Rewritebooks::update_account_batch');

$routes->get('/admin/stock_summary', 'Admin\Stock_summary::index');
$routes->get('/admin/reports/item_summary', 'Admin\Stock_summary::item_summary');
$routes->get('/admin/export/account_ledger_condensed', 'Admin\Export::account_ledger_condensed');
$routes->get('/admin/export/account_ledger_columnar', 'Admin\Export::account_ledger_columnar');
$routes->get('/admin/export/account_ledger_detailed', 'Admin\Export::account_ledger_detailed'); 
$routes->get('/admin/export/item_summary', 'Admin\Export::item_summary'); 
$routes->get('/admin/export/account_summary', 'Admin\Export::account_summary'); 
$routes->get('/admin/export/stock_status', 'Admin\Export::stock_status'); 
$routes->get('/admin/banking_voucher_print', 'Admin\Export::banking_voucher_print');
$routes->get('/admin/export/trial_balance', 'Admin\Export::trial_balance');

$routes->get('/admin/gst/gstsummary', 'Admin\Gst::index');

$routes->get('/admin/gst/outwardsupplies', 'Admin\Gst::outwardsupplies');
$routes->get('/admin/gstr1/report_detail', 'Admin\Gst::gstr1_report_detail');
$routes->get('/admin/gstr1/ledger', 'Admin\Gst::gstr1_ledger');
$routes->post('/admin/gstr1/transactions', 'Admin\Gst::ajax_gstr1_transactions');

$routes->get('/admin/etaxes/preview_gstr1_report', 'Admin\Etaxes::preview_gstr1_report');


$routes->get('/admin/gst/gstsummary_report_detail', 'Admin\Gst::gstsummary_report_detail');
$routes->get('/admin/gst/hsn_summary_ledger', 'Admin\Gst::hsn_summary_ledger');
$routes->get('/admin/gst/ledger', 'Admin\Gst::ledger');
$routes->get('/admin/my_credentials/remove/(:any)',  'Admin\My_credentials::remove/$1');
$routes->get('/admin/my_credentials/modify/(:any)',  'Admin\My_credentials::modify/$1');
$routes->get('/admin/bulk_updation/account_address_update',  'Admin\Bulk_updation::account_address_update');


$routes->get('/admin/bulk_updation/load_accounts',  'Admin\Bulk_updation::load_accounts');