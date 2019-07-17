<?php
namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\Exception\UserHadActionException;

class UserController
{
    protected $view;
    protected $userService;

    public function __construct($view, EntityManager $em)
    {
        $this->view        = $view;
        $this->userService = new UserService($em);
    }
    
    public function listAll($request, $response, $args)
    {

        $datosUsers = $this->userService->fetchAll(
            array(
            ),
            array(
                'name' => 'ASC',
                'lastname' => 'ASC'
            )
        );

        $users    = array();
        $datosUI  = array();
        $template = 'viewUsers.html.twig';

        foreach ($datosUsers as $userObject) {
            $users[] = $userObject->toArray();
        }
    
        $datosUI['users']      = $users;
        $datosUI['breadcrumb'] = 'Usuarios';

        return $this->view->render($response, $template, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idUser = $args['iduser'];
        $user   = $this->userService->fetchOne(array('id' => $idUser));

        $template = 'editPlayer.html.twig';

        $datosUI['user']       = $user->toArray();
        $datosUI['breadcrumb'] = 'Editar Usuario';

        return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post = $request->getParsedBody();

        $datosUI = array();
        $users = array();
        
        if (is_array($post)) {
            $this->userService->add($post);
            $template = 'viewUsers.html.twig';
            $message[]  = 'El usuario se agregó exitosamente.';

            // BUSQUEDA DE DATOS PARA LA UI

            $datosUsers = $this->userService->fetchAll();

            foreach ($datosUsers as $userObject) {
                $users[] = $userObject->toArray();
            }
    
            $datosUI['users']      = $users;
            $datosUI['breadcrumb'] = 'Usuarios';
            $datosUI['message']    = $message;
        }
        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $datosUI = array();

        $datosUI['breadcrumb'] = 'Nuevo Usuario';
        $template              = 'addUser.html.twig';

        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post = $request->getParsedBody();

        $this->userService->update($post);
        $message[]  = 'El usuario se actualizó exitosamente';
        $template = 'viewUsers.html.twig';

        // BUSQUEDA DE DATOS PARA LA UI
        $datosUI = array();
        $users   = array();

        $datosUsers = $this->userService->fetchAll();

        if (is_array($datosUsers)) {
            foreach ($datosUsers as $userObject) {
                $users[] = $userObject->toArray();
            }            
        }

        $datosUI['users']      = is_null($users) ? [] : $users;
        $datosUI['breadcrumb'] = 'Usuarios';
        $datosUI['message']    = $message;
        
        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idUser = $args['iduser'];

        try {
            $this->userService->delete($idUser);
            $message[] = 'El usuario se eliminó exitosamente';
        } catch (UserHadActionException $e) {
            $message[] = $e->getMessage();
        }

        $template = 'viewUsers.html.twig';

        // BUSQUEDA DE DATOS PARA LA UI
        $datosUI = array();
        $users   = array();
        $datosUsers = $this->userService->fetchAll();

        if (is_array($datosUsers)) {
            foreach ($datosUsers as $userObject) {
                $users[] = $userObject->toArray();
            }            
        }

        $datosUI['users']      = is_null($users) ? [] : $users;
        $datosUI['breadcrumb'] = 'Usuarios';
        $datosUI['message']    = $message;

        return $this->view->render($response, $template, $datosUI);
    }
}
