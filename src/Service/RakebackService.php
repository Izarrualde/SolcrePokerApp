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

      $rakebackAlgorithms      = glob($param);
      $rakebackAlgorithmsNames = [];

      if (is_array($rakebackAlgorithms)) {
          foreach ($rakebackAlgorithms as $alg) {
              $path_parts                = pathinfo($alg);
              $rakebackAlgorithmsNames[] = $path_parts['filename'];
          }
      }

        return $rakebackAlgorithmsNames;
    }
}
