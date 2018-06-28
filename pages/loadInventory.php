<div id='loadInventory'>
  <div class='container'>
    <div class='form-inline form-group'>
      <label for='productSKUSearch' class='mr-2'>SKU: </label>
      <input type='text' id='productSKUSearch' class='form-control mr-2' size='15' />
      <button class='btn btn-sm btn-secondary mr-2' id='productSKULookup' tabindex='-1'>Lookup</button>
      <button id='productBrowse' class='btn btn-sm btn-primary' data-toggle='modal' data-target='#productBrowseModal'>Browse...</button>
    </div>
  </div>
  <table class='table table-hover table-sm table-fixed' id='loadInventoryQueue'>
    <thead class='thead-light'>
      <tr>
        <th class="col-1">Qty.</th>
        <th class='col-5'>Description</th>
        <th class='col-2'>Category</th>
        <th class='col-2'>Price</th>
        <th class='col-2'>Print</th>
      </tr>
    </thead>
    <tbody>

    </tbody>
  </table>
  <button class='btn btn-sm btn-success' id='saveInventory'>Save</button>
  <?php
    require(SITE_ROOT.'/pages/load_inventory/productBrowse.php');
  ?>
</div>
<script>
  $(window).on("load", () => {
    $("#productSKUSearch").focus();
  });

  $("#productSKUSearch").on("keyup", (e) => {
    if(e.which == 13){
      $("#productSKULookup").click();
    }
  });

  $("#loadInventoryQueue").on("input", ".qty", (e) => {
    if($(e.target).val() <= 0) $(e.target).parent().parent().remove();
  });

  $("#saveInventory").click(() => {
    let $rows = $("#loadInventoryQueue tbody tr");
    for(row of $rows){
      let params = {
        id: row.id,
        qty: $(row).children().children(".qty").val()
      };
      $.post("<?=SITE_ROOT?>/processing/addInventory.php", JSON.stringify(params), () => {
        $(row).remove();
      });
    }
  });

  $("#productSKULookup").click(() => {
    let sku = $("#productSKUSearch").val();
    if(!sku){
      return;
    }
    let params = {
      sku: sku
    };
    $.post("<?=SITE_ROOT?>/processing/skuLookup.php", JSON.stringify(params), (result) => {
      let data = JSON.parse(result);
      if(data.status == 'ok'){
        if($("#"+data.id).length > 0){
          increment(data.id);
        } else {
          addItem(data);
        }
        $("#productSKUSearch").focus();
      } else {
        alert("Item not found.");
      }
      $("#productSKUSearch").val("");
    });
  });

  $("#loadInventoryQueue tbody").on("click", ".print", (e) => {
    let $row = $(e.currentTarget).parent().parent();
    let id = $row.attr("id");
    let category = $row.children(".cat").text();
    let name = $row.children(".name").text();
    let price = $row.children(".price").text();
    let params = {
      barcode: id,
      category: category,
      name: name,
      price: price
    };
    console.log(params);
    return;
    $.post("<?=SITE_ROOT?>/processing/printQuery.php", JSON.stringify({ id: id }), (result) => {
      let data = JSON.parse(result);
      for(type of data.type){
        printLabel(type, params);
      }
    });
  });

  //functions
  function addItem(params){
    let html = `<tr id='${params.id}'>
      <td class='col-1'><input type='number' class='form-control qty' style='width: 75px' value='1' /></td>
      <td class='col-5 name'>${params.name}</td>
      <td class='col-2 cat'>${params.cat}</td>
      <td class='col-2 price'>$${round(params.price)}</td>
      <td class='col-2'><button class='btn btn-sm btn-warning print'>Print Labels</button></td>
    </tr>`;
    $("#loadInventoryQueue tbody").prepend(html);
  }

  function increment(id){
    let $qtyField = $("#"+id+" .qty");
    $qtyField.val(parseInt($qtyField.val())+1);
  }
</script>
