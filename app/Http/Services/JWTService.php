<?php
  namespace App\Http\Services;

  use App\Interfaces\IJwtService;
  use MiladRahimi\Jwt\Generator;
  use MiladRahimi\Jwt\Parser;
  use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
  use MiladRahimi\Jwt\Exceptions\ValidationException;
  use MiladRahimi\Jwt\Validator\Rules\NotEmpty;
  use MiladRahimi\Jwt\Validator\Rule;
  use MiladRahimi\Jwt\Validator\DefaultValidator;
  use App\Http\Services\EventLogService;
  use App\Models\LogLevel;

  class JwtService implements IJwtService {

    public function __construct(EventLogService $logService)
    {
      $this->logService = $logService;
    }

    public function GenerateJwt($secretKey, $signer, $user)
    {
      $jwt = null;

      if($signer == "HS256")
      {
        $signer = new HS256($secretKey);
        $generator = new Generator($signer);

        $jwt = $generator->generate((array)$user);
      }

      return $jwt;
    }

    public function ValidateJwt($jwt, $signer, $secretKey)
    {
        $claims = null;

        //Set the validation rules. TODO: Move this functionality into its own factory class.
        $validator = new DefaultValidator();
        $validator->addRule('Email', new NotEmpty(true));
        $validator->addRule('Role', new NotEmpty(true));
        $validator->addRule('PrimaryPhone', new NotEmpty(true));
        $validator->addRule('FirstName',new NotEmpty(true));
        $validator->addRule('LastName', new NotEmpty(true));

        if($signer == "HS256")
        {
            $signer = new HS256($secretKey);

            // Parse the token
            $parser = new Parser($signer, $validator);
            $claims = $parser->parse($jwt);
        }
        else 
        {
          //Unknown/invalid signer. 
          throw new Exception("Invalid signer ".$signer);
        }

        return $claims;
    }
  }
?>