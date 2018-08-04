<?php
  //Turn off error reporting -- enable ONLY when debugging
  //error_reporting(0);

  //Remote host URL
  //$remote = "http://www.pricebustersgames.com/pbadmin/pos-api";
  $remote = "http://127.0.0.1/dev/pos-api";

  //Import constant definitions
  require(SITE_ROOT.'/includes/constants.php');

  //Import config file for DB connections
  require(SITE_ROOT.'/includes/db.php');

  //Import PHP files
  foreach(glob(SITE_ROOT.'/includes/autoload/php/*') as $file){
    include($file);
  }

  //Import classes
  foreach(glob(SITE_ROOT.'/includes/autoload/classes/*') as $file){
    include($file);
  }

  if(!isset($suppressMarkup)){

    //Bootstrap JS -- ORDER IS IMPORTANT
    ?>
    <script src='<?=SITE_ROOT?>/includes/bootstrap_js/jquery.js'></script>
    <script src='<?=SITE_ROOT?>/includes/bootstrap_js/popper.js'></script>
    <script src='<?=SITE_ROOT?>/includes/bootstrap_js/bootstrap.js'></script>

    <?php
    //Import CSS files
    foreach(glob(SITE_ROOT.'/includes/autoload/css/*') as $file){
      ?>
        <link rel='stylesheet' type='text/css' href='<?=$file?>'>
      <?php
    }

    //Import DYMO framework
    ?>
      <script src="<?=SITE_ROOT?>/includes/DYMO.Label.Framework.latest.js"></script>
    <?php

    //Import JS files
    foreach(glob(SITE_ROOT.'/includes/autoload/js/*') as $file){
      ?>
        <script src='<?=$file?>'></script>
      <?php
    }
  }
  $store = new Store;
?>
