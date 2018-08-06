<div class='modal fade' id='checkoutModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog modal-sm' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='checkoutTitle'>Checkout</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-group'>
          <label for='checkoutCash'>Cash: </label>
          <div class='input-group'>
            <div class='input-group-prepend'>
              <div class='input-group-text pl-1' style='width:15px;'>$</div>
            </div>
            <input type='text' class='form-control currency col-4' id='checkoutCash' value='0.00' />
          </div>
        </div>
        <div class='form-group'>
          <label for='checkoutCredit'>Credit: </label>
          <div class='input-group'>
            <div class='input-group-prepend'>
              <div class='input-group-text pl-1' style='width:15px;'>$</div>
            </div>
            <input type='text' class='form-control currency col-4' id='checkoutCredit' value='0.00' />
          </div>
        </div>
        <div class='form-group'>
          <label for='checkoutGiftcard'>Gift Card: </label>
          <div class='input-group'>
            <div class='input-group-prepend'>
              <div class='input-group-text pl-1' style='width:15px;'>$</div>
            </div>
            <input type='text' class='form-control currency col-4' id='checkoutGiftcard' value='0.00' />
          </div>
        </div>
        <p><strong>Trade Credit Applied: $<span id='checkoutStoreCredit'>0.00</span></strong></p>
        <p><strong><span id='checkoutText'>Balance</span> Due: $<span id='checkoutDue'>0.00</span></strong></p>
      </div>
      <div class='modal-footer'>
        <button class='btn btn-primary' id='checkoutSubmit' disabled>Checkout</button>
        <button class='btn btn-secondary' data-dismiss='modal' id='checkoutClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("shown.bs.modal", "#checkoutModal", () => {
    let total = Number($("#total").text());
    if(total <= 0){
      $("#checkoutSubmit").attr("disabled", false);
      total = -total;
      $("#checkoutText").text("Change");
    } else {
      $("#checkoutSubmit").attr("disabled", true);
      $("#checkoutText").text("Balance");
    }
    $("#checkoutDue").text(round(total));
    $("#checkoutStoreCredit").text(round($("#storeCredit").text()));
    $("#checkoutCash").select();
  });

  $("body").on("hidden.bs.modal", "#checkoutModal", () => {
    $("#checkoutModal input").val("0.00");
  });

  $("#checkoutModal").on("click", ".currency", (e) => {
    $(e.currentTarget).val($("#checkoutDue").text());
  });

  $("#checkoutModal").on("change click keyup", ".currency", () => {
    let total = Number($("#total").text());
    let fields = $("#checkoutModal .currency");
    for (let i = 0, n = fields.length; i < n; i++){
      total = total - Number(fields[i].value);
    }
    if(total <= 0){
      $("#checkoutSubmit").attr("disabled", false);
      total = -total;
      $("#checkoutText").text("Change");
    } else {
      $("#checkoutSubmit").attr("disabled", true);
      $("#checkoutText").text("Balance");
    }
    $("#checkoutDue").text(round(total));
  });

  $("#checkoutSubmit").click(() => {
    let params = {
      "empID": userID,
      "total": $("#total").text(),
      "sales_tax": $("#tax").text(),
      "tender_cash": $("#checkoutCash").val(),
      "tender_credit": $("#checkoutCredit").val(),
      "tender_giftcard": $("#checkoutGiftcard").val(),
      "change_due": $("#checkoutDue").text(),
      "items": []
    };
    let rows = $("#registerMain tbody tr");
    let item = [];
    let row;
    let pid;
    for(let i = 0, n = rows.length; i < n; i++){
      row = rows[i];
      if($(row).attr("id")) {
        pid = $(row).attr("id").replace("reg", "");
      } else {
        pid = null;
      }
      item = {
        "entry_type": 1,
        "products_id": pid,
        "categories_id": $("#registerMain tbody tr:eq("+i+") .catID").val(),
        "products_quantity": $("#registerMain tbody tr:eq("+i+") .qty").text(),
        "products_name": $("#registerMain tbody tr:eq("+i+") .desc").text(),
        "products_discount": $("#registerMain tbody tr:eq("+i+") .discount").text().replace("%", ""),
        "products_price_ea": $("#registerMain tbody tr:eq("+i+") .priceEa").text().replace("$", ""),
        "products_price_ext": $("#registerMain tbody tr:eq("+i+") .priceExt").text().replace("$", "")
      };
      params.items.push(item);
    }
    $.post("<?=SITE_ROOT?>/processing/checkout.php", JSON.stringify(params), function(response){
      data = JSON.parse(response);
      $("#checkoutClose").click();
      generateReceipt(data.transID);
      $("#receiptModal").modal();
      $("#registerMain tbody").html("");
      updateTotal();
    });
  });
</script>
