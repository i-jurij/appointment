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
//$lehgth_cal = 8; количество отображаемых дней для записи
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
