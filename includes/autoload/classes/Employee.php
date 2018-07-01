<?php
  class Employee {
    public function __construct($employee_id){
      global $db;
      
      if(!is_int($employee_id)){
        return false;
      }
      $stmt = "SELECT * FROM employee WHERE employee_id = $employee_id";
      $query = $db->query($stmt);
      while($result = $query->fetch_array()){
        foreach($result as $a => $b){
          $prop = str_replace('employee_', '', $a);
          $this->{$prop} = $b;
        }
      }
      
      $this->name = $this->first_name.' '.$this->last_name;
    }
    
    public function login(){
      global $db;
      
      $sql = $db->prepare("UPDATE app_config SET value = ? WHERE setting = 'CURRENT_LOGIN'");
      $sql->bind_param("s", strval($this->id));
      $sql->execute();
      $sql->close();
      
      $loginExpire = date("Y:m:d H:i:s", strtotime("tomorrow"));
      $sql = $db->prepare("UPDATE app_config SET value = ? WHERE setting = 'CURRENT_LOGIN_EXPIRATION'");
      $sql->bind_param("s", $loginExpire);
      $sql->execute();
      $sql->close();
    }
    
    public function logout(){
      global $db;
      
      $stmt = "UPDATE app_config SET value = NULL WHERE setting IN ('CURRENT_LOGIN', 'CURRENT_LOGIN_EXPIRATION')";
      $db->query($stmt);
    }
  }
?>
