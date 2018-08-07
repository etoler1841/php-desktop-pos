<div class='modal fade' id='payoutModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog modal-sm' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='payoutModalTitle'>Enter Payout</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-group'>
          <label for='payoutAmt'>Amount (pre-tax): </label>
          <div class='input-group'>
            <div class='input-group-prepend'>
              <div class='input-group-text' style='width: 15px;'>$</div>
            </div>
            <input type='text' id='payoutAmt' value='0.00' class='form-control currency col-3' />
          </div>
        </div>
        <div class='form-group'>
          <label for='payoutTax'>Tax: </label>
          <div class='input-group'>
            <div class='input-group-prepend'>
              <div class='input-group-text' style='width: 15px;'>$</div>
            </div>
            <input type='text' id='payoutTax' value='0.00' class='form-control currency col-3' />
          </div>
        </div>
        <div class='form-group'>
          <label for='payoutDescription'>Description:</label>
          <input type='text' id='payoutDescription' class='form-control' />
        </div>
        <div class='form-group'>
          <label for='payoutCategory'>Category: </label>
          <select class='form-control' id='payoutCategory'>
            <option selected>--Select Category--</option>
            <?php
              foreach(getCategories() as $catID => $catName){
                echo "<option value='$catID'>$catName</option>";
              }
            ?>
          </select>
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' id='payoutSubmit'>Save Payout</button>
        <button type='button' class='btn btn-secondary' data-dismiss='modal' id='payoutClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("hidden.bs.modal", "#payoutModal", () => {
    $("#payoutAmt").val("0.00");
    $("#payoutTax").val("0.00");
    $("#payoutDescription").val("");
    $("#manualItemCategory").prop("selectedIndex", 0);
  });

  $("#payoutModal").on("click", ".form-control", (e) => {
    $(e.currentTarget).select();
  });

  let updateTax
  $("#payoutAmt").on("focus", () => {
    if($("#payoutTax").val() == "0.00"){
      updateTax = true;
    } else {
      updateTax = false;
    }
  });

  $("#payoutAmt").on("keyup blur", (e) => {
    if(updateTax){
      let val = parseFloat($(e.currentTarget).val());
      let tax = parseFloat(<?= $store->SALES_TAX ?>);
      $("#payoutTax").val(round(val*tax));
    }
  });

  $("#payoutSubmit").click(() => {
    let params = {
      "employeeId": userID,
      "amt": $("#payoutAmt").val(),
      "tax": $("#payoutTax").val(),
      "desc": $("#payoutDescription").val(),
      "category": $("#payoutCategory").val()
    }
    $.post("<?=SITE_ROOT?>/processing/payout.php", JSON.encode(params));
  });
</script>
