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

    	$datosBuyins = $this->buyinSessionService->fetchAllBuyins( $idSession);

        $session = $this->sessionService->fetchOne(array('id' => $idSession));

        $buyins = array(); 
    	$datosUI = array();

        foreach ($datosBuyins as $buyinObject) 
		{
			$buyins[] = $buyinObject->toArray();
		}

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['buyins'] = $buyins;
        $datosUI['breadcrumb'] = 'Buyins';

    	return $this->view->render($response, $template, $datosUI);
    }

    public function list($request, $response, $args) {
	    $id = $args['idbuyin'];
	    $buyin = $this->buyinSessionService->fetchOne(array('id' => $id));

        $template = 'editBuyin.html.twig';
        $datosUI = array();
		$datosUI['buyin'] = $buyin->toArray(); //entrego un array
        $datosUI['breadcrumb'] = 'Editar Buyin';

    	return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args) {
        $post = $request->getParsedBody();

        $datosUI = array();
        if (is_array($post))
        {

            $this->buyinSessionService->add($post);  
            $template = 'buyins.html.twig';
            $message = 'El buyin se agregó exitosamente';
            $datosBuyins = $this->buyinSessionService->fetchAllBuyins($post['idSession']);

        //extraigo datos de la bdd

            $buyins = array();
            $session = $this->sessionService->fetchOne(array('id' => $post['idSession']));

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
        $session = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
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
        $idSession = $post['idSession'];

        $this->buyinSessionService->update($post);
        $message = 'El buyin se actualizó exitosamente';
        $template = 'buyins.html.twig';

        //extraigo datos de la BD
        $session = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($post['idSession']);
        $buyins = array();
        $datosUI = array();
        foreach ($datosBuyins as $buyin) {
            $buyins[] = $buyin->toArray(); 
        }
        if ($session instanceof SessionEntity){
           $datosUI['session'] = $session->toArray(); 
        }
        
        $datosUI['session']['buyins'] = $buyins;
        $datosUI['breadcrumb'] = 'Buyins';
        $datosUI['message'] = $message;

        return $this->view->render($response, $template, $datosUI);
    }


    public function delete($request, $response, $args) {
        $idSession = $args['idSession'];
        $id = $args['idbuyin'];

        $this->buyinSessionService->delete($id);
        $message = 'El buyin se eliminó exitosamente';
        $template = 'buyins.html.twig';
        $datosUI = array();

        //extraigo datos de la DB
        $session = $this->sessionService->fetchOne(array('id' => $idSession));

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