<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);
  foreach($data as $a => $b){
    ${$a} = $b;
  }
  if(!$id) exit(json_encode(array('error' => 'Item ID cannot be empty.')));
  $stmt = "SELECT p.products_id, p.products_name, p.master_categories_id, c.categories_name, p.products_quantity, p.products_price
       FROM products p
       LEFT JOIN categories c ON p.master_categories_id = c.categories_id
       WHERE ";
  if(is_numeric($id)){
    $stmt .= "p.products_id = $id
         OR ";
  }
  $stmt .= "p.products_model LIKE '$id'
        LIMIT 1";
  $result = $db->query($stmt);
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $return = array('id' => $row['products_id'],
            'name' => $row['products_name'],
            'catID' => $row['master_categories_id'],
            'cat' => $row['categories_name'],
            'stock' => $row['products_quantity'],
            'price' => $row['products_price']);
  }
  if (!isset($return)) $return = array('error' => 'Item not found.');
  echo json_encode($return);
?>
