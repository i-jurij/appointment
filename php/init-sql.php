<?php
try
{
  $pdo = new PDO("mysql:host=$databaseHost", $databaseUser, $databasePassword);
  // set the PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $databaseName = "`".str_replace("`","``",$databaseName)."`";
  if ($pdo->query("CREATE DATABASE IF NOT EXISTS $databaseName"))
  {
      //echo "$databaseName DB created or exist<br />";
  }
  if ($pdo->query("use $databaseName"))
  {
      //echo  "$databaseName DB connected successfully<br />";
  }
  //create db tables
  $sql ="CREATE table IF NOT EXISTS `masters`(
          ID INT AUTO_INCREMENT PRIMARY KEY,
          master_name VARCHAR( 30 ) default NULL,
          sec_name VARCHAR( 30 ) default NULL,
          master_fam VARCHAR( 30 ) default NULL,
          master_phone_number VARCHAR( 20 ) NOT NULL,
          spec VARCHAR( 50 ) default NULL,
          data_priema DATE NOT NULL,
          data_uvoln VARCHAR( 30 ) default NULL);" ;
  $pdo->exec($sql);

}
catch (\Exception $e)
{
  echo $e->getMessage() . '<br />';
  die();
}
