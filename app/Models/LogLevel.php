<?php
namespace App\Models;

 enum LogLevel
{
  case Alert;
  case Critical;
  case Error;
  case Warning;
  case Info;
  case Debug;
}
?>
