<?php
/**
* вывод дат в массив
* $endtime - время сегодня, после которого сегодняшняя дата не выводится
* $lehgth_cal - количество выводимых дней, цифра
* return $date - массив значений (день недели две буквы&nbsp;дата в формате ГГ-мм-дд)
*/
function appointment_days($endtime = "17:00", $lehgth_cal = 8){
  $now = new DateTimeImmutable();
  $endnow = new DateTimeImmutable($endtime);
  if ( $endnow >= $now )
  {
    $startDate = new DateTimeImmutable();
    $endDate = new DateTimeImmutable('+'.$lehgth_cal.' day');
    $period = new DatePeriod($startDate, new \DateInterval('P1D'), $endDate->modify('+1 day'));
    foreach ($period as $date2) {
      $engdayweek = $date2->format('D');
      $rudayweek = en_dayweek_to_rus($engdayweek);
      $date[] = $rudayweek . "&nbsp;". $date2->format('Y-m-d');
      //echo $rudayweek . "<br>". $date2->format('d') . "<br>\n";
    }
  }
  elseif ($now >= $endnow)
  {
    $lc = $lehgth_cal+1;
    $startDate = new DateTimeImmutable('+1 day');
    $endDate = new DateTimeImmutable('+'.$lc.' day');
    $period = new DatePeriod($startDate, new \DateInterval('P1D'), $endDate->modify('+1 day'));
    foreach ($period as $date2) {
      $engdayweek = $date2->format('D');
      $rudayweek = en_dayweek_to_rus($engdayweek);
      $date[] = $rudayweek . "&nbsp;". $date2->format('Y-m-d');
      //echo $rudayweek . "<br>". $date2->format('d') . "<br>\n";
    }
  }
  return $date;
}

/**
 * Check if a table exists in the current database.
 *
 * param PDO $pdo PDO instance connected to a database.
 * param string $table Table to search for.
 * return bool TRUE if table exists, FALSE if no table found.
 */
function tableExists($pdo, $table) {
    // Try a select statement against the table
    // Run it in try-catch in case PDO is in ERRMODE_EXCEPTION.
    try {
        $result = $pdo->query("SELECT 1 FROM {$table} LIMIT 1");
    } catch (Exception $e) {
        // We got an exception (table not found)
        return FALSE;
    }
    // Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
    return $result !== FALSE;
}

      function mb_ucfirst($text) {
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
      }

    function translit_to_lat($textcyr) {
      $cyr = ['Ц','ц', 'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п', 'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я', 'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П', 'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
      ];
      $lat = ['C','c', 'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p', 'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya', 'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P', 'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
      ];
      $textlat = str_replace($cyr, $lat, $textcyr);
      return $textlat;
    }

    function translit_to_cyr($textlat) {
      $cyr = ['Ц','ц', 'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п', 'р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я', 'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П', 'Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я'
      ];
      $lat = ['C','c', 'a','b','v','g','d','e','io','zh','z','i','y','k','l','m','n','o','p', 'r','s','t','u','f','h','ts','ch','sh','sht','a','i','y','e','yu','ya', 'A','B','V','G','D','E','Io','Zh','Z','I','Y','K','L','M','N','O','P', 'R','S','T','U','F','H','Ts','Ch','Sh','Sht','A','I','Y','e','Yu','Ya'
      ];
      $textcyr = str_replace($lat, $cyr, $textlat);
      return $textcyr;
    }

    function en_dayweek_to_rus($dayweek) {
      $cyr = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
      $lat = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      $dayrus = str_replace($lat, $cyr, $dayweek);
      return $dayrus;
    }

    function en_month_to_rus($month) {
      $lat = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'sept', 'oct', 'nov', 'dec' ];
      $cyr = ['Январь' , 'Февраль' , 'Март' , 'Апрель' , 'Май' , 'Июнь' , 'Июль' , 'Август' , 'Сентябрь' , 'Октябрь' , 'Ноябрь' , 'Декабрь',  'янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'сент', 'окт', 'ноя', 'дек' ];
      $ru_month = str_replace($lat, $cyr, $month);
      return $ru_month;
    }
?>
