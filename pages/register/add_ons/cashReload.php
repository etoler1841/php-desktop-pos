<div class='modal fade' id='cashReloadModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog modal-sm' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='cashReloadModalTitle'>Cash Reload</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-group'>
          <label for='cashReloadAmt'>Amount: </label>
          <div class='input-group'>
            <div class='input-group-prepend'>
              <div class='input-group-text' style='width: 15px;'>$</div>
            </div>
            <input type='text' id='cashReloadAmt' value='0.00' class='form-control currency col-3' />
          </div>
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' id='cashReloadSubmit'>Save Cash Reload</button>
        <button type='button' class='btn btn-secondary' data-dismiss='modal' id='cashReloadClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("hidden.bs.modal", "#cashReloadModal", () => {
    $("#cashReloadAmt").val("0.00");
  });

  $("body").on("shown.bs.modal", "#cashReloadModal", () => {
    $("#cashReloadAmt").select();
  });

  $("#cashReloadAmt").on("keyup", (e) => {
    if(e.which === 13){
      $("#cashReloadSubmit").click();
    }
  });

  $("#cashReloadModal").on("click", ".form-control", (e) => {
    $(e.currentTarget).select();
  });

  $("#cashReloadSubmit").click(() => {
    let params = {
      "employeeId": userID,
      "amt": $("#cashReloadAmt").val(),
    }
    $.post("<?=SITE_ROOT?>/processing/cashDrop.php", JSON.stringify(params), () => {
      $("#cashReloadClose").click();
    });
  });
</script>
