<?php
Namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;

class UserController
{
    protected $view;
    protected $UserService;

	public function __construct(Twig $view, EntityManager $em) {
        $this->view = $view;
    	$this->userService = new UserService($em); 
    }
    
    public function listAll($request, $response, $args) {
    	$datosUI = array();
        $template = 'viewUsers.html.twig';
        $datosUsers = $this->userService->fetchAll(
            array(
            ), 
            array(
                'name' => 'ASC', 
                'lastname' => 'ASC'
            )
        );

        $users = array();

        foreach ($datosUsers as $userObject) 
        {
            $users[] = $userObject->toArray();
        }
    
        $datosUI['users'] = $users;
        $datosUI['breadcrumb'] = 'Usuarios';

    	return $this->view->render($response, $template, $datosUI);
    }

    public function list($request, $response, $args) {
	    $idUser = $args['iduser'];
        $template = 'editPlayer.html.twig';
	    $user = $this->userService->fetchOne(array('id' => $idUser));
		$datosUI['user'] = $user->toArray();
        $datosUI['breadcrumb'] = 'Editar Usuario';
    	return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args) {
        $post = $request->getParsedBody();
        $datosUI = array();
        
        if (is_array($post))
        {
            /*
            $userObject = new UserEntity(null, $post['password'], null, $post['email'], $post['lastname'], $post['firstname'],  $post['username'], $post['multiplier'], $post['active'], $post['hours'], $post['points'], $post['sessions'], $post['results'], $post['cashin']);
            */
            $this->userService->add($post); 
            $template = 'viewUsers.html.twig';
            $message = 'El usuario se agregó exitosamente.';
            // BUSQUEDA DE DATOS PARA LA UI
            $datosUI = array();

            //extraigo datos de la bdd
           $datosUsers = $this->userService->fetchAll();
           $users = array();

           foreach ($datosUsers as $userObject) 
           {
               $users[] = $userObject->toArray();
           }
    
            $datosUI['users'] = $users;
            $datosUI['breadcrumb'] = 'Usuarios';
            $datosUI['message'] = $message;
        }   
        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args) 
    {   
        $datosUI = array();
        $datosUI['breadcrumb'] = 'Nuevo Usuario';
        $template = 'addUser.html.twig';
        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args) {
        $post = $request->getParsedBody();
        //$idSession = $args['idSession'];
        /*$userObject = new UserEntity($post['id'], $post['password'], null, $post['email'], $post['lastname'], $post['name'],  $post['name'], $post['multiplier'], $post['isActive'], $post['hours'], $post['points'], $post['sessions'], $post['results'], $post['cashin']);
        */
        $this->userService->update($post);
        $message = 'El usuario se actualizó exitosamente';
        $template = 'viewUsers.html.twig';

        // BUSQUEDA DE DATOS PARA LA UI
        $datosUI = array();

        //extraigo datos de la bdd
        $datosUsers = $this->userService->fetchAll();
        $users = array();

        foreach ($datosUsers as $userObject) 
        {
            $users[] = $userObject->toArray();
        }
    
        $datosUI['users'] = $users;
        $datosUI['breadcrumb'] = 'Usuarios';
        $datosUI['message'] = $message;
        
        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args) {
	    $idUser = $args['iduser'];

        //if (is_array($_GET))
        $this->userService->delete($idUser);
        $message = 'El usuario se eliminó exitosamente';
        $template = 'viewUsers.html.twig';
        $datosUI = array();
        // BUSQUEDA DE DATOS PARA LA UI
        $datosUI = array();
        //extraigo datos de la bdd
        $datosUsers = $this->userService->fetchAll();
        $users = array();

        foreach ($datosUsers as $userObject) 
        {
            $users[] = $userObject->toArray();
        }
    
        $datosUI['users'] = $users;
        $datosUI['breadcrumb'] = 'Usuarios';
        $datosUI['message'] = $message;
        return $this->view->render($response, $template, $datosUI);
    }
}