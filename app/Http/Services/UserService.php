<?php

namespace App\Http\Services;
use App\Models\User;
use App\Models\UserCreationError;
use App\Interfaces\IUserService;
use App\Interfaces\IJwtService;
use App\Interfaces\IPasswordService;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use App\Models\UserClaim;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserService implements IUserService
{
    public function __construct(IJwtService $jwtService, IPasswordService $passwordService, EventLogService $logService)
    {
        $this->logService = $logService;
        $this->jwtService = $jwtService;
        $this->passwordService = $passwordService;
    }

    const DEFAULT_CLAIMS = ["Email", "exp", "aud", "FirstName", "iat", "iss", "LastName", "PrimaryPhone", "sub"];


    public function GetUserCount() 
    {
        return User::count();
    }

    public function GetUsers()
    {
        return User::all();
    }

    //Given a JWT, validate it and, if valid, create a new user. 
    //The user info supplied in the JWT must conform to the user requirements specified by the system. 
    //If this is the first user created in the system then pass 0 into $createUserId; the resulting user will 
    //be given the ROOT_USER claim. Otherwise, this function will check to see if the user ID has the 
    //CREATE_USER claim before creating the user.  

    //If the creation process fails, the method returns a result containing the reason for the failure. 
    public function CreateUserFromJwt($externalJwt, $password, $createUserId)
    {
        $this->logService->LogApplicationEvent(LogLevel::Info, "Validate claims from external JWT", $externalJwt);

        if($externalJwt != null)
        {
            //The signer and secret key are stored in .env. 
            $signer = $_ENV["JWT_ALGO"];
            $secretKey = $_ENV["JWT_EXTERNAL_SECRET"];

            try 
            {
                $claims = $this->jwtService->ValidateJwt($externalJwt, $signer, $secretKey);
            }
            catch(Exception $exception)
            {
                $this->logService->LogApplicationEvent(LogLevel::Error, "Error occurred while attempting to create user from JWT", $exception);
                
                throw $exception;
            }

            if($claims != null)
            {
                //New users must specify a password
                if($password != null)
                {
                    if($this->passwordService->PasswordMeetsSecurityRequirements($password) == true)
                    {
                        $this->logService->LogApplicationEvent(LogLevel::Info, "JWT validated. Attempting to create user.");
                        
                        //If this is the first user, give the user the ROOT_USER role.  
                        $count = $this->getUserCount();

                        if($count == 0)
                        {
                            $claims['Role'] = "ROOT_USER";
                        }

                        //Attempt to create a user from the JWT. 
                        $result = $this->createUserFromClaims($claims, $password);

                        return $result;
                    }
                    else 
                    {
                        //Password doesn't meet security requirements.
                        $errorMessage = "Password does not meet security requirements.";

                        $this->logService->LogApplicationEvent(LogLevel::Error, $errorMessage);
                        
                        return new UserCreationError($errorMessage, 100);
                    }
                }
                else 
                {
                    //Password is required.
                    $errorMessage = "Password is required.";
                    $this->logService->LogApplicationEvent(LogLevel::Error, $errorMessage);
                    
                    return new UserCreationError($errorMessage, 101);
                }
            }
            else
            {
                //Claims are missing from the JWT.
                $errorMessage = "Unable to retrieve claims from the JWT.";
                $this->logService->LogApplicationEvent(LogLevel::Error, $errorMessage);
                
                return new UserCreationError($errorMessage, 200);
            }
        }
        else 
        {
            //JWT was not supplied in the request. 
            $errorMessage = "JWT was not supplied.";
            $this->logService->LogApplicationEvent(LogLevel::Error, $errorMessage);

            return new UserCreationError($errorMessage, 201); 
        }        
    }

    //Deletes all claims for the specified user. 
    public function ClearUserClaims($userId)
    {
        UserClaim::where('userId', $userId)->delete();
    }

    public function RemoveUserClaims($userId, $claimNames)
    {
        foreach($claimNames as $claimName)
        {
            UserClaim::where('userId', $userId )
            ->where('claim', $claimName)
            ->delete();
        }
    }

    public function UpdateUser($user)
    {
        $existingUser = User::where('id', $user->id).first();

        if($existingUser != null)
        {
            $existingUser->name = $user->name;
            $existingUser->firstName = $user->firstName;
            $existingUser->lastName = $user->lastName;
            $existingUser->primaryPhone = $user->primaryPhone;

            $existingUser->save();

            return true;
        }

        return false;
    }

    //Sets claims for the specified user. If a claim doesn't exist then it will be created. 
    //If the claim exists then it will be modified. 
    //TODO: Set up a list of claim names to validate against.
    public function SetUserClaims($userId, $claims)
    {
        //Retrieve all of the claims for the user. 
        $userClaims = UserClaim::where('userId', $userId);

        //Map the claims to an associative array keyed by claim name.
        $mappedClaims = array_column($userClaims, 'claim');
        
        //Update any existing claims, and insert any new ones.
        foreach($claims as $userClaim)
        {
            $index = array_seach($userClaim->claim, $mappedClaims);
            if($index == false)
            {
                $claim = $mappedClaims[$index];

                $claim->valid_begin = $userClaim->valid_begin;
                $claim->valid_end = $userClaim->valid_end;
                $claim->data = $userClaim->data;
                $claim->save();
            }
            else 
            {
                //Insert the claim.
                $userClaim->userId = $userId;
                UserClaim::create([
                    'userId' => $userId, 
                    'claim' => $userClaim->claim, 
                    'data' => $userClaim->data,
                    'valid_begin' => $userClaim->valid_begin, 
                    'valid_end' => $userClaim->valid->end]);
            }
        }
    }

    public function GetUserClaims($userId)
    {
        return UserClaim::where('userId', $userId);
    }

    public function GetUserById($userId)
    {
        $user = Users::where('id', $userId)->first();

        if($user != null)
        {
            $claims = UserClaims::where('userId', $userId);
            $user->claims = $claims;
        }

        return $user;
    }
    public function CreateUserFromClaims($claims, $password)
    {
        $user = null;
        
        
        DB::beginTransaction();

        try {           
            $user = User::create([
                'name' => $claims['FirstName'].' '.$claims['LastName'],
                'email' => $claims['Email'],
                'firstName' => $claims['FirstName'],
                'lastName' => $claims['LastName'],
                'primaryPhone' => $claims['PrimaryPhone'],
                'password' => Hash::make($password),
            ]);

            if($user != null)
            {
                foreach($claims as $key => $claim)
                {
                    if(in_array($key, UserService::DEFAULT_CLAIMS) == false)
                    {
                        UserClaim::create([
                            'userId' => $user->id, 
                            'claim' => $key,
                            'data' => $claim
                        ]);
                    }
                }
            }   
        }
        catch(QueryException $ex)
        {
            DB::rollback();
            $this->logService->LogApplicationEvent(LogLevel::Error, "Database Error creating user from claims.", $ex);

            return null;
        }
        catch(Exception $ex)
        {
            DB::rollback();
            $this->logService->LogApplicationEvent(LogLevel::Error, "Error creating user from claims.", $ex);

            return null;
        }

        DB::commit();
        return User::find($user->id);
    }
}
?>