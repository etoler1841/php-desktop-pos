<?php
  function updateDB(){
    global $mysqlL;
    global $mysqlR;
    
    //Find the newest category in the local DB
    $stmt = "SELECT categories_id FROM categories ORDER BY categories_id DESC LIMIT 1";
    $result = $mysqlL->query($stmt);
    if($result->num_rows > 0){
      $row = $result->fetch_array(MYSQLI_NUM);
      $maxCat = $row[0];
    } else {
      $maxCat = 0;
    }
    
    //Find categories on the server not yet in the local DB, get their info, and insert into the local DB
    $stmt = "SELECT c.categories_id, cd.categories_name, c.parent_id
         FROM ".TBL_CATEGORIES." c
         LEFT JOIN ".TBL_CATEGORIES_DESCRIPTION." cd ON c.categories_id = cd.categories_id
         WHERE c.categories_id > $maxCat";
    $result = $mysqlR->query($stmt);
    $stmt2 = $mysqlL->prepare("INSERT INTO categories
                   SET categories_id = ?,
                     categories_name = ?,
                     parent_id = ?");
    if($result->num_rows > 0){
      while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $stmt2->bind_param("isi", $row['categories_id'], $row['categories_name'], $row['parent_id']);
        $stmt2->execute();
      }
    }
    $stmt2->close();
    
    //Find the newest product in the local DB
    $stmt = "SELECT products_id FROM products ORDER BY products_id DESC LIMIT 1";
    $result = $mysqlL->query($stmt);
    if($result->num_rows > 0){
      $row = $result->fetch_array(MYSQLI_NUM);
      $maxProd = $row[0];
    } else {
      $maxProd = 0;
    }
    
    //Find products on the server not yet in the local DB, get their info, and insert into the local DB
    $stmt = "SELECT p.products_id, pd.products_name, p.products_quantity, p.products_model, p.products_price, p.master_categories_id
         FROM ".TBL_PRODUCTS." p
         LEFT JOIN ".TBL_PRODUCTS_DESCRIPTION." pd ON p.products_id = pd.products_id
         WHERE p.products_id > $maxProd";
    $result = $mysqlR->query($stmt);
    $stmt2 = $mysqlL->prepare("INSERT INTO products
                   SET products_id = ?,
                     products_name = ?,
                     products_quantity = ?,
                     products_model = ?,
                     products_price = ?,
                     master_categories_id = ?");
    if($result->num_rows > 0){
      while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $stmt2->bind_param("isisdi", $row['products_id'], $row['products_name'], $row['products_quantity'], $row['products_model'], $row['products_price'], $row['master_categories_id']);
        $stmt2->execute();
      }
    }
    $stmt2->close();
  }
?>
