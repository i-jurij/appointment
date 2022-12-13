<?php
    $zpdo = new PDO("mysql:host=$databaseHost", $databaseUser, $databasePassword);
    // set the PDO error mode to exception
    $zpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //$databaseName = "`".str_replace("`","``",$databaseName)."`";
    $zpdo->query("use $databaseName");

    $zsql = "SELECT ID, master_name, sec_name, master_fam, master_phone_number, spec, data_priema, data_uvoln FROM `masters`";
    $vib_spez = $zpdo->query($zsql)->fetchAll();

    $masters = array();
    foreach ($vib_spez as $result_row)
    {
      //проверяем, не уволен ли мастер
      $uvolen = $result_row['data_uvoln'];
      $img='';
      if (empty($uvolen))
      {
        $master = array($img, $result_row['master_name'], $result_row['master_fam'], $result_row['master_phone_number'], $result_row['spec'], $result_row['data_priema'], $result_row['sec_name'], $result_row['ID']) ;
        $masters[] = $master ;
      }
    }
    unset($master);
    $zpdo = null ;
?>
