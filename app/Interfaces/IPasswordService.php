<?php

namespace App\Interfaces;

interface IPasswordService
{
  public function GenerateBasicPassword($length);
  public function PasswordMeetsSecurityRequirements($password);
}
?>