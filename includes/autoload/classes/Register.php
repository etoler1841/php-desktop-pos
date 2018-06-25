<?php
  class Register {
    public function __construct($batchID){
      global $mysqlL;

      $stmt = "SELECT * FROM register_batch WHERE register_batch_id = $batchID";
      $result = $mysqlL->query($stmt)->fetch_array(MYSQLI_ASSOC);

      $this->batchID = $result['register_batch_id'];
      $this->cashOpen = $result['opening_cash'];
      $this->timeOpen = $result['opening_time'];
      $this->employeeOpen = intval($result['opening_employee']);
      $this->cashClose = $result['closing_cash'];
      $this->timeClose = $result['closing_time'];
      $this->employeeClose = intval($result['closing_employee']);
      $this->cidDifference = $result['closing_cid_difference'];

      $stmt = "SELECT SUM(tender_cash), SUM(change_due)
           FROM transaction
           WHERE time >= '".$this->timeOpen."'";
      if($this->timeClose) $stmt .= " AND time <= '".$this->timeClose."'";
      $result = $mysqlL->query($stmt)->fetch_array(MYSQLI_NUM);
      $this->cashInDrawer = $this->cashOpen+$result[0]-$result[1];

      $name = function($empID){
        $emp = new Employee($empID);
        return $emp->name;
      };
      $this->employeeOpenName = $name($this->employeeOpen);
      if($this->timeClose) $this->employeeCloseName = $name($this->employeeClose);
    }

    public function getTender(){
      global $mysqlL;

      $startTime = $this->timeOpen;
      $endTime = ($this->timeClose) ? $this->timeClose : date("Y-m-d H:i:s");
      $stmt = "SELECT SUM(tender_cash)-SUM(change_due) AS cash,
           SUM(tender_credit) AS `credit card`,
           SUM(tender_giftcard) AS `gift card`
           FROM transaction
           WHERE time >= '$startTime'
           AND time <= '$endTime'";
      $tender = $mysqlL->query($stmt)->fetch_array(MYSQLI_ASSOC);

      return $tender;
    }

    public function getTotals(){
      global $mysqlL;

      $startTime = $this->timeOpen;
      $endTime = ($this->timeClose) ? $this->timeClose : date("Y-m-d H:i:s");
      $transTypes = $this->getTransTypes();
      $stmt = $mysqlL->prepare("SELECT SUM(te.products_price_ext), SUM(te.products_quantity), SUM(t.sales_tax)
                    FROM transaction_entry te
                    LEFT JOIN transaction t ON te.transaction_id = t.transaction_id
                    WHERE t.time >= '$startTime'
                    AND t.time <= '$endTime'
                    AND te.entry_type = ?");
      $totals = array('grand' => array('total' => 0, 'qty' => 0, 'tax' => 0));
      foreach($transTypes as $type){
        $stmt->bind_param("i", $type['id']);
        $stmt->execute();
        $stmt->bind_result($total, $qty, $tax);
        $stmt->store_result();
        $stmt->fetch();
        $totals[$type['desc']] = array('total' => $total, 'qty' => $qty);
        if(in_array($type['id'], array(2, 3, 5))) continue;
        $totals['grand']['total'] += $total;
        $totals['grand']['qty'] += $qty;
        $totals['grand']['tax'] += $tax;
      }
      $stmt->close();

      return $totals;
    }

    public static function getTransTypes(){
      $transTypes = array(
        array('id' => 1,
            'desc' => 'sales'),
        array('id' => 2,
            'desc' => 'trades'),
        array('id' => 3,
            'desc' => 'purchases'),
        array('id' => 4,
            'desc' => 'returns'),
        array('id' => 5,
            'desc' => 'payouts')
      );

      return $transTypes;
    }
  }
?>
