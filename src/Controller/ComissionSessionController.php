<?php
Namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\ComissionSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Entity\ComissionSessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;

class ComissionSessionController
{
    protected $view;
    protected $comissionSessionService;
    protected $sessionService;

	public function __construct(Twig $view, EntityManager$em) {
        $this->view = $view;
    	$this->comissionSessionService = new ComissionSessionService($em);
        $this->sessionService = new SessionService($em); 
    }

    public function listAll($request, $response, $args) {
        $idSession = $args['idSession'];
        $template = 'comissions.html.twig';
    	//$datosComissions = $this->comissionSessionService->find($idSession);
        //$session = $this->sessionService->findOne($idSession);

        $datosComissions = $this->comissionSessionService->fetchAll(array('session' => $idSession));

        $session = $this->sessionService->fetchOne(array('id' => $idSession));

        $comissions = array();
        $datosUI = array();

    	foreach ($datosComissions as $comissionObject) 
		{
			$comissions[] = $comissionObject->toArray();
		}

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['comissions'] = $comissions;
        $datosUI['breadcrumb'] = 'Comissions';

    	return $this->view->render($response, $template, $datosUI);
    }

 
    public function list($request, $response, $args) {
	    $id = $args['idcomission'];
        $idSession = $args['idSession'];
        $template = 'editComission.html.twig';
	    // $comission = $this->comissionSessionService->findOne($id);
        // $session = $this->sessionService->findOne($idSession);

        $comission = $this->comissionSessionService->fetchOne(array('id' => $id));

        $session = $this->sessionService->fetchOne(array('id' => $idSession));

        $datosUI = array();
        
        $datosUI['session'] = $session->toArray();
		$datosUI['session']['comission'] = $comission->toArray();
        $datosUI['breadcrumb'] = 'Comisiones';
    	return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args) {
        $post = $request->getParsedBody();
        $idSession = $args['idSession'];

        $datosUI = array();
        
        if (is_array($post))
        {
            //$comissionObject = new ComissionSession($post['id'], $post['idSession'], $post['hour'], $post['comission']);

            $this->comissionSessionService->add($post); 
            $template = 'comissions.html.twig';
            $message = 'la comission se ingresó exitosamente.';

            //extraigo datos de la bdd
            $datosComissions = $this->comissionSessionService->fetchAll(array('session' => $idSession));
            // como sabe fetchAll que lo que le paso es una idSession.

            $comissions = array();
            $datosUI = array();
            $session = $this->sessionService->fetchOne(array('id' => $idSession));


            foreach ($datosComissions as $comission)
            {
                $comissions[] = $comission->toArray(); 
            }

            $datosUI['session'] = $session->toArray();
            $datosUI['session']['comissions'] = $comissions;
            $datosUI['breadcrumb'] = 'Comisiones';
            $datosUI['message'] = $message;
        }
        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args) 
    {
        $idSession = $args['idSession'];
        $session = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosUI = array();
        $datosUI['session'] = $session->toArray();

        $datosUI['breadcrumb'] = 'Nueva Comision';
        $template = 'newcomissions.html.twig';

        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args) {
        $post = $request->getParsedBody();

        $id = $args['idcomission'];
        $idSession = $post['idSession'];

        // $idSession = $args['idSession'];
        // $comissionObject = new ComissionSession($post['id'], $post['idSession'], $post['hour'], $post['comission']);
        $this->comissionSessionService->update($post);
        $message = 'La comisión se actualizó exitosamente';
        $template = 'comissions.html.twig';

        //extraigo datos de la BD
        $idSession = $post['idSession'];
        $session = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosComissions = $this->comissionSessionService->fetchAll(array('session' => $idSession));
        $comissions = array();
        $datosUI = array();
        foreach ($datosComissions as $comission) {
            $comissions[] = $comission->toArray(); 
        }

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['comissions'] = $comissions;
        $datosUI['breadcrumb'] = 'Comisiones';
        $datosUI['message'] = $message;

        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args) {
        //$idSession = $args['idSession'];
        $id = $args['idcomission'];
        $idSession = $args['idSession'];
        //if (is_array($_GET))
        // $this->comissionSessionService->delete($this->comissionSessionService->findOne($id));
        $this->comissionSessionService->delete($id);
        $message = 'La comisión se eliminó exitosamente';
        $template = 'comissions.html.twig';
        $datosUI = array();

        //extraigo datos para DB
        $comissions = array();
        $session = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosComissions = $this->comissionSessionService->fetchAll(array('session' => $idSession));

        foreach ($datosComissions as $comission)  {
            $comissions[] = $comission->toArray(); 
        }

        $datosUI = array();

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['comissions'] = $comissions;
        $datosUI['breadcrumb'] = 'Comisiones';
        $datosUI['message'] = $message;

        return $this->view->render($response, $template, $datosUI);
    }
}