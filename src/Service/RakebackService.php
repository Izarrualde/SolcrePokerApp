<?php
namespace Solcre\lmsuy\Service;

use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\Exception\PathIsNotDirException;

class RakebackService
{
    protected $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    public function fetchAll()
    {
        if (!is_dir($this->path)) {
            throw new PathIsNotDirException();
        }

        $param = $this->path.'/*'; // var_dump($param);

        $rakebackAlgorithms = glob($param);
        $algorithmsNames    = [];

        if (is_array($rakebackAlgorithms)) {
            foreach ($rakebackAlgorithms as $alg) {
                $pathParts         = pathinfo($alg);
                $algorithmsNames[] = $pathParts['filename'];
            }
        }

        return $algorithmsNames;
    }
}
