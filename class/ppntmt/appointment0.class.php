<?php
//CONNECT
//you need to connect the css and js files for the class to work properly:
//add into html HEAD next:
/* <?php require_once('path_to_dir/ppntmt/appointment0.class.php'); echo Appointment0::add_css_js('path_to_dir'); ?> */
//path_to_dir eg first/second/ppntmt -> first/second
//
//WORK
//has no required input parameters, default presented below
//connect in PHP8 may be so:
//$var = new AppointmentDatesTimes(endtime : "18:00",
//                                 lehgth_cal : 14,
//                                 tz : "Europe/Moscow",
//                                 period : 30,
//                                 weekend : array("Вс", "Sun",),
//                                 rest_day : array('1979-09-18','1979-09-18'),
//                                 holiday : array('1979-09-18','1979-09-18'),
//                                 lunch : array("12:00", "12:30"),
//                                 exist_app_date_time_arr : array('2022-12-07' => array('10:00', '14:00', '14:00')),
//                                 view_date_format : 'd.m',
//                                 view_time_format = 'H:i');
//
//PROPERTIES
//
//$endtime = "17:00"; время после которого сегодня запись недоступна, а в списке добавляется еще один день
//$lehgth_cal = 8; количество отображаемых дней для записи
//$tz = "Europe/Moscow"; часовой пояс
//$period = 60;  промежуток времени между записями, мб любой, преобразуется кратно 10,
//то есть 7 мин -> 10 мин, 23 мин -> 30 мин и тп
//кроме промежутков > 10, но < 15 - преобразуется в 15 минутный промежуток
//
//$weekend = array('Сб' => '14:00', 'Sat' => '14:00','Вс' => '', "Sun" => '',)
// постоянно планируемые в организации выходные, ключ - название дня,
//значение - пустое, если целый день выходной, или начало отдыха в 24часовом формате HH:mm
//
//$holiday =  array('1979-09-18','2005-05-31',) - праздничные дни на год
//
//$lunch = array("12:00",) - время обеда, если перерыв на обед допустим 1 час,
//а $period = 20, то $lunch = array("12:00", "12:20", "12:40")
//
//$worktime = array('09.00', '18:00'), рабочее время $worktime[0] - начало, $worktime[1] - конец
//
//////////////// DATA related to a specific MASTER:
//$rest_day = array('1979-09-18','2005-05-31',) - запланированные выходные дни и часы,
//могут быть получены из рабочего графика мастера
//
//$exist_app_date_time_arr - массив предыдущих записей к мастеру в формате array('date' => array('times')),
//где date в формате день недели две буквы WD и YYYY-mm-dd, eg "Пт 2022-12-02"
//а array('times') - массив времен записей в в 24часовом формате HH:mm
//eg ['2022-12-02' => array('12:00', '15:00'), '2022-12-03' => array('13:00')]
/////////////////////////////////
//
//
//PROPERTIES
//
//$this->adates - массив дат в формате день недели две буквы WD и YYYY-mm-dd, eg "Пт 2022-12-02"
//если нужны названия дней недели на английском - закомментируйте строку
//$rudayweek = $this->en_dayweek_to_rus($engdayweek); в функции pre_dates($lehgth_cal)
//если нужны названия дней недели на другом языке, замените русские сокращения на нужные
//в массиве $cyr функции en_dayweek_to_rus($dayweek)
//
//$this->atimes - массив времен для записи в в 24часовом формате HH:mm, например $time = array('09:00', '11:00', '13:00', '15:00', '17:00');
//
//$this->appointment_dates - даты дней записи + disabled для всех выходных, праздников и тд
// eg "Пн 2022-12-08 disabled"
//
//$this->appointment_times - времена записи на каждый день с пометками disabled
////////////////////////////////
//$this->html_view - строка c html кодом и данными для вывода на страницу:
//<div class="master_datetime" id="master_datetime">
//  <div class="master_dates">
//    <div class="master_date">
//      <input type="radio" class="dat" id="DATEd" name="date" value="DATE" />
//      <label for="DATEd">WEEKDAY<br />WEEKDAY+NUMBER_DAY_OF_MONTH</label>
//    </div>
//  </div>
//  <div class="master_times" style="display:none;" id="tDATE">
//    <div class="master_time ">
//      <input type="radio" id="DATETIME" name="time" value="TIME" required />
//      <label for="DATETIME">TIME</label>
//    </div>
//  </div>
//</div>
////////////////////////////////

