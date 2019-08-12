<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\UserSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Entity\UserSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Exception\UserSessionAlreadyAddedException;
use Solcre\Pokerclub\Exception\TableIsFullException;
use Solcre\Pokerclub\Exception\UserSessionNotFoundException;
use Exception;

class UserSessionController
{
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_500 = 500;

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

    public function listAll($request, $response, $args)
    {

        $idSession    = $args['idSession'];
        $datosUI      = [];
        $usersSession = [];

        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
    
        if (isset($datosUsersSession)) {
            foreach ($datosUsersSession as $userSessionObject) {
                $usersSession[] = $userSessionObject->toArray();
            }
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session           = $this->sessionService->fetchOne(array('id' => $idSession));
            
            $datosUI['session']                 = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['usersSession'] = $usersSession;
            $datosUI['breadcrumb']              = 'Usuarios de Sesion';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = $usersSession;
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $id          = $args['idusersession'];
        $datosUI = [];

        $userSession = $this->userSessionService->fetchOne(array('id' => $id));

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['userSession'] = is_null($userSession) ? [] : $userSession->toArray();
            $datosUI['breadcrumb']  = 'Editar Usuario';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = is_null($userSession) ? [] : $userSession->toArray();
        }
        
        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post       = $request->getParsedBody();
        $idSession  = $post['idSession'];
        $usersAdded = [];
        $datosUI    = null;
        $message    = [];
        
        if (is_array($post)) {
            foreach ($post['user_id'] as $userId) {
                $data = [
                    'start'      => $post['start'],
                    'end'        => $post['end'],
                    'isApproved' => $post['approved'],
                    'points'     => $post['accumulatedPoints'],
                    'idSession'  => $post['idSession'],
                    'idUser'     => $userId
                ];

                try {
                    $usersAdded[] = $this->userSessionService->add($data);
                    $message[] = 'Se agregó exitosamente.';
                    $response = $response->withStatus(self::STATUS_CODE_201); //magic number
                } catch (UserSessionAlreadyAddedException $e) {
                    $message[] = $e->getMessage();
                    $response = $response->withStatus(self::STATUS_CODE_400);
                } catch (TableIsFullException $e) {
                    $message[] = $e->getMessage();
                    $response = $response->withStatus(self::STATUS_CODE_400);
                }
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'userSession/listAll.html.twig';
                $this->view->setTemplate($template);
                // BUSQUEDA DE DATOS PARA LA UI
                $session           = $this->sessionService->fetchOne(array('session' => $idSession));
                $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));

                $usersSession = [];

                if (isset($datosUsersSession)) {
                    foreach ($datosUsersSession as $userSessionObject) {
                        $usersSession[] = $userSessionObject->toArray();
                    }
                }

                $datosUI['session']                 = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['usersSession'] = $usersSession;
                $datosUI['breadcrumb']              = 'Usuarios de Sesión';
                $datosUI['message']                 = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                if (!empty($usersAdded)) {
                    $usersAddedToArray = [];
                    foreach ($usersAdded as $userSession) {
                        $usersAddedToArray[] = $userSession->toArray();
                    }
                    $datosUI = $usersAddedToArray;
                }
            }
        }

            return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $datosUI = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session    = $this->sessionService->fetchOne(array('session' => $idSession));
            $datosUsers = $this->userService->fetchAll();

            $users   = [];
            
            if (is_array($datosUsers)) {
                foreach ($datosUsers as $userObject) {
                    $users[] = $userObject->toArray();
                }
            }
            
            $datosUI['session']    = isset($session) ? $session->toArray() : [];
            $datosUI['users']      = $users;
            $datosUI['breadcrumb'] = 'Nuevo UserSession';
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $post['idSession'];
        $message   = [];
        $datosUI   = [];

        if (is_array($post)) {
            try {
                $userSession = $this->userSessionService->update($post);
                $message[]  = 'El usuario se actualizó exitosamente';
            // @codeCoverageIgnoreStart
            } catch (UserSessionAlreadyAddedException $e) {
                $message[] = $e->getMessage();
            // @codeCoverageIgnoreEnd
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'userSession/listAll.html.twig';
                $this->view->setTemplate($template);

                // BUSQUEDA DE DATOS PARA LA UI
                $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
                $session           = $this->sessionService->fetchOne(array('id' => $idSession));
            
                $usersSession = [];

                if (is_array($datosUsersSession)) {
                    foreach ($datosUsersSession as $userSessionObject) {
                        $usersSession[] = $userSessionObject->toArray();
                    }
                }

                $datosUI['session']                 = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['usersSession'] = $usersSession;
                $datosUI['breadcrumb']              = 'Usuarios de Sesión';
                $datosUI['message']                 = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($userSession) ? [] : $userSession->toArray();
            }
        }
        
        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $id        = $args['idusersession'];
        $datosUI   = [];
        $message   = [];
        
        // JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(self::STATUS_CODE_204);
        }

        $datosUI  = null;
        
        try {
            $delete = $this->userSessionService->delete($id);
            $message[]  = 'El usuario se eliminó exitosamente de la sesión';
        // @codeCoverageIgnoreStart
        } catch (UserSessionNotFoundException $e) {
            $response = $response->withStatus(self::STATUS_CODE_404);
            $message[] = $e->getMessage();
        } catch (\Exception $e) {
            $response = $response->withStatus(self::STATUS_CODE_500);
            $message[] = $e->getMessage();
        }
        // @codeCoverageIgnoreEnd

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'userSession/listAll.html.twig';
            $this->view->setTemplate($template);

            //BUSQUEDA DE DATOS PARA LA UI
            $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
            $session           = $this->sessionService->fetchOne(array('id' => $idSession));

            $usersSession = [];

            if (isset($datosUsersSession)) {
                foreach ($datosUsersSession as $userSessionObject) {
                    $usersSession[] = $userSessionObject->toArray();
                }
            }

            $datosUI['session']                 = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['usersSession'] = $usersSession;
            $datosUI['breadcrumb']              = 'Usuarios de Sesión';
            $datosUI['message']                 = $message;
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function formClose($request, $response, $args)
    {
        $id          = $args['idusersession'];
        $userSession = $this->userSessionService->fetchOne(array('id' => $id));
        
        $datosUI = [];

        $datosUI['userSession'] = $userSession->toArray();
        $datosUI['breadcrumb']  = 'Cerrar Session de Usuario';

        return $this->view->render($request, $response, $datosUI);
    }

    public function close($request, $response, $args)
    {
        $id   = $args['idusersession'];
        $post = $request->getParsedBody();

        if (is_array($post)) {
            try {
                $userSession = $this->userSessionService->close($post);
                $message[]  = 'El usuario ha salido de la sesión';
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'userSession/listAll.html.twig';
                $this->view->setTemplate($template);

                //BUSQUEDA DE DATOS PARA LA UI
                $idSession = $userSession->getSession()->getId();
                $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
                $session           = $this->sessionService->fetchOne(array('session' => $idSession));
                
                $datosUI      = null;
                $usersSession = [];

                foreach ($datosUsersSession as $userSessionObject) {
                    $usersSession[] = $userSessionObject->toArray();
                }

                $datosUI['session']                 = $session->toArray();
                $datosUI['session']['usersSession'] = $usersSession;
                $datosUI['breadcrumb']              = 'Usuarios de Sesión';
                $datosUI['message']                 = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($userSession) ? [] : $userSession->toArray();
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
