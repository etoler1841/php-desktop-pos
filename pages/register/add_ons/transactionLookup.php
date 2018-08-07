<div class='modal fade' id='transactionLookupModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog modal-sm' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='quickRemoveModalTitle'>Transaction Lookup</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-group'>
          <label for='transactionID'>Transaction ID: </label>
          <input class='form-control' type='text' id='transactionID' />
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' id='transactionLookupSubmit'>Lookup</button>
        <button type='button' class='btn btn-secondary' data-dismiss='modal' id='transactionLookupClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("hidden.bs.modal", "#transactionLookupModal", () => {
    $("#transactionID").val("");
  });

  $("#transactionID").keyup((e) => {
    if(e.which == 13){
      $("#transactionLookupSubmit").click();
    }
  });

  $("#transactionLookupSubmit").click(() => {
    let id = $("#transactionID").val();
    let params = {
      "id": id,
      "empID": userID
    };
    $.post("<?=SITE_ROOT?>/processing/transactionLookup.php", JSON.stringify(params), (response) => {
      if(response == 'success'){
        window.location.href = "?page=transactionLookup&transID="+id;
      } else {
        window.alert("Transaction not found.");
        $("#transactionID").val("");
      }
    });
  });
</script>
