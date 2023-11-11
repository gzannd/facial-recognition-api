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

    public function ValidateExternalJwt($jwt, $signer, $secretKey)
    {   
        $claims = [];

        try 
        {
            $claims = $this->jwtService->ValidateJwt($jwt, $signer, $secretKey);
        }
        catch(Exception $exception)
        {
           
        }

        return $claims;
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

    //Given a base64 encoded JWT, a signer, and a secret key, validates the JWT
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

            try 
            {
              $claims = $parser->parse($jwt);
            }
            catch(Exception $exception)
            {
              //Unable to validate or create claims. 
              $this->logService->LogApplicationEvent(LogLevel::Error, "An exception occurred while attempting to create or validate an external JWT: ".$exception->getMessage(), $jwt);
            }
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