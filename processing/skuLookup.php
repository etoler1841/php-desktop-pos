<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  foreach($data as $a => $b){
    ${$a} = $mysqlL->real_escape_string($b);
  }
  $stmt = $mysqlL->prepare("SELECT p.products_id, p.products_name, p.master_categories_id, c.categories_name, p.products_price
                            FROM products p
                            LEFT JOIN categories c ON p.master_categories_id = c.categories_id
                            WHERE p.products_model LIKE ?
                            LIMIT 1");
  $stmt->bind_param("s", $sku);
  $stmt->execute();
  $stmt->bind_result($products_id, $products_name, $master_categories_id, $categories_name, $products_price);
  $stmt->store_result();
  if($stmt->num_rows){
    $stmt->fetch();
    $return = array('status' => 'ok',
                    'id' => $products_id,
                    'name' => $products_name,
                    'catID' => $master_categories_id,
                    'cat' => $categories_name,
                    'price' => $products_price);
  } else {
    $return = array();
  }

  echo json_encode($return);
?>
