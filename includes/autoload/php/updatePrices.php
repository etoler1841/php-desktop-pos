<?php
  function updatePrices($store, $prodId){
    global $db;
    global $remote;

    $sql = "SELECT new_price
            FROM price_change
            WHERE products_id = $prodId";
    $result = $db->query($sql)->fetch_array(MYSQLI_NUM);
    $price = $result[0];

    $sql = "UPDATE products
            SET products_price = $price
            WHERE products_id = $prodId";
    $db->query($sql);

    $token = $store->SECURITY_TOKEN;
    $ch = curl_init();
    $headers = array("Authorization: bearer $token");
    $data = array(
      "price" => $price
    );
    curl_setopt($ch, CURLOPT_URL, "$remote/price-change/$prodId");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = json_decode(curl_exec($ch));
    if($res->status === 'ok'){
      $sql = "DELETE FROM price_change WHERE products_id = $prodId";
      $db->query($sql);
      return true;
    } else {
      return false;
    }
  }
?>
