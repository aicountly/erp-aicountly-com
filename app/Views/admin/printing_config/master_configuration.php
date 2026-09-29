<?php $header = array( 	'title' => 'Printing configuration' ); ?>
<?php echo view('includes/header',$header); ?>
<style>
:root {
            --primary-green: #25b003;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
        }
   /*.main-container {*/
   /*         background: white;*/
   /*         border-radius: 8px;*/
        
   /*     }*/






        .header {
            padding: 14px 0px;
            border-bottom: 1px solid var(--border-color);
            border-radius: 8px 8px 0 0;
        }

        .header h1 {
            color: #333;
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .taskmenus {
            display: flex;
            gap: 4px;
            align-items: center;
            
        }

        .taskmenus span {
            font-size: 30px;
            color: #000;
            padding: 5px;
            border-radius: 50%;
            cursor: pointer;
        }

        .taskmenus a {
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
       
        }

        .taskmenus a:hover {
            background: #e9ecef;
            color: #333;
            border-radius: 50%;
        }




        /* .material-symbols-outlined {
            font-size: 18px;
            font-weight: 500;
        } */

        .back-btn {
            border: 1px solid #25b003;
            color: var(--primary-green);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12.8px;
            font-weight: 700;
        }

        .back-btn:hover {
            background: #25b003;
            border: none;
            color: white;
        }

        .content-wrapper {
            display: flex;
            min-height: 600px;
        }

        .sidebar {
            /*background: var(--light-gray);*/
            width: 280px;
            padding: 20px 0;
            border-right: 1px solid var(--border-color);
        }

       .sidebar  .nav-item {
            margin: 5px 15px;
            border-radius: 8px;
        }

        .nav-link {
            font-weight: 700;
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #666;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background: white;
            color: #333;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .nav-link.active {
            font-weight: 700;
            background: white;
            color: var(--primary-green);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-left: 3px solid var(--primary-green);
        }

        .nav-icon {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            padding: 25px;
        }

        /* Grid layout for boxes */
        .sections-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .section-box {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: relative;
        }

        .section-header {
            background: var(--light-gray);
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            position: relative;
        }

        .section-title {
            color: #333;
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
            text-align: center;
            /* display: flex; */
            align-items: center;
        }

        .section-title i {
            margin-right: 10px;
            color: var(--primary-green);
            font-size: 1.1rem;
        }

        .status-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 20px;
            height: 20px;
            background: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-icon i {
            color: white;
            font-size: 0.7rem;
        }

        .section-content {
            padding: 20px;
        }

        .config-row {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            /*padding: 10px 12px;*/
            transition: all 0.2s ease;
            width: 100%;
            font-size: 0.9rem;
            background: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-green);
            box-shadow: 0 0 0 0.2rem rgba(37, 176, 3, 0.25);
            outline: none;
        }

        .btn-go {
            background: var(--primary-green);
            border: none;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.2s ease;
            width: 20%;
            font-size: 15px;
            margin-left: 40%;
        }

        .btn-go:hover {
            background: rgb(30, 140, 2);
             color: white;
        }
		.btn-go a:hover {
             color: white;
			 text-decoration:none;
        }
		.btn-go a{
             color: white;
        }

        /* Checkbox styles - only for Credit Notes */
        .checkbox-group {
            margin-top: 15px;
        }
        .section-content label {
             font-weight: 500;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .checkbox-item input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.1);
            accent-color: var(--primary-green);
        }

        .checkbox-item label {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
            cursor: pointer;
        }
       
        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
            
            .sections-grid {
                grid-template-columns: 1fr;
            }
			
        }
