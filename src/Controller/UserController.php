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

class UserController extends BaseController
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
        $users          = null;
        $datosUI        = [];

        $datosUsers = $this->userService->fetchAll(
            array(
            ),
            array(
                'name' => 'ASC',
                'lastname' => 'ASC'
            )
        );

        if (is_array($datosUsers)) {
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
            $response = $response->withStatus(parent::STATUS_CODE_200);
            $datosUI = $users;
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idUser         = $args['iduser'];
        $datosUI        = [];
        $user           = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;
        $message        = null;

        try {
            $user = $this->userService->fetch(array('id' => $idUser));
            $status  = parent::STATUS_CODE_200;  
        } catch (UserNotFoundException $e) {
            $message[] = $e->getMessage();
            $status  = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status  = parent::STATUS_CODE_500;
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if ($status == $expectedStatus) {
                $datosUI['user'] = isset($user) ? $user->toArray() : [];
            }
            
            $datosUI['breadcrumb'] = 'Editar Usuario';
           
            if (isset($message)) {
                $datosUI['message'] = $message;
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = isset($user) ? $user->toArray() : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function loadData($message)
    {
        $data = [];
        
        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'user/listAll.html.twig';
            $this->view->setTemplate($template);

            $datosUsers = $this->userService->fetchAll();
            $users = [];

            if (is_array($datosUsers)) {
                foreach ($datosUsers as $userObject) {
                    $users[] = $userObject->toArray();
                }
            }
        
            $data['users']      = $users;
            $data['breadcrumb'] = 'Usuarios';
            $data['message']    = $message;
        }

        return $data;
    }

    public function add($request, $response, $args)
    {
        $post    = $request->getParsedBody();
        $datosUI = [];
        $message = null;
        $status  = null;
        
        if (is_array($post)) {
            try {
                $user      = $this->userService->add($post);
                $message[] = 'El usuario se agregó exitosamente.';
                $status    = parent::STATUS_CODE_201;
            } catch (UserInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $datosUI  =  $this->loadData($message);
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI  = isset($user) ? $user->toArray() : [];
                $response = $response->withStatus($status);
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
        $post    = $request->getParsedBody();
        $datosUI = [];
        $message = null;
        $status  = null;

        if (is_array($post)) {
            try {
                $user      = $this->userService->update($post);
                $message[] = 'El usuario se actualizó exitosamente';
                $status    = parent::STATUS_CODE_200;
            } catch (UserInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (UserNotFoundException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $datosUI  =  $this->loadData($message);
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI  = isset($user) ? $user->toArray() : [];
                $response = $response->withStatus($status);
            } 
        }
        
        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idUser  = $args['iduser'];
        $datosUI = null;
        $message = null;
        $status  = null;
        
        try {
            $delete    = $this->userService->delete($idUser);
            $message[] = 'El usuario se eliminó exitosamente';
            $status    = parent::STATUS_CODE_204;
        } catch (UserHadActionException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_400;
        } catch (UserNotFoundException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_500;
        }

        if ($this->view instanceof TwigWrapperView) {
            $datosUI  = $this->loadData($message);            
        }
        
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
