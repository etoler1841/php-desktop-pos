<?php
  class Store {
    public function __construct(){
      global $mysqlL;

      $stmt = "SELECT * FROM app_config";
      $query = $mysqlL->query($stmt);
      while($result = $query->fetch_array()){
        if(in_array($result[0], array('STORE_ID', 'CURRENT_LOGIN', 'REGISTER_ID'))){
          $this->{$result[0]} = intval($result[1]);
        } else {
          $this->{$result[0]} = $result[1];
        }
      }

      $this->FULL_ADDRESS = "PriceBustersGames.com\n".$this->STORE_ADDRESS."\n".$this->STORE_CITY.", ".$this->STORE_STATE." ".$this->STORE_ZIP."\n".phoneFormat($this->STORE_PHONE);
    }

    public function registerBatch(){
      global $mysqlL;

      $stmt = "SELECT register_batch_id
               FROM register_batch
               ORDER BY register_batch_id DESC
               LIMIT 1";
      $result = $mysqlL->query($stmt)->fetch_array(MYSQLI_NUM);
      return $result[0];
    }

    public function cidDifference($count){
      global $mysqlL;

      $stmt = "SELECT SUM(tender_cash), SUM(change_due)
               FROM transaction
               WHERE time >= (SELECT opening_time FROM register_batch WHERE register_batch_id = ".$this->registerBatch().")";
      $result = $mysqlL->query($stmt)->fetch_array(MYSQLI_NUM);
      $cashIn = $result[0]-$result[1];

      $stmt = "SELECT opening_cash
               FROM register_batch
               WHERE register_batch_id = ".$this->registerBatch();
      $result = $mysqlL->query($stmt)->fetch_array(MYSQLI_NUM);
      $openingCash = $result[0];
      return $count-($openingCash+$cashIn);
    }

    public function logout(){
      global $mysqlL;

      $stmt = "UPDATE app_config
                SET value = NULL
                WHERE setting IN ('CURRENT_LOGIN', 'CURRENT_LOGIN_EXPIRATION')";
      $mysqlL->query($stmt);
      $this->CURRENT_LOGIN = '';
      $this->CURRENT_LOGIN_EXPIRATION = '';
    }
  }
?>
