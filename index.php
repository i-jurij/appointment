<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8" />
  <title>Appointment</title>
  <meta name="description" content="Appointment to masters">
  <META NAME="keywords" CONTENT="Appointment">
  <meta HTTP-EQUIV="Content-type" CONTENT="text/html; charset=UTF-8">
  <meta HTTP-EQUIV="Content-language" CONTENT="ru-RU">
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
  <meta name="author" content="I-Jurij">
  <?php include 'ppntmt/head.html'; ?>
</head>
  <body>
    <form method="post" action="" id="zapis_usluga_form" class="form_zapis_usluga">
      <div class="choice margin_bottom_1rem" id="time_choice"></div>
    </form>
    <div class="choice display_none" id="zapis_end"></div>
    <div class="back shad rad pad mar">
    <?php
    //чтобы ucfirst работал для кириллицы и других многобайтных кодировок
    mb_internal_encoding("UTF-8");
    require_once('ppntmt/appointment.php');
    //$bmw = new Appointment0(lehgth_cal : 8, period : 60, rest_day : $vyhd, exist_app_date_time_arr : $zapisi);
    $bmw = new Ppntmt\Appointment();
    print $bmw->html;
    ?>
    </div>
    <?php
    /*
    $reflectionMethod = new ReflectionMethod(get_class($bmw), '__construct');
    var_dump($reflectionMethod->getFileName());
    var_dump($reflectionMethod->getStartLine());
    */
    ?>
  </body>
</html>
