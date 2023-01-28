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
    $bmw = new Ppntmt\Appointment();
    // if necessary, set values to properties
    $bmw->lehgth_cal = 14;
    $bmw->endtime = "17:00";
    $bmw->tz = "Europe/Simferopol";
    $bmw->org_weekend = array('Сб' => '14:00', 'Sat' => '14:00',
                      'Вс' => '', "Sun" => '',);
    $bmw->rest_day_time = array('2022-12-17' => array(), '2022-12-15' => ['16:00', '17:00', '18:00'], );
    $bmw->holiday =  array('1979-09-18', '2005-05-31',);
    $bmw->period = 60;
    $bmw->worktime = array('09:00', '19:00');
    $bmw->lunch = array("12:00", 40);
    $bmw->exist_app_date_time_arr = ['2023-02-03' => array('11:00' => '', '13:00' => '', '14:30' => null),
                                      '2023-02-06' => array('13:00' => '30', '13:30' => '30', '15:00' => 40),
                                      '2023-02-07' => ['09:00' => '140'],
                                      '2023-02-08' => ['09:00' => '40', '09:40' => '30', '10:10' => '60'], ];
    $bmw->view_date_format = 'd.m';
    $bmw->view_time_format = 'H:i';
    // get date time
    $bmw->get_app();
    // output result
    print $bmw->html();
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
