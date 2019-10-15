<?php
namespace Solcre\lmsuy\Controller;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Exception\SessionNotFoundException;
use Solcre\Pokerclub\Exception\SessionInvalidException;
use Solcre\Pokerclub\Exception\ClassNotExistingException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Exception;

use Solcre\lmsuy\Service\RakebackService;

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
        $status         = null;

        $datosSessions = $this->sessionService->fetchAll();

        if (is_array($datosSessions)) {
            foreach ($datosSessions as $sessionObject) {
                $sessions[] = $sessionObject->toArray();
            }
        }

        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(parent::STATUS_CODE_200);
            $datosUI = $sessions;
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idSession      = $args['idSession'];
        $datosUI        = [];
        $session        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (SessionNotFoundException $e) {
            $status  = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $status  = ($e->getCode() == parent::STATUS_CODE_404) ? parent::STATUS_CODE_404 : parent::STATUS_CODE_500;
        }

        if ($this->view instanceof JsonView) {
            $datosUI = is_null($session) ? [] : $session->toArray();
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post    = $request->getParsedBody();
        $datosUI = [];
        $status  = null;

        if (is_array($post)) {
            try {
                $session = $this->sessionService->add($post);
                $status = parent::STATUS_CODE_201;
            } catch (SessionInvalidException $e) {
                $status    = parent::STATUS_CODE_400;
            } catch (IncompleteDataException $e) {
                $status    = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $status    = parent::STATUS_CODE_500;
            }

            if ($this->view instanceof JsonView) {
                $datosUI  = isset($session) ? $session->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post    = $request->getParsedBody();
        $datosUI = [];
        $status  = null;
        
        if (is_array($post)) {
            try {
                $session = $this->sessionService->update($post);
                $status    = parent::STATUS_CODE_200;
            } catch (SessionInvalidException $e) {
                $status    = parent::STATUS_CODE_400;
            } catch (SessionNotFoundException $e) {
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $status    = parent::STATUS_CODE_500;
            }

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
        $status    = null;

        try {
            $delete    = $this->sessionService->delete($idSession);
            $status    = parent::STATUS_CODE_204;
        } catch (SessionNotFoundException $e) {
            $status    = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $status    = parent::STATUS_CODE_500;
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
        
        try {
            $this->sessionService->calculateRakeback($idSession);
            $status    = parent::STATUS_CODE_200;
        } catch (SessionNotFoundException $e) {
            $status    = parent::STATUS_CODE_404;
        } catch (ClassNotExistingException $e) {
            $status    = parent::STATUS_CODE_400;
        } catch (\Exception $e) {
            $status    = parent::STATUS_CODE_500;
        }

        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
