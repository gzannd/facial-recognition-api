<?php
namespace App\Interfaces;

interface IUserService {
    public function CreateUserFromJwt($jwt, $createUserId);
    public function CreateUserFromClaims($claims, $password);
    public function GetUserCount();
    public function GetUsers();
    public function ClearUserClaims($userId);
    public function RemoveUserClaims($userId, $claimNames);
    public function SetUserClaims($userId, $claims);
    public function GetUserClaims($userId);
    public function GetUserById($userId);
}

?>