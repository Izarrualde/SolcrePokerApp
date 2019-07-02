<?php
namespace Solcre\lmsuy\Exception;

class UserHadActionException extends \Exception
{

    public function __construct()
    {
        parent::__construct("El usuario ha participado en alguna sesión.");
    }
}
