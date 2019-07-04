<?php
namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\UserSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\UserSessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\Exception\UserSessionAlreadyAddedException;
use Solcre\lmsuy\Exception\TableIsFullException;

class UserSessionController
{
    protected $view;
    protected $userSessionService;
    protected $userService;
    protected $sessionService;

    public function __construct(Twig $view, EntityManager $em)
    {
        $this->view               = $view;
        $this->userService        = new UserService($em);
        $this->sessionService     = new SessionService($em);
        $this->userSessionService = new UserSessionService($em, $this->userService);
    }

    public function listAll($request, $response, $args)
    {

        $idSession         = $args['idSession'];
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
        $session           = $this->sessionService->fetchOne(array('id' => $idSession));

        $template = 'users.html.twig';

        $datosUI = array();

        $usersSession = array();

        foreach ($datosUsersSession as $userSessionObject) {
            $usersSession[] = $userSessionObject->toArray();
        }

        $datosUI['session']                 = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb']              = 'Usuarios de Sesion';

           return $this->view->render($response, $template, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $id          = $args['idusersession'];
        $userSession = $this->userSessionService->fetchOne(array('id' => $id));

        $template = 'editUser.html.twig';

        $datosUI = array();

        $datosUI['userSession'] = $userSession->toArray();
        $datosUI['breadcrumb']  = 'Editar Usuario';

        return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args)
    {

        $post      = $request->getParsedBody();
        $idSession = $post['idSession'];
        $message = array();
        
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
                    $this->userSessionService->add($data);
                    $message[] = 'Se agregó exitosamente.';
                // @codeCoverageIgnoreStart 
                } catch (UserSessionAlreadyAddedException $e) {
                    $message[] = $e->getMessage();
                } catch (TableIsFullException $e) {
                    $message[] = $e->getMessage();
                // @codeCoverageIgnoreEnd
                }
                
            }

            $template = 'users.html.twig';

            // BUSQUEDA DE DATOS PARA LA UI
            $session           = $this->sessionService->fetchOne(array('session' => $idSession));
            $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));

            $datosUI = array();
            $usersSession = array();

            foreach ($datosUsersSession as $userSessionObject) {
                $usersSession[] = $userSessionObject->toArray();
            }

            $datosUI['session']                 = $session->toArray();
            $datosUI['session']['usersSession'] = $usersSession;
            $datosUI['breadcrumb']              = 'Usuarios de Sesión';
            $datosUI['message']                 = $message;
        }
            return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];

        $session    = $this->sessionService->fetchOne(array('session' => $idSession));
        $datosUsers = $this->userService->fetchAll();

        $users   = array();
        $datosUI = array();

        foreach ($datosUsers as $userObject) {
            $users[] = $userObject->toArray();
        }
        
        $datosUI['session']    = $session->toArray();
        $datosUI['users']      = $users;
        $datosUI['breadcrumb'] = 'Nuevo UserSession';
        $template              = 'newusers.html.twig';

        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $post['idSession'];
        $this->userSessionService->update($post);

        $message = array();
        $message[]  = 'El usuario se actualizó exitosamente';
        $template = 'users.html.twig';

        // BUSQUEDA DE DATOS PARA LA UI
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
        $session           = $this->sessionService->fetchOne(array('id' => $idSession));
        
        $datosUI      = array();
        $usersSession = array();

        foreach ($datosUsersSession as $userSessionObject) {
               $usersSession[] = $userSessionObject->toArray();
        }

        $datosUI['session']                 = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb']              = 'Usuarios de Sesión';
        $datosUI['message']                 = $message;

        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $id        = $args['idusersession'];

        $this->userSessionService->delete($id);

        $message = array();
        $message[]  = 'El usuario se eliminó exitosamente de la sesión';
        $template = 'users.html.twig';

        //BUSQUEDA DE DATOS PARA LA UI
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
        $session           = $this->sessionService->fetchOne(array('id' => $idSession));

        $datosUI      = array();
        $usersSession = array();

        foreach ($datosUsersSession as $userSessionObject) {
            $usersSession[] = $userSessionObject->toArray();
        }

        $datosUI['session']                 = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb']              = 'Usuarios de Sesión';
        $datosUI['message']                 = $message;

        return $this->view->render($response, $template, $datosUI);
    }

    public function formClose($request, $response, $args)
    {
        $id          = $args['idusersession'];
        $userSession = $this->userSessionService->fetchOne(array('id' => $id));
        
        $template = 'closeUserSession.html.twig';
        
        $datosUI = array();

        $datosUI['userSession'] = $userSession->toArray();
        $datosUI['breadcrumb']  = 'Cerrar Session de Usuario';
        
        return $this->view->render($response, $template, $datosUI);
    }

    public function close($request, $response, $args)
    {
        $id   = $args['idusersession'];
        $post = $request->getParsedBody();

        $userSessionObject = $this->userSessionService->fetchOne(array('id' => $id));
        $idSession         = $userSessionObject->getSession()->getId();
        $this->userSessionService->close($post);

        $message = array();
        $message[]  = 'El usuario ha salido de la sesión';
        $template = 'users.html.twig';

        //BUSQUEDA DE DATOS PARA LA UI
        
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
        $session           = $this->sessionService->fetchOne(array('session' => $idSession));
        
        $datosUI      = array();
        $usersSession = array();

        foreach ($datosUsersSession as $userSession) {
            $usersSession[] = $userSession->toArray();
        }

        $datosUI['session']                 = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb']              = 'Usuarios de Sesión';
        $datosUI['message']                 = $message;

        return $this->view->render($response, $template, $datosUI);
    }
}
