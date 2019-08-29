<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\BuyinSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Service\UserSessionService;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Exception\BuyinInvalidException;
use Solcre\Pokerclub\Exception\BuyinNotFoundException;
use Exception;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;

class BuyinSessionController extends BaseController
{
    protected $view;
    protected $buyinSessionService;
    protected $sessionService;
    protected $userSessionService;
    protected $userService;

    public function __construct(View $view, EntityManager $em)
    {
        $this->view                = $view;
        $this->sessionService      = new SessionService($em);
        $this->userService         = new UserService($em);
        $this->userSessionService  = new UserSessionService($em);
        $this->buyinSessionService = new BuyinSessionService($em, $this->userSessionService);
    }

    public function listAll($request, $response, $args)
        {
        $idSession      = $args['idSession'];
        $buyins         = null;
        $datosUI        = null;
        $message        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));   
            $status  = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
                $message = $e->getMessage();
                $status  = parent::STATUS_CODE_404;
        }
        
        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);
        if (is_array($datosBuyins)) {
            foreach ($datosBuyins as $buyinObject) {
                $buyins[] = $buyinObject->toArray();
            }
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if ($status == $expectedStatus) {
                $datosUI['session']           = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['buyins'] = $buyins;
            }

            $datosUI['breadcrumb']        = 'Buyins';

            if (isset($message)) {
                $datosUI['message'] = $message;
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = $buyins;
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $id        = $args['idbuyin'];
        $idSession = $args['idSession'];
        $datosUI        = null;
        $comission      = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;
        $message        = null;

        try {
            $buyin    = $this->buyinSessionService->fetch(array('id' => $id));
            $status = parent::STATUS_CODE_200;             
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $status = parent::STATUS_CODE_404;
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if ($status == $expectedStatus) {
                $session = $this->sessionService->fetch(array('id' => $idSession));
                $datosUI['session']          = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['buyin'] = is_null($buyin) ? [] : $buyin->toArray();
            }

            $datosUI['breadcrumb'] = 'Editar Buyin';
            
            if (isset($message)) {
                $datosUI['message'] = $message;
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = is_null($buyin) ? [] : $buyin->toArray();
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }


    public function loadData($idSession, $message)
    {
        $data = null;

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'buyinSession/listAll.html.twig';
            $this->view->setTemplate($template);
                
            try {
               $session = $this->sessionService->fetch(array('id' => $idSession)); 
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
            }
            
            $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);
            $buyins = [];

            if (is_array($datosBuyins)) {
                foreach ($datosBuyins as $buyin) {
                    $buyins[] = $buyin->toArray();
                }
            }

            $data['session']           = isset($session) ? $session->toArray() : [];
            $data['session']['buyins'] = $buyins;
            $data['breadcrumb']        = 'Buyins';
            $data['message']           = $message;
        }

        return $data;
    }

    public function add($request, $response, $args)
    {
        $post           = $request->getParsedBody();
        $idSession      = $args['idSession'];
        $datosUI        = null;
        $message        = null;
        $status         = null;

        if (is_array($post)) {
            try {
                $buyin = $this->buyinSessionService->add($post);
                $message[] = 'El buyin se agregó exitosamente';
                $status = parent::STATUS_CODE_201;
            } catch (BuyinInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }
            
            $datosUI  = $this->view instanceof JsonView ? 
                (isset($buyin) ? $buyin->toArray() : null) : 
                $this->loadData($idSession, $message);

            if ($this->view instanceof JsonView) {
                $response = $response->withStatus($status);
            }

        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession    = $args['idSession'];
        $datosUI      = [];
        $usersSession = [];
        $message      = null;


        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
                $message = $e->getMessage();
                $status  = parent::STATUS_CODE_404;
        }
        
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if (isset($datosUsersSession)) {
                foreach ($datosUsersSession as $userSessionObject) {
                    $usersSession[] = $userSessionObject->toArray();
                }
            }

            $datosUI['session']                 = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['usersSession'] = $usersSession;
            $datosUI['breadcrumb']              = 'Nuevo Buyin';
            
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
        $datosUI        = null;
        $message        = null;
        $status         = null;

        if (is_array($post)) {
            try {
                $buyin = $this->buyinSessionService->update($post);
                $message[] = 'El buyin se actualizó exitosamente';
                $status    = parent::STATUS_CODE_200;
            } catch (BuyinInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (BuyinNotFoundException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }
            
            $datosUI  = $this->view instanceof JsonView ? 
                (isset($buyin) ? $buyin->toArray() : null) : 
                $this->loadData($idSession, $message);

            if ($this->view instanceof JsonView) {
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }


    public function delete($request, $response, $args)
    {
        $idSession      = $args['idSession'];
        $id             = $args['idbuyin'];
        $datosUI        = null;
        $message        = null;
        $status         = null;

        try {
            $delete = $this->buyinSessionService->delete($id);
            $message[]  = 'El buyin se eliminó exitosamente';
            $status    = parent::STATUS_CODE_204;
        } catch (BuyinNotFoundException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_500;
        }
        
        if ($this->view instanceof TwigWrapperView) {
            $datosUI  = $this->loadData($idSession, $message);
        }
        
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
