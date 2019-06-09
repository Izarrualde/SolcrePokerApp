<?php
Namespace Solcre\lmsuy\Controller;

use Psr\Container\ContainerInterface;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;

class SessionController
{
    protected $view;
    protected $sessionService;

	public function __construct(\Slim\Views\Twig $view) {
        $this->view = $view;
    	$this->sessionService = new SessionService(new ConnectLmsuy_db()); 
    }

    public function listAll($request, $response, $args) {
    	$datosSessions = $this->sessionService->find();
    	$sessions = array();
        $datosUI = array();

    	foreach ($datosSessions as $sessionObject) 
		{
			$sessions[] = $sessionObject->toArray();
		}
        $datosUI['sessions'] = $sessions;
    	return $this->view->render($response, 'index.html.twig', $datosUI);
    }

    public function list($request, $response, $args) {
	    $idSession = $args['idSession'];
	    $session = $this->sessionService->findOne($idSession);
        $datosUI = array();
		$datosUI['session'] = $session->toArray();

    	return $this->view->render($response, 'editSession.html.twig', $datosUI);
    }

    public function add($request, $response, $args) {
    	$post = $request->getParsedBody();
    	$datosUI = array();
    	if (is_array($post))
    	{
    		$sessionObject = new SessionEntity(null, $post['date'], $post['title'], $post['description'], null /*photo*/, $post['seats'], null /*seatswaiting*/ , null /*reservewainting*/, $post['startTime'], $post['startTimeReal'], $post['endTime']);
	   	 	$this->sessionService->add($sessionObject);
   			$message = 'La sesión se agregó exitosamente';
            $template = 'index.html.twig';
			$datosSessions = $this->sessionService->find();
            $sessions = array();
            $datosUI = array();

            foreach ($datosSessions as $sessionObject) 
            {
                $sessions[] = $sessionObject->toArray();
            }
            $datosUI['sessions'] = $sessions;
		    $datosUI['message'] = $message;
    	}

    	return $this->view->render($response, $template, $datosUI);
    }
	public function form($request, $response, $args) {
    	$datosUI = array();
    	$template = 'newsession.html.twig';
    	return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args) {
    	$post = $request->getParsedBody();

	    //$idSession = $args['idSession'];
		$sessionObject = new SessionEntity($post['idSession'], $post['created_at'], $post['title'], $post['description'], null /*photo*/, $post['count_of_seats'], null /*seatswaiting*/ , null /*reservewainting*/, $post['created_at'], $post['real_start_at'], $post['end_at']);
	    $this->sessionService->update($sessionObject);
   		$message = 'La Sesión se actualizó exitosamente';
		$datosSessions = $this->sessionService->find();
        $sessions = array();
        $datosUI = array();

        foreach ($datosSessions as $sessionObject) 
        {
            $sessions[] = $sessionObject->toArray();
        }
        $datosUI['sessions'] = $sessions;
        $datosUI['message'] = $message;
    	
    	return $this->view->render($response, 'index.html.twig', $datosUI);
    }

    public function delete($request, $response, $args) {
	    $idSession = $args['idSession'];
	    //if (is_array($_GET))
	    $this->sessionService->delete($this->sessionService->findOne($idSession));
	    $message = 'La Sesión se eliminó exitosamente';
		$datosSessions = $this->sessionService->find();
    	$datosUI = array();
        $sessions = array();

        foreach ($datosSessions as $sessionObject) 
        {
             $sessions[] = $sessionObject->toArray();
        }
        $datosUI['sessions'] = $sessions;
        $datosUI['message'] = $message;

    	return $this->view->render($response, 'index.html.twig', $datosUI);
    }
}