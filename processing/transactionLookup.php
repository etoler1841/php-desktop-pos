<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents("php://input"), true);

  $stmt = "SELECT 1 FROM transaction WHERE transaction_id = ".$data['id'];
  $result = $mysqlL->query($stmt);
  if($result->num_rows > 0){
    echo "success";
  }
?>
