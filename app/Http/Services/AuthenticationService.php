<?php
namespace App\Http\Services;

use App\Models\User;
use App\Interfaces\IUserService;
use App\Interfaces\IJwtService;
use App\Interfaces\IPasswordService;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use App\Models\UserClaim;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class AuthenticationService
{
    public function CheckCanUpdateUser($authUser, $user)
    {
        //RULE: The root user's inactive/disabled flag cannot be set to false.
        if($user->role == "ROOT_USER" && ($user->inactive == true || $user->disabled == true))
        {
            return false;        
        }

        //RULE: The root user's info can only be updated by that user. 
        if($user->role == "ROOT_USER" && $authUser->id != $user->id)
        {
            return false;
        }

        //RULE: Disabled/inactive users cannot update any user. 
        if($authUser->inactive == true || $authUser->disabled == true)
        {
            return false;
        }

        //RULE: Non-root users without the UPDATE_USER claim cannot update any user.
        if($authUser->role != "ROOT_USER")
        {
            foreach($authUser->claims as $claim)
            {
                if($claim->claim == "UPDATE_USER") 
                {
                    return true;
                }
            }
        
            return false;
        }

        return true;
    }
}

?>