<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\ComissionSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Entity\ComissionSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Psr\Container\ContainerInterface;
use Solcre\Pokerclub\Exception\ComissionInvalidException;
use Solcre\Pokerclub\Exception\ComissionNotFoundException;
use Solcre\Pokerclub\Exception\SessionNotFoundException;
use Exception;

class ComissionSessionController extends BaseController
{
    protected $view;
    protected $comissionService;
    protected $sessionService;

    public function __construct(View $view, EntityManager $em)
    {
        $this->view             = $view;
        $this->comissionService = new ComissionSessionService($em);
        $this->sessionService   = new SessionService($em);
    }

    public function listAll($request, $response, $args)
    {
        $idSession      = $args['idSession'];
        $comissions     = null;
        $datosUI        = [];
        $message        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status  = parent::STATUS_CODE_500;
        }

        $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));

        if (is_array($datosComissions)) {
            foreach ($datosComissions as $comissionObject) {
                $comissions[] = $comissionObject->toArray();
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = isset($comissions) ? $comissions : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }
 
    public function list($request, $response, $args)
    {
        $id             = $args['idcomission'];
        $idSession      = $args['idSession'];
        $datosUI        = [];
        $comission      = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;
        $message        = null;

        try {
            $comission = $this->comissionService->fetch(array('id' => $id));
            $status    = parent::STATUS_CODE_200;
        } catch (ComissionNotFoundException $e) {
            $message[] = $e->getMessage();
            $status  = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status  = parent::STATUS_CODE_500;
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = isset($comission) ? $comission->toArray() : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function loadData($idSession, $message)
    {
        $data = null;

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'comissionSession/listAll.html.twig';
            $this->view->setTemplate($template);

            $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));
            // @codeCoverageIgnoreStart
            try {
                $session         = $this->sessionService->fetch(array('id' => $idSession));
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
            }
            // @codeCoverageIgnoreEnd
            
            $comissions = [];

            if (is_array($datosComissions)) {
                foreach ($datosComissions as $comission) {
                    $comissions[] = $comission->toArray();
                }
            }
            
            $data['session']               = isset($session) ? $session->toArray() : [];
            $data['session']['comissions'] = $comissions;
            $data['breadcrumb']            = 'Comisiones';
            $data['message']               = $message;
        }

        return $data;
    }

    public function add($request, $response, $args)
    {
        $post           = $request->getParsedBody();
        $idSession      = $args['idSession'];
        $datosUI        = [];
        $message        = null;
        $status         = null;

        if (is_array($post)) {
            try {
                $comission = $this->comissionService->add($post);
                $message[] = 'la comission se ingresó exitosamente.';
                $status = parent::STATUS_CODE_201;
            } catch (ComissionInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $datosUI  = [];
        $message = null;

        try {
            $session   = $this->sessionService->fetch(array('id' => $idSession));
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['session']    = isset($session) ? $session->toArray() : [];
            $datosUI['breadcrumb'] = 'Nueva Comision';

            if (isset($message)) {
                $datosUI['message']    = $message;
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post           = $request->getParsedBody();
        $idSession      = $post['idSession'];
        $datosUI        = [];
        $message        = null;
        $status         = null;

        if (is_array($post)) {
            try {
                $comission = $this->comissionService->update($post);
                $message[] = 'la comisión se actualizó exitosamente.';
                $status    = parent::STATUS_CODE_200;
            } catch (ComissionInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (ComissionNotFoundException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }

            if ($this->view instanceof JsonView) {
                $response = $response->withStatus($status);
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI  = isset($comission) ? $comission->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $id             = $args['idcomission'];
        $idSession      = $args['idSession'];
        $datosUI        = null;
        $message        = null;
        $status         = null;

        try {
            $delete    = $this->comissionService->delete($id);
            $message[] = 'La comisión se eliminó exitosamente';
            $status    = parent::STATUS_CODE_204;
        } catch (ComissionNotFoundException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_500;
        }
        
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }
        
        return $this->view->render($request, $response, $datosUI);
    }
}
