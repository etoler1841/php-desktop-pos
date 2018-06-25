<?php $register = new Register($store->registerBatch()); ?>
<div class='modal fade' id='closeRegisterModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='closeRegisterModalTitle'>Close Register</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='form-row'>
          <div class='col'></div>
          <div class='col'>Loose</div>
          <div class='col'>Rolls</div>
          <div class='col'></div>
        </div>
        <?php
          $coins = array(
            array(
              'name_sing' => 'penny',
              'name_pl' => 'Pennies',
              'loose_mult' => .01,
              'rolls_mult' => .5
            ),
            array(
              'name_sing' => 'nickel',
              'name_pl' => 'Nickels',
              'loose_mult' => .05,
              'rolls_mult' => 2
            ),
            array(
              'name_sing' => 'dime',
              'name_pl' => 'Dimes',
              'loose_mult' => .1,
              'rolls_mult' => 5
            ),
            array(
              'name_sing' => 'quarter',
              'name_pl' => 'Quarters',
              'loose_mult' => .25,
              'rolls_mult' => 10
            )
          );
          foreach($coins as $coin){
            echo "<div class='form-row'>
                <div class='col'>".$coin['name_pl']."</div>
                <div class='col'><input type='number' class='form-control form-control-sm' id='".$coin['name_sing']."Loose' value='0'><input type='hidden' class='multiplier' value='".$coin['loose_mult']."' /></div>
                <div class='col'><input type='number' class='form-control form-control-sm' id='".$coin['name_sing']."Rolls' value='0'><input type='hidden' class='multiplier' value='".$coin['rolls_mult']."' /></div>
                <div class='col'>$<span id='".$coin['name_sing']."Total' class='lineTotal'>0.00</span></div>
                </div>";
          }
        ?>
        <div class='form-row'>
          <div class='col'></div>
          <div class='col'>Loose</div>
          <div class='col'>Bundles</div>
          <div class='col'></div>
        </div>
        <?php
          $bills = array(
            array(
              'name_full' => 'ones',
              'name_abb' => '$1',
              'loose_mult' => 1,
              'bundle_mult' => 50
            ),
            array(
              'name_full' => 'fives',
              'name_abb' => '$5',
              'loose_mult' => 5,
              'bundle_mult' => 250
            ),
            array(
              'name_full' => 'tens',
              'name_abb' => '$10',
              'loose_mult' => 10,
              'bundle_mult' => 500
            ),
            array(
              'name_full' => 'twenties',
              'name_abb' => '$20',
              'loose_mult' => 20,
              'bundle_mult' => 1000
            ),
            array(
              'name_full' => 'fifties',
              'name_abb' => '$50',
              'loose_mult' => 50,
              'bundle_mult' => 2500
            ),
            array(
              'name_full' => 'hundreds',
              'name_abb' => '$100',
              'loose_mult' => 100,
              'bundle_mult' => 5000
            )
          );
          foreach($bills as $bill){
            echo "<div class='form-row'>
                <div class='col'>".$bill['name_abb']."</div>
                <div class='col'><input type='number' class='form-control form-control-sm' id='".$bill['name_full']."Loose' value='0'><input type='hidden' class='multiplier' value='".$bill['loose_mult']."' /></div>
                <div class='col'><input type='number' class='form-control form-control-sm' id='".$bill['name_full']."Rolls' value='0'><input type='hidden' class='multiplier' value='".$bill['bundle_mult']."' /></div>
                <div class='col'>$<span id='".$bill['name_full']."Total' class='lineTotal'>0.00</span></div>
                </div>";
          }
        ?>
        <div class='form-row'>
          <div class='col'>Other</div>
          <div class='col'><input type='number' class='form-control form-control-sm' id='other' value='0' /><input type='hidden' class='multiplier' value='1' /></div>
          <div class='col'></div>
          <div class='col'>$<span id='otherTotal' class='lineTotal'>0.00</span></div>
        </div>
        <div class='form-row'>
          <div class='col'><strong>Total</strong></div>
          <div class='col'></div>
          <div class='col'></div>
          <div class='col'><strong>$<span id='closeRegisterTotal'>0.00</span></strong></div>
        </div>
        <div class='form-row' id='closeRegisterCID' style='display:none;'>
          <div class='col'><strong>Cash in drawer</strong></div>
          <div class='col'></div>
          <div class='col'></div>
          <div class='col'><strong>$<?= number_format($register->cashInDrawer, 2) ?></strong></div>
        </div>
      </div>
      <div class='modal-footer'>
        <button type='button' class='btn btn-primary' id='closeRegisterSubmit'>Close Register</button>
        <button type='button' class='btn btn-secondary' data-dismiss='modal' id='closeRegisterClose'>Cancel</button>
      </div>
    </div>
  </div>
</div>
<script>
  $("body").on("hidden.bs.modal", "#quickRemoveModal", () => {
    let inputs = $("input[type='number']");
    for(let i = 0; i < inputs.length; i++){
      inputs[i].val(0);
    }
  });

  $("#closeRegisterSubmit").click(() => {
    let mode;
    if($("#closeRegisterSubmit").text() == 'Open Register'){
      mode = 'open';
    } else {
      mode = 'close';
    }
    let params = {
      "mode": mode,
      "count": $("#closeRegisterTotal").text(),
    };
    $.post("<?=SITE_ROOT?>/processing/closeRegister.php", JSON.stringify(params), function(results){
      if(mode == 'open'){
        window.location.reload();
      } else if (mode == 'close'){
        console.log(results);
        data = JSON.parse(results);
        window.location.href = "?page=reports&mode=daily&batch="+data.batchID;
      }
    });
  });

  $("#closeRegisterModal").on("focus", "input", (e) => {
    $(e.currentTarget).select();
  });

  $("#closeRegisterModal").on("change keyup", "input", (e) => {
    if(Number($(e.currentTarget).val()) < 0){
      $(e.currentTarget).val(0);
    }
    if($(e.currentTarget).attr("id") != "other"){
      $(e.currentTarget).val(round($(e.currentTarget).val(), 0));
    }
    let total = 0;
    let lineFields = $(e.currentTarget).parent().parent().children().children(".form-control");
    let multipliers = $(e.currentTarget).parent().parent().children().children(".multiplier");
    let lineTotal = 0;
    for(let j = 0, k = lineFields.length; j < k; j++){
      lineTotal += Number($(lineFields[j]).val())*Number($(multipliers[j]).val());
    }
    $(e.currentTarget).parent().siblings().children(".lineTotal").text(round(lineTotal));
    let lineTotals = $("#closeRegisterModal .modal-body .lineTotal");
    for(let i = 0, n = lineTotals.length; i < n; i++){
      total += Number($(lineTotals[i]).text());
    }
    $("#closeRegisterTotal").text(round(total));
  });
</script>
