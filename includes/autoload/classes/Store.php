<?php
  class Store {
    public function __construct(){
      global $db;

      $stmt = "SELECT * FROM app_config";
      $query = $db->query($stmt);
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
      global $db;

      $stmt = "SELECT register_batch_id
               FROM register_batch
               ORDER BY register_batch_id DESC
               LIMIT 1";
      $result = $db->query($stmt)->fetch_array(MYSQLI_NUM);
      return $result[0];
    }

    public function cidDifference($count){
      global $db;

      $stmt = "SELECT SUM(tender_cash), SUM(change_due)
               FROM transaction
               WHERE time >= (SELECT opening_time FROM register_batch WHERE register_batch_id = ".$this->registerBatch().")";
      $result = $db->query($stmt)->fetch_array(MYSQLI_NUM);
      $cashIn = $result[0]-$result[1];

      $stmt = "SELECT opening_cash
               FROM register_batch
               WHERE register_batch_id = ".$this->registerBatch();
      $result = $db->query($stmt)->fetch_array(MYSQLI_NUM);
      $openingCash = $result[0];
      return $count-($openingCash+$cashIn);
    }

    public function dbLastDownload($date){
      global $db;

      $update = date("Y-m-d H:i:s", $date);
      $stmt = "UPDATE app_config
               SET value = '$update'
               WHERE setting = 'DB_LAST_DOWNLOAD'";
      $db->query($stmt);
    }

    public function logout(){
      global $db;

      $stmt = "UPDATE app_config
                SET value = NULL
                WHERE setting IN ('CURRENT_LOGIN', 'CURRENT_LOGIN_EXPIRATION')";
      $db->query($stmt);
      $this->CURRENT_LOGIN = '';
      $this->CURRENT_LOGIN_EXPIRATION = '';
    }
  }
?>
