<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Entity\UserEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Exception\UserHadActionException;
use Solcre\Pokerclub\Exception\UserNotFoundException;
use Solcre\Pokerclub\Exception\UserInvalidException;
use Solcre\Pokerclub\Exception\IncompleteDataException;
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
        $users   = null;
        $datosUI = [];

        $datosUsers = $this->userService->fetchAll(
            array(
            ),
            array(
                'name'     => 'ASC',
                'lastname' => 'ASC'
            )
        );

        if (is_array($datosUsers)) {
            foreach ($datosUsers as $userObject) {
                $users[] = $userObject->toArray();
            }
        }

        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(parent::STATUS_CODE_200);
            $datosUI  = $users;
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idUser  = $args['iduser'];
        $datosUI = [];
        $user    = null;
        $status  = null;

        try {
            $user   = $this->userService->fetch(array('id' => $idUser));
            $status = parent::STATUS_CODE_200;
        } catch (UserNotFoundException $e) {
            $status = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $status = parent::STATUS_CODE_500;
        }

        if ($this->view instanceof JsonView) {
            $datosUI  = isset($user) ? $user->toArray() : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post    = $request->getParsedBody();
        $datosUI = [];
        $status  = null;

        if (is_array($post)) {
            try {
                $user   = $this->userService->add($post);
                $status = parent::STATUS_CODE_201;
            } catch (UserInvalidException $e) {
                $status = parent::STATUS_CODE_400;
            } catch (IncompleteDataException $e) {
                $status = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $status = parent::STATUS_CODE_500;
            }

            if ($this->view instanceof JsonView) {
                $datosUI  = isset($user) ? $user->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post    = $request->getParsedBody();
        $datosUI = [];
        $status  = null;

        if (is_array($post)) {
            try {
                $user   = $this->userService->update($post);
                $status = parent::STATUS_CODE_200;
            } catch (UserInvalidException $e) {
                $status = parent::STATUS_CODE_400;
            } catch (UserNotFoundException $e) {
                $status = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $status = parent::STATUS_CODE_500;
            }

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
        $status  = null;
        
        try {
            $delete = $this->userService->delete($idUser);
            $status = parent::STATUS_CODE_204;
        } catch (UserHadActionException $e) {
            $status = parent::STATUS_CODE_400;
        } catch (UserNotFoundException $e) {
            $status = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $status = parent::STATUS_CODE_500;
        }
        
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
