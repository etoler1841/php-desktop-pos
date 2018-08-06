<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  extract($data);

  $sql = "SELECT employee_id, pos_login_expiry
          FROM employee
          WHERE employee_pin = ?";
  $stmt = $db->prepare($sql);
  $stmt->bind_param("s", $pin);
  $stmt->execute();
  $stmt->bind_result($userId, $expiry);
  $stmt->store_result();
  $stmt->fetch();
  if($userId){
    $timeout = strtotime($expiry);
    if($timeout && $timeout > time()){
      $employee = new Employee($userId);
      $return['status'] = 'ok';
      $return['userID'] = $userId;
      $return['userFull'] = $employee->name;
      $return['userShort'] = $employee->shortName;
    } else {
      $return['status'] = 'expired';
    }
  } else {
    $return['status'] = 'err';
  }

  echo json_encode($return);
?>
