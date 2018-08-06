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

    public function priceUpdates(){
      global $db;
      global $remote;

      $sql = "SELECT 1
              FROM price_change
              WHERE products_id = ?";
      $checkStmt = $db->prepare($sql);
      $token = $this->SECURITY_TOKEN;
      $sql = "INSERT INTO price_change
              SET products_id = ?,
                  new_price = ?";
      $insertStmt = $db->prepare($sql);
      $sql = "UPDATE price_change
              SET new_price = ?
              WHERE products_id = ?";
      $updateStmt = $db->prepare($sql);

      $ch = curl_init();
      $headers = array("Authorization: bearer $token");
      curl_setopt($ch, CURLOPT_URL, "$remote/price-change");
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $res = json_decode(curl_exec($ch));
      if($res->status === 'ok'){
        foreach($res->results as $row){
          extract((array)$row);
          $checkStmt->bind_param("i", $products_id);
          $checkStmt->execute();
          $checkStmt->store_result();
          if($checkStmt->num_rows){
            $updateStmt->bind_param("di", $new_price, $products_id);
            $updateStmt->execute();
          } else {
            $insertStmt->bind_param("id", $products_id, $new_price);
            $insertStmt->execute();
          }
        }
      }
    }

    public function dbTransactionUpload(){
      global $db;
      global $remote;

      $token = $this->SECURITY_TOKEN;
      $headers = array("Authorization: bearer $token");
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);

      $stmt = "SELECT upload_queue_id, products_id, products_quantity
               FROM upload_queue
               WHERE uploaded = 0";
      $result = $db->query($stmt);
      $stmt = $db->prepare("UPDATE upload_queue
                            SET uploaded = 1
                            WHERE upload_queue_id = ?");
      while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $data = array(
          "qty" => $row['products_quantity']
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, "$remote/inventory/".$row['products_id']);
        $res = json_decode(curl_exec($ch));
        if($res->status === 'ok'){
          $stmt->bind_param("i", $row['upload_queue_id']);
          $stmt->execute();
        }
      }
      $stmt->close();
    }

    public function dlQueue(){
      global $db;
      global $remote;

      $token = $this->SECURITY_TOKEN;
      $headers = array("Authorization: bearer $token");
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_URL, $remote."/product/queue");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $sql = "UPDATE products
              SET products_quantity = products_quantity + ?
              WHERE products_id = ?";
      $stmt = $db->prepare($sql);

      $res = json_decode(curl_exec($ch));
      if($res->status === 'ok'){
        foreach($res->results as $row){
          extract((array)$row);
          $stmt->bind_param("ii", $products_quantity, $products_id);
          $stmt->execute();
        }
      }
    }
  }
?>
