<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  $stmt = "INSERT INTO transaction
       SET employee = ".$store->CURRENT_LOGIN.",
         total = ".$data['total'].",
         sales_tax = ".$data['sales_tax'].",
         tender_cash = ".$data['tender_cash'].",
         tender_credit = ".$data['tender_credit'].",
         tender_giftcard = ".$data['tender_giftcard'].",
         change_due = ".$data['change_due'];
  $db->query($stmt);
  $transactionID = $db->insert_id;

  $stmt1 = $db->prepare("INSERT INTO transaction_entry
                 SET transaction_id = $transactionID,
                   entry_type = ?,
                   products_id = ?,
                   categories_id = ?,
                   products_quantity = ?,
                   products_name = ?,
                   products_discount = ?,
                   products_price_ea = ?,
                   products_price_ext = ?");
  $stmt2 = $db->prepare("UPDATE products
                 SET products_quantity = products_quantity - ?
                 WHERE products_id = ?");
  $stmt3 = $db->prepare("INSERT INTO upload_queue
                 SET products_id = ?,
                   products_quantity = -?");
  for($i = 0, $n = sizeof($data['items']); $i < $n; $i++){
    foreach($data['items'][$i] as $a => $b){
      ${$a} = $b;
    }
    $stmt1->bind_param("iiiisddd", $entry_type, $products_id, $categories_id, $products_quantity, $products_name, $products_discount, $products_price_ea, $products_price_ext);
    $stmt1->execute();

    if($entry_type = 1 && $products_id){
      $stmt2->bind_param("ii", $products_quantity, $products_id);
      $stmt2->execute();
      $stmt3->bind_param("ii", $products_id, $products_quantity);
      $stmt3->execute();
    }
  }
  $stmt1->close();
  $stmt2->close();
  $stmt3->close();
  dbTransactionUpload();

  echo json_encode(array("transID" => $transactionID));
?>
