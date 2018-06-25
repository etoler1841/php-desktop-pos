<?php
  function getCategories($parentID = 0){
    global $mysqlR;
    
    if(!intval($parentID)){
      $parentID = 0;
    }
    $stmt = "SELECT c.categories_id, cd.categories_name
         FROM categories c
         LEFT JOIN categories_description cd ON c.categories_id = cd.categories_id
         WHERE c.parent_id = $parentID";
    $result = $mysqlR->query($stmt);
    while($row = $result->fetch_array(MYSQLI_NUM)){
      $return[$row[0]] = $row[1];
    }
    sort($return);
    
    return $return;
  }
?>
