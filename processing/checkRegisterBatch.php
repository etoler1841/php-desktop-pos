<?php
    $suppressMarkup = 1;
    define('SITE_ROOT', '..');
    require(SITE_ROOT.'/includes/includes.php');
    $sql = "SELECT closing_time
            FROM register_batch
            ORDER BY register_batch_id DESC
            LIMIT 1";
    $result = $db->query($sql)->fetch_array(MYSQLI_ASSOC);
    if($result['closing_time'] != '') echo 0;
?>