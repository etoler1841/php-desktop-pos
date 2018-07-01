<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  foreach($_POST as $a => $b){
    ${$a} = $b;
  }
  $sql = $db->prepare("SELECT employee_id, employee_password FROM employee WHERE employee_username = ?");
  $sql->bind_param("s", $username);
  $sql->execute();
  $sql->bind_result($employee_id, $employee_password);
  $sql->store_result();
  $sql->fetch();
  $sql->close();
  if(password_verify($password, $employee_password)){
    $employee = new Employee(intval($employee_id));
    $employee->login();
    updateDB();
    echo "success";
  }
?>
