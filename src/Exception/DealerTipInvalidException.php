<?php
namespace Solcre\lmsuy\Exception;

class DealerTipInvalidException extends \Exception
{

    public function __construct()
    {
        parent::__construct("El Dealer Tip debe ser numérico.");
    }
}
