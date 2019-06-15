<?php
Namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\UserSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\userSessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;

class UserSessionController
{
    protected $view;
    protected $userSessionService;
    protected $userService;
    protected $sessionService;

	public function __construct(Twig $view, EntityManager $em) {
        $this->view = $view;
    	$this->userService = new UserService($em); 
        $this->sessionService = new SessionService($em);
        $this->userSessionService = new UserSessionService($em); 
    }

    public function listAll($request, $response, $args) {

        
        $idSession = $args['idSession'];
        $template = 'users.html.twig';
        $datosUI = array();
    	$datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));



        $session = $this->sessionService->fetchOne(array('id' => $idSession));

        $usersSession = array();

        foreach ($datosUsersSession as $userSessionObject) {
            $usersSession[] = $userSessionObject->toArray();   

        }

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb'] = 'Usuarios de Sesión';

       	return $this->view->render($response, $template, $datosUI);

    }

    public function list($request, $response, $args) {
	    $id = $args['idusersession'];
	    $userSession = $this->userSessionService->fetchOne(array('id' => $id));
        $template = 'editUser.html.twig';
        $datosUI = array();

		$datosUI['userSession'] = $userSession->toArray();
        $datosUI['breadcrumb'] = 'Editar Usuario';

    	return $this->view->render($response, $template, $datosUI);
    }


//ver como convertir esta function
    public function add($request, $response, $args) {
        $post = $request->getParsedBody();
        $idSession = $post['idSession'];

        $datosUI = array();
        
        if (is_array($post))
        {
            $session = $this->sessionService->fetchOne(array('session' => $idSession));
            $res = array();
            foreach ($post['user_id'] as $user_id) 
            {
                $userSessionObject = new UserSession($post['id'], $session, $user_id, $post['approved'], $post['accumulatedPoints'], $post['cashout'], $post['start'], $post['end']);
                $res[$user_id] = $this->userSessionService->add($userSessionObject);
            }

            $template = 'users.html.twig';
            $errors = array_filter($res, function ($item){return !$item;});
            $message = (count($post['user_id'])==count($errors))? 'Los usuarios se agregaron exitosamente' : count($errors) .' usuarios no se agregaron';

            //extraigo datos de la bdd
            $datosUI = array();
            $datosUsersSession = $this->userSessionService->find(array('session' => $idSession));
            $session = $this->sessionService->findOne(array('session' => $idSession));
            $usersSession = array();

            foreach ($datosUsersSession as $userSessionObject) {
                $usersSession[] = $userSessionObject->toArray();            
            }

            $datosUI['session'] = $session->toArray();
            $datosUI['session']['usersSession'] = $usersSession;
            $datosUI['breadcrumb'] = 'Usuarios de Sesión';
            $datosUI['message'] = $message;
        }
            return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args) 
    {
        $idSession = $args['idSession'];
        $session = $this->sessionService->fetchOne(array('session' => $idSession));
        $datosUsers = $this->userService->fetchAll();
        $users = array();
        foreach ($datosUsers as $userObject) 
        {
            $users[] = $userObject->toArray();
        }

        $datosUI = array();
        $datosUI['session'] = $session->toArray();
        $datosUI['users'] = $users;
        $datosUI['breadcrumb'] = 'Nuevo UserSession';
        $template = 'newusers.html.twig';
        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args) {
        $post = $request->getParsedBody();
        $idSession = $post['idSession'];
        //$idSession = $args['idSession'];
        // $userSessionObject = new UserSession($post['id'], $this->sessionService->findOne($post['idSession']), $post['idUser'], $post['approved'], $post['accumulatedPoints'], $post['cashout'], $post['start'], $post['end']);
        $this->userSessionService->update($post);
        $message = 'El usuario se actualizó exitosamente';
        $template = 'users.html.twig';
        //extraigo datos de la bdd
        $datosUI = array();
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
        $session = $this->sessionService->fetchOne(array('session' => $idSession));
        $usersSession = array();

        foreach ($datosUsersSession as $userSessionObject) {
               $usersSession[] = $userSessionObject->toArray();            
        }

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb'] = 'Usuarios de Sesión';
        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args) {
        $idSession = $args['idSession'];
        $id = $args['idusersession'];
        //if (is_array($_GET))
        $this->userSessionService->delete($id);
        $message = 'El usuario se eliminó exitosamente de la sesión';
        $template = 'users.html.twig';

        //BUSQUEDA DE DATOS PARA LA UI
        $datosUI = array();
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
        $session = $this->sessionService->fetchOne(array('session' => $idSession));
        $usersSession = array();

        foreach ($datosUsersSession as $userSessionObject) {
            $usersSession[] = $userSessionObject->toArray();            
        }

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb'] = 'Usuarios de Sesión';
        $datosUI['message'] = $message;

        return $this->view->render($response, $template, $datosUI);
    }

    public function formClose($request, $response, $args) {
        $id = $args['idusersession'];
        $userSession = $this->userSessionService->fetchOne(array('id' => $id));
        $template = 'closeUserSession.html.twig';
        $datosUI = array();

        $datosUI['userSession'] = $userSession->toArray();
        $datosUI['breadcrumb'] = 'Cerrar Session de Usuario';
        return $this->view->render($response, $template, $datosUI);
    }

// revisar esta function
    public function close($request, $response, $args) {
        $id = $args['idusersession'];
        $post = $request->getParsedBody();
        $userSessionObject = $this->userSessionService->fetchOne(array('id' => $id));
        $idSession = $userSessionObject->getIdSession();
        $this->userSessionService->close($post);
        $message = 'El usuario ha salido de la sesión';
        $template = 'users.html.twig'; 

        //BUSQUEDA DE DATOS PARA LA UI

        $datosUI = array();
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
        $session = $this->sessionService->fetchOne(array('session' => $idSession));
        $usersSession = array();

        foreach ($datosUsersSession as $userSession) {
            $usersSession[] = $userSession->toArray();            
        }

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb'] = 'Usuarios de Sesión';
        $datosUI['message'] = $message;

        return $this->view->render($response, $template, $datosUI); 
    }
}