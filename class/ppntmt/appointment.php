<?php
//Класс для отображения времен для записи на прием к специалисту.
//Класс не требует входных параметров но они могут быть заданы, требования описаны ниже.
//Класс возвращает массив времен с готовой html разметкой для отображения в браузере.
//Даты отображаются в <input type="radio" id="DATEd" name="date" value="DATE" />,
//время - в <input type="radio" id="DATETIME" name="time" value="TIME" />.
//Для работы требует подключения файлов ppntmt/js/jsappointment.js и ppntmt/css/*,
//для чего можно в <head></head> поместить
/* <?php echo Ppntmt\Appointment::add_css_js('path_to_dir'); ?> */
//или добавить содержимое "ppntmt/js/head.php" другим способом.
//
//CONNECT
//<?php require_once('path_to_dir/ppntmt/appointment.php');
//you need to connect the css and js files for the class to work properly:
//add to your index.php (or file.php that contain html head) into tag HEAD next:
/* <?php echo Ppntmt\Appointment::add_css_js('path_to_dir'); ?> */
//or add contents of the file "ppntmt/js/head.php" into tag head in another way
//
//WORK
//class has no required input parameters, default presented below
//connect in PHP8 may be so:
//$var = new Ppntmt\Appointment(endtime : "18:00",
//                       lehgth_cal : 14,
//                       tz : "Europe/Moscow",
//                       period : 30,
//                       weekend : array("Вс", "Sun",),
//                       rest_day_time : array('1979-09-18' => [''],'2005-05-31' => ['17:00', '18:00']),
//                       holiday : array('1979-09-18','1979-09-18'),
//                       lunch : array("12:00", "12:30"),
//                       exist_app_date_time_arr : array('date' => array('times' => 'duration', ), ),
//                       view_date_format : 'd.m',
//                       view_time_format = 'H:i');
//
//
//PROPERTIES
//
//$endtime = "17:00"; время, после которого даты начинаются с завтрашней (те запись на сегодня уже недоступна)
//$lehgth_cal = 11; количество отображаемых дней для записи
//$tz = "Europe/Moscow"; часовой пояс
//$period = 60;  интервал времен для записи (09:00, 10:00, 11:00, ...),
//мб любой, преобразуется кратно 10,, то есть 7 мин -> 10 мин, 23 мин -> 30 мин и тп
//кроме промежутков > 10, но < 15 - преобразуется в 15 минутный промежуток
//
//$weekend = array('Сб' => '14:00', 'Sat' => '14:00','Вс' => '', "Sun" => '',)
// постоянно планируемые в организации выходные, ключ - название дня,
//значение - пустое, если целый день выходной,
//или время начала отдыха в 24часовом формате HH:mm
//
//$holiday =  array('1979-09-18','2005-05-31',) - праздничные дни хоть на 10 лет вперед
//
//$lunch = array('12:00', '40') - массив c двумя значениями: время начала HH:mm
//и длительность обеденного перерыва в минутах
//
//$worktime = array('09.00', '18:00'), рабочее время $worktime[0] - начало, $worktime[1] - конец
//обозначает начало времен для записи и конечное время
//
//
//////////////// DATA related to a specific MASTER:
//
//$rest_day_time = array('1979-09-18' => array(),'2005-05-31' => ['17:00', '18:00']) - запланированные выходные дни и часы мастера,
//получены из рабочего графика мастера, если массив значений пуст - выходной целый день.
//Значение равно началу времени отгула, длительность не указывается и будет равна $period,
//те, если период = 60 минут, а отсутствовать мастер будет 2 часа после 17:00
//запись такая $rest_day_time = array('дата YYYY-mm-dd' => ['17:00', '18:00'])
//
//$exist_app_date_time_arr - массив предыдущих записей к мастеру
//в формате array('date' => array('times' => 'duration')),
//где date в формате день недели две буквы WD и YYYY-mm-dd, eg "Пт 2022-12-02"
//а 'times' => 'duration' - ассоциативный массив времен записей в
//24часовом формате HH:mm в ключе и длительности услуги в минутах в значении,
//длительность можно не указывать (null or ''), тогда она считается равной $period
//eg ['2022-12-02' => array('12:00' => '30', '15:00' => ''), '2022-12-03' => array('13:00' => '20')]
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
//$this->appointment_dates - даты дней записи + disabled для всех выходных, праздников и тд
// eg "Пн 2022-12-08 disabled"
//
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

