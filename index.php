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
    function dlQueue(){
      $.post("<?=SITE_ROOT?>/processing/dlQueue.php");
    }

    dlQueue();
    setInterval(() => {
      dlQueue();
    }, 60000);

    if(!suppressPIN){
      $("#pinModal").modal("show");
    }

    $("#pinModal").on("shown.bs.modal", () => {
      $("#pin").focus();
    });

    $("#logout").click(function(){
      let params = {
        empID: userID
      };
      $.post("<?=SITE_ROOT?>/processing/logout.php", JSON.stringify(params), () => {
        location.reload(true);
      });
    });
  </script>
</body>
