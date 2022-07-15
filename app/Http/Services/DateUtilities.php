<?php
namespace App\Http\Services;

class DateUtilities
{
  public static function convertDateTime($dateTime)
  {
    try
    {
      return date( 'Y-m-d H:i:s', strtotime(str_replace('-', '/', $dateTime)));
    }
    catch(Exception)
    {
      return null;
    }
  }
}
 ?>
