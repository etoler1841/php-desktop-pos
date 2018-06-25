<div class='modal fade' id='receiptModal' tabindex='-1' role='dialog'>
  <div class='modal-dialog modal-sm' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='btn btn-primary' id='receiptPrint'>Print</button>
      </div>
      <div class='modal-body receipt' style='text-align: center'>
        <div id='receiptTop'>
          <img src='<?=SITE_ROOT?>/includes/img/logo.jpg' alt='Price Busters Games' width='150px' height='150px' />
          <p>
            <?=$store->STORE_ADDRESS?><br />
            <?=$store->STORE_CITY.', '.$store->STORE_STATE.' '.$store->STORE_ZIP?><br />
            <?=phoneFormat($store->STORE_PHONE)?><br />
            http://www.PriceBustersGames.com/
          </p>
        </div>
        <div id='receiptBody'>
          <p align='left'>
            Cashier: <span id='receiptCashier'></span><br />
            Transaction time: <span id='receiptTransTime'></span>
          </p>
          <table>
            <tbody id='receiptItems'>

            </tbody>
            <tfoot>
              <tr id='receiptSubtotal'>

              </tr>
              <tr id='receiptTax'>

              </tr>
              <tr id='receiptTotal'>

              </tr>
              <tr id='receiptTenderCash'>

              </tr>
              <tr id='receiptTenderCredit'>

              </tr>
              <tr id='receiptTenderGiftCard'>

              </tr>
              <tr id='receiptChangeDue'>
                <td colspan='2'>Change Due:</td>
              </tr>
            </tfoot>
          </table>
        </div>
        <div id='receiptBottom'>
          <h5>Return Policy</h5>
          <p>
            All products are covered under a 30-day replacement guarantee. If
            your items are defective, bring them back within 30 days of your
            purchase and we will gladly exchange them for a working copy of
            the same item. If an identical item is unavailable, you will
            instead receive store credit in the amount of your purchase.
          </p>
          <p>
            <strong>No refunds will be offered.</strong>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  function generateReceipt(transID){
    params = {
      "transID": 3
    };
    $.post("<?=SITE_ROOT?>/processing/receipt.php", JSON.stringify(params), (result) => {
      data = JSON.parse(result);
      for(line of data){
        switch(line.type) {
          case "employee":
            $("#receiptCashier").html(line.name);
            break;
          case "item":
            $("#receiptItems").append(`
              <tr>
                <td>${line.qty}</td>
                <td>${line.name}</td>
                <td>${line.amt}</td>
              </tr>
            `);
            break;
          case "subtotal":
            $("#receiptSubtotal").html(`
              <td colspan='2' class='receiptTotal'>Subtotal:</td>
              <td>${line.amt}</td>
            `);
            break;
          case "tax":
            $("#receiptTax").html(`
              <td colspan='2' class='receiptTotal'>Sales tax:</td>
              <td>${line.amt}</td>
            `);
            break;
          case "time":
            $("#receiptTransTime").html(line.time);
            break;
          case "total":
            $("#receiptTotal").html(`
              <td colspan='2' class='receiptTotal'><strong>Total:</strong></td>
              <td><strong>${line.amt}</strong></td>
            `);
            break;
          case "tender":
            if(line.amt.cash.replace("$", "") != 0){
              $("#receiptTenderCash").html(`
                <td colspan='2'>Cash Tendered:</td>
                <td>${line.amt.cash}</td>
              `);
            } else {
              $("#receiptTenderCash").hide();
            }
            if(line.amt.credit.replace("$", "") != 0){
              $("#receiptTenderCredit").html(`
                <td colspan='2'>Credit Card Tendered:</td>
                <td>${line.amt.credit}</td>
              `);
            } else {
              $("#receiptTenderCredit").hide();
            }
            if(line.amt.giftcard.replace("$", "") != 0){
              $("#receiptTenderGiftCard").html(`
                <td colspan='2'>Gift Card Tendered:</td>
                <td>${line.amt.giftcard}</td>
              `);
            } else {
              $("#receiptTenderGiftCard").hide();
            }
            break;
          case "change":
            $("#receiptChangeDue").append(`
              <td>${line.amt}</td>
            `);
            break;
        }
      }
    });
  }

  $("#receiptPrint").click(() => {
    $("#receiptModal .modal-body").printThis({
      importCSS: false,
      printContainer: false,
      loadCSS: "<?=SITE_ROOT?>/includes/css/receipt.css",
      removeInline: true
    });
  });
</script>
