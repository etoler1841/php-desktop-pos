<?php
  function updateDB($store){
    global $db;
    $token = $store->SECURITY_TOKEN;

    $ch = curl_init();
    $headers = array("Authorization: bearer $token");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $lastUpdate = strtotime($store->DB_LAST_DOWNLOAD);
    $now = time();
    updateCategories($ch, $lastUpdate);
    updateProducts($ch, $lastUpdate);
    if(!$db->error){
      $store->dbLastDownload(time());
    }
  }

  function updateCategories($ch, $lastUpdate){
    global $db;

    $values = array(
      'limit' => '1',
      'offset' => '0',
      'after' => $lastUpdate
    );

    $valArr = array();
    foreach($values as $key => $val){
      $valArr[] = $key.'='.$val;
    }
    $vals = implode("&", $valArr);
    curl_setopt($ch, CURLOPT_URL, "$remote/category/?$vals");
    $res = json_decode(curl_exec($ch));
    if($res->status !== 'ok' || !sizeof((array)$res->results)) return;

    $values['limit'] = '100';

    $sql = "SELECT 1
            FROM categories
            WHERE categories_id = ?";
    $checkStmt = $db->prepare($sql);

    $sql = "INSERT INTO categories
            SET categories_id = ?,
                categories_name = ?,
                parent_id = ?";
    $insertStmt = $db->prepare($sql);

    $i = 0;
    do {
      $values['offset'] = $i*$values['limit'];
      $valArr = array();
      foreach($values as $key => $val){
        $valArr[] = $key.'='.$val;
      }
      $vals = implode("&", $valArr);
      curl_setopt($ch, CURLOPT_URL, "http://pricebustersgames.com/pbadmin/pos-api/category/?$vals");
      $res = json_decode(curl_exec($ch));
      if($res->status === 'ok'){
        foreach($res->results as $cat){
          extract((array)$cat);

          $checkStmt->bind_param("i", $categories_id);
          $checkStmt->execute();
          $checkStmt->store_result();
          if(!$checkStmt->num_rows){
            $insertStmt->bind_param("isi", $categories_id, $categories_name, $parent_id);
            $insertStmt->execute();
          }
        }
        $i++;
      } else {
        print_r($res->errors);
        exit;
      }
    } while($res->results);
  }

  function updateProducts($ch, $lastUpdate){
    global $db;

    $values = array(
      'limit' => '1',
      'offset' => '0',
      'after' => $lastUpdate
    );

    $valArr = array();
    foreach($values as $key => $val){
      $valArr[] = $key.'='.$val;
    }
    $vals = implode("&", $valArr);
    curl_setopt($ch, CURLOPT_URL, "$remote/product/?");
    $res = json_decode(curl_exec($ch));
    if($res->status !== 'ok' || !sizeof((array)$res->results)) return;

    $values['limit'] = '100';

    $sql = "SELECT 1
            FROM products
            WHERE products_id = ?";
    $checkStmt = $db->prepare($sql);

    $sql = "INSERT INTO products
            SET products_id = ?,
                products_name = ?,
                products_quantity = ?,
                products_model = ?,
                products_price = ?,
                master_categories_id = ?";
    $stmt = $db->prepare($sql);

    $i = 0;
    do {
      $values['offset'] = $i*$values['limit'];
      $valArr = array();
      foreach($values as $key => $val){
        $valArr[] = $key.'='.$val;
      }
      $vals = implode("&", $valArr);
      curl_setopt($ch, CURLOPT_URL, "$remote/product/?$vals");
      $res = json_decode(curl_exec($ch));
      if($res->status === 'ok'){
        foreach($res->results as $prod){
          extract((array)$prod);

          $checkStmt->bind_param("i", $products_id);
          $checkStmt->execute();
          $checkStmt->store_result();
          if(!$checkStmt->num_rows){
            $stmt->bind_param("isisdi", $products_id, $products_name, $products_quantity, $products_model, $products_price, $categories_id);
            $stmt->execute();
          }
        }
        $i++;
      } else {
        print_r($res->errors);
        exit;
      }
    } while($res->results);
  }
?>
