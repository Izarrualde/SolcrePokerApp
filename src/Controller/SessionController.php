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

	public function __construct(Twig $view, EntityManager $em) {
        $this->view = $view;
    	$this->sessionService = new SessionService($em); 
    }

    public function listAll($request, $response, $args) {
    	$datosSessions = $this->sessionService->fetchAll();
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
	    $session = $this->sessionService->fetchOne($idSession);
        $datosUI = array();
		$datosUI['session'] = $session->toArray();

    	return $this->view->render($response, 'editSession.html.twig', $datosUI);
    }

    public function add($request, $response, $args) {
    	$post = $request->getParsedBody();
    	$datosUI = array();
    	if (is_array($post))
    	{
    		$this->sessionService->add($post);
   			$message = 'La sesión se agregó exitosamente';
            $template = 'index.html.twig';
			$datosSessions = $this->sessionService->fetchAll();
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
	    $this->sessionService->update($post);
   		$message = 'La Sesión se actualizó exitosamente';
		$datosSessions = $this->sessionService->fetchAll();
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
	    $this->sessionService->delete($idSession);
	    $message = 'La Sesión se eliminó exitosamente';
		$datosSessions = $this->sessionService->fetchAll();
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