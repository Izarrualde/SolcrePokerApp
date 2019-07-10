<?php
namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\BuyinSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Service\UserSessionService;
use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\BuyinSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\Exception\BuyinInvalidException;

class BuyinSessionController
{
    protected $view;
    protected $buyinSessionService;
    protected $sessionService;
    protected $userSessionService;
    protected $userService;

    public function __construct(Twig $view, EntityManager $em)
    {
        $this->view                = $view;
        $this->sessionService      = new SessionService($em);
        $this->userService         = new UserService($em);
        $this->userSessionService  = new UserSessionService($em);
        $this->buyinSessionService = new BuyinSessionService($em, $this->userSessionService);
    }

    public function listAll($request, $response, $args)
    {
        $idSession   = $args['idSession'];
        $template    = 'buyins.html.twig';
        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);
        $session     = $this->sessionService->fetchOne(array('id' => $idSession));
        $buyins      = array();
        $datosUI     = array();

        foreach ($datosBuyins as $buyinObject) {
            $buyins[] = $buyinObject->toArray();
        }

        $datosUI['sessions']           = $session->toArray();
        $datosUI['sessions']['buyins'] = $buyins;
        $datosUI['breadcrumb']        = 'Buyins';

        return $this->view->render($response, $template, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $id       = $args['idbuyin'];
        $buyin    = $this->buyinSessionService->fetchOne(array('id' => $id));
        $template = 'editBuyin.html.twig';
        $datosUI  = array();

        $datosUI['buyin']      = $buyin->toArray();
        $datosUI['breadcrumb'] = 'Editar Buyin';

        return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $datosUI = array();
        $message = array();

        if (is_array($post)) {
            try {
                $this->buyinSessionService->add($post);
                $message[] = 'El buyin se agregó exitosamente';
            } catch (BuyinInvalidException $e) {
                $message[] = $e->getMessage();
            }
            $template = 'buyins.html.twig';

            //extraigo datos de la bdd
            $buyins      = array();
            $session     = $this->sessionService->fetchOne(array('id' => $post['idSession']));
            $datosBuyins = $this->buyinSessionService->fetchAllBuyins($post['idSession']);
            if (is_array($datosBuyins)) {
                foreach ($datosBuyins as $buyin) {
                    $buyins[] = $buyin->toArray();
                }                
            }

            $datosUI['session']           = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['buyins'] = $buyins;
            $datosUI['breadcrumb']        = 'Buyins';
            $datosUI['message']           = $message;
        }

        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];

        $session           = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));

        
        $datosUI      = array();
        $usersSession = array();

        foreach ($datosUsersSession as $userSessionObject) {
            $usersSession[] = $userSessionObject->toArray();
        }

        $datosUI['session']                 = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb']              = 'Nuevo Buyin';
        $template                           = 'newbuyins.html.twig';

        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $post['idSession'];

        if (is_array($post)) {
            try {
                $this->buyinSessionService->update($post);
                $message[] = 'El buyin se actualizó exitosamente';
            // @codeCoverageIgnoreStart
            } catch (BuyinInvalidException $e) {
                $message[] = $e->getMessage();
            // @codeCoverageIgnoreEnd
            }
        }

        $template = 'buyins.html.twig';

        //BUSQUEDA DE DATOS PARA LA UI
        $session     = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($post['idSession']);

        $buyins  = array();
        $datosUI = array();

        foreach ($datosBuyins as $buyin) {
            $buyins[] = $buyin->toArray();
        }

        if ($session instanceof SessionEntity) {
            $datosUI['session'] = $session->toArray();
        }
        
        $datosUI['session']           = $session->toArray();
        $datosUI['session']['buyins'] = $buyins;
        $datosUI['breadcrumb']        = 'Buyins';
        $datosUI['message']           = $message;

        return $this->view->render($response, $template, $datosUI);
    }


    public function delete($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $id        = $args['idbuyin'];

        $this->buyinSessionService->delete($id);
        $message[]  = 'El buyin se eliminó exitosamente';
        $template = 'buyins.html.twig';

        //extraigo datos de la DB
        $datosUI = array();
        $buyins  = array();

        $session     = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);

        foreach ($datosBuyins as $buyin) {
            $buyins[] = $buyin->toArray();
        }

        $datosUI['session']           = $session->toArray();
        $datosUI['session']['buyins'] = $buyins;
        $datosUI['breadcrumb']        = 'Buyins';
        $datosUI['message']           = $message;

        return $this->view->render($response, $template, $datosUI);
    }
}
