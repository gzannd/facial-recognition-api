<?php
namespace App\Interfaces;
use App\Models\User;

interface IPushNotificationService
{
  public function updateToken(User $user, String $token);
  public function sendPushNotification($title, $message, $userIds);
}
?>
