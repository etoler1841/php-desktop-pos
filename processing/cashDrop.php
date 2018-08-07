<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents("php://input"), true);

  $store->cashDrop($data['employeeId'], $data['amt']);
?>
