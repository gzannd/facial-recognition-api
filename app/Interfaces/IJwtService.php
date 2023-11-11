<?php
namespace App\Interfaces;

interface IJwtService
{
  public function GenerateJwt($secretKey, $signer, $user);
  public function ValidateExternalJwt($jwt, $signer, $secretKey);
}

?>
