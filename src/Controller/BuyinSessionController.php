<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\BuyinSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Service\UserSessionService;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Exception\BuyinInvalidException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;
use Solcre\Pokerclub\Exception\BuyinNotFoundException;
use Exception;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;

class BuyinSessionController extends BaseController
{
    protected $view;
    protected $buyinSessionService;
    protected $sessionService;
    protected $userSessionService;
    protected $userService;

    public function __construct(View $view, EntityManager $em)
    {
        $this->view                = $view;
        $this->sessionService      = new SessionService($em);
        $this->userService         = new UserService($em);
        $this->userSessionService  = new UserSessionService($em);
        $this->buyinSessionService = new BuyinSessionService($em, $this->userSessionService);
    }

    public function listAll($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $buyins    = null;
        $datosUI   = [];
        $status    = null;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
                $status = parent::STATUS_CODE_404;
        }
        
        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);
        if (is_array($datosBuyins)) {
            foreach ($datosBuyins as $buyinObject) {
                $buyins[] = $buyinObject->toArray();
            }
        }

        if ($this->view instanceof JsonView) {
            $datosUI  = isset($buyins) ? $buyins : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $id        = $args['idbuyin'];
        $idSession = $args['idSession'];
        $datosUI   = [];
        $comission = null;
        $status    = null;

        try {
            $buyin  = $this->buyinSessionService->fetch(array('id' => $id));
            $status = parent::STATUS_CODE_200;
        } catch (BuyinNotFoundException $e) {
            $status = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $status  = ($e->getCode() == parent::STATUS_CODE_404) ? 
            parent::STATUS_CODE_404 : 
            parent::STATUS_CODE_500;
        }

        if ($this->view instanceof JsonView) {
            $datosUI  = isset($buyin) ? $buyin->toArray() : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $args['idSession'];
        $datosUI   = [];
        $status    = null;

        if (is_array($post)) {
            try {
                $buyin  = $this->buyinSessionService->add($post);
                $status = parent::STATUS_CODE_201;
            } catch (BuyinInvalidException $e) {
                $status = parent::STATUS_CODE_400;
            } catch (IncompleteDataException $e) {
                $status = parent::STATUS_CODE_400;
            } catch (UserSessionNotFoundException $e) {
                $status = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $status = parent::STATUS_CODE_500;
            }

            if ($this->view instanceof JsonView) {
                $datosUI  = isset($buyin) ? $buyin->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $post['idSession'];
        $datosUI   = [];
        $status    = null;

        if (is_array($post)) {
            try {
                $buyin  = $this->buyinSessionService->update($post);
                $status = parent::STATUS_CODE_200;
            } catch (BuyinInvalidException $e) {
                $status = parent::STATUS_CODE_400;
            } catch (IncompleteDataException $e) {
                $status = parent::STATUS_CODE_400;
            } catch (UserSessionNotFoundException $e) {
                $status = parent::STATUS_CODE_404;
            } catch (BuyinNotFoundException $e) {
                $status = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $status = parent::STATUS_CODE_500;
            }

            if ($this->view instanceof JsonView) {
                $datosUI  = isset($buyin) ? $buyin->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $id        = $args['idbuyin'];
        $datosUI   = null;
        $status    = null;

        try {
            $delete = $this->buyinSessionService->delete($id);
            $status = parent::STATUS_CODE_204;
        } catch (BuyinNotFoundException $e) {
            $status = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $status = parent::STATUS_CODE_500;
        }
        
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
