<?php
  $suppressMarkup = 1;
  define("SITE_ROOT", '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  foreach($data as $a => $b){
    ${$a} = $db->real_escape_string($b);
  }
  $stmt = "SELECT l.*
           FROM products p
           LEFT JOIN labels l ON p.master_categories_id = l.categories_id
           WHERE p.products_id = $id";
  $row = $db->query($stmt)->fetch_array(MYSQLI_ASSOC);
  $types = 'standard', 'barcode', 'game_case', 'game_sleeve';
  $return = array();
  foreach($types as $type){
    if($row[$type] == 1) $return[] = $type;
  }

  echo json_encode($return);
?>