</style>
<div class="container-fluid">
        <div class="main-container">
            <!-- Header -->
          
             
            <div class="header">
                <div class="d-flex justify-content-between align-items-center">
                    <h1><i class="fas fa-print me-2"></i>Printing configuration(Transactions)</h1>
                    <div class="d-flex align-items-center ">
                        <div class="taskmenus">     
                            <a data-bs-toggle="offcanvas" href="#moreoptions" role="button" aria-controls="moreoptions"><span class="material-symbols-outlined open-comingsoon">offline_bolt</span></a>
                            <a href="#"><span class="material-symbols-outlined open-comingsoon">print</span></a>
                            <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">download</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">CSV</a></li>
                                <li><a class="dropdown-item" href="#">Excel</a></li>
                                <li><a class="dropdown-item" href="#">Document</a></li>
                            </ul>
                            <a href="#" data-bs-toggle="dropdown" aria-expanded="false"><span class="material-symbols-outlined open-comingsoon">share</span></a> 
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Facebook</a></li>
                                <li><a class="dropdown-item" href="#">Twitter</a></li>
                                <li><a class="dropdown-item" href="#">Instagram</a></li>
                            </ul>       
                        </div>
                        <a href="<?php echo history_back();?>" class="back-btn btn btn-outline-success btn-sm showinline-md">
                          « Back
                        </a>
                    </div>
                </div>
            </div>
             

            <!-- Content -->
            <div class="content-wrapper">
                <!-- Sidebar -->
                <div class="sidebar">
                    <div class="nav-item">
                        <a href="#" class="nav-link active">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            Sales
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-shopping-bag nav-icon"></i>
                            Purchases
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-university nav-icon"></i>
                            Banking
                        </a>
                    </div>
                    <div class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="fas fa-box nav-icon"></i>
                            Items
                        </a>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="main-content">
                    <div class="sections-grid">
                        <!-- Sale Invoice Box -->
                        <div class="section-box">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-file-invoice"></i>
                                    Sale Invoice
                                </h3>
                               
                            </div>
                            <div class="section-content">
                                <div class="config-row">
                                    <div>
									<?php $sel_sale_sersval = arrayfrstval($sale_voucher_series);?>
                                         <label class="mb-2">Voucher Series</label>
										 <?php echo form_dropdown('sale_voucher_series', $sale_voucher_series,$sel_sale_sersval,' id="sale_voucher_series" class="voucher_series form-select" ');?>                                       
                                    </div>
                                    <button class="btn btn-go"><a href="<?php echo $base_url;?>printing_config/setdefault_template/transactions/sales/sale_invoice">GO</a></button>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Order Box -->
                        <div class="section-box">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-clipboard-list"></i>
                                    Sales Order
                                </h3>
                              
                            </div>
                            <div class="section-content">
                                <div class="config-row">
                                    <div>
									<?php
									$sel_saleordr_sersval = arrayfrstval($sale_order_series);
									?>
                                         <label class="mb-2">Voucher Series</label>
                                        <?php echo form_dropdown('saleordr_voucher_series', $sale_order_series,$sel_saleordr_sersval,' id="saleordr_voucher_series" class="voucher_series form-select" ');?> 
                                    </div>
                                     <button class="btn btn-go"><a href="<?php echo $base_url;?>printing_config/setdefault_template/transactions/sales/sales_order">GO</a></button>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Notes Box (with checkboxes) -->
                        <div class="section-box">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-credit-card"></i>
                                    Credit Notes
                                </h3>
                               
                            </div>
                            <div class="section-content">
                                <div class="config-row">
                                    <div><?php
									$sel_crnote_sersval = arrayfrstval($creditnote_series);
									?>
                                      <label class="mb-2">Voucher Series</label>
                                       <?php echo form_dropdown('crnote_voucher_series', $creditnote_series,$sel_crnote_sersval,' id="crnote_voucher_series" class="voucher_series form-select" ');?> 
                                    </div>
                                    <button class="btn btn-go"><a href="<?php echo $base_url;?>printing_config/setdefault_template/transactions/sales/credit_notes">GO</a></button>
                                </div>
                                <!-- Only Credit Notes has checkboxes -->
                               
                            </div>
                        </div> 
                        <!-- Quotations Box -->
                        <div class="section-box">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-quote-right"></i>
                                    Quotations
                                </h3>
                              
                            </div>
                            <div class="section-content">
                                <div class="config-row">
                                    <div><?php $sel_quotation_sersval = arrayfrstval($quotations_series);;?>
                                         <label class="mb-2">Voucher Series</label>
                                        <?php echo form_dropdown('qtn_voucher_series', $quotations_series,$sel_quotation_sersval,' id="qtn_voucher_series" class="voucher_series form-select" ');?> 
                                    </div>
                                    <button class="btn btn-go"><a href="<?php echo $base_url;?>printing_config/setdefault_template/transactions/sales/quotations">GO</a></button>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Challan Box -->
                        <div class="section-box">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="fas fa-truck"></i>
                                    Delivery Challan
                                </h3>
                            
                            </div>
                            <div class="section-content">
                             <div class="checkbox-group">
                                 <label class="mb-2">Voucher Series</label>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="credit-default1">
                                        <label for="credit-default1">Default checkbox</label>
                                    </div>
                                    <div class="checkbox-item">
                                        <input type="checkbox" id="credit-default2">
                                        <label for="credit-default2">Default checkbox</label>
                                    </div>
                                       <div class="checkbox-item">
                                        <input type="checkbox" id="credit-default2">
                                        <label for="credit-default2">Default checkbox</label>
                                    </div>
                                       <div class="checkbox-item">
                                        <input type="checkbox" id="credit-default2">
                                        <label for="credit-default2">Default checkbox</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  
<?php echo view('includes/footer_scripts'); ?>
<script>
$(".openmaster_div").on("click",function(){
  $(this).addClass('activetab').siblings().removeClass('activetab');	
  var divid = $(this).data("id");	  
  $(".subdivs").hide();
  $("#"+divid).show();  
});
document.addEventListener('DOMContentLoaded', function() {
            // Sidebar navigation
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Simple GO button click (no loading animation)
            const goBtns = document.querySelectorAll('.btn-go');
            goBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Simple click feedback
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                });
            });
        });
</script>
</body>
</html>
