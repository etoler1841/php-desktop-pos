<div class='modal fade' id='quickRemoveModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog modal-lg' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='quickRemoveModalTitle'>Quick Remove Item</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-inline form-group'>
          <label for='quickRemoveScan' class='mr-2'>Scan: </label>
          <input class='form-control' type='text' id='quickRemoveScan' size='35' />
        </div>
        <div class='form-inline form-group'>
          <label for='quickRemoveSearch' class='mr-2'>Search: </label>
          <input class='form-control' type='text' id='quickRemoveSearch' size='35' />
        </div>
        <div id='quickRemoveResults'>
          <table class='table table-hover table-sm table-fixed' id='quickRemoveResults'>
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
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' id='quickRemoveSubmit'>Remove Item</button>
        <button type='button' class='btn btn-secondary' data-dismiss='modal' id='quickRemoveClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("hidden.bs.modal", "#quickRemoveModal", () => {
    $("#quickRemoveScan").val("");
    $("#quickRemoveSearch").val("");
    $("#quickRemoveResultCount").text("0");
    $("#quickRemoveResults tbody").html("");
  });

  $("#quickRemoveSearch").keyup((e) => {
    if($(e.currentTarget).val().length >= 3){
      let params = { "term": $(e.currentTarget).val() };
      $.post("<?=SITE_ROOT?>/processing/lookupItem.php", JSON.stringify(params), function(result){
        data = JSON.parse(result);
        $("#quickRemoveResultCount").text(data.length);
        let list = "";
        for(let i = 0, n = data.length; i < n; i++){
          list += "<tr id='"+data[i].id+"'><td class='col-1'>"+data[i].stock+"</td><td class='col-6'>"+data[i].name+"</td><td class='col-3'>"+data[i].cat+"</td><td class='col-2'>$"+round(data[i].price)+"</td></tr>";
        }
        $("#quickRemoveResults tbody").html(list);
      });
    }
  });

  $("#quickRemoveScan").keypress((e) => {
    if(e.which == 13){
      let params = { "id": $(e.currentTarget).val() };
      $.post("<?=SITE_ROOT?>/processing/scanItem.php", JSON.stringify(params), function(result){
        let data = JSON.parse(result);
        if(data.error){
          window.alert(data.error);
        } else {
          $("#quickRemoveResultCount").text("1");
          let list = "<tr id='"+data.id+"'><td class='col-1'>"+data.stock+"</td><td class='col-6'>"+data.name+"</td><td class='col-3'>"+data.cat+"</td><td class='col-2'>$"+round(data.price)+"</td></tr>";
          $("#quickRemoveResults tbody").html(list);
        }
      });
    }
  });

  $("#quickRemoveModal tbody").on("click", "tr", (e) => {
    $("#quickRemoveModal tbody").removeClass("table-primary");
    $(e.currentTarget).addClass("table-primary");
  });

  $("#quickRemoveSubmit").click(() => {
    let id = $(".table-primary").attr("id");
    let params = { "id": id };
    $.post("<?=SITE_ROOT?>/processing/removeItem.php", JSON.stringify(params));
    $("#quickRemoveClose").click();
  });
</script>
