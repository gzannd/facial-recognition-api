<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FCMService;
use App\Http\Controllers\Controller;

use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use App\Models\SystemStatusModel;
use Kutia\Larafirebase\Facades\Larafirebase;

class PushNotificationsController extends Controller
{

  public function __construct(
    EventLogService $eventLogService)
  {
      $this->eventLogService = $eventLogService;
  }


  public function updateToken(Request $request){
      try{
          $request->user()->update(['fcm_token'=>$request->token]);
          return response()->json([
              'success'=>true
          ]);
      }catch(\Exception $e){
          report($e);
          return response()->json([
              'success'=>false
          ],500);
      }
  }


    public function sendNotificationToUser($id)
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
