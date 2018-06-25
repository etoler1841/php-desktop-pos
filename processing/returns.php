<?php
  $suppressMarkup = 1;
  define('SITE_ROOT', '..');
  require(SITE_ROOT.'/includes/includes.php');
  $data = json_decode(file_get_contents('php://input'), true);

  switch($data['method']){
    case 'returnItem':
      $stmt = "SELECT products_id,
                      categories_id,
                      products_quantity,
                      quantity_returned,
                      products_name,
                      products_discount,
                      products_price_ea
                FROM transaction_entry
                WHERE transaction_entry_id = ".$data['entryID'];
      $result = $mysqlL->query($stmt)->fetch_array(MYSQLI_ASSOC);

      $teStmt = $mysqlL->prepare("INSERT INTO transaction_entry
                                SET transaction_id = ?,
                                    entry_type = 4,
                                    products_id = ?,
                                    categories_id = ?,
                                    products_quantity = ?,
                                    quantity_returned = NULL,
                                    products_name = ?,
                                    products_discount = ?,
                                    products_price_ea = ?,
                                    products_price_ext = ?");
      switch($data['returnType']){
        case 'single':
          $result['price'] = number_format($result['products_price_ea']*(100-$result['products_discount'])/100, 2);
          $result['tax'] = number_format($result['price']*$store->SALES_TAX, 2);
          $result['total'] = number_format($result['price']+$result['tax'], 2);
          $stmt = "INSERT INTO transaction
                    SET employee = ".$store->CURRENT_LOGIN.",
                        total = -".$result['total'].",
                        sales_tax = -".$result['tax'].",
                        tender_cash = 0.00,
                        tender_credit = 0.00,
                        tender_giftcard = 0.00,
                        change_due = ".$result['total'];
          $mysqlL->query($stmt);
          $transID = $mysqlL->insert_id;
          $qty = 1;
          $teStmt->bind_param("iiiisddd",
                            $transID,
                            $result['products_id'],
                            $result['categories_id'],
                            $qty,
                            $result['products_name'],
                            $result['products_discount'],
                            $result['products_price_ea'],
                            $result['price']);
          $teStmt->execute();
          echo $mysqlL->error;
          $stmt = "UPDATE transaction_entry
                    SET quantity_returned = quantity_returned + 1
                    WHERE transaction_entry_id = ".$data['entryID'];
          $mysqlL->query($stmt);
          $return['amt'] = $result['total'];
          break;
        case 'line':
          $result['price'] = number_format(($result['products_quantity']-$result['quantity_returned'])*$result['products_price_ea']*(100-$result['products_discount'])/100, 2);
          $result['tax'] = number_format($result['price']*$store->SALES_TAX, 2);
          $result['total'] = number_format($result['price']+$result['tax'], 2);
          $stmt = "INSERT INTO transaction
                    SET employee = ".$store->CURRENT_LOGIN.",
                        total = -".$result['total'].",
                        sales_tax = -".$result['tax'].",
                        tender_cash = 0.00,
                        tender_credit = 0.00,
                        tender_giftcard = 0.00,
                        change_due = ".$result['total'];
          $mysqlL->query($stmt);
          $transID = $mysqlL->insert_id;
          $newQty = $result['products_quantity'] - $result['quantity_returned'];
          $teStmt->bind_param("iiiisddd",
                              $transID,
                              $result['products_id'],
                              $result['categories_id'],
                              $newQty,
                              $result['products_name'],
                              $result['products_discount'],
                              $result['products_price_ea'],
                              $result['price']);
          $teStmt->execute();
          $stmt = "UPDATE transaction_entry
                    SET quantity_returned = products_quantity
                    WHERE transaction_entry_id = ".$data['entryID'];
          $mysqlL->query($stmt);
          $return['amt'] = $result['total'];
          break;
        }
      if(!$mysqlL->connect_error){
        $return['status'] = 'success';
      }
      break;
  }
  echo json_encode($return);
?>
