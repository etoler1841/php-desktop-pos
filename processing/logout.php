<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $store = new Store;
  if(isset($_POST['logout'])){
    $employee = new Employee($store->CURRENT_LOGIN);
    $employee->logout();
  }
?>
