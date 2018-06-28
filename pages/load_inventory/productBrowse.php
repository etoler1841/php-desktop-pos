<div class='modal fade' id='productBrowseModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog modal-lg' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='productBrowseModalTitle'>Browse Products</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-inline form-group'>
          <label for='productBrowseSearch' class='mr-2'>Search: </label>
          <input class='form-control' type='text' id='productBrowseSearch' size='35' />
        </div>
        <div id='productBrowseResults'>
          <h6><span id='productBrowseResultCount'>0</span> results</h6>
          <table class='table table-hover table-sm table-fixed' id='productBrowseResults'>
            <thead class='thead-light'>
              <tr>
                <th class='col-7'>Description</th>
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
        <button type='button' class='btn btn-primary' id='productBrowseSubmit' disabled>Add Item</button>
        <button type='button' class='btn btn-secondary' data-dismiss='modal' id='productBrowseClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("hidden.bs.modal", "#productBrowseModal", () => {
    $("#productBrowseSearch").val("");
    $("#productBrowseResultCount").text("0");
    $("#productBrowseResults tbody").html("");
    $("#productBrowseSubmit").attr("disabled", true);
  });

  $("#productBrowseSearch").keyup((e) => {
    if($(e.currentTarget).val().length >= 3){
      let params = { "term": $(e.currentTarget).val() };
      $.post("<?=SITE_ROOT?>/processing/lookupItem.php", JSON.stringify(params), function(result){
        data = JSON.parse(result);
        $("#productBrowseResultCount").text(data.length);
        let list = "";
        for(let i = 0, n = data.length; i < n; i++){
          list += `<tr id='res_${data[i].id}'>
            <td class='col-7 name'>${data[i].name}</td>
            <td class='col-3 cat'>${data[i].cat}</td>
            <td class='col-2 price'>$${round(data[i].price)}</td>
          </tr>`;
        }
        $("#productBrowseResults tbody").html(list);
        if(!$("#lookupItemResults .table-primary").length){
          $("#productBrowseSubmit").attr("disabled", true);
        }
      });
    }
  });

  $("#productBrowseModal tbody").on("click", "tr", (e) => {
    $("#productBrowseModal tbody tr").removeClass("table-primary");
    $(e.currentTarget).addClass("table-primary");
    $("#productBrowseSubmit").attr("disabled", false);
  });

  $("#productBrowseSubmit").click(() => {
    let id = $(".table-primary").attr("id").replace("res_", "");
    if($("#"+id).length){
      increment(id);
    } else {
      let name = $("#res_"+id+" .name").text();
      let cat = $("#res_"+id+" .cat").text();
      let price = $("#res_"+id+" .price").text().replace("$", "");
      let params = {
        id: id,
        name: name,
        cat: cat,
        price: price
      };
      addItem(params);
    }
    $("#productBrowseClose").click();
  });
</script>
