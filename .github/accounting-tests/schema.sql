-- SANDBOX-ONLY schema for exercising the report code. Column names are taken from the
-- application's own queries/inserts; production DDL is not in the repository.
DROP TABLE IF EXISTS hobogstinm, itmoppyval, accttxnmst, accoppybal, undercrsmt, acctmaster, accgrpmstn, grpparentn, cmpfymastr, vchtxnconso, hobomaster, acctmstdet, cmpmastern, vchtypemst, vchbridgen, vchgstsumn, gstrinwsup, itemtxnmst, billsundry, cmptxnmstn, acctvchreg CASCADE;

CREATE TABLE cmpfymastr (
  cmpfymastr_id serial PRIMARY KEY, cmp_id int, fy_beg_date date, fy_end_date date,
  def_val_method int, is_imported int DEFAULT 0
);
CREATE TABLE grpparentn (
  acc_grp_parent_id int PRIMARY KEY, acc_grp_parent_name text, acc_grp_parent_restrict int DEFAULT 0
);
CREATE TABLE accgrpmstn (
  acc_grp_id serial PRIMARY KEY, cmp_id int, acc_grp_name text, acc_grp_alias text,
  acc_grp_is_active int DEFAULT 1
);
CREATE TABLE acctmaster (
  acc_id serial PRIMARY KEY, cmp_id int, acc_name text, acc_alias text, acc_print_name text,
  bsd_id int NULL, contact_id int NULL, tax_cat_mst_id int NULL, acc_is_active int DEFAULT 1,
  acc_is_restrict int DEFAULT 0
);
CREATE TABLE undercrsmt (
  cmp_id int, cmpfymastr_id int, crs_is_active int DEFAULT 1, crs_mst_id int, crs_mst_type int,
  under_crs_mst_id int DEFAULT 0, crs_mst_parent_id int DEFAULT 0, under_main_id int DEFAULT 0,
  crs_mst_is_primary int DEFAULT 0
);
CREATE TABLE accoppybal (
  cmp_id int, cmpfymastr_id int, acc_id int, hobo_id int, acc_op_bal numeric(18,2) DEFAULT 0,
  acc_py_bal numeric(18,2) DEFAULT 0, acc_memo_bal numeric(18,2) DEFAULT 0, bsd_id int NULL
);
CREATE TABLE accttxnmst (
  acc_txn_id bigserial PRIMARY KEY, cmp_id int, hobo_id int, acc_id int, acc_txn_date date,
  acc_txn_dr_cr int, acc_txn_amt numeric(18,2), acc_txn_fcy numeric(18,2) DEFAULT 0,
  acc_txn_type int DEFAULT 1, txn_id int DEFAULT 0, vch_txn_id int DEFAULT 0
);
CREATE TABLE vchtxnconso (
  vch_txn_id serial PRIMARY KEY, cmp_id int, hobo_id int, txn_id int, vch_type_id int,
  vch_date date, acc_txn_type int DEFAULT 1, vch_sub_type_id int NULL, vch_series_id int NULL
);
CREATE INDEX ON accttxnmst (cmp_id, acc_id, acc_txn_date);
CREATE INDEX ON undercrsmt (cmp_id, cmpfymastr_id, crs_mst_type, crs_mst_id);

CREATE TABLE itmoppyval (
  cmp_id int, cmpfymastr_id int, hobo_id int, itm_id_unit_id text, itm_op_val_amt numeric(18,2),
  itm_py_val_amt numeric(18,2) DEFAULT 0, itm_val_method_id int, mat_cent_id int
);

CREATE TABLE hobomaster (cmp_id int, hobo_id int, hobo_name text);
CREATE TABLE acctmstdet (cmp_id int, acc_id int, acc_is_sys_acc int, acc_is_sez int);
CREATE TABLE cmpmastern (cmp_id int PRIMARY KEY, cmp_name text, cmp_status int DEFAULT 1);
CREATE TABLE vchtypemst (vch_type_id int PRIMARY KEY, vch_name text);

-- tables read only by the audit's voucher detail (column names from the application's own inserts)
CREATE TABLE vchbridgen (cmp_id int, vch_bridge_type int, vch_txn_id_src int, vch_txn_id_dest int);
CREATE TABLE vchgstsumn (
  cmp_id int, vch_txn_id int, txn_id int, acc_bsd_id int, acc_bsd_type int, vch_taxable_value numeric(18,2) DEFAULT 0,
  vch_igst numeric(18,2) DEFAULT 0, vch_cgst numeric(18,2) DEFAULT 0, vch_sgst_ugst numeric(18,2) DEFAULT 0,
  vch_cess numeric(18,2) DEFAULT 0, vch_total_tax numeric(18,2) DEFAULT 0
);
CREATE TABLE gstrinwsup (cmp_id int, vch_txn_id int, inwsup_bill_ref_no text);
CREATE TABLE itemtxnmst (cmp_id int, hobo_id int, vch_txn_id int, itm_id_unit_id text, itm_txn_qty numeric(18,4), itm_txn_amt numeric(18,2), itm_txn_type int DEFAULT 1, itm_txn_dr_cr int DEFAULT 1);
CREATE TABLE hobogstinm (cmp_id int, hobo_id int, hobo_gstin_type int);
-- bill-sundry master: which ledger carries which GST component. bsd_type 1 = tax, bsd_input_output 1 = input
-- / 2 = output, tax_cat_type 1 = GST, tax_cat_sub_type 1 IGST 2 CGST 3 SGST 4 UTGST 5 cess.
CREATE TABLE billsundry (bsd_id int PRIMARY KEY, bsd_name text, bsd_type int, bsd_input_output int, tax_cat_type int, tax_cat_sub_type int);
-- the composition-transaction row every ledger row points at, and the voucher register (a listing table)
CREATE TABLE cmptxnmstn (txn_id serial PRIMARY KEY, cmp_id int, vch_series_id int, vch_txn_id int, master_id int, master_id_type text);
CREATE TABLE acctvchreg (
  acct_vch_reg_id bigserial PRIMARY KEY, acct_vch_type int, cmp_id int, vch_txn_id int, txn_id int NULL,
  vch_date date, acc_id int, acc_txn_dr_amt numeric(18,2) DEFAULT 0, acc_txn_cr_amt numeric(18,2) DEFAULT 0,
  vch_narr text, hobo_id int, acc_txn_type int DEFAULT 1
);
