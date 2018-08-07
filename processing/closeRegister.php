<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  extract($data);
  if($mode == 'open'){
    updateDB($store);
    $store->priceUpdates();
    $stmt = "INSERT INTO register_batch
             SET opening_cash = ".$count.",
                 opening_employee = ".$empID;
  } elseif ($mode == 'close'){
    $stmt = "UPDATE register_batch
             SET closing_cash = ".$count.",
                 closing_time = '".date("Y-m-d H:i:s")."',
                 closing_employee = ".$empID.",
                 closing_cid_difference = ".$store->cidDifference($count)."
             WHERE register_batch_id = ".$store->registerBatch();
  }
  $db->query($stmt);

  echo json_encode(array("batchID" => $store->registerBatch()));
?>
