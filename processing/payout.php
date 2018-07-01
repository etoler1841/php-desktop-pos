<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  foreach($data as $a => $b){
    ${$a} = $db->real_escape_string($b);
  }
  $stmt = "INSERT INTO transaction
       SET employee = ".$store->CURRENT_LOGIN.",
         time = '".date("Y-m-d H:i:s")."',
         total = ".$amt.",
         sales_tax = -".$tax.",
         change_due = ".$amt;
  $db->query($stmt);
  $transactionID = $db->insert_id;

  $stmt = "INSERT INTO transaction_entry
       SET transaction_id = ".$transactionID.",
         entry_type = 5,
         categories_id = 0,
         products_quantity = 1,
         products_name = ".$desc.",
         products_discount = 0.00,
         products_price_ea = ".$amt.",
         products_price_ext = ".$amt;
  $db->query($stmt);
?>