namespace Ppntmt;

class Appointment
{
  protected int $lehgth_cal;
  protected string $endtime;
  protected string $tz;
  protected int $period;
  protected array $weekend;
  protected array $rest_day_time;
  protected array $holiday;
  protected array $lunch;
  protected array $worktime;
  protected array $exist_app_date_time_arr;
  protected string $view_date_format;
  protected string $view_time_format;

  public function __construct($lehgth_cal = 14,
                              $endtime = "17:00",
                              $tz = "Europe/Moscow",
                              $weekend = array('Сб' => '14:00', 'Sat' => '14:00',
                                                'Вс' => '', "Sun" => '',),
                              $rest_day_time = array('2022-12-17' => array(), '2022-12-15' => ['16:00', '17:00', '18:00'], ),
                              $holiday =  array('1979-09-18', '2005-05-31',),
                              $period = 60,
                              $worktime = array('09:00', '19:00'),
                              $lunch = array("12:00", 40),
                              //$exist_app_date_time_arr = ['2022-12-14' => array('11:00' => '60', '13:00' => 30, '14:30' => '50'), '2022-12-15' => array('13:00' => '30', '15:00' => 40), '2022-12-16' => ['09:00' => '140']],
                              $exist_app_date_time_arr = ['2022-12-14' => array('11:00' => '', '13:00' => '', '14:30' => null),
                                                          '2022-12-15' => array('13:00' => '30', '13:30' => '30', '15:00' => 40),
                                                          '2022-12-16' => ['09:00' => '140'],
                                                          '2022-12-19' => ['09:00' => '40', '09:40' => '30', '10:10' => '60'], ],
                              $view_date_format = 'd.m',
                              $view_time_format = 'H:i')
  {
    $this->adates = $this->all_dates($endtime, $lehgth_cal, $tz);
    $this->appointment_dates = $this->marked_dates($weekend, $rest_day_time, $holiday);
    $this->round_period = $this->round_period($period);
    $this->times = $this->times($worktime);
    $this->weekend = $this->weekend_times($weekend);
    $this->rest_times = $this->rest_times($rest_day_time, $worktime);
    $this->appointment_times = $this->appointment_times($exist_app_date_time_arr);
    $this->result = $this->result($lunch);
    $this->html = $this->html($view_date_format, $view_time_format);
  }

  public static function add_css_js($path_to_class_dir)
  {
    $path_to_class_dir .= (substr($path_to_class_dir, -1) == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR);
    if (is_file($path_to_class_dir.'ppntmt/js/head.php'))
    {
      include($path_to_class_dir.'ppntmt/js/head.php');
    }
    else
    {
      echo 'ERROR!<br />Set the path to the directory where the directory "ppntmt" is located in<br />
            in '. htmlentities('<head>
                <?php echo Appointment::add_css_js("path_to_dir"); ?>
               </head>').'<br />
            eg if "first/second/ppntmt", path_to_dir = "first/second"<br />
            Ошибка!<br />Введите путь к каталогу внутри которого находится каталог "ppntmt"<br />
            в '. htmlentities('<head>
                <?php echo Appointment::add_css_js("path_to_dir"); ?>
               </head>').'<br />
            например, если "первый/второй/ppntmt", path_to_dir = "первый/второй"';
    }
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
    $startDate = new \DateTimeImmutable();
    $endDate = new \DateTimeImmutable('+'.$lehgth_cal.' day');
    $period = new \DatePeriod($startDate, new \DateInterval('P1D'), $endDate->modify('+1 day'));
    foreach ($period as $date2)
    {
      $engdayweek = $date2->format('D');
      $rudayweek = $this->en_dayweek_to_rus($engdayweek);
      $date[] = $rudayweek . "&nbsp;". $date2->format('Y-m-d');
    }
    return $date;
  }

