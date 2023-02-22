# Класс для вывода времен записи на прием к специалисту
# A class for displaying the time for making an appointment with a specialist.  

Класс не требует входных параметров, но они могут быть установлены, требования ниже.  
Класс возвращает массив времен для записи или строку с готовой html разметкой.  
Даты в `<input type="radio" id="DATEd" name="date" value="DATE" />`,     
Времена в `<input type="radio" id="DATETIME" name="time" value="TIME" />`.     
 Требуется подключение css и js файлов: добавьте в тег HEAD содержимое файла
"ppntmt/head.html", например так `<?php include 'ppntmt/head.html'; ?>`   

The class does not require input parameters, but they can be set, the requirements are described below.  
The class returns an array of times with ready-made html markup for display in the browser.  
Dates are displayed in `<input type="radio" id="DATEd" name="date" value="DATE" />`,  
times are displayed in `<input type="radio" id="DATETIME" name="time" value="TIME" />`.  

You need to connect the css and js files for the class to work properly:  
add to your index.php (or file.php that contain html head) into tag HEAD  
contents of the file "ppntmt/js/head.php" eg `<?php include 'ppntmt/head.html'; ?>`   
 
## CONNECT  
`<?php require_once('path_to_dir/ppntmt/appointment.php'); ?>`   

## WORK  
Class has no required input parameters, default presented below    
`$var = new Ppntmt\Appointment();`  
`// if necessary, set values to properties`   
`$var->lehgth_cal = 14;`   
`$bmw->endtime = "17:00";` 
`$bmw->result(); // array of times`    
`print $bmw->html(); // string with html code`   

## PROPERTIES FOR SETTING BY USER 

`$endtime = "17:00";`  
Время, после которого даты начинаются с завтрашней (те запись на сегодня уже недоступна)  
The time after which the dates start from tomorrow (those records are no longer available for today)  

`$lehgth_cal = 8;`  
Количество отображаемых дней для записи  
The number of days displayed for an appointment  

`$tz = "Europe/Moscow";`  
Часовой пояс   
Timezone  

`$period = 60;`   
Интервал времен для записи (09:00, 10:00, 11:00, ...),  
мб любой, преобразуется кратно 10,, то есть 7 мин -> 10 мин, 23 мин -> 30 мин и тп  
кроме промежутков > 10, но < 15 - преобразуется в 15 минутный промежуток  
Time interval for an appointment, can be any, converted multiple of 10  
but if 10 < $period < 15 then = 15  

`$weekend = array('Сб' => '14:00', 'Sat' => '14:00','Вс' => '', "Sun" => '',)`  
Постоянно планируемые в организации выходные, ключ - название дня,  
значение - пустое, если целый день выходной,  
или время начала отдыха в 24часовом формате HH:mm  
Weekends that are constantly planned in the organization, the key is the name of the day,  
the value is empty if the whole day is off,  
or the start time of the rest in the 24-hour format HH:mm  

`$holiday =  array('1979-09-18','2005-05-31',)` - праздничные дни хоть на 10 лет вперед  

`$lunch = array('12:00', '40')`  
Массив c двумя значениями: время начала HH:mm и длительность обеденного перерыва в минутах  
An array with two values: the start time HH:mm and the duration of the lunch break in minutes  

`$worktime = array('09.00', '18:00')`  
Рабочее время $worktime[0] - начало, $worktime[1] - конец  
Working time $worktime[0] - start, $worktime[1] - end  


### DATA related to a specific MASTER:  

`$rest_day_time = array('1979-09-18' => array(),'2005-05-31' => ['17:00', '18:00'])`  

Запланированные выходные дни и часы мастера,  
получены из рабочего графика мастера, если массив значений пуст - выходной целый день.  
Значение равно началу времени отгула, длительность не указывается и будет равна $period,  
те, если период = 60 минут, а отсутствовать мастер будет 2 часа после 17:00  
запись такая `$rest_day_time = array('дата YYYY-mm-dd' => ['17:00', '18:00'])`  

The scheduled days off and the master's hours,  
are obtained from the master's work schedule, if the array of values is empty - the whole day off.  
The value is equal to the beginning of the time off, the duration is not specified and will be equal to $period,  
those if the period = 60 minutes, and the master will be absent 2 hours after 17:00  
the entry is `$rest_day_time = array('date YYYY-mm-dd' => ['17:00', '18:00'])`  

`$exist_app_date_time_arr`  

Массив предыдущих записей к мастеру  
в формате `array('date' => array('times' => 'duration'))`,  
где date в формате день недели две буквы WD и YYYY-mm-dd, eg "Пт 2022-12-02"  
а 'times' => 'duration' - ассоциативный массив времен записей в  
24часовом формате HH:mm в ключе и длительности услуги в минутах в значении,  
длительность можно не указывать (null or ''), тогда она считается равной $period  
eg `['2022-12-02' => array('12:00' => '30', '15:00' => ''), '2022-12-03' => array('13:00' => '20')]`  

Array of previous entries to the master  
in the array format `array('date' => array('time' => 'duration'))`,  
where the date in the day of the week format is two letters SHD and YYYY-mm-dd, for example "Fri 2022-12-02"  
a 'times' => 'duration' is an associative array of appointed times in  
24-hour HH format:mm in the key and the duration of the service in minutes in the value,  
the duration can be omitted (zero or ''), then it is considered equal to $period  
for example  `['2022-12-02' => array('12:00' => '30', '15:00' => "), '2022-12-03' => array('13:00' => '20')]`  


## FUNCTIONS  

`$this->adates()`  

Массив дат в формате день недели две буквы WD и YYYY-mm-dd, eg "Пт 2022-12-02"  
если нужны названия дней недели на английском - закомментируйте строку 
`$rudayweek = $this->en_dayweek_to_rus($engdayweek);` в функции `pre_dates($lehgth_cal)`  
если нужны названия дней недели на другом языке, замените русские сокращения на нужные  
в массиве `$cyr` функции `en_dayweek_to_rus($dayweek)`  

Array of dates in the format day of the week two letters WD and YYYY-mm-dd, eg "Fri 2022-12-02"  
if you need the names of the days of the week in English - comment the line  
`$ru day week = $this->en_dayweek_to_rus($engdayweek);` in the function `pre_dates($lehgth_cal)`  
if you need the names of the days of the week in another language,  
replace the Russian abbreviations with the necessary ones  
in the `$cyr` array of the `en_dayweek_to_rus($dayweek)` function  

`$this->marked_dates()`  

даты дней записи, где все выходные, праздники и тд помечены "disabled"  

The dates, where all weekends, holidays, etc. are marked "disabled"  
 eg "Пн 2022-12-08 disabled"  

`$this->html_view()`  

Строка c html кодом и данными для вывода на страницу:  

A string with html code and data for output to the page:  
``<div class="master_datetime" id="master_datetime">``  
  `<div class="master_dates">`    
    `<div class="master_date">`   
      `<input type="radio" class="dat" id="DATEd" name="date" value="DATE" />`    
      `<label for="DATEd">WEEKDAY<br />WEEKDAY+NUMBER_DAY_OF_MONTH</label>`   
    `</div>`    
  `</div>`    
  `<div class="master_times" style="display:none;" id="tDATE">`   
    `<div class="master_time ">`    
      `<input type="radio" id="DATETIME" name="time" value="TIME" required />`    
      `<label for="DATETIME">TIME</label>`    
    `</div>`    
  `</div>`    
`</div>`    
