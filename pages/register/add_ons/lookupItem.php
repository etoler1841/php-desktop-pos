<div class='modal fade' id='lookupItemModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog modal-lg' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='lookupItemModalTitle'>Lookup Item</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-inline form-group'>
          <label for='lookupItemSearch' class='mr-2'>Search: </label>
          <input class='form-control' type='text' id='lookupItemSearch' size='35' />
        </div>
        <h6><span id='lookupItemResultCount'>0</span> results</h6>
        <table class='table table-hover table-sm table-fixed' id='lookupItemResults'>
          <thead class='thead-light'>
            <tr>
              <th class='col-1'>Qty.</th>
              <th class='col-6'>Description</th>
              <th class='col-3'>Category</th>
              <th class='col-2'>Price</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' id='lookupItemSubmit' disabled>Add Item</button>
        <button type='button' class='btn btn-secondary' data-dismiss='modal' id='lookupItemClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("hidden.bs.modal", "#lookupItemModal", () => {
    $("#lookupItemSearch").val("");
    $("#lookupItemResultCount").text("0");
    $("#lookupItemResults tbody").html("");
    $("#lookupItemSubmit").attr("disabled", true);
  });

  $("#lookupItemSearch").keyup((e) => {
    if($(e.currentTarget).val().length >= 3){
      let params = { term: $(e.currentTarget).val() };
      $.post("<?=SITE_ROOT?>/processing/lookupItem.php", JSON.stringify(params), function(result){
        data = JSON.parse(result);
        $("#lookupItemResultCount").text(data.length);
        let list = "";
        for(let i = 0, n = data.length; i < n; i++){
          list += `<tr id='${data[i].id}'>
            <td class='col-1'>${data[i].stock}</td>
            <td class='col-6'>${data[i].name}</td>
            <td class='col-3'>${data[i].cat}</td>
            <td class='col-2'>$${round(data[i].price)}</td>
          </tr>`;
        }
        $("#lookupItemResults tbody").html(list);
        if(!$("#lookupItemResults .table-primary").length) {
          $("#lookupItemSubmit").attr("disabled", true);
        }
      });
    }
  });

  $("#lookupItemModal tbody").on("click", "tr", (e) => {
    $("#lookupItemModal tbody tr").removeClass("table-primary");
    $(e.currentTarget).addClass("table-primary");
    $("#lookupItemSubmit").attr("disabled", false);
  });

  $("#lookupItemSubmit").click(() => {
    let id = $(".table-primary").attr("id");
    if($("#reg"+id).length){
      let newQty = parseInt($("#reg"+id+" .qty").text())+1;
      let priceEa = parseFloat($("#reg"+id+" .priceEa").text().replace("$", ""));
      let newPriceExt = round(newQty*priceEa);
      $("#reg"+id+" .qty").html(newQty);
      $("#reg"+id+" .priceExt").html("$"+newPriceExt);
      updateTotal();
    } else {
      let params = { "id": id };
      $.post("<?=SITE_ROOT?>/processing/scanItem.php", JSON.stringify(params), function(result){
        let data = JSON.parse(result);
        data.qty = 1;
        data.type = 1;
        data.disc = 0;
        let row = registerItemRow(data);
        $("#registerMain tbody").append(row).on("done", updateTotal());
      });
    }
    $("#lookupItemClose").click();
  });
</script>
