<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $params = json_decode(file_get_contents('php://input'), true);
  $stmt = "SELECT products_quantity, products_name, products_price_ext
       FROM transaction_entry
       WHERE transaction_id = ".$params['transID'];
  $data = array();
  $result = $db->query($stmt);
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $data[] = array(
      "type" => "item",
      "qty" => $row['products_quantity'],
      "name" => $row['products_name'],
      "amt" => ($row['products_price_ext'] < 0) ? '-$'.$row['products_price_ext'] : '$'.$row['products_price_ext']
    );
  }

  $stmt = "SELECT e.employee_first_name, t.time, SUM(te.products_price_ext) AS products_price_ext, t.sales_tax, t.total, t.tender_cash, t.tender_credit, t.tender_giftcard, t.change_due
       FROM   transaction t
       LEFT JOIN transaction_entry te ON t.transaction_id = te.transaction_id
       LEFT JOIN employee e ON t.employee = e.employee_id
       WHERE t.transaction_id = ".$params['transID'];
  $result = $db->query($stmt)->fetch_array(MYSQLI_ASSOC);
  $data[] = array(
    "type" => "employee",
    "name" => $result['employee_first_name']
  );
  $data[] = array(
    "type" => "time",
    "time" => date("D M j, Y g:ia")
  );
  $data[] = array(
    "type" => "subtotal",
    "amt" => ($result['products_price_ext'] < 0) ? '-$'.$result['products_price_ext'] : '$'.$result['products_price_ext']
  );
  $data[] = array(
    "type" => "tax",
    "amt" => ($result['sales_tax'] < 0) ? '-$'.$result['sales_tax'] : '$'.$result['sales_tax']
  );
  $data[] = array(
    "type" => "total",
    "amt" => ($result['total'] < 0) ? '-$'.$result['total'] : '$'.$result['total']
  );
  $data[] = array(
    "type" => "tender",
    "amt" => array(
          "cash" => ($result['tender_cash'] < 0) ? "-$".$result['tender_cash'] : "$".$result['tender_cash'],
          "credit" => ($result['tender_credit'] < 0) ? "-$".$result['tender_credit'] : "$".$result['tender_credit'],
          "giftcard" => ($result['tender_giftcard'] < 0) ? "-$".$result['tender_giftcard'] : "$".$result['tender_giftcard']
    )
  );
  $data[] = array(
    "type" => "change",
    "amt" => ($result['change_due'] < 0) ? "-$".$result['change_due'] : "$".$result['change_due']
  );

  echo json_encode($data);
?>
