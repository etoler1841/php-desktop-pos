<div id='register'>
  <?php
    require(SITE_ROOT.'/pages/register/sidebar.php');
    require(SITE_ROOT.'/pages/register/main.php');
    require(SITE_ROOT.'/pages/register/bottom.php');
  ?>
  <?php
    foreach(glob(SITE_ROOT.'/pages/register/add_ons/*') as $file){
      require($file);
    }
  ?>
</div>
<script>
  //functions
  function updateTotal(){
    let subtotal = 0;
    let storeCredit = 0;
    let rows = $("#registerMain tbody tr");
    let type, lineTotal;
    for(let i = 0, n = rows.length; i < n; i++){
      type = Number($("#registerMain tbody tr:eq("+i+") .type").val());
      lineTotal = Number($("#registerMain tbody tr:eq("+i+") .priceExt").html().replace("$", ""));
      switch (type){
        case 1:
          subtotal += lineTotal;
          break;
        case 2:
          storeCredit -= lineTotal;
          break;
        case 3:
          subtotal += lineTotal;
          break;
      }
    }
    subtotal = round(subtotal);
    storeCredit = round(storeCredit);
    let taxable = Number(subtotal)-Number(storeCredit);
    let tax = round(Number(taxable)*<?=$store->SALES_TAX?>);
    let total = round(Number(taxable)+Number(tax));
    if(taxable < 0){
      tax = round(0);
      total = round(taxable);
    }
    $("#subtotal").text(subtotal);
    $("#storeCredit").text(storeCredit);
    $("#tax").text(tax);
    $("#total").text(total);
    if(total){
      $("#checkout").attr("disabled", false);
    } else {
      $("#checkout").attr("disabled", true);
    }
  }

  function updateLines(){
    let rows = $("#registerMain tbody tr");
    let data;
    for(let i = 0, n = rows.length; i < n; i++){
      data = {
        "id": $("#registerMain tbody tr:eq("+i+")").attr("id").replace("reg", ""),
        "catID": $("#registerMain tbody tr:eq("+i+") .catID").val(),
        "qty": $("#registerMain tbody tr:eq("+i+") .qty").text(),
        "type": $("#registerMain tbody tr:eq("+i+") .type").val(),
        "name": $("#registerMain tbody tr:eq("+i+") .desc").text(),
        "price": $("#registerMain tbody tr:eq("+i+") .priceEa").text().replace("$", ""),
        "disc": $("#registerMain tbody tr:eq("+i+") .discount").text().replace("%", "")
      };
      $("#registerMain tbody tr:eq("+i+")").replaceWith(registerItemRow(data));
    }
  }

  //listeners
  window.onload = () => {
    $("#scanField").focus();
    $.post("<?=SITE_ROOT?>/processing/checkRegisterBatch.php", "", (result) => {
      let buttons = $("#registerSidebar button");
      if (result === '0'){
        for(let i = 0, n = buttons.length; i < n-1; i++){
          $(buttons[i]).attr("disabled", true);
        }
        $("#closeRegister").text("Open Register");
        $("#closeRegisterModalTitle").text("Open Register");
        $("#closeRegisterSubmit").text("Open Register");
        $("#closeRegisterCID").hide();
      } else {
        for(let i = 0, n = buttons.length; i < n-1; i++){
          $(buttons[i]).attr("disabled", false);
        }
        $("#closeRegister").text("Close Register");
        $("#closeRegisterModalTitle").text("Close Register");
        $("#closeRegisterSubmit").text("Close Register");
        $("#closeRegisterCID").show();
      }
    });
  };

  $(".currency").blur((e) => {
    let val = $(e.currentTarget).val();
    if(isNaN(Number(val))){
      val = 0;
    }
    $(e.currentTarget).val(round(val));
  });

  $("#registerMain").on("click", ".action", (e) => {
    if($(e.currentTarget).hasClass("add")){
      let qtyField = $(e.currentTarget).parent().siblings(".qty");
      qtyField.text(Number(qtyField.text())+1);
    }

    if($(e.currentTarget).hasClass("subtract")){
      let qtyField = $(e.currentTarget).parent().siblings(".qty");
      qtyField.text(Number(qtyField.text())-1);
      if(Number(qtyField.text()) <= 0){
        $(e.currentTarget).parent().parent().remove();
      }
    }

    if($(e.currentTarget).hasClass("remove")){
      $(e.currentTarget).parent().parent().remove();
    }

    updateLines();
    updateTotal();
  });

  $("#registerMain").on("click", ".clickEdit", (e) => {
    let text = $(e.currentTarget).text().replace(/\'/g, "&apos;").replace("%", "");
    let size;
    if($(e.currentTarget).parent().hasClass("desc")){
      size = 35;
    } else if($(e.currentTarget).parent().hasClass("priceEa")){
      size = 3;
    } else {
      size = 2;
    }
    $(e.currentTarget).html("<input type='text' value='"+text+"' size='"+size+"'id='clickEditField' />");
    $("#clickEditField").select();
    $(e.currentTarget).removeClass("clickEdit");
  });

  $("#registerMain").on("blur", "#clickEditField", (e) => {
    let text = $(e.currentTarget).val();
    if($(e.currentTarget).parent().parent().hasClass("priceEa")){
      if(text === ''){
        text = Number(0.00);
      } else {
        text = round(text);
      }
    } else if($(e.currentTarget).parent().parent().hasClass("discount")){
      if(text === ''){
        text = "0.00%";
      } else {
        text = round(text)+"%";
      }
    } else {
      if(text === ''){
        text = '-';
      }
    }
    $(e.currentTarget).parent().addClass("clickEdit");
    $(e.currentTarget).parent().html(text);
    updateLines();
    updateTotal();
  });

  $("#scanField").keypress((e) => {
    if(e.which == 13){
      let id = $("#scanField").val();
      if($("#reg"+id).length){
        let newQty = parseInt($("#reg"+id+" .qty").text())+1;
        let priceEa = parseFloat($("#reg"+id+" .priceEa").text().replace("$", ""));
        let newPriceExt = round(newQty*priceEa);
        $("#reg"+id+" .qty").html(newQty);
        $("#reg"+id+" .priceExt").html("$"+newPriceExt);
        updateTotal();
      } else {
        let params = { "id": id };
        $.post("<?=SITE_ROOT?>/processing/scanItem.php", JSON.stringify(params), function(result){;
          let data = JSON.parse(result);
          if(data.error){
            window.alert(data.error);
          } else {
            data.qty = 1;
            data.type = 1;
            data.disc = 0;
            let row = registerItemRow(data);
            $("#registerMain tbody").append(row).on("done", updateTotal());
          }
        });
      }
      $("#scanField").val("");
    }
  });

  $("#voidTransaction").click(() => {
    if(confirm("Are you sure you want to void this transaction?")){
      $("#registerMain table tbody").html("");
      updateTotal();
    }
    $("#scanField").focus();
  });
</script>
