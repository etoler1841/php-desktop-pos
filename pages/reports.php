<?php
  extract($_GET);
  if($mode == 'daily'){
    $register = new Register($store->registerBatch());
    $reportName = "Daily Report: Batch #".$register->batchID;
    $startTime = $register->timeOpen;
    $endTime = ($register->timeClose) ? $register->timeClose : date("Y-m-d H:i:s");
  }
  $totals = $register->getTotals();
  $tender = $register->getTender();
  $transTypes = $register->getTransTypes();

  $stmt = $db->prepare("SELECT c.categories_name, SUM(te.products_quantity), SUM(te.products_price_ext)
                            FROM transaction_entry te
                            LEFT JOIN transaction t ON te.transaction_id = t.transaction_id
                            LEFT JOIN products p ON te.products_id = p.products_id
                            LEFT JOIN categories c ON te.categories_id = c.categories_id
                            WHERE t.time >= '$startTime'
                            AND t.time <= '$endTime'
                            AND te.entry_type = ?
                            GROUP BY c.categories_name
                            ORDER BY c.categories_name ASC");
  foreach($transTypes as $type){
    $stmt->bind_param("i", $type['id']);
    $stmt->execute();
    $stmt->bind_result($catName, $qty, $total);
    $stmt->store_result();
    while($stmt->fetch()){
      $info[$type['desc']][] = array('catName' => $catName, 'qty' => $qty, 'total' => $total);
    }
  }
  $stmt->close();

  $stmt = "SELECT SUM(tender_cash)-SUM(change_due) AS cash,
          SUM(tender_credit) AS `credit card`,
          SUM(tender_giftcard) AS `gift card`
       FROM transaction
       WHERE time >= '$startTime'
       AND time <= '$endTime'
       AND transaction_id IN (
         SELECT transaction_id FROM transaction_entry
       )";
  $tender = $db->query($stmt)->fetch_array(MYSQLI_ASSOC);

  $drawer = array(
          'openingCash' => array('line' => 'Opening cash:',
            'amt' => $register->cashOpen),
          'cashInDrawer' =>array('line' => 'Cash in drawer:',
            'amt' => $register->cashInDrawer),
          'closingCash' => array('line' => 'Closing cash:'),
          'cidDiff' => array('line' => 'CID difference:')
        );
  if($register->cashClose){
    $drawer['closingCash']['amt'] = $register->cashClose;
    $drawer['cidDiff']['amt'] = $register->cidDifference;
  }
?>
<div id='report' class='report container'>
  <h3><?=$reportName?></h3>
  <p>
    <h5>Report Details</h5>
    <strong>Generated: </strong> <?=date("l, F j, Y g:i:sa")?> by <span class='employee'></span><br />
    <?php if($mode == 'daily'){
      ?>
      <strong>Batch opened:</strong> <?=date("l, F j, Y g:i:sa", strtotime($register->timeOpen)).' by '.$register->employeeOpenName?><br />
      <?php if($register->timeClose){
        ?>
        <strong>Batch closed:</strong> <?=date("l, F j, Y g:i:sa", strtotime($register->timeClose)).' by '.$register->employeeCloseName?><br />
        <?php
      }
    } ?>
  </p>
  <?php
  foreach($transTypes as $type){
    if(in_array($type['desc'], array('drops', 'reloads'))) continue;
    if(!empty($info[$type['desc']])){ ?>
    <table class='table table-sm report'>
      <thead class='thead-light'>
        <tr class='row'>
          <th class='col-6'><?= ucwords($type['desc']) ?></th>
          <th class='col-2'>%</th>
          <th class='col-2'># Items</th>
          <th class='col-2'>Total</th>
        </tr>
      </thead>
      <tbody>
        <?php
          foreach($info[$type['desc']] as $row){
            echo "<tr class='row'>
                <td class='col-6'>".$row['catName']."</td>
                <td class='col-2'>".number_format(abs(($row['total']/$totals[$type['desc']]['total']))*100, 2)."%</td>
                <td class='col-2'>".$row['qty']."</td>
                <td class='col-2'>$".number_format(abs($row['total']), 2)."</td>
                </tr>";
          }
        ?>
      </tbody>
      <tfoot>
        <tr class='row'>
          <td class='col-6'></td>
          <td class='col-2 text-right'><?= ucwords($type['desc']) ?> total:</td>
          <td class='col-2'><?= $totals[$type['desc']]['qty'] ?></td>
          <td class='col-2'>$<?= number_format(abs($totals[$type['desc']]['total']), 2) ?></td>
        </tr>
      </tfoot>
    </table>
  <?php }
  } ?>
  <div>
    <table class='table table-sm report'>
      <tbody>
        <tr class='row'>
          <td class='col-5'></td>
          <td class='col-4 text-right pr-5'>Net Sales (sales - returns):</td>
          <td class='col-1'><?= $totals['grand']['qty'] ?></td>
          <td class='col-1'>$<?= number_format($totals['grand']['total'], 2) ?></td>
          <td class='col-1'></td>
        </tr>
        <tr class='row'>
          <td class='col-5'></t+d>
          <td class='col-4 text-right pr-5'>Sales Tax:</td>
          <td class='col-1'></td>
          <td class='col-1'>$<?= number_format($totals['grand']['tax'], 2) ?></td>
          <td class='col-1'></td>
        </tr>
        <?php if($register->cashDropsQty){ ?>
        <tr class='row'>
          <td class='col-5'></td>
          <td class='col-4 text-right pr-5'>Cash Drops:</td>
          <td class='col-1'><?=$register->cashDropsQty?></td>
          <td class='col-1'>$<?= number_format($register->cashDrops, 2) ?></td>
          <td class='col-1'></td>
        </tr>
        <?php } ?>
        <?php if($register->cashReloads){ ?>
        <tr class='row'>
          <td class='col-5'></td>
          <td class='col-4 text-right pr-5'>Cash Reloads:</td>
          <td class='col-1'><?=$register->cashReloadsQty?></td>
          <td class='col-1'>$<?= number_format($register->cashReloads, 2) ?></td>
          <td class='col-1'></td>
        </tr>
        <?php } ?>
        <?php foreach($tender as $type => $amt){
          ?>
          <tr class='row'>
            <td class='col-5'></td>
            <td class='col-4 text-right pr-5'><?= ucwords($type) ?> Tendered:</td>
            <td class='col-1'></td>
            <td class='col-1'>$<?= number_format($amt, 2) ?></td>
            <td class='col-1'></td>
          </tr>
        <?php }
        ?>
      </tbody>
    </table>
    <table class='table table-sm report'>
      <tbody>
        <?php foreach($drawer as $line){
          if(in_array($line['line'], array('Closing cash:', 'CID difference:')) && !$register->cashClose){
            continue;
          } ?>
          <tr class='row'>
            <td class='col-5'></td>
            <td class='col-4 text-right pr-5'><?= $line['line'] ?></td>
            <td class='col-1'></td>
            <td class='col-1'>
            <?php if($line['amt'] < 0) echo '-'; ?>
            $<?= number_format(abs($line['amt']), 2) ?>
            </td>
            <td class='col-1'></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>
<script>
  $("#printButton").click(() => {
    window.print();
  });

  $("#pinModal").on("hide.bs.modal", () => {
    $("#report .employee").html(userFull);
    $("#report").show();
  });
</script>
