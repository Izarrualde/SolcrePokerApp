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
        $idSession      = $args['idSession'];
        $datosUI        = [];
        $usersSession   = null;
        $message        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
        }

        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
    
        if (is_array($datosUsersSession)) {
            foreach ($datosUsersSession as $userSessionObject) {
                $usersSession[] = $userSessionObject->toArray();
            }
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if ($status == $expectedStatus) {            
                $datosUI['session']                 = isset($session) ? $session->toArray() : [];
                $datosUI['session']['usersSession'] = $usersSession;
            }

            $datosUI['breadcrumb'] = 'Usuarios de Sesion';

            if (isset($message)) {
                $datosUI['message'] = $message;
            }            
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = isset($usersSession) ? $usersSession : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $id          = $args['idusersession'];
        // $idSession      = $args['idSession'];
        $datosUI        = [];
        $comission      = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;
        $message        = null;

        try {
            $userSession = $this->userSessionService->fetch(array('id' => $id));
            $status    = parent::STATUS_CODE_200;        
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status  = parent::STATUS_CODE_404;
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if ($status == $expectedStatus) {
                $datosUI['userSession'] = isset($userSession) ? $userSession->toArray() : [];
            }

            $datosUI['breadcrumb']  = 'Editar Usuario';
            if (isset($message)) {
                $datosUI['message'] = $message;
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = isset($userSession) ? $userSession->toArray() : [];
            $response = $response->withStatus($status);
        }
        
        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post           = $request->getParsedBody();
        $idSession      = $post['idSession'];
        $usersAdded     = [];
        $datosUI        = [];
        $status         = null;
        $message        = null;

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
                    $message[]    = 'Se agregó exitosamente.';
                    // $status       = parent::STATUS_CODE_201;
                } catch (UserSessionAlreadyAddedException $e) {
                    $message[] = $e->getMessage();
                    // $status    = parent::STATUS_CODE_400;
                } catch (TableIsFullException $e) {
                    $message[] = $e->getMessage();
                    // $status    = parent::STATUS_CODE_400;
                } catch (\Exception $e) {
                    $message[] = $e->getMessage();
                    $status    = parent::STATUS_CODE_500;
                }
            }

            // solo recojo status del ultimo intento, corregir para jsonview 
            // datos ui esta bien, pero presentar status negativo, si hay un 400 mostrar ese

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'userSession/listAll.html.twig';
                $this->view->setTemplate($template);
                
                try {
                    $session = $this->sessionService->fetch(array('id' => $idSession));  
                } catch (\Exception $e) {
                    $message[] = $e->getMessage();
                    // $status    = parent::STATUS_CODE_404;
                }
               
                $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));

                $usersSession = [];

                if (is_array($datosUsersSession)) {
                    foreach ($datosUsersSession as $userSessionObject) {
                        $usersSession[] = $userSessionObject->toArray();
                    }
                }

                $datosUI['session']                 = isset($session) ? $session->toArray() : [];
                $datosUI['session']['usersSession'] = $usersSession;
                $datosUI['breadcrumb']              = 'Usuarios de Sesión';
                $datosUI['message']                 = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                if (!empty($usersAdded)) {
                    $usersAddedToArray = [];
                    //var_dump($usersAdded);
                    foreach ($usersAdded as $userSession) {
                        $usersAddedToArray[] = $userSession->toArray();
                    }
                    $datosUI = $usersAddedToArray;
                }
                $response = $response->withStatus($this->setStatusForResponse($status, count($usersAdded) )); 
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $datosUI   = [];
        $message   = null;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
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
                $userSession = $this->userSessionService->update($post);
                $message[]  = 'El usuario se actualizó exitosamente';
                $status    = parent::STATUS_CODE_200;
            } catch (UserSessionNotFoundException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'userSession/listAll.html.twig';
                $this->view->setTemplate($template);
                
                try {
                     $session = $this->sessionService->fetch(array('id' => $idSession));  
                } catch (\Exception $e) {
                    $message[] = $e->getMessage();
                    $status    = parent::STATUS_CODE_404;
                }

                $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));

                $usersSession = [];

                if (is_array($datosUsersSession)) {
                    foreach ($datosUsersSession as $userSessionObject) {
                        $usersSession[] = $userSessionObject->toArray();
                    }
                }

                $datosUI['session']                 = isset($session) ? $session->toArray() : [];
                $datosUI['session']['usersSession'] = $usersSession;
                $datosUI['breadcrumb']              = 'Usuarios de Sesión';
                $datosUI['message']                 = $message;
            }

            // JsonView
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
        $message   = null;
        $status    = null;
        
        try {
            $delete = $this->userSessionService->delete($id);
            $message[]  = 'El usuario se eliminó exitosamente de la sesión';
            $status    = parent::STATUS_CODE_204;
        } catch (UserSessionNotFoundException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_500;
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'userSession/listAll.html.twig';
            $this->view->setTemplate($template);
            
            try {
                 $session = $this->sessionService->fetch(array('id' => $idSession));  
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            }

            $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));

            $usersSession = [];

            if (is_array($datosUsersSession)) {
                foreach ($datosUsersSession as $userSessionObject) {
                    $usersSession[] = $userSessionObject->toArray();
                }
            }

            $datosUI['session']                 = isset($session) ? $session->toArray() : [];
            $datosUI['session']['usersSession'] = $usersSession;
            $datosUI['breadcrumb']              = 'Usuarios de Sesión';
            $datosUI['message']                 = $message;
        }
        
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function formClose($request, $response, $args)
    {
        $id      = $args['idusersession'];
        $datosUI = [];

        try {
            $userSession = $this->userSessionService->fetch(array('id' => $id));            
        } catch (UserSessionNotFoundException $e) {
            $message[] = $e->getMessage();
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
        }        

        $datosUI['userSession'] = isset($userSession) ? $userSession->toArray() : [];
        $datosUI['breadcrumb']  = 'Cerrar Session de Usuario';
        
        if
         (isset($message)) {
            $datosUI['message']  = $message;    
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
                $message[]   = 'El usuario ha salido de la sesión';
                $status      = parent::STATUS_CODE_200;   
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'userSession/listAll.html.twig';
                $this->view->setTemplate($template);

                //BUSQUEDA DE DATOS PARA LA UI
                // add try
                $session           = $this->sessionService->fetch(array('id' => $idSession));
                $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));

                $usersSession = [];

                if (is_array($datosUsersSession)) {
                    foreach ($datosUsersSession as $userSessionObject) {
                        $usersSession[] = $userSessionObject->toArray();
                    }
                }

                $datosUI['session']                 = isset($session) ? $session->toArray() : [];
                $datosUI['session']['usersSession'] = $usersSession;
                $datosUI['breadcrumb']              = 'Usuarios de Sesión';
                $datosUI['message']                 = $message;
            }
            
            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = isset($userSession) ? $userSession->toArray() : [];
                $response = $response->withStatus($status);
            }
        }
        
        return $this->view->render($request, $response, $datosUI);
    }
}
