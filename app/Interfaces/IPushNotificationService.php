<?php
namespace App\Interfaces;

interface IPushNotificationService
{
  public function sendPushNotification($title, $message, $userIds);
}
?>
