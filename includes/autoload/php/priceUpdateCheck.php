<?php
  function priceUpdateCheck(){
    global $db;

    $sql = "SELECT 1 FROM price_change";
    $count = $db->query($sql)->num_rows;
    if($count){
      echo " <span class='badge badge-warning'>$count</span>";
    }
  }
?>
