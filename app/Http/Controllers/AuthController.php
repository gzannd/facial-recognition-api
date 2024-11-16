<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserCreationError;
use App\Interfaces\IJwtService;
use App\Interfaces\IPasswordService;
use App\Http\Services\EventLogService;
use App\Http\Services\UserService;
use App\Models\LogLevel;
use Illuminate\Database\QueryException;
use App\Http\Services\AuthenticationService;

class AuthController extends Controller
{

    public function __construct(UserService $userService, AuthenticationService $authService, EventLogService $logService, IJWTService $jwtService, IPasswordService $passwordService)
    {
        $this->middleware('auth:api', ['except' => ['login','register', 'createUserFromJwt']]);
        $this->logService = $logService;
        $this->jwtService = $jwtService;
        $this->passwordService = $passwordService;
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function listUsers(Request $request)
    {
        return response()->json($this->userService->GetUsers());
    }

    public function updateUserInfo(Request $request)
    {
        //The request should contain a User model. The route should contain a valid ID. 
        $input = $request->all();

        if($input != null)
        {
            $user = new User();
            $user->fill($input);    
            $user->id = $request->route("id");

            $authUser = Auth::user();

            if($this->authService->CheckCanUpdateUser($authUser, $input))
            {
                $result = $userService->UpdateUser($input);
            }
            else 
            {
                return response()->json([
                    'status' => 'unauthorized',
                    'message' => 'You are not allowed to perform this action.',
                ], 401);
            }
            
        }
        else 
        {
            return response()->json([
                'status' => 'badrequest',
                'message' => 'User info is requred.',
            ], 400);
        }
    }

    public function createUserFromJwt(Request $request)
    {
        $this->logService->LogApplicationEvent(LogLevel::Info, "Validate claims from external JWT", $request);

        //The JWT should be included in the request body as a base64 encoded string. 
        $jwt = $request->input('jwt');

        $this->logService->LogApplicationEvent(LogLevel::Info, "JWT", $jwt);

        //Password must be included.
        $password = $request->input('password');
        
        if($jwt == null)
        {
            $this->logService->LogApplicationEvent(LogLevel::Error, "JWT was not supplied in request.");

            //JWT is missing.
            return response()->json([
                'status' => 'badrequest',
                'message' => 'JWT is required',
            ], 400);
        }

        if($password == null)
        {
            $this->logService->LogApplicationEvent(LogLevel::Error, "Password was not supplied in request.");

            //JWT is missing.
            return response()->json([
                'status' => 'badrequest',
                'message' => 'Password is required',
            ], 400);
        }

        try 
        {
            $user = Auth::user();
            $result = $this->userService->CreateUserFromJwt($jwt, $password, $user);

            if($result instanceof User)
            {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User created successfully',
                    'user' => $result
                    ]);
            }
            else if($result instanceof UserCreationError) 
            {
                //Something went wrong when creating the user. 
                $message = "";

                if($result->errorCode < 200){
                    //Errors with code < 200 may safely be reported to the caller.
                    $message = $result->reason;
                }
                else {
                    $message = "Unable to create a new user.";
                }

                //The majority of the issues that may happen here are bad input so return a 400.
                return response()->json([
                    'status' => 'badrequest',
                    'message' => $message,
                    'errorCode' => $result->errorCode
                ], 400);
            }   
            else {
                //Something really went wrong here.
                return response()->json([
                    'status' => 'internalservererror',
                    'message' => 'Unable to create a new user.',
                ], 500);
            }
        }
        catch(Exception $ex)
        {
            return response()->json([
                'status' => 'internalservererror',
                'message' => 'An error occurred while attempting to create a new user.',
            ], 500);
        }
       
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
    }    

    public function logout()
    {
        try {
            Auth::logout();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out',
            ]);
        }
        catch(\Exception $ex)
        {
            $this->logService->LogApplicationEvent(LogLevel::Error, "An error occurred while attempting to log out user ID ". Auth::user()->id, $ex->message);

            return response()->json([
                'status' => 'internalservererror',
                'message' => 'unable to log out. Please try again later.'
            ]);
        }
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }



    public function register(Request $request){
        
        try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        } catch (\Illuminate\Validation\ValidationException $th) {
            return $th->validator->errors();
        }

    }

    public function change_password(Request $request)
    {
        $input = $request->all();
        $userid = Auth::guard('api')->user()->id;
        $rules = array(
            'old_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required|same:new_password',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            $arr = array("status" => 400, "message" => $validator->errors()->first(), "data" => array());
        } else {
            try {
                if ((Hash::check(request('old_password'), Auth::user()->password)) == false) {
                    $arr = array("status" => 400, "message" => "Check your old password.", "data" => array());
                } else if ((Hash::check(request('new_password'), Auth::user()->password)) == true) {
                    $arr = array("status" => 400, "message" => "Please enter a password which is not similar then current password.", "data" => array());
                } else {
                    User::where('id', $userid)->update(['password' => Hash::make($input['new_password'])]);
                    $arr = array("status" => 200, "message" => "Password updated successfully.", "data" => array());
                }
            } catch (\Exception $ex) {
                if (isset($ex->errorInfo[2])) {
                    $msg = $ex->errorInfo[2];
                } else {
                    $msg = $ex->getMessage();
                }
                $arr = array("status" => 400, "message" => $msg, "data" => array());
            }
        }
        return response()->json($arr);
    }
}
