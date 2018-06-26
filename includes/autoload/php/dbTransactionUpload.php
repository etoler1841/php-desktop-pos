<?php
  function dbTransactionUpload(){
    global $mysqlL;
    global $mysqlR;

    if(!$mysqlR->connect_error){
      $stmt = "SELECT upload_queue_id, products_id, products_quantity
               FROM upload_queue
               WHERE uploaded = 0";
      $result = $mysqlL->query($stmt);
      $stmt2 = $mysqlR->prepare("UPDATE products
                                 SET products_quantity = products_quantity + ?
                                 WHERE products_id = ?");
      $stmt3 = $mysqlL->prepare("UPDATE upload_queue
                                 SET uploaded = 1
                                 WHERE upload_queue_id = ?");
      while($row = $result->fetch_array(MYSQLI_ASSOC)){
        $stmt2->bind_param("ii", $row['products_quantity'], $row['products_id']);
        $stmt2->execute();
        $stmt3->bind_param("i", $row['upload_queue_id']);
        $stmt3->execute();
      }
      $stmt2->close();
    }
  }
?>
