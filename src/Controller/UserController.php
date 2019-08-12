<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Exception\UserHadActionException;
use Solcre\Pokerclub\Exception\UserNotFoundException;
use Solcre\Pokerclub\Exception\UserInvalidException;
use Exception;

class UserController
{
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_500 = 500;
    
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
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idUser  = $args['iduser'];
        $datosUI = null;

        $user = $this->userService->fetchOne(array('id' => $idUser));

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['user']       = isset($user) ? $user->toArray() :  [];
            $datosUI['breadcrumb'] = 'Editar Usuario';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            if (isset($user)) {
                $datosUI  = $user->toArray();
            } else {
                $response = $response->withStatus(self::STATUS_CODE_404);
            }
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
                $datosUI = isset($user) ? $user->toArray() : [];
                $response = $response->withStatus(self::STATUS_CODE_201);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $datosUI = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['breadcrumb'] = 'Nuevo Usuario';
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $datosUI = null;
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
                if (isset($user)) {
                    $datosUI  = $user->toArray();
                } else {
                    $response = $response->withStatus(self::STATUS_CODE_404);
                }
            }
        }
        
        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idUser = $args['iduser'];
        $datosUI = null;
        $users   = [];

        // JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(self::STATUS_CODE_204); //magic number
        }
        
        try {
            $delete = $this->userService->delete($idUser);
            $message[] = 'El usuario se eliminó exitosamente';
        } catch (UserHadActionException $e) {
            $response = $response->withStatus(self::STATUS_CODE_500);
            $message[] = $e->getMessage();
        } catch (UserNotFoundException $e) {
            $response = $response->withStatus(self::STATUS_CODE_404);
            $message[] = $e->getMessage();
        } catch (\Exception $e) {
            $response = $response->withStatus(self::STATUS_CODE_500);
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

        return $this->view->render($request, $response, $datosUI);
    }
}