class Appointment0
{
    public function __construct(public $endtime = "17:00",
                                public int $lehgth_cal = 8,
                                public $tz = "Europe/Moscow",
                                public $period = 60,
                                public $weekend = array('Сб' => '14:00', 'Sat' => '14:00','Вс' => '', "Sun" => '',),
                                public $rest_day = array('1979-09-18', '2005-05-31',),
                                public $holiday =  array('1979-09-18', '2005-05-31',),
                                public $lunch = array("12:00", "12:30"),
                                public $worktime = array('09:00', '19:00'),
                                public $exist_app_date_time_arr = array(),
                                public $view_date_format = 'd.m',
                                public $view_time_format = 'H:i',
                                public $path_to_class_dir = '')
    {
      $this->adates = $this->all_dates($endtime, $lehgth_cal, $tz);
      $this->atimes = $this->all_times($period, $lunch, $worktime);
      $this->appointment_dates = $this->appointment_dates($weekend, $rest_day, $holiday);
      $this->appointment_times = $this->appointment_times($weekend, $rest_day, $holiday, $exist_app_date_time_arr, $tz);
      $this->html_view = $this->html_view($view_date_format, $view_time_format);
    }

    public function en_dayweek_to_rus($dayweek)
    {
      $cyr = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
      $lat = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      $dayrus = str_replace($lat, $cyr, $dayweek);
      return $dayrus;
    }

    protected function pre_dates($lehgth_cal)
    {
      $startDate = new DateTimeImmutable();
      $endDate = new DateTimeImmutable('+'.$lehgth_cal.' day');
      $period = new DatePeriod($startDate, new \DateInterval('P1D'), $endDate->modify('+1 day'));
      foreach ($period as $date2)
      {
        $engdayweek = $date2->format('D');
        $rudayweek = $this->en_dayweek_to_rus($engdayweek);
        $date[] = $rudayweek . "&nbsp;". $date2->format('Y-m-d');
      }
      return $date;
    }

    protected function all_dates($endtime, $lehgth_cal, $tz)
    {
      $now = new DateTimeImmutable('now', new DateTimeZone($tz));
      $endnow = new DateTimeImmutable($endtime);
      if ($now > $endnow)
      {
        $lehgth_cal++;
        $res = $this->pre_dates($lehgth_cal);
        array_shift($res);
      }
      else
      {
        $res = $this->pre_dates($lehgth_cal);
      }
      return $res;
    }

    // создает массив времен на рабочий день с указанным промежутком $period в минутах
    protected function all_times($period, $lunch, $worktime) {
      $round_period = ($period > 10 && $period < 16) ? 15 : ceil($period / 10) * 10;

      $start = DateTimeImmutable::createFromFormat('H:i', $worktime[0]);
      $end = DateTimeImmutable::createFromFormat('H:i', $worktime[1]);
      $interval = new DateInterval('PT'.$round_period.'M');
      //$period = new DatePeriod($start, $interval, $end->modify('+'.$round_period.' minutes'));
      $period = new DatePeriod($start, $interval, $end);
      foreach ($period as $time)
      {
        //if not lunch time
        if ( !in_array($time->format('H:i'), $lunch) )
        {
          $times[] = $time->format('H:i');
        }
      }
      return $times;
    }

    protected function appointment_dates($weekend, $rest_day, $holiday)
    {
      foreach ($this->adates as $value)
      {
        list( $name_of_day, $data ) = explode('&nbsp;', $value);
        if (  in_array($data, $rest_day, true)
              or in_array($data, $holiday, true))
        {
          $value = $name_of_day.'&nbsp;'.$data.'&nbsp;disabled';
        }
        $r = explode('&nbsp;', $value);
        if (array_key_exists($name_of_day, $weekend) && !isset($r[2]) && empty($weekend[$name_of_day]))
        {
          $value = $name_of_day.'&nbsp;'.$data.'&nbsp;disabled';
        }
        $app_days[] = $value;
      }
      foreach ($app_days as $key => $value)
      {
        $arr = explode('&nbsp;', $value);
        if ( !isset($arr[2]) ) //if not isset third element that contains "disabled"
        {
          $app_days[$key] = $arr[0].'&nbsp;'.$arr[1].'&nbsp;checked';//checked first work day
          break;//and exit the loop
        }
      }
      return $app_days;
    }

