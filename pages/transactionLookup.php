<?php
  $transID = $_GET['transID'];

  $stmt = "SELECT e.employee_first_name, t.time, t.total, t.sales_tax
           FROM transaction t
           LEFT JOIN employee e ON t.employee = e.employee_id
           WHERE t.transaction_id = $transID";
  $data['transInfo'] = $db->query($stmt)->fetch_array(MYSQLI_ASSOC);

  $stmt = "SELECT transaction_entry_id, entry_type, products_id, products_quantity, quantity_returned, products_name, products_discount, products_price_ea, products_price_ext
           FROM transaction_entry
           WHERE transaction_id = $transID";
  $result = $db->query($stmt);
  while($row = $result->fetch_array(MYSQLI_ASSOC)){
    $types = Register::getTransTypes();
    $data['items'][] = array(
      'entry_type' => ucwords(substr($types[$row['entry_type']-1]['desc'], 0, -1)),
      'id' => $row['transaction_entry_id'],
      'qty' => $row['products_quantity'],
      'returned' => $row['quantity_returned'],
      'name' => $row['products_name'],
      'disc' => $row['products_discount']."%",
      'price_ea' => "$".$row['products_price_ea'],
      'price_ext' => ($row['products_price_ext'] < 0) ? "-$".$row['products_price_ext'] : "$".$row['products_price_ext']
    );
  }
?>
<div id='transactionInfo'>
  <p>
    <strong>Cashier: </strong> <?= $data['transInfo']['employee_first_name'] ?><br />
    <strong>Transaction time:</strong> <?= date("D M j, Y g:ia", strtotime($data['transInfo']['time'])) ?>
  </p>
</div>
<div id='transactionItems'>
  <table class='table table-striped table-sm table-fixed'>
    <thead class='thead-light'>
      <tr>
        <th class='col-1'>Type</th>
        <th class='col-1'>Qty.</th>
        <th class='col-5'>Description</th>
        <th class='col-1'>Disc.</th>
        <th class='col-2'>Price ea.</th>
        <th class='col-2'>Price ext.</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $data['totals']['subtotal'] = 0;
        foreach($data['items'] as $item){
          echo "<tr id='ret".$item['id']."' class='clickable";
          if($item['qty']-$item['returned'] > 0) echo " returnable";
          echo "'>
                  <td class='col-1'>".$item['entry_type']."</td>
                  <td class='col-1'>".$item['qty']."</td>
                  <td class='col-5'>".$item['name']."</span></td>
                  <td class='col-1'>".$item['disc']."</span></td>
                  <td class='col-2'>".$item['price_ea']."</span></td>
                  <td class='col-2'>".$item['price_ext']."</td>
                </tr>";
          if($item['returned'] > 0){
            echo "<tr class='note'>
                    <td class='col-12'>&#8627;".$item['returned']." Returned</td>
                  </tr>";
          }
          $data['totals']['subtotal'] += number_format(str_replace("$", "", $item['price_ext']), 2);
        }
        $data['totals']['tax'] = number_format($data['totals']['subtotal']*$store->SALES_TAX, 2);
        $data['totals']['grand'] = number_format($data['totals']['subtotal'] + $data['totals']['tax'], 2);
      ?>
    </tbody>
  </table>
  <div id='transactionTotals'>
    <p>Subtotal: $<?=$data['totals']['subtotal']?></p>
    <p>Tax: $<?=$data['totals']['tax']?></p>
    <p>Total: $<?=$data['totals']['grand']?></p>
    <p>
      <button class='btn btn-sm btn-primary' id='returnSingle' disabled>Return 1</button><br />
      <button class='btn btn-sm btn-secondary' id="returnLine" disabled>Return All</button><br />
      <button class='btn btn-sm btn-secondary' id='voidTransaction'>Void Transaction</button>
    </p>
  </div>
</div>
<script>
  $("#transactionItems table").on("click", "tbody .clickable", (e) => {
    $(e.currentTarget).addClass("highlight").siblings().removeClass("highlight");
    if($(e.currentTarget).hasClass("returnable")){
      $("#returnSingle").attr("disabled", false);
      $("#returnLine").attr("disabled", false);
    } else {
      $("#returnSingle").attr("disabled", true);
      $("#returnLine").attr("disabled", true);
    }
  });

  // $(window).on("keyup", (e) => {
  //   if(e.which == "40"){
  //     if($(".highlight").length == 0){
  //       $("#transactionItems table tbody .clickable:first").addClass("highlight");
  //     } else {
  //       let index = $(".highlight")[0].rowIndex;
  //       let rows = $("#transactionItems table tr").length;
  //       if(index < rows-1){
  //         $(".highlight").removeClass("highlight");
  //         $("#transactionItems table tbody .clickable:("+(index)+")").addClass("highlight");
  //       }
  //     }
  //     $("#returnSingle").attr("disabled", false);
  //     $("#returnLine").attr("disabled", false);
  //   }
  //   if(e.which == "38"){
  //     if($(".highlight").length == 0){
  //       $("#transactionItems table tbody .clickable:last-child").addClass("highlight");
  //     } else {
  //       let index = $(".highlight")[0].rowIndex;
  //       if(index > 1){
  //         $(".highlight").removeClass("highlight");
  //         $("#transactionItems table tbody .highlight + .clickable").addClass("highlight");
  //       }
  //     }
  //     $("#returnSingle").attr("disabled", false);
  //     $("#returnLine").attr("disabled", false);
  //   }
  // });

  $("#returnSingle").click(() => {
    if(window.confirm("Are you sure you want to return this item?")){
      let itemID = $(".highlight").attr("id").replace("ret", "");
      let params = {
        "method": "returnItem",
        "returnType": "single",
        "entryID": itemID
      }
      $.post("processing/returns.php", JSON.stringify(params), (response) => {
        console.log(response);
        data = JSON.parse(response);
        if(data.status == 'success'){
          window.alert("Item successfully returned.\n\nChange due: $"+data.amt);
        } else {
          window.alert("There was a problem processing your return. Please try again.");
        }
        window.location.reload();
      });
    }
  });

  $("#returnLine").click(() => {
    if(window.confirm("Are you sure you want to return this item?")){
      let itemID = $(".highlight").attr("id").replace("ret", "");
      let params = {
        "method": "returnItem",
        "returnType": "line",
        "entryID": itemID
      }
      $.post("processing/returns.php", JSON.stringify(params), (response) => {
        console.log(response);
        data = JSON.parse(response);
        if(data.status == 'success'){
          window.alert("Item successfully returned.\n\nChange due: $"+data.amt);
        } else {
          window.alert("There was a problem processing your return. Please try again.");
        }
        window.location.reload();
      });
    }
  });
</script>
