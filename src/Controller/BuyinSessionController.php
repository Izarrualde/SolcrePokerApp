<?php
Namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\BuyinSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Service\UserSessionService;
use \Solcre\lmsuy\Service\UserService;
use \Solcre\lmsuy\Entity\BuyinSessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;

class BuyinSessionController
{
    protected $view;
    protected $buyinSessionService;
    protected $sessionService;
    protected $UserSessionService;
    protected $UserService;

	public function __construct(Twig $view, EntityManager $em) {
        $this->view = $view;
    	$this->sessionService = new SessionService($em);
        $this->userService = new UserService($em); 
        $this->userSessionService = new UserSessionService($em);
        $this->buyinSessionService = new BuyinSessionService($em);
    }

    public function listAll($request, $response, $args) {
        $idSession = $args['idSession'];
        $template = 'buyins.html.twig';
    	$datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);
        $session = $this->sessionService->fetchOne($idSession);
        $buyins = array(); //dejo cada buyin como un array para entregarlo a datosUI en ese formato
    	$datosUI = array();
        foreach ($datosBuyins as $buyinObject) 
		{
			$buyins[] = $buyinObject->toArray();
		}
        //buyins es un array de arrays.
        $datosUI['session'] = $session->toArray();
        $datosUI['session']['buyins'] = $buyins;
        $datosUI['breadcrumb'] = 'Buyins';

    	return $this->view->render($response, $template, $datosUI);
    }

    public function list($request, $response, $args) {
	    $id = $args['idbuyin'];
	    $buyin = $this->buyinSessionService->fetchOne($id);

        $template = 'editBuyin.html.twig';
        $datosUI = array();
		$datosUI['buyin'] = $buyin->toArray(); //entrego un array
        $datosUI['breadcrumb'] = 'Editar Buyin';

    	return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args) {
        $buyin = $request->getParsedBody();
        $datosUI = array();
        if (is_array($buyin))
        {
            //$buyinObject = new BuyinSession($buyin['id'], $buyin['idSession'], $buyin['idUserSession'], $buyin['amountCash'], $buyin['amountCredit'], '2', date('c'), $buyin['approved']);

            $this->buyinSessionService->add($post);  
            $template = 'buyins.html.twig';
            $message = 'El buyin se agregó exitosamente';
            $datosBuyins = $this->buyinSessionService->fetchAllBuyins($buyin['idSession']);

        //extraigo datos de la bdd

            $buyins = array();
            $session = $this->sessionService->fetchOne($buyin['idSession']);

            foreach ($datosBuyins as $buyin)
            {
                $buyins[] = $buyin->toArray(); 
            }

            $datosUI['session'] = $session->toArray();
            $datosUI['session']['buyins'] = $buyins;
            $datosUI['breadcrumb'] = 'Buyins';
            $datosUI['message'] = $message;
        }

        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args) 
    {
        $idSession = $args['idSession'];
        $session = $this->sessionService->fetchOne($idSession);
        $datosUsersSession = $this->userSessionService->fetchAll($idSession);
        $usersSession = array();
        foreach ($datosUsersSession as $userSessionObject) {
            $usersSession[] = $userSessionObject->toArray();
        }
        $datosUI = array();
        $datosUI['session'] = $session->toArray();
        $datosUI['session']['usersSession'] = $usersSession;
        $datosUI['breadcrumb'] = 'Nuevo Buyin';
        $template = 'newbuyins.html.twig';
        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args) {
        $post = $request->getParsedBody();

        // $idSession = $args['idSession'];
        // $buyinObject = new BuyinSession($post['id'], $post['idSession'], $post['idUserSession'], $post['amountCash'], $post['amountCredit'], '2', date('c'), $post['approved']);
        $this->buyinSessionService->update($post);
        $message = 'El buyin se actualizó exitosamente';
        $template = 'buyins.html.twig';

        //extraigo datos de la BD
        $session = $this->sessionService->fetchOne($post['idSession']);
        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($post['idSession']);
        $buyins = array();
        $datosUI = array();
        foreach ($datosBuyins as $buyin) {
            $buyins[] = $buyin->toArray(); 
        }

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['buyins'] = $buyins;
        $datosUI['breadcrumb'] = 'Buyins';
        $datosUI['message'] = $message;

        return $this->view->render($response, $template, $datosUI);
    }


    public function delete($request, $response, $args) {
        $idSession = $args['idSession'];
        $id = $args['idbuyin'];
        //if (is_array($_GET))
        //$this->buyinSessionService->delete($this->buyinSessionService->findOne($id));
        $this->buyinSessionService->delete($id);
        $message = 'El buyin se eliminó exitosamente';
        $template = 'buyins.html.twig';
        $datosUI = array();

        //extraigo datos de la DB
        $session = $this->sessionService->fetchOne($idSession);

        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);
        $buyins = array();

        foreach ($datosBuyins as $buyin)  {
            $buyins[] = $buyin->toArray(); 
        }

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['buyins'] = $buyins;
        $datosUI['breadcrumb'] = 'Buyins';
        $datosUI['message'] = $message;

        return $this->view->render($response, $template, $datosUI);
    }
}