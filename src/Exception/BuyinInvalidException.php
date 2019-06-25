<?php
namespace Solcre\lmsuy\Exception;

class BuyinInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("El buyin debe ser numérico.");
    }
}
