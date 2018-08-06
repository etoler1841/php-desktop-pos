<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents("php://input"), true);
  extract($data);
  $sql = $db->prepare("SELECT employee_id, employee_password FROM employee WHERE employee_username = ?");
  $sql->bind_param("s", $user);
  $sql->execute();
  $sql->bind_result($employee_id, $employee_password);
  $sql->store_result();
  $sql->fetch();
  $sql->close();
  if(password_verify($pass, $employee_password)){
    $employee = new Employee((int)$employee_id);
    $employee->login();
    $return['status'] = 'ok';
    $return['userFull'] = $employee->name;
    $return['user'] = $employee->shortName;
  } else {
    $return['status'] = 'err';
  }

  echo json_encode($return);
?>
