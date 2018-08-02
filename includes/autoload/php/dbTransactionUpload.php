<?php
  function dbTransactionUpload($store){
    global $db;
    $token = $store->SECURITY_TOKEN;

    $ch = curl_init();
    $headers = array("Authorization: bearer $token");
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
      curl_setopt($ch, CURLOPT_URL, "http://www.pricebustersgames.com/pbadmin/pos-api/inventory/".$row['products_id']);
      $res = json_decode(curl_exec($ch));
      if($res->status === 'ok'){
        $stmt->bind_param("i", $row['upload_queue_id']);
        $stmt->execute();
      }
    }
    $stmt->close();
  }
?>
