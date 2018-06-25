<?php
  $stmt = "SELECT value FROM app_config WHERE setting = 'SECURITY_TOKEN'";
  $result = $mysqlL->query($stmt)->fetch_array();
  $token = $result[0];
?>
<div class='loginBoxWrapper'>
  <div class='loginBox'>
    <p id='warning'></p>
    <p class='form-group'>
      <label for='username'>Username:</label><input type='text' name='username' id='username' class='form-control' />
    </p>
    <p class='form-group'>
      <label for='password'>Password:</label><input type='password' name='password' id='password' class='form-control' />
    </p>
    <p style='text-align: center'><button id='login' class='btn btn-primary btn-sm'>Login</button></p>
  </div>
</div>
<script>
  $("#username").focus();

  $("#login").click(function(){
    login();
  });
  $("#username").keypress(function(e){
    if(e.which == 13){
      login();
    }
  });
  $("#password").keypress(function(e){
    if(e.which == 13){
      login();
    }
  });

  function login(){
    let username = $("#username").val();
    let password = $("#password").val();
    let params = "username="+username+"&password="+password+"&token=<?=$token?>";
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
      if(this.readyState == 4 & this.status == 200){
        console.log(this.responseText);
        if(this.responseText == 'success'){
          window.location.reload(true);
        } else {
          $("#warning").html("Login failed.");
          $("#warning").addClass("alert alert-danger mt-0 pt-0 mb-0 pb-0");
          $("#password").val("");
        }
      }
    };
    xhttp.open("POST", "<?=SITE_ROOT?>/processing/login.php");
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(params);
  }
</script>
<?php
  exit();
?>
