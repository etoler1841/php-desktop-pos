<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  foreach($data as $a => $b){
    ${$a} = $db->real_escape_string($b);
  }
  $stmt = "SELECT p.products_id, p.products_name, p.master_categories_id, c.categories_name, p.products_quantity, p.products_price
       FROM products p
       LEFT JOIN categories c ON p.master_categories_id = c.categories_id
       WHERE p.products_name LIKE '%$term%'
       ORDER BY c.categories_name ASC, p.products_name ASC, p.products_id ASC";
  $result = $db->query($stmt);
  $return = array();
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    foreach($row as $a => $b){
      ${$a} = $b;
    }
    $return[] = array('id' => $products_id,
              'name' => $products_name,
              'catID' => $master_categories_id,
              'cat' => $categories_name,
              'stock' => $products_quantity,
              'price' => $products_price);
  }
  echo json_encode($return);
?>
