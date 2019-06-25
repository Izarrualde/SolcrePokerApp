<?php
namespace Solcre\lmsuy\Exception;

class ServiceTipInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("El Service Tip debe ser numérico.");
    }
}