  public function all_dates($endtime, $lehgth_cal, $tz)
  {
    $now = new \DateTimeImmutable('now', new \DateTimeZone($tz));
    $endnow = new \DateTimeImmutable($endtime);
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

  public function marked_dates($weekend, $rest_day_time, $holiday)
  {
    foreach ($this->adates as $value)
    {
      list( $name_of_day, $data ) = explode('&nbsp;', $value);
      if (  in_array($data, $holiday, true))
      {
        $value = $name_of_day.'&nbsp;'.$data.'&nbsp;disabled';
      }
      $r = explode('&nbsp;', $value);
      if (array_key_exists($name_of_day, $weekend) && !isset($r[2]) && empty($weekend[$name_of_day]))
      {
        $value = $name_of_day.'&nbsp;'.$data.'&nbsp;disabled';
      }
      $z = explode('&nbsp;', $value);
      if (array_key_exists($data, $rest_day_time) && !isset($r[2]) && !isset($z[2]) && empty($rest_day_time[$data]))
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

  protected function round_period($period)
  {
    $round_period = ($period > 10 && $period < 16) ? 15 : ceil($period / 10) * 10;
    return $round_period;
  }

  public function times($worktime)
  {
    $start = \DateTimeImmutable::createFromFormat('H:i', $worktime[0]);
    $end = \DateTimeImmutable::createFromFormat('H:i', $worktime[1]);
    $interval = new \DateInterval('PT'.$this->round_period.'M');
    $times_dtobj = new \DatePeriod($start, $interval, $end);
    foreach ($times_dtobj as $time)
    {
      $times[] = $time->format('H:i');
    }
    return $times;
  }

  public function weekend_times($weekend)
  {
    $timearr = $this->times;
    foreach ($this->appointment_dates as $data)
    {
      $arr = explode('&nbsp;', $data); //$arr[0] - weekday, $arr[1] - date, $arr[2] - disabled
      foreach ($timearr as $t)
      {
        $time = \DateTimeImmutable::createFromFormat('Y-m-d_H:i', $arr[1].'_'.$t);
        if ( !isset($arr[2]) || $arr[2] === 'checked' )
        {
            if (array_key_exists($arr[0], $weekend) && !empty($weekend[$arr[0]]) )
            {
              $weekend_time = \DateTimeImmutable::createFromFormat('Y-m-d_H:i', $arr[1].'_'.$weekend[$arr[0]]);
              if ($weekend_time <= $time)
              {
                $times[$arr[1]][] = $t.'&nbsp;disabled';
              }
              else
              {
                $times[$arr[1]][] = $t;
              }
            }
            else
            {
              $times[$arr[1]][] = $t;
            }
        }
      }
    }
    return $times;
  }

  public function rest_times($rest_day_time, $worktime)
  {
    $timearr = $this->weekend;
    //create array with all start-end rest hours
    foreach ($rest_day_time as $date => $times)
    {
      $res[$date] = array();
      if (isset($times) && !empty($times))
      {
        foreach ($times as $time)
        {
          $start = \DateTimeImmutable::createFromFormat('Y-m-d_H:i', $date.'_'.$time);
          $end = $start->add(new \DateInterval('PT'.$this->round_period.'M'));
          if (isset($pre_end) && $start == $pre_end )
          {
            if ($end < $worktime[1])
            {
              array_push($res[$date], $end);
            }
          }
          else
          {
            if ($end < $worktime[1])
            {
              array_push($res[$date], $start, $end);
            }
          }
          $pre_end = $end;
        }
      }
    }
    unset($date, $times, $time, $pre_end, $end, $start);

    //merge default datetime and start-end rest hours arrays
    foreach ($timearr as $date => $times )
    {
      if (array_key_exists($date, $rest_day_time) && isset($res[$date]) && !empty($res[$date]))
      {
        foreach ($res[$date] as $rest_time)
        {
          //допишем в массив времен времена записей, которых там еще нет
          if (!array_search($rest_time->format('H:i'), $times))
          {
            array_push($timearr[$date], $rest_time->format('H:i'));
          }
        }
      }
      //сортировка всех времен по возрастанию
      sort($timearr[$date], SORT_REGULAR);
    }
    unset($date, $times, $rest_time, $time, $time_pre, $arr, $dt);

    //проверим все времена и пометим все часы отдыха
    foreach ($timearr as $date => $times )
    {
      if (array_key_exists($date, $rest_day_time) && isset($rest_day_time[$date]) && !empty($rest_day_time[$date]))
      {
        foreach ($rest_day_time[$date] as $rest_time)
        {
          $start = \DateTimeImmutable::createFromFormat('Y-m-d_H:i', $date.'_'.$rest_time);
          $end = $start->add(new \DateInterval('PT'.$this->round_period.'M'));
          $start_end[$rest_time] = array('start' => $start, 'end' => $end);
        }
        unset($rest_time, $start, $end);

        foreach ($start_end as $rest_time)
        {
          foreach ($times as $key => $time)
          {
            $arr = explode('&nbsp;', $time);
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d_H:i', $date.'_'.$arr[0]);
            if ($dt >= $rest_time['start'] && $dt < $rest_time['end']  && !isset($arr[2]))
            {
              $timearr[$date][$key] = $time.'&nbsp;disabled';
            }
          }
        }
        $start_end = array();
      }
    }
    unset($date, $times, $rest_time, $t, $time, $time_pre, $arr, $dt);
    return $timearr;
  }

  public function appointment_times($exist_app_date_time_arr)
  {
    //default date and time array
    $dt = $this->rest_times;
    foreach ($dt as $date => $times)
    { //если для данной даты есть записи - создаем массив времен записей (начало, конец)
      if (array_key_exists($date, $exist_app_date_time_arr))
      {
        $start_end_array = array();
        foreach ($exist_app_date_time_arr[$date] as $serv_time => $serv_len)
        {
          $serv_start = \DateTimeImmutable::createFromFormat('Y-m-d_H:i', $date.'_'.$serv_time);
          if (!empty($serv_len))
          {
            //if length of service > 5 then minutes, else hours
            //если длительность услуги меньше 5  - значит обозначено в часах
            $r = ( $serv_len > 5 ) ? 'M' : 'H';
            $serv = ( $serv_len > 5 ) ? $serv_len : ceil(($serv_len * 60) / 10) * 10;
            $serv_end = $serv_start->add(new \DateInterval('PT'.$serv.$r));
          }
          else
          {
            $serv_end = $serv_start->add(new \DateInterval('PT'.$this->round_period.'M'));
          }

          if (isset($pre_serv_end) && $serv_start == $pre_serv_end)
          {
            array_push($start_end_array, $serv_end);
          }
          else
          {
            array_push($start_end_array, $serv_start, $serv_end);
          }
          $pre_serv_end = $serv_end;
        }

        //объединим массив времен для записей и массив с началом и концом каждой записи
        foreach ($start_end_array as $val)
        {
          //допишем в массив времен времена записей, которых там еще нет
          if (!in_array($val->format('H:i'), $times))
          {
            array_push($dt[$date], $val->format('H:i'));
          }
        }

        //сортировка всех времен по возрастанию
        sort($dt[$date], SORT_REGULAR);
      }
    }

    //просмотрим все времена для каждой даты и пометим времена услуг
    foreach ($dt as $date => $times)
    {
      $start_end = array();
      if (array_key_exists($date, $exist_app_date_time_arr))
      { //и если для даты есть записи - создадим массив времен с ключами start\end и значениями начала и конца
        foreach ($exist_app_date_time_arr[$date] as $serv_time => $serv_len)
        {
          $serv_start = \DateTimeImmutable::createFromFormat('Y-m-d_H:i', $date.'_'.$serv_time);
          if (!empty($serv_len))
          {
            //if length of service > 5 then minutes, else hours
            //если длительность услуги меньше 5  - значит обозначено в часах
            $r = ( $serv_len > 5 ) ? 'M' : 'H';
            $serv = ( $serv_len > 5 ) ? $serv_len : ceil(($serv_len * 60) / 10) * 10;
            $serv_end = $serv_start->add(new \DateInterval('PT'.$serv.$r));
          }
          else
          {
            $serv_end = $serv_start->add(new \DateInterval('PT'.$this->round_period.$r));
          }
          $start_end[$serv_time] = array('start' => $serv_start, 'end' => $serv_end);
        }

        //пометим времена услуг
        foreach ($start_end as $sst => $ttime)
        {
          foreach ($times as $key => $time)
          {
            $arr = explode('&nbsp;', $time);
            $ddt = \DateTimeImmutable::createFromFormat('Y-m-d_H:i', $date.'_'.$arr[0]);
            if ( $ttime['start'] <= $ddt && $ttime['end'] > $ddt && !isset($arr[1]))
            {
              $dt[$date][$key] = $time.'&nbsp;disabled';
            }
          }
        }
      }
    }
    return $dt;
  }

  public function result($lunch)
  {
    $lunch_start = \DateTimeImmutable::createFromFormat('H:i', $lunch[0]);
    $lunch_end = $lunch_start->add(new \DateInterval('PT'.$lunch[1].'M'));
    $app_dt = $this->appointment_times;
    foreach ($app_dt as $date => $timess)
    {
      foreach ($timess as $key => $time)
      {
        $arr = explode('&nbsp;', $time);
        $dt = \DateTimeImmutable::createFromFormat('H:i', $arr[0]);
        if ($lunch_start == $dt && !isset($arr[1]))
        {
          $app_dt[$date][$key] = $time.'&nbsp;disabled';
        }
        elseif (isset($r) && $lunch_start > $r && $lunch_start < $dt)
        {
          array_splice($app_dt[$date], $key, 0, $lunch_start->format('H:i'));
        }
        if ($lunch_end < $dt && $lunch_end > $r && $dt != $lunch_start)
        {
          array_splice($app_dt[$date], $key, 0, $lunch_end->format('H:i'));
        }
        $r = $dt;
      }
    }
    return $app_dt;
  }

  public function html($view_date_format, $view_time_format)
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
      $view_date = \DateTimeImmutable::createFromFormat('Y-m-d', $arr[1]);
      $view .= '<div class="master_date">
                  <input type="radio" class="dat" id="'.$arr[1].'d" name="date" value="'.$arr[1].'" ' . $dis . ' required />
                  <label for="'.$arr[1].'d">'.$arr[0].'<br />'.$view_date->format($view_date_format).'</label>
                </div>';
    }
    $view .= '</div> ';

    foreach ($this->result as $key => $times_of_date)
    {
      $view .= '<div class="master_times" style="display:none;" id="t' .  $key . '"> ';
      foreach ($times_of_date as $time)
      {
        $ar = explode('&nbsp;', $time);
        $t = str_replace(":", "", $ar[0]);
        $dis = (isset($ar[1])) ? $ar[1] : '';
        $view_time = \DateTimeImmutable::createFromFormat('Y-m-d H:i', $key.' '.$ar[0]);
        $view .= '<div class="master_time ">
                    <input type="radio" id="' .  $key .  $t . '" name="time" value="' .  $ar[0] . '" '.$dis.' required />
                    <label for="' .  $key . $t . '">' . $view_time->format($view_time_format) . '</label>
                  </div>';
      }
      $view .= '</div> ';
    }
    return $view;
  }
//end class
}