<?php
namespace Solcre\lmsuy\Controller;

// use Exception;
// use Doctrine\ORM\EntityManager;
// use Slim\Views\Twig;
// use \Solcre\lmsuy\View\TwigWrapperView;
// use \Solcre\lmsuy\View\JsonView;

class BaseController
{
    const STATUS_CODE_200 = 200;
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_500 = 500;

/*
    public function __construct(	)
    {

    }

        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);
        if (isset($datosBuyins)) {
            foreach ($datosBuyins as $buyinObject) {
                $buyins[] = $buyinObject->toArray();
            }
        }

        $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));

        if (is_array($datosComissions)) {
            foreach ($datosComissions as $comissionObject) {
                $comissions[] = $comissionObject->toArray();
            }            
        }


$name = 'datos'.$nombrecontroller
$name = $this->$nombrecontroller.'Service'->fetchall(array('session'=>$idSession));
if (is_array($name)) {
  foreach ($name as $nombrecontroller.'Object') {
    $nombrecontroller.'s'[] = $nombrecontroller.'Object'->toArray();
  }
}

*/

}
