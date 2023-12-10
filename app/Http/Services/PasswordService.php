<?php

namespace App\Http\Services;

use App\Interfaces\IPasswordService;
use App\Http\Services\EventLogService;
use App\Models\LogLevel;

class PasswordService implements IPasswordService {

    public function __construct(EventLogService $logService, $minPasswordLength, $maxPasswordLength)
    {
        $this->minPasswordLength = $minPasswordLength;
        $this->maxPasswordLength = $maxPasswordLength;
        $this->logService = $logService;
    }
  
    private function LogPasswordCreationEvent()
    {
        $this->logService->LogApplicationEvent(LogLevel::Info, "Password service has generated a new password.");
    }

    private function LogPasswordCreationFailure($exception)
    {
        $this->logService->LogApplicationEvent(LogLevel::Error, "Password service failed to generate a new password.", $exception);
 
    }

    public function PasswordMeetsSecurityRequirements($password)
    {
        return $password != null
         && strlen($password) >= $this->minPasswordLength 
         && strlen($password) <= $this->maxPasswordLength;
    }

    public function GenerateBasicPassword($length) 
    {
        if ($length < $this->minPasswordLength || $length > $this->maxPasswordLength) {
            $ex = new InvalidArgumentException("Password length must be between 8 and 128 characters.");
            $this->LogPasswordCreationFailure($ex);
            throw $ex;
        }

        $securePassword = "";
    
        try 
        {
            // Generate a random salt using random_bytes
            $salt = bin2hex(random_bytes(32));
        
            // Use the password_hash function to hash the salted password
            $hashedPassword = password_hash($salt, PASSWORD_ARGON2ID);
        
            // Combine the salt and hashed password
            $securePassword = $salt . $hashedPassword;
        
            // Truncate the result to the specified length
            $securePassword = substr($securePassword, 0, $length);
        
            $this->LogPasswordCreationEvent();
        }
        catch(Exception $ex)
        {
            $this->LogPasswordCreationFailure($ex);
        }

        return $securePassword;
    }
}
?>