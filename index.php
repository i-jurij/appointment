<?php
//чтобы ucfirst работал для кириллицы и других многобайтных кодировок
mb_internal_encoding("UTF-8");
include 'php/head.php';
include 'php/sql_db_var.php';
include_once 'php/init-sql.php';
include_once 'php/func.php';
include_once 'php/masters-sql.php';

?>
  <form method="post" action="" id="zapis_usluga_form" class="form_zapis_usluga">
    <div class="choice margin_bottom_1rem" id="time_choice">
      <?php
        $id = $masters[array_rand($masters)][7];
        //print $id.'<br />';
        include 'php/datetime-sql-query.php';
      ?>
    </div>
  </form>
  <div class="choice display_none" id="zapis_end"></div>
<!--  <div class="zapis_usluga margin_rlb1">
    <button type="button" class="buttons" id="button_back" value=""  >Назад</button>
    <button type="button" class="buttons" id="button_next" value=""  >Далее</button>
  </div>
-->
<!-- <script type="text/javascript" src="js/appointment.js"></script> -->
<div class="back shad rad pad mar">
<?php
print $id.'<br />';
//require_once('class/ppntmt/appointment0.class.php');
//$bmw = new Appointment0(lehgth_cal : 8, period : 60, rest_day : $vyhd, exist_app_date_time_arr : $zapisi);
//print $bmw->html_view;

print '<pre>';
#$bmw = new Ppntmt\Appointment(rest_day : $vyhd, exist_app_date_time_arr : $arr);
$bmw = new Ppntmt\Appointment();
//$bmw->all_dates("17:00", 8, "Europe/Moscow");
print '<br />';
//print_r($bmw->appointment_times); print '<br />';
//print_r($bmw->rest_times);
//print_r($bmw->result);
print '</pre>';

print $bmw->html;
?>
</div>
<?php
include 'php/foot.php';
/*
$reflectionMethod = new ReflectionMethod(get_class($bmw), '__construct');
var_dump($reflectionMethod->getFileName());
var_dump($reflectionMethod->getStartLine());
*/
?>
