<?php

namespace App\Http\Services;
use App\Models\User;
use App\Interfaces\IUserService;
use App\Interfaces\IJwtService;
use App\Interfaces\IPasswordService;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;

class UserService implements IUserService
{
    public function __construct(IJwtService $jwtService, IPasswordService $passwordService, EventLogService $logService)
    {
        $this->logService = $logService;
        $this->jwtService = $jwtService;
        $this->passwordService = $passwordService;
    }

    public function GetUserCount() 
    {
        return User::count();
    }

    //Given a JWT, validate it and, if valid, create a new user. 
    //The user info supplied in the JWT must conform to the user requirements specified by the system. 
    //If this is the first user created in the system then pass 0 into $createUserId; the resulting user will 
    //be given the ROOT_USER claim. Otherwise, this function will check to see if the user ID has the 
    //CREATE_USER claim before creating the user.  
    public function CreateUserFromJwt($jwt, $createUserId)
    {
        $this->logService->LogApplicationEvent(LogLevel::Info, "Validate claims from external JWT", $jwt);

        if($jwt != null)
        {
            //The signer and secret key are stored in .env. 
            $signer = $_ENV["JWT_ALGO"];
            $secretKey = $_ENV["JWT_EXTERNAL_SECRET"];

            try 
            {
                $claims = $this->jwtService->ValidateJwt($jwt, $signer, $secretKey);
            }
            catch(Exception $exception)
            {
                $this->logService->LogApplicationEvent(LogLevel::Error, "Error occurred while attempting to create user from JWT", $exception);
                
                throw $exception;
            }

            if($claims != null)
            {
                $this->logService->LogApplicationEvent(LogLevel::Info, "JWT validated. Attempting to create user.");
                
                //If this is the first user, give the user the ROOT_USER role.  
                $count = $this->getUserCount();

                if($count == 0)
                {
                   $claims['Role'] = "ROOT_USER";
                }

                //Attempt to create a user from the JWT. 
                $result = $this->createUserFromClaims($claims, $this->passwordService->GenerateBasicPassword(16));

                return $result;
            }
            else 
            {
                $this->logService->LogApplicationEvent(LogLevel::Error, "Unable to retrieve claims from the JWT.");
                
                return null;
            }
        }
        else 
        {
            $this->logService->LogApplicationEvent(LogLevel::Error, "JWT was not supplied in request.");

            //JWT is missing.
            return response()->json([
                'status' => 'badrequest',
                'message' => 'JWT is required',
            ], 400);
        }        
    }

    public function CreateUserFromClaims($claims, $password)
    {
        try {
            $user = User::create([
                'name' => $claims['FirstName'].' '.$claims['LastName'],
                'email' => $claims['Email'],
                'firstName' => $claims['FirstName'],
                'lastName' => $claims['LastName'],
                'primaryPhone' => $claims['PrimaryPhone'],
                'role' => $claims['Role'],
                'password' => Hash::make($password),
            ]);

            $token = Auth::login($user);

            return ['user' => $user, 'token' => $token];
        }
        catch(QueryException $ex)
        {
            $this->logService->LogApplicationEvent(LogLevel::Error, "Database Error creating user from claims.", $ex);

            return null;
        }
        catch(Exception $ex)
        {
            $this->logService->LogApplicationEvent(LogLevel::Error, "Error creating user from claims.", $ex);

            return null;
        }
    }

}
?>