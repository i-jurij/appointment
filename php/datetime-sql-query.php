<?php
include $_SERVER['DOCUMENT_ROOT'].'/appointment/php/sql_db_var.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/appointment/php/init-sql.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/appointment/php/func.php';

//if (isset($_POST['master']) and !empty($_POST['master']))
if (isset($id))
{
  //list($m0,$m1,$m2,$master_phone,$m4,$id) = explode('#', $_POST['master']);
  //$mp = '+'.trim($master_phone);
  try {
    $tablec = "`".str_replace("`","``",$id)."`";
    //echo '<div class="back_pad_mar">' . $tablec . '</div>';

    if (tableExists($pdo, $tablec)) {
      $q_zap = "SELECT den, vremia FROM $tablec WHERE den >= CURRENT_DATE()";
      $zapisi = $pdo->query($q_zap)->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_COLUMN);
      //echo '<span class="back_pad_mar">Данные о записях получены</span>';
      //получим данные о выходных днях (все поля пустые кроме den)
      $sql = "SELECT den FROM $tablec
                WHERE den >= CURRENT_DATE() AND (vremia = '' OR vremia IS NULL
                AND (tlf_client ='' or tlf_client IS NULL))";
      $vyhd = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
      //print_r($vyhd);
    }
    else
    {
      $zap0 = true; //проверим $zap0 в zapis-datetime-grafik.php, если пусто, просто выводим даты и время
    }
  }
  catch (PDOException $e)
  {
    print "Error!: " . $e->getMessage() . '<br />';
    die();
  }
  //include_once $_SERVER['DOCUMENT_ROOT'].'/appointment/php/zapis-datetime-grafik.php';
}
?>
