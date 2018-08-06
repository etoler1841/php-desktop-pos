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
      $this->shortName = $this->first_name.' '.substr($this->last_name, 0, 1).'.';
    }

    public function login(){
      global $db;

      $expiry = date("Y-m-d H:i:s", strtotime("+6 hours"));
      $sql = "UPDATE employee
              SET pos_login_expiry = ?
              WHERE employee_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("si", $expiry, $this->id);
      $stmt->execute();
      $stmt->close();
    }

    public function logout(){
      global $db;

      $now = date("Y-m-d H:i:s");
      $sql = "UPDATE employee
              SET pos_login_expiry = ?
              WHERE employee_id = ?";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("si", $now, $this->id);
      $stmt->execute();
    }
  }
?>
