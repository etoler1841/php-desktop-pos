<div class='modal fade' id='pinModal' data-backdrop='static' data-keyboard='false' tabindex='-1' role='dialog' z-index='-1'>
  <div class='modal-dialog modal-sm' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='pinModalTitle'></h5>
      </div>
      <div class='modal-body'>

      </div>
      <div class='modal-footer'>

      </div>
    </div>
  </div>
</div>
<script>
  function showPINLogin(){
    $("#pinModalTitle").html("Enter PIN");
    $("#pinModal .modal-body").html(`
      <p>Enter your PIN or <a href='javascript:void(0);' id='pinOff'>login with your password</a>.</p>
      <div class='form-group'>
        <input type='password' class='form-control' id='pin' placeholder='PIN' />
      </div>
    `);
    $("#pinModal .modal-footer").html(`
      <button class='btn btn-primary' id='pinSubmit'>Login</button>
    `);
    $("#pin").focus();
  }

  function showPasswordLogin(){
    $("#pinModalTitle").html("Login");
    $("#pinModal .modal-body").html(`
      <p>Please login with your username and password or <a href='javascript:void(0);' id='pinOn'>try a different PIN</a>.</p>
      <div class='form-group'>
        <label for='username'>Username</label>
        <input type='text' id='username' class='form-control' />
      </div>
      <div class='form-group'>
        <label for='password'>Password</label>
        <input type='password' id='password' class='form-control' />
      </div>
    `);
    $("#pinModal .modal-footer").html(`
      <button class='btn btn-primary' id='loginSubmit'>Login</button>
    `);
    $("#username").focus();
  }

  let userShort, userFull, userID;

  $(document).on("keyup", "#pin", (e) => {
    if(e.which === 13){
      $("#pinSubmit").click();
    }
  });

  $(document).on("keyup", "#username", (e) => {
    if(e.which === 13){
      $("#loginSubmit").click();
    }
  });

  $(document).on("keyup", "#password", (e) => {
    if(e.which === 13){
      $("#loginSubmit").click();
    }
  });

  $(document).on("show.bs.modal", "#pinModal", () => {
    showPINLogin();
  });

  $(document).on("click", "#pinOn", () => {
    showPINLogin();
  });

  $(document).on("click", "#pinOff", () => {
    showPasswordLogin();
  });

  $(document).on("click", "#pinSubmit", () => {
    let pin = $("#pin").val();
    let params = {
      pin: pin
    };
    $.post("<?=SITE_ROOT?>/processing/pin.php", JSON.stringify(params), (res) => {
      let data = JSON.parse(res);
      switch(data.status){
        case 'ok':
        userFull = data.userFull;
        userShort = data.userShort;
        userID = data.userID;
        $("#pinModal").modal("hide");
          break;
        case 'expired':
          showPasswordLogin();
          break;
        case 'err':
          alert("That PIN doesn't match a user in our records. Please try again.");
          break;
        default:
          alert("We experienced an unknown error. Please try again.");
      }
      $("#pin").val("");
    });
  });

  $(document).on("click", "#loginSubmit", () => {
    let user = $("#username").val();
    let pass = $("#password").val();
    let params = {
      user: user,
      pass: pass
    };
    $.post("<?=SITE_ROOT?>/processing/login.php", JSON.stringify(params), (res) => {
      let data = JSON.parse(res);
      switch(data.status){
        case 'ok':
          $("#pinModal").modal("hide");
          userFull = data.userFull;
          userName = data.userShort;
          userID = data.userID;
          break;
        case 'err':
          alert("Invalid username or password.");
          break;
        default:
          alert("We experienced an unknown error. Please try again.");
      }
      $("#username").val("");
      $("#password").val("");
    });
  });
</script>
