<?php
namespace Solcre\lmsuy\Controller;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Exception\SessionNotFoundException;
use Solcre\Pokerclub\Exception\SessionInvalidException;
use Solcre\Pokerclub\Exception\ClassNotExistingException;
use Exception;

class SessionController extends BaseController
{
    protected $view;
    protected $sessionService;

    public function __construct(View $view, EntityManager $em)
    {
        $this->view           = $view;
        $this->sessionService = new SessionService($em);
    }

    public function listAll($request, $response, $args)
    {
        $sessions       = [];
        $datosUI        = [];
        $message        = null;
        $status         = null;

        $datosSessions = $this->sessionService->fetchAll();

        if (is_array($datosSessions)) {
            foreach ($datosSessions as $sessionObject) {
                $sessions[] = $sessionObject->toArray();
            }
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['sessions'] = $sessions;
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(parent::STATUS_CODE_200);
            $datosUI = $sessions;
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idSession      = $args['idSession'];
        $message        = null;
        $datosUI        = [];
        $session        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (SessionNotFoundException $e) {
            $message[] = $e->getMessage();
            $status  = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status  = ($e->getCode() == parent::STATUS_CODE_404) ? parent::STATUS_CODE_404 : parent::STATUS_CODE_500;
        }
 
        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if ($status == $expectedStatus) {
                $datosUI['session'] = isset($session) ? $session->toArray() : [];
            }
            
            $datosUI['breadcrumb'] = 'Editar Sesión';
            
            if (isset($message)) {
                $datosUI['message'] = $message;
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = is_null($session) ? [] : $session->toArray();
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function loadData($message)
    {
        $data = null;

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'session/listAll.html.twig';
            $this->view->setTemplate($template);

            $datosSessions = $this->sessionService->fetchAll();
            $sessions = [];

            if (is_array($datosSessions)) {
                foreach ($datosSessions as $sessionObject) {
                    $sessions[] = $sessionObject->toArray();
                }
            }

            $data['sessions']   = $sessions;
            $data['message']    = $message;
        }

        return $data;
    }

    public function add($request, $response, $args)
    {
        $post    = $request->getParsedBody();
        $datosUI = [];
        $message = null;
        $status  = null;

        if (is_array($post)) {
            try {
                $session = $this->sessionService->add($post);
                $message[] = 'La sesión se agregó exitosamente.';
                $status = parent::STATUS_CODE_201;
            } catch (SessionInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }
            
            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $datosUI  =  $this->loadData($message);
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI  = isset($session) ? $session->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $datosUI = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['breadcrumb'] = 'Nueva Sesión';
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post    = $request->getParsedBody();
        $datosUI = [];
        $message = null;
        $status  = null;
        
        if (is_array($post)) {
            try {
                $session = $this->sessionService->update($post);
                $message[] = 'La sesión se actualizó exitosamente.';
                $status    = parent::STATUS_CODE_200;
            } catch (SessionInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (SessionNotFoundException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $datosUI  =  $this->loadData($message);
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI  = isset($session) ? $session->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $datosUI   = null;
        $message   = null;
        $status    = null;

        try {
            $delete    = $this->sessionService->delete($idSession);
            $message[] = 'La Sesión se eliminó exitosamente';
            $status    = parent::STATUS_CODE_204;
        } catch (SessionNotFoundException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_500;
        }
        
        if ($this->view instanceof TwigWrapperView) {
            $datosUI  = $this->loadData($message);
        }
        
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function calculatePoints($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $datosUI   = null;
        $sessions  = null;
        $message   = null;
        
        try {
            $this->sessionService->calculateRakeback($idSession);
            $message[] = 'Puntos asignados exitosamente.';
            $status    = parent::STATUS_CODE_200;
        } catch (SessionNotFoundException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_404;
        } catch (ClassNotExistingException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_400;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_500;
        }

        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        if ($this->view instanceof TwigWrapperView) {
            $datosUI = $this->loadData($message);
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
