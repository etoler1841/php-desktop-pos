<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents("php://input"), true);
  if(isset($_POST['logout'])){
    $employee = new Employee($data['empID']);
    $employee->logout();
  }
?>
