<?php
  function getCategories($parentID = 0){
    $ch = curl_init();

    if(!intval($parentID)){
      $parentID = 0;
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, "$remote/category/".$parentID);
    $response = json_decode(curl_exec($ch));
    if($response->results){
      foreach($response->results['children'] as $child){
        $return[$child['categories_id']] = $child['categories_name'];
      }
    }
    sort($return);

    return $return;
  }
?>
