<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  foreach($data as $a => $b){
    ${$a} = $b;
  }
  $stmt = "UPDATE products
       SET products_quantity = products_quantity - 1
       WHERE products_id = $id";
  $db->query($stmt);
  
  $stmt = "INSERT INTO upload_queue
       SET products_id = $id,
         products_quantity = -1";
  $db->query($stmt);
?>
