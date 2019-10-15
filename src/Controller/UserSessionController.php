<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\UserSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Exception\UserSessionAlreadyAddedException;
use Solcre\Pokerclub\Exception\TableIsFullException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;
use Solcre\Pokerclub\Exception\InsufficientUserSessionTimeException;
use Exception;

class UserSessionController extends BaseController
{
    protected $view;
    protected $userSessionService;
    protected $userService;
    protected $sessionService;

    public function __construct(View $view, EntityManager $em)
    {
        $this->view               = $view;
        $this->userService        = new UserService($em);
        $this->sessionService     = new SessionService($em);
        $this->userSessionService = new UserSessionService($em, $this->userService);
    }

    public function setStatusForResponse($status, $lenght)
    {
        if (isset($status)) {
            return $status;
        }

        return ($lenght > 0 ? parent::STATUS_CODE_201 : parent::STATUS_CODE_400);
    }

    public function listAll($request, $response, $args)
    {
        $idSession    = $args['idSession'];
        $datosUI      = [];
        $usersSession = null;
        $status       = null;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
                $status = parent::STATUS_CODE_404;
        }

        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
    
        if (is_array($datosUsersSession)) {
            foreach ($datosUsersSession as $userSessionObject) {
                $usersSession[] = $userSessionObject->toArray();
            }
        }

        if ($this->view instanceof JsonView) {
            $datosUI  = isset($usersSession) ? $usersSession : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $id        = $args['idusersession'];
        $datosUI   = [];
        $comission = null;
        $status    = null;
 

        try {
            $userSession = $this->userSessionService->fetch(array('id' => $id));
            $status    = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
            $status  = parent::STATUS_CODE_404;
        }

        if ($this->view instanceof JsonView) {
            $datosUI  = isset($userSession) ? $userSession->toArray() : [];
            $response = $response->withStatus($status);
        }
        
        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post       = $request->getParsedBody();
        $idSession  = $post['idSession'];
        $usersAdded = [];
        $datosUI    = [];
        $status     = null;

        if (is_array($post)) {
            foreach ($post['user_id'] as $userId) {
                $data = [
                    'isApproved' => $post['approved'],
                    'points'     => $post['points'],
                    'idSession'  => $post['idSession'],
                    'idUser'     => $userId
                ];

                try {
                    $usersAdded[] = $this->userSessionService->add($data);
                    // $status = parent::STATUS_CODE_201;
                } catch (UserSessionAlreadyAddedException $e) {
                    // $status = parent::STATUS_CODE_400;
                } catch (TableIsFullException $e) {
                    // $status = parent::STATUS_CODE_400;
                } catch (\Exception $e) {
                    $status    = parent::STATUS_CODE_500;
                }
            }

            if ($this->view instanceof JsonView) {
                if (!empty($usersAdded)) {
                    $usersAddedToArray = [];
                    foreach ($usersAdded as $userSession) {
                        $usersAddedToArray[] = $userSession->toArray();
                    }
                    $datosUI = $usersAddedToArray;
                }
                $response = $response->withStatus($this->setStatusForResponse($status, count($usersAdded)));
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
                $userSession = $this->userSessionService->update($post);
                $status    = parent::STATUS_CODE_200;
            } catch (UserSessionNotFoundException $e) {
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $status    = parent::STATUS_CODE_500;
            }

            if ($this->view instanceof JsonView) {
                $datosUI = isset($userSession) ? $userSession->toArray() : [];
                $response = $response->withStatus($status);
            }
        }
        
        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $id        = $args['idusersession'];
        $datosUI   = null;
        $status    = null;
        
        try {
            $delete = $this->userSessionService->delete($id);
            $status = parent::STATUS_CODE_204;
        } catch (UserSessionNotFoundException $e) {
            $status = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $status = parent::STATUS_CODE_500;
        }
        
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function close($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $id        = $args['idusersession'];
        $idSession = $args['idSession'];
        $datosUI   = [];
        $status    = null;

        if (is_array($post)) {
            try {
                $userSession = $this->userSessionService->close($post);
                $status      = parent::STATUS_CODE_200;
            } catch (InsufficientUserSessionTimeException $e) {
                $status = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $status = parent::STATUS_CODE_500;
            }
            
            if ($this->view instanceof JsonView) {
                $datosUI  = isset($userSession) ? $userSession->toArray() : [];
                $response = $response->withStatus($status);
            }
        }
        
        return $this->view->render($request, $response, $datosUI);
    }
}
