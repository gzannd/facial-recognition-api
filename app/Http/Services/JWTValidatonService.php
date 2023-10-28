<?php
  namespace App\Http\Services;

  use MiladRahimi\Jwt\Generator;
  use MiladRahimi\Jwt\Parser;
  use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;


  class JwtValidationService {

    public function ValidateJwt($jwt, $signer, $secretKey)
    {
        $claims = null;

        if($signer == "HS256")
        {
            $signer = new HS256($secretKey);

            // Parse the token
            $parser = new Parser($signer);
            $claims = $parser->parse($jwt);
        }

        return $claims;
    }
  }
?>