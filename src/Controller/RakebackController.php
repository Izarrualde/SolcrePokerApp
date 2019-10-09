<?php
namespace Solcre\lmsuy\Controller;

use Solcre\lmsuy\Service\RakebackService;
use Psr\Container\ContainerInterface;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\lmsuy\Exception\PathIsNotDirException;
use Exception;

class RakebackController extends BaseController
{
    protected $view;
    protected $rakebackService;

    public function __construct(View $view)
    {
        $this->view            = $view;
        $path                  = '../src/Rakeback';
        $this->rakebackService = new RakebackService($path);
    }

    public function listAll($request, $response, $args)
    {
        try {
            $rakebackAlhortihms = $this->rakebackService->fetchAll();
            $status  = parent::STATUS_CODE_200;
        } catch (PathIsNotDirException $e) {
            $status  = parent::STATUS_CODE_404;
        }

        $datosUI  = isset($rakebackAlhortihms) ? $rakebackAlhortihms : [];
        $response = $response->withStatus($status);

        return $this->view->render($request, $response, $datosUI);
    }
}
