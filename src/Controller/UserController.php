<?php
namespace Solcre\lmsuy\Controller;

//use \Solcre\lmsuy\Service\UserService;
//use \Solcre\lmsuy\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use \Solcre\lmsuy\View\TwigWrapperView;
//use Solcre\lmsuy\Exception\UserHadActionException;

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
        $users   = [];
        $datosUI = [];

        $datosUsers = $this->userService->fetchAll(
            array(
            ),
            array(
                'name' => 'ASC',
                'lastname' => 'ASC'
            )
        );

        if (isset($datosUsers)) {
            foreach ($datosUsers as $userObject) {
                $users[] = $userObject->toArray();
            }
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['users']      = $users;
            $datosUI['breadcrumb'] = 'Usuarios';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = $users;
            $response = $response->withStatus(200); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idUser  = $args['iduser'];
        $datosUI = [];

        $user   = $this->userService->fetchOne(array('id' => $idUser));

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['user']       = is_null($user) ? [] : $user->toArray();
            $datosUI['breadcrumb'] = 'Editar Usuario';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = is_null($user) ? [] : $user->toArray();
            $response = $response->withStatus(200); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $datosUI = [];
        $message = [];
        
        if (is_array($post)) {
            try {
                $user = $this->userService->add($post);
                $message[]  = 'El usuario se agregó exitosamente.';
            } catch (UserInvalidException $e) {
                $message[] = $e->getMessage();
            }

            // TwigWrapperView 
            if ($this->view instanceof TwigWrapperView) {
                $template = 'user/listAll.html.twig';
                $this->view->setTemplate($template);

                // BUSQUEDA DE DATOS PARA LA UI
                $datosUsers = $this->userService->fetchAll();

                if (isset($datosUsers)) {
                    foreach ($datosUsers as $userObject) {
                        $users[] = $userObject->toArray();
                    }                    
                }
        
                $datosUI['users']      = $users;
                $datosUI['breadcrumb'] = 'Usuarios';
                $datosUI['message']    = $message;
            }    

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($user) ? [] : $user->toArray();
                $response = $response->withStatus(201); //magic number
            }

        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $datosUI = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'user/form.html.twig';
            $this->view->setTemplate($template); 
            $datosUI['breadcrumb'] = 'Nuevo Usuario';
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $datosUI = [];
        $users   = [];
        $message = [];

        if (is_array($post)) {
            try {
                $user = $this->userService->update($post);
                $message[]  = 'El usuario se actualizó exitosamente';
            // @codeCoverageIgnoreStart
            } catch (UserInvalidException $e) {
                 $message[] = $e->getMessage();
            // @codeCoverageIgnoreEnd
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'user/listAll.html.twig';
                $this->view->setTemplate($template); 

                // BUSQUEDA DE DATOS PARA LA UI
                $datosUsers = $this->userService->fetchAll();

                if (isset($datosUsers)) {
                    foreach ($datosUsers as $userObject) {
                        $users[] = $userObject->toArray();
                    }            
                }

                $datosUI['users']      = $users;
                $datosUI['breadcrumb'] = 'Usuarios';
                $datosUI['message']    = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($users) ? [] : $users->toArray();
                $response = $response->withStatus(200); //magic number
            }
        }
        
        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idUser = $args['iduser'];
        $datosUI = [];
        $users   = [];

        try {
            $delete = $this->userService->delete($idUser); 
            $message[] = 'El usuario se eliminó exitosamente';   
        } catch (UserHadActionException $e) {
            $message[] = $e->getMessage();
        }
        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            
            $template = 'user/listAll.html.twig';
            $this->view->setTemplate($template);

            // BUSQUEDA DE DATOS PARA LA UI
            $datosUsers = $this->userService->fetchAll();

            if (isset($datosUsers)) {
                foreach ($datosUsers as $userObject) {
                    $users[] = $userObject->toArray();
                }            
            }

            $datosUI['users']      = $users;
            $datosUI['breadcrumb'] = 'Usuarios';
            $datosUI['message']    = $message;
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(204); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
