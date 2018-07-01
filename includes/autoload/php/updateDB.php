<?php
  function updateDB(){
    global $db;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Find the newest category in the local DB
    $stmt = "SELECT categories_id FROM categories ORDER BY categories_id DESC LIMIT 1";
    $result = $db->query($stmt);
    if($result->num_rows > 0){
      $row = $result->fetch_array(MYSQLI_NUM);
      $maxCat = $row[0];
    } else {
      $maxCat = 0;
    }

    //Find categories on the server not yet in the local DB, get their info, and insert into the local DB
    $sql = "INSERT INTO categories
            SET categories_id = ?,
                categories_name = ?,
                parent_id = ?";
    $stmt1 = $db->prepare($sql);
    $sql = "INSERT INTO labels
            SET categories_id = ?,
                standard = ?,
                barcode = ?,
                game_case = ?,
                game_sleeve = ?";
    $stmt2 = $db->prepare($sql);
    do{
      $cat = $maxCat+1;
      $val = $cat."?tree=0";
      curl_setopt($ch, CURLOPT_URL, "http://www.pricebustersgames.com/pbadmin/pos-api/category/".$val);
      $response = json_decode(curl_exec($ch));
      if($response->results){
        extract($response->results[0]);
        $stmt1->bind_param("isi", $categories_id, $categories_name, $parent_id);
        $stmt1->execute();
        curl_setopt($ch, CURLOPT_URL,
        "http://www.pricebustersgames.com/pbadmin/pos-api/label/".$cat);
        extract(json_decode(curl_exec($ch)));
        $stmt2->bind_param("iiiii", $categories_id, $standard, $barcode, $game_case, $game_sleeve);
        $stmt2->close();
        $maxCat = $cat;
      }
    } while($response->results);
    $stmt1->close();
    $stmt2->close();

    //Find the newest product in the local DB
    $stmt = "SELECT products_id FROM products ORDER BY products_id DESC LIMIT 1";
    $result = $db->query($stmt);
    if($result->num_rows > 0){
      $row = $result->fetch_array(MYSQLI_NUM);
      $maxProd = $row[0];
    } else {
      $maxProd = 0;
    }

    //Find products on the server not yet in the local DB, get their info, and insert into the local DB
    $sql = "INSERT INTO products
            SET products_id = ?,
              products_name = ?,
              products_quantity = ?,
              products_model = ?,
              products_price = ?,
              master_categories_id = ?";
    $stmt = $db->prepare($sql);
    do{
      $prod = $maxProd+1;
      curl_setopt($ch, CURLOPT_URL, "http://www.pricebustersgames.com/pbadmin/pos-api/product/".$prod);
      $response = json_decode(curl_exec($ch));
      if($response->results){
        extract($response->results);
        $stmt1->bind_param("isisdi", $products_id, $products_name, $products_quantity, $products_model, $products_price, $categories_id);
        $stmt1->execute();
        $maxProd = $prod;
      }
    } while($response->results);
  }
?>
