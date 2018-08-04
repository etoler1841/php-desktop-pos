<nav class='navbar navbar-expand-sm navbar-dark bg-primary fixed-top' style='width:100%'>
  <a class='navbar-brand' href='<?=SITE_ROOT?>'><strong>PB</strong>Point of Sale</a>
  <div class='collapse navbar-collapse'>
    <ul class='navbar-nav mr-auto'>
      <li class='nav-item'>
        <a class='nav-link' href='?page=register'>Register</a>
      </li>
      <li class='nav-item'>
        <a class='nav-link' href='?page=loadInventory'>Load Inventory</a>
      </li>
      <li class='nav-item'>
        <a class='nav-link' href='?page=reports&mode=daily&batch=<?=$store->registerBatch()?>'>View Report</a>
      </li>
      <li class='nav-item'>
        <a class='nav-link' href='?page=priceUpdates'>Price Updates<?=priceUpdateCheck()?></a>
      </li>
    </ul>
  </div>
  <span class='navbar-text'>
    Hello, <?=$employee->first_name?>
  </span>
</nav>
