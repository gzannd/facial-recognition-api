<?php

namespace App\Http\Services;

use App\Models\User;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use Kutia\Larafirebase\Facades\Larafirebase;
use App\Interfaces\IPushNotificationService;

class PushNotificationResult
{
    public $success = false;
    public $idsNotFound = [];
    public $messages = [];
}

class LarafirebasePushNotificationService implements IPushNotificationService
{
  const FCM_TOKEN_FIELD = "fcm_token";

  public function __construct(EventLogService $eventLogService)
  {
      $this->eventLogService = $eventLogService;
  }

  //Updates a user's FCM token. Requires a User object.
  public function updateToken(User $user, String $token)
  {
    $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Updating FCM push notification token for user ".$user->id." to ".$token);

    try
    {
      $user()->update([FCM_TOKEN_FIELD => $token]);
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "FCM token for user ".$user->id." successfully updated to ".$token);
      return true;
    }
    catch(\Exception $e)
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Exception, "Error while attempting to update FCM token for user ".$user->id,".", $e);
      return false;
    }
  }

  public function sendPushNotification($title, $message, $userIds)
  {
    $result = new PushNotificationResult();

    $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Retrieving user info for ".implode($userIds));

    //Retrieve all specified users from the database.
    $users = User::find($userIds, ['id', $this::FCM_TOKEN_FIELD]);
    $dbUserIds = [];
    $fcmTokens = [];

    foreach($users as $user)
    {
      $dbUserIds[] = $user["id"];
      $fcmTokens[] = $user["fcm_token"];
    }

    $fcmTokens[] = "foo";
    $missingIds = [];

    //Note any users that weren't retreived.
    try
    {
      foreach($userIds as $id)
      {
        if(array_search($id, $dbUserIds) == false) //Not the most efficient way to do this, but we're only searching a small number of items.
        {
          $missingIds[] = $id;
        }
      }
    }
    catch(\Exception $e)
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Error, "Error while attempting to retrieve user information for user IDs: ".implode($userIds), $e);
    }


    if(count($missingIds) > 0)
    {
      $this->eventLogService->LogApplicationEvent(LogLevel::Info, "One or more user IDs were not found: ".implode($missingIds));
      $result->idsNotFound = $missingIds;
    }

    if(count($fcmTokens) > 0)
    {
      try
      {
        $laraResult = Larafirebase::withTitle($title)
          ->withBody($message)
          ->sendNotification($fcmTokens);

        echo($laraResult);
        if($laraResult->success == count($fcmTokens))
        {
          //All messages were delivered.
          $result->success = true;
        }
        else
        {
          //Some messages failed. Log the failed message IDs.

        }

      }
      catch(\Exception $e)
      {
        report($e);
      }
    }

    return $result;
  }
}
?>
