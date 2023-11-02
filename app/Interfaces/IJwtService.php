<?php
namespace App\Interfaces;

interface IJwtService
{
  public function GenerateJwt($secretKey, $signer, $user);
  public function ValidateJwt($jwt, $signer, $secretKey);
}

?>
