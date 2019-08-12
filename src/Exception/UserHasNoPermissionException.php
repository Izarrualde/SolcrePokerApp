<?php
namespace Solcre\lmsuy\Exception;

/**
 * @codeCoverageIgnore
 */
class UserHasNoPermissionException extends \Exception
{

    public function __construct()
    {
        parent::__construct("Ingreso denegado.");
    }
}