    protected function appointment_times($weekend, $rest_day, $holiday, $exist_app_date_time_arr, $tz)
    {
      foreach ($this->appointment_dates as $value)
      {
        $arr = explode('&nbsp;', $value);//$arr[0] - weekday, $arr[1] - date YYYY-mm-dd, #arr[2] - if isset = disabled
        //проверка на выходной
        $key_rest = array_search($arr[1], $rest_day);
        $key_weekend = array_key_exists($arr[0], $weekend);
        $key_holiday = array_search($arr[1], $holiday);
        if ( (isset($rest_day[$key_rest]) && $arr[1] === $rest_day[$key_rest])
              or ($key_weekend && empty($weekend[$arr[0]]))
              or (isset($holiday[$key_holiday]) && $arr[1] === $holiday[$key_holiday]) )
        {
          foreach ($this->atimes as $val)
          {
            $times[] = $val.'&nbsp;disabled';
          }
        }
        elseif ($key_weekend && !empty($weekend[$arr[0]]))
        {
          $r = 0;
          foreach ($this->atimes as $key => $val)
          {
            if ($val === $weekend[$arr[0]] )
            {
              $r++;
              $times[] = $val.'&nbsp;disabled';
            }
            elseif ($r > 0)
            {
              $times[] = $val.'&nbsp;disabled';
            }
            else
            {
              $times[] = $val;
            }
          }
        }
        else
        {
          foreach ($this->atimes as $val)
          {
            //ПРОВЕРКА НА СЕГОДНЯ И ОТКЛЮЧЕНИЕ ПРОШЛЫХ ВРЕМЕН, чтобы не было записей на прошедшее время
            $nowtime = new DateTimeImmutable('+1 hours', new DateTimeZone($tz));
            $freetime = DateTimeImmutable::createFromFormat('H:i', $val);
            $nowdate = new DateTimeImmutable('now');
            $free = ( $nowdate->format('Y-m-d') === $arr[1] && $freetime < $nowtime ) ? false : true;
            if ($free)
            {
                //if appointment array is not empty
                if ( $exist_app_date_time_arr )
                {
                  if (!empty($exist_app_date_time_arr[$arr[1]]))
                  {
                    $key_time = array_search($val, $exist_app_date_time_arr[$arr[1]]);
                    $times[] = ( $val === $exist_app_date_time_arr[$arr[1]][$key_time] ) ? $val.'&nbsp;disabled' : $val;
                  }
                  else //если для данной даты записей нет, просто время
                  {
                    $times[] = $val;
                  }
                }
                else //если записей нет, просто время
                {
                  $times[] = $val;
                }
            }
            else
            {
              $times[] = $val.'&nbsp;disabled';
            }
          }
        }
        $restimes[$arr[1]] = $times;
        $times = array();
      }
      return $restimes;
    }

    protected function html_view($view_date_format, $view_time_format)
    {
      $view = '<div class="master_datetime" id="master_datetime">
                <div class="master_dates">';
      foreach ($this->appointment_dates as $date)
      {
        //разберем на части $date
        //$arr[0] - weekday, $arr[1] - date, $arr[2] - if isset: disabled or checked
        $arr = explode('&nbsp;', $date);
        $dis = (isset($arr[2])) ? $arr[2] : '';
        //list( $year,$month,$day) = explode('-', $arr[1]);
        $view_date = DateTimeImmutable::createFromFormat('Y-m-d', $arr[1]);
        $view .= '<div class="master_date">
                    <input type="radio" class="dat" id="'.$arr[1].'d" name="date" value="'.$arr[1].'" ' . $dis . ' required />
                    <label for="'.$arr[1].'d">'.$arr[0].'<br />'.$view_date->format($view_date_format).'</label>
                  </div>';
      }
      $view .= '</div> ';

      foreach ($this->appointment_times as $key => $times_of_date)
      {
        $view .= '<div class="master_times" style="display:none;" id="t' .  $key . '"> ';
        foreach ($times_of_date as $time)
        {
          $ar = explode('&nbsp;', $time);
          $t = str_replace(":", "", $ar[0]);
          $dis = (isset($ar[1])) ? $ar[1] : '';
          $view_time = DateTimeImmutable::createFromFormat('Y-m-d H:i', $key.' '.$ar[0]);
          $view .= '<div class="master_time ">
                      <input type="radio" id="' .  $key .  $t . '" name="time" value="' .  $ar[0] . '" '.$dis.' required />
                      <label for="' .  $key . $t . '">' . $view_time->format($view_time_format) . '</label>
                    </div>';
        }
        $view .= '</div> ';
      }
      return $view;
    }

    public static function add_css_js($path_to_class_dir){
      $path_to_class_dir .= (substr($path_to_class_dir, -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR);
      if (is_file($path_to_class_dir.'ppntmt/js/head.php'))
      {
        include($path_to_class_dir.'ppntmt/js/head.php');
      }
      else
      {
        echo "ERROR!<br />Set the path to the directory where the directory 'ppntmt' is located<br />
              eg first/second/ppntmt -> first/second
              Ошибка!<br />Введите путь к каталогу внутри которого находится каталог 'ppntmt'<br />
              например первый/второй/ppntmt -> первый/второй";
      }

    }
}

?>
