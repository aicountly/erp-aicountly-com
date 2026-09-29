<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt Table</title>
  <style>
    body {
     
      
      margin: 20px;
    }

   table {
  width: 100%;
  border: 1px solid black; /* Specify bottom border only */
  margin-top: 20px;
}
    h2{
	  text-align: center;
	  }
	   p{
	  text-align: right;
	  }
	  p1{
	  font-size:20px;
	  }

    th, td {
      padding: 10px;
      text-align: left;
     border: 1px solid black;
    }

    th {
      background-color:white;
    }
  </style>
</head>
<body onload="print();">

    <?php
    $r = $s1['voucher_txn_id'];
    $v = $s1['comp_vch_no'];
    $d = $s1['voucher_date'];
    ?>
    <table>
        <tr>
            <th colspan="2">GSTIN: <br>
                <h2>RECEIPT<br><?php echo strtoupper($company_name);?><br><p1><?php echo $company_adrs1;?> <br>CIN: </p1></h2>
            </th>
        </tr>
        <tr>
            <th>Receipt No. : <?php echo $v; ?></th>
            <th>Dated : <?php echo $d; ?></th>
        </tr>
        <tr>
            <th>Party : 
                <?php 
                foreach ($s3 as $acc):
                    echo $acc['acc_name'] . ', ';
                endforeach;
                ?>
            </th>
            <th>GSTIN / UIN : <br>Place of Supply : Tamilnadu (33)<br> Amount (Rs.) : <?php echo $a1['amo']; ?></th>
        </tr>
        <tr>
            <th colspan="2"><p>for <?php echo strtoupper($company_name);?></p><br>Rs.<?php echo $a1['amo']; ?><br> Rupees  <br>(Cheque Subject to Realisation)<br><p> Authorised Signatory</p></th>
        </tr>
    </table>



</body>

</html>
