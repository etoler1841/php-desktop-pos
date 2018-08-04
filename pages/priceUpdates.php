<?php
  $store->priceUpdates();
  $sql = "SELECT pc.products_id AS id, p.products_name AS name, pc.new_price AS new, p.products_price AS old
          FROM price_change pc
          LEFT JOIN products p ON pc.products_id = p.products_id
          LEFT JOIN categories c ON p.master_categories_id = c.categories_id
          ORDER BY c.categories_name ASC, p.products_name ASC";
  $result = $db->query($sql);
?>
<div id='priceUpdates'>
  <table class='table table-striped table-fixed table-hover'>
    <thead>
      <tr class='thead-light'>
        <th class='col-9'>Product Name</th>
        <th class='col-1'>Old Price</th>
        <th class='col-1'>New Price</th>
        <th class='col-1'>Update</th>
      </tr>
    </thead>
    <tbody>
      <?php
        while($row = $result->fetch_array(MYSQLI_ASSOC)){
          extract($row);
          echo "<tr id='$id'>
            <td class='col-9'>$name</td>
            <td class='col-1'>\$".number_format($old, 2)."</td>
            <td class='col-1'>\$".number_format($new, 2)."</td>
            <td class='col-1'><button class='update'>Update</button></td>
          </tr>";
        }
      ?>
    </tbody>
  </table>
</div>
<script>
  $(".update").click((e) => {
    let id = $(e.target).parent().parent().attr("id");
    let params = {
      id: id
    };
    $.post("<?=SITE_ROOT?>/processing/priceUpdates.php", JSON.stringify(params), (res) => {
      let data = JSON.parse(res);
      if(data.status === 'ok'){
        $("#"+id).remove();
      }
    });
  });
</script>
