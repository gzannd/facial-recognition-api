<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Interfaces\IPushNotificationService;
use App\Models\User;
use App\Services\FCMService;
use App\Http\Controllers\Controller;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use App\Models\SystemStatusModel;

class PushNotificationsController extends Controller
{
  public function __construct(
    IPushNotificationService $pushNotificationService,
    EventLogService $eventLogService)
  {
      $this->eventLogService = $eventLogService;
      $this->pushNotificationService = $pushNotificationService;
  }


  public function updateToken(Request $request){
      try{
          $request->user()->update(['fcm_token'=>$request->token]);
          return response()->json([
              'success'=>true
          ]);
      }
      catch(\Exception $e)
      {
          report($e);

          return response()->json(['success'=>false],500);
      }
  }


  private function exceptionResponse($request, $message, $exception)
  {
    $this->eventLogService->LogApplicationEvent(LogLevel::Error, $message, $exception);

    return response()->json(['success' => false,
                             'message'=>$message], 500);
  }

  private function badRequestResponse($request, $message)
  {
    $this->eventLogService->LogApplicationEvent(LogLevel::Error, $message, $request);

    return response()->json(['success' => false,
                             'message'=>$message], 400);
  }

  public function sendPushNotification(Request $request)
  {
    $this->eventLogService->LogApplicationEvent(LogLevel::Info, "Processing Push Notification request", $request);

    $message = $request->input('message');
    $title = $request->input('title');
    $userIds = [];

    if(strlen($message) == 0 || strlen($title) == 0)
    {
      $message = "Message and Title must be defined.";
      $this->eventLogService->LogApplicationEvent(LogLevel::Error, $message, $request);

      return $this->badRequestResponse($message, $request);
    }

    try
    {
      //The list of userIds must be a list of integers, encoded as a comma separated string.
      $userIds = explode(",", $request->input('userIds'));
      foreach ($userIds as &$i) $i = (int) $i; //Convert the string values to integers.
    }
    catch(\Exception $e)
    {
      return $this->exceptionResponse($request, $message, $e);
    }

    if(count($userIds) > 0)
    {
      try
      {
        $result = $this->pushNotificationService->sendPushNotification($title, $message, $userIds);

        if($result->success == true)
        {
          return response()->json(['success' => true], 200);
        }
        else
        {
          //Something went wrong. Log the issue and return a 500.
          return $this->exceptionResponse($request, "An error occurred while processing the Push Notification request.", "");
        }
      }
      catch(\Exception $e)
      {
        return $this->exceptionResponse("An error occurred while processing the Push Notification request.", $request, $e);
      }
    }
    else
    {
      //No IDs were found, so no messages were sent.
      return response()->json(['success' => true,
                                'message' => "No valid user IDs were found. Notification not sent."], 200);
    }

  }

    private function sendNotificationToUser($id)
    {
       // get a user to get the fcm_token that already sent.
       $user = User::findOrFail($id);

       if($user)
       {
         try
         {
            //$result = Larafirebase::withTitle('Test Title')->withBody('Test body')->sendNotification([$user->fcm_token]);
            //echo($result);
            return Larafirebase::withTitle('Test Title')
            ->withBody('Test body')
            ->withImage('https://firebase.google.com/images/social.png')
            ->withIcon('https://seeklogo.com/images/F/firebase-logo-402F407EE0-seeklogo.com.png')
            ->withSound('default')
            ->withClickAction('https://www.google.com')
            ->withPriority('high')
            ->withAdditionalData([
                'color' => '#rrggbb',
                'badge' => 0,
            ])
            ->sendNotification($user->fcm_token);
//           return Larafirebase::fromArray(['title' => 'Test Title', 'body' => 'Test body'])->sendNotification([$user->fcm_token]);
         }
         catch(\Exception $e)
         {
           echo($e);
           report($e);
           return response()->json([
               'success'=>false
           ],500);
         }
       }
       else
       {
         //User wasn't found, return a 404.
         return response()->json(['success'=>false], 404);
       }

      return response()->json(['success'=>true], 200);
    }
}
