<?php
  $suppressMarkup = 1;
  define("SITE_ROOT", '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  if(updatePrices($store, $data['id'])){
    $return['status'] = 'ok';
  } else {
    $return['status'] = 'err';
  }

  echo json_encode($return);
?>
