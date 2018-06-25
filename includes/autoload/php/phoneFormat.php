<?php
  function phoneFormat($phone){
    switch(strlen($phone)){
      case 7:
        $newPhone = substr($phone, 0, 3)."-".substr($phone, 3, 4);
        break;
      case 10:
        $newPhone = "(".substr($phone, 0, 3).")".substr($phone, 3, 3)."-".substr($phone, 6, 4);
        break;
      default:
        $newPhone = $phone;
        break;
    }
    return $newPhone;
  }
?>
