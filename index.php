<head>
  <?php
    define('SITE_ROOT', '.');
    require(SITE_ROOT.'/includes/includes.php');
    require(SITE_ROOT.'/pin.php');
  ?>
  <title>Price Busters Games - Point of Sale</title>
</head>
<body>
  <div id='wrapper'>
    <?php
      require(SITE_ROOT.'/includes/menu.php');
    ?>
    <div id='content'>
      <?php
        $page = (isset($_GET['page'])) ? $_GET['page'] : 'register';
        if(!strpos($page, '.')) $page .= '.php';
        if(substr($page, 0, 1) != '/') $page = '/'.$page;
        if(!strpos($page, '/pages')) $page = '/pages'.$page;
        require(SITE_ROOT.$page);
      ?>
    </div>
  </div>
  <div id='footer'>
    <button id='logout' class='btn btn-dark btn-sm'>Logout</button>
  </div>
  <script>
    $("#pinModal").modal("show");

    $("#pinModal").on("shown.bs.modal", () => {
      $("#pin").focus();
    });

    $("#logout").click(function(){
      xhttp = new XMLHttpRequest();
      xhttp.onreadystatechange = function(){
        if(this.readyState == 4 & this.status == 200){
          window.location.reload(true);
        }
      };
      xhttp.open('POST', 'processing/logout.php');
      xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhttp.send('logout=true');
    });
  </script>
</body>
