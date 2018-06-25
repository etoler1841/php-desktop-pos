<head>
  <?php
    define('SITE_ROOT', '.');
    require(SITE_ROOT.'/includes/includes.php');
  ?>
  <title>Price Busters Games - Point of Sale</title>
</head>
<body>
  <div id='wrapper'>
    <?php
      //If the login has expired, force Logout
      if(strtotime($store->CURRENT_LOGIN_EXPIRATION) < time()) $store->logout();

      //Redirect if user is not logged in
      if(!$store->CURRENT_LOGIN) require(SITE_ROOT.'/login.php');

      //Otherwise, set the employee info and load a page
      $employee = new Employee(intval($store->CURRENT_LOGIN));
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
