<div class='modal fade' id='manualItemModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='manualItemModalTitle'>Add Manual Item</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-group mr-3' style='float: left;'>
          <label for='manualItemQty'>Quantity: </label>
          <input class='form-control' type='number' id='manualItemQty' value='1' />
        </div>
        <div class='form-group' style='margin-left: 10px !important;'>
          <label for='manualItemPrice'>Price: </label>
          <div class='input-group' style='width: 125px;'>
            <div class='input-group-prepend'>
              <div class='input-group-text pl-1' style='width:15px;'>$</div>
            </div>
            <input class='form-control currency col-8' type='text' id='manualItemPrice' value='0.00' />
          </div>
        </div>
        <div class='form-group'>
          <label for='manualItemDesc'>Description: </label>
          <input class='form-control' type='text' id='manualItemDesc' />
        </div>
        <div class='form-group'>
          <label for='manualItemCategory'>Category: </label>
          <select class='form-control' id='manualItemCategory'>
            <option selected>--Select Category--</option>
            <?php
              foreach(getCategories() as $catID => $catName){
                echo "<option value='$catID'>$catName</option>";
              }
            ?>
          </select>
        </div>
        <div class='row'>
          <span class='col-2'><label>Type:</label></span>
          <span class='col-3'><input type='radio' name='type' id='typeSale' value='1' checked> <label class='radio-inline' for='typeSale'>Sale</label></span>
          <span class='col-3'><input type='radio' name='type' id='typeTrade' value='2'> <label class='radio-inline' for='typeTrade'>Trade</label></span>
          <span class='col-3'><input type='radio' name='type' id='typePurchase' value='3'> <label class='radio-inline' for='typePurchase'>Purchase</label></span>
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' id='manualItemSubmit'>Add Item</button>
        <button type='button' class='btn btn-secondary' data-dismiss='modal' id='manualItemClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("hidden.bs.modal", "#manualItemModal", () => {
    $("#manualItemQty").val("1");
    $("#manualItemPrice").val("0.00");
    $("#manualItemDesc").val("");
    $("#manualItemCategory").prop("selectedIndex", 0);
    $("#typeSale").prop("checked", true);
  });

  $("#manualItemModal").on("click", ".form-control", (e) => {
    $(e.currentTarget).select();
  });

  $("#manualItemQty").change((e) => {
    if(Number($(e.currentTarget).val()) < 1){
      $(e.currentTarget).val(1);
    }
    $(e.currentTarget).val(round($(e.currentTarget).val(), 0));
  });

  $("#manualItemSubmit").click(() => {
    let type = $("input[name='type']:checked").val();
    console.log(type);
    let data = {
      "id": null,
      "type": type,
      "catID": $("#manualItemCategory").val(),
      "qty": $("#manualItemQty").val(),
      "name": $("#manualItemDesc").val(),
      "price": $("#manualItemPrice").val(),
      "disc": 0
    };
    let row = registerItemRow(data);
    $("#registerMain tbody").append(row).on("done", updateTotal());
    $("#manualItemClose").click();
  });
</script>
