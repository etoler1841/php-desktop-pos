<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  extract($data);

  // update pricing data if not already in inventory
  $sql = "SELECT products_quantity
          FROM products
          WHERE products_id = $id";
  $result = $db->query($sql)->fetch_array(MYSQLI_ASSOC);
  if(!$result[0]){
    $ch = curl_init();
    $headers = "Authorization: bearer $token";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "http://www.pricebustersgames.com/pbadmin/pos-api/product/$id");
    $res = json_decode(curl_exec($ch));
    if($res->status === 'ok'){
      $sql = "UPDATE products
              SET products_price = ?
              WHERE products_id = $id";
      $stmt = $db->prepare($sql);
      $stmt->bind_param("d", $res->products_price);
      $stmt->execute();
      $stmt->close();
      
      $data = array(
        'price' => $res->products_price;
      );
      curl_setopt($ch, CURLOPT_URL, "http://www.pricebustersgames.com/pbadmin/pos-api/price-change/$id");
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
      curl_exec($ch);
    }
  }

  $stmt = "UPDATE products
           SET products_quantity = products_quantity + $qty
           WHERE products_id = $id";
  $db->query($stmt);

  $stmt = "INSERT INTO upload_queue
           SET products_id = $id,
               products_quantity = $qty";
  $db->query($stmt);
  dbTransactionUpload($store);
?>
