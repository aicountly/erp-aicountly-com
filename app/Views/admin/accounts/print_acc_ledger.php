<?php $header = array( 	'title' => 'Account Ledger' ); ?>
<?php echo view('includes/header',$header); ?>

<style>
        body {
            width: 210mm;
            font-family:'Segoe UI','Arial';
            font-size:8.5pt; 
            margin: 0 auto;
            padding: 0;
          
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* border: 1px solid black; */
            border-bottom: none;
            padding: 10px;
        }

        .header div {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .header h4 {
            text-align: center;
            margin: 0;
         
        }

        table {
            border-collapse: collapse;
            width:100%; font-family:'Segoe UI';   
            font-size:8.5pt;
        }

        th {
            border: 1px solid black;
            padding: 8px;
            text-overflow: ellipsis;
           
        }
        td {
            padding : 4px 6px;
            border-left: 1px solid;
            border-right: 1px solid;
            text-align: center;
        
        }

        th.particulars {
            text-align: left;
        }
        td.particulars {
            text-align: left;
        }

        .date-account-row th {
            text-align: left;
            padding: 5px 0px 5px 15px;
     
        }

        .footer-account-row th {
            text-align: right;
            padding: 5px 15px 5px 0px;
   
        }

        .header-info {
            text-align: left;
            padding-left: 15px;
        }
        .balance-sheet{
            border-top: 1px solid black; 
            width: 50%;
            margin-top: 0;
            margin-bottom: 5px;
        }

        th.debit, th.credit, th.balance {
             font-weight: bold;
          
         }

       td.debit, td.credit, td.balance {
        text-align: right;
         }


        /* Style for the download button */
        .pdf-download-btn {
            display: block;
            margin: 10px auto;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            font-size: 14px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .pdf-download-btn:hover {
            background-color: #218838;
        }

    </style>

<div id="ledgerContent">
    <table>
        <div class="header">
            <div>
                <span>Logo</span>
                <span>Here</span>
            </div>
            <div style="padding-left: 50px;">
                <h4><strong style="font-size: 10.5pt;">Ledger</strong></h4>
                <hr class="balance-sheet">
                <span style="font-size: 11.5pt;">Company Name</span>
            </div>
            <div style="    margin-right: 50px;">
                <span>GSTIN :</span>
                <span>B.O. :</span>
                <span>CIN :</span>
            </div>
        </div>
            <thead>
                <tr class="date-account-row">
                    <th colspan="17" style="border: none;">From 01/04/2023 to 31/03/2024</th>
                    <th colspan="17" style="border: none;">Account: </th>
                </tr>
                <tr>
                    <th colspan="4" class="debit">Date</th>
                    <th colspan="3" class="debit">Type</th>
                    <th colspan="3" class="debit">Vch No.</th>
                    <th colspan="12" class="particulars  debit">Particulars</th>
                    <th colspan="3" class="debit">Narration</th>
                    <th colspan="3" class="debit">Debit(₹)</th>
                    <th colspan="3" class="credit">Credit(₹)</th>
                    <th colspan="3" class="balance">Balance(₹)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Repeat this block for each transaction entry -->
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3"></td>
                    <td colspan="3"></td>
                    <td colspan="12" class="particulars">Opening Balance</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">1</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP) <br>BEING SALARY PAID TO VIVEK
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">2</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">3</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">4</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">5</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">6</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">7</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">8</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">9</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">10</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">11</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">12</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">13</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">14</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">15</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">16</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <tr>
                    <td colspan="4">01/04/2023</td>
                    <td colspan="3">Pymt</td>
                    <td colspan="3">17</td>
                    <td colspan="12" class="particulars">Dr Salary Payable to Vivek (KMP)
                        (KMP)INB/NEFT/AXIC65516565/Vivekeshw</td>
                    <td colspan="3"></td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                    <td colspan="3" class="debit">0.00 Dr</td>
                </tr>
                <!-- Example end -->
            </tbody>
            <tfoot>
                <tr class="footer-account-row">
                    <th colspan="34" style="text-align: center; border-bottom: none; border-left: none; border-right: none;">contd. on page 2</th>
                </tr>
            </tfoot>
            <tfoot>
                <tr class="footer-account-row">
                    <th colspan="25" class="debit">Totals c/o</th>
                    <th colspan="3" class="debit">0.00</th>
                    <th colspan="3" class="debit">0.00</th>
                    <th colspan="3" class="debit"></th>
                </tr>
            </tfoot>
    </table>
   </div>


<?php echo view('includes/footer_scripts'); ?>