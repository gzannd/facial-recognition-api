<?php
namespace App\Interfaces;

interface IUserService {
    public function CreateUserFromJwt($jwt, $createUserId);
    public function CreateUserFromClaims($claims, $password);
    public function GetUserCount();
}

?>