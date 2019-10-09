<?php
namespace Solcre\lmsuy\Exception;

/**
 * @codeCoverageIgnore
 */
class PathIsNotDirException extends \Exception
{

    public function __construct()
    {
        parent::__construct("La ruta dada no es un directorio");
    }
}
