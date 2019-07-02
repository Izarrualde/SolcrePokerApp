<?php
namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\ComissionSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Entity\ComissionSessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
// use \Solcre\lmsuy\Twig\Func;
use \Solcre\lmsuy\Exception\ComissionInvalidException;

class ComissionSessionController
{
    protected $view;
    protected $comissionService;
    protected $sessionService;

    public function __construct(Twig $view, EntityManager$em)
    {
        $this->view                    = $view;
        $this->comissionService = new ComissionSessionService($em);
        $this->sessionService          = new SessionService($em);
    }

    public function listAll($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $template = 'comissions.html.twig';

        $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));
        $session         = $this->sessionService->fetchOne(array('id' => $idSession));

        $comissions = array();
        $datosUI    = array();

        foreach ($datosComissions as $comissionObject) {
            $comissions[] = $comissionObject->toArray();
        }

        $datosUI['session']               = $session->toArray();
        $datosUI['session']['comissions'] = $comissions;
        $datosUI['breadcrumb']            = 'Comissions';

        return $this->view->render($response, $template, $datosUI);
    }

 
    public function list($request, $response, $args)
    {
        $id        = $args['idcomission'];
        $idSession = $args['idSession'];

        $template = 'editComission.html.twig';

        $comission = $this->comissionService->fetchOne(array('id' => $id));
        $session   = $this->sessionService->fetchOne(array('id' => $idSession));

        $datosUI = array();
        
        $datosUI['session']              = $session->toArray();
        $datosUI['session']['comission'] = $comission->toArray();
        $datosUI['breadcrumb']           = 'Comisiones';

        return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $args['idSession'];

        $datosUI = array();
        $message = array();
        
        if (is_array($post)) {
            try {
                $this->comissionService->add($post);
                $message[] = 'la comission se ingresó exitosamente.';
            } catch (ComissionInvalidException $e) {
                $message[] = $e->getMessage();
            }
            
            $template = 'comissions.html.twig';

            //extraigo datos de la bdd
            $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));
            $session         = $this->sessionService->fetchOne(array('id' => $idSession));

            $comissions = array();
            
            foreach ($datosComissions as $comission) {
                $comissions[] = $comission->toArray();
            }

            $datosUI['session']               = $session->toArray();
            $datosUI['session']['comissions'] = $comissions;
            $datosUI['breadcrumb']            = 'Comisiones';
            $datosUI['message']               = $message;
        }

        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $session   = $this->sessionService->fetchOne(array('id' => $idSession));
        
        $template = 'newcomissions.html.twig';
        $datosUI  = array();

        $datosUI['session']    = $session->toArray();
        $datosUI['breadcrumb'] = 'Nueva Comision';

        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $id        = $args['idcomission'];
        $idSession = $post['idSession'];

        if (is_array($post)) {
            try {
                $this->comissionService->update($post);
                $message[] = 'la comission se actualizó exitosamente.';
            } catch (ComissionInvalidException $e) {
                $message[] = $e->getMessage();
            }
        }

        $template = 'comissions.html.twig';

        //BUSQUEDA DE DATOS PARA LA UI
        $idSession = $post['idSession'];

        $session         = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));

        $comissions = array();
        $datosUI    = array();

        foreach ($datosComissions as $comission) {
            $comissions[] = $comission->toArray();
        }

        $datosUI['session']               = $session->toArray();
        $datosUI['session']['comissions'] = $comissions;
        $datosUI['breadcrumb']            = 'Comisiones';
        $datosUI['message']               = $message;

        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args)
    {

        $id        = $args['idcomission'];
        $idSession = $args['idSession'];

        $this->comissionService->delete($id);

        $message[]  = 'La comisión se eliminó exitosamente';
        $template = 'comissions.html.twig';

        //extraigo datos para DB
        $datosUI    = array();
        $comissions = array();

        $session         = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));

        foreach ($datosComissions as $comission) {
            $comissions[] = $comission->toArray();
        }

        $datosUI['session']               = $session->toArray();
        $datosUI['session']['comissions'] = $comissions;
        $datosUI['breadcrumb']            = 'Comisiones';
        $datosUI['message']               = $message;

        return $this->view->render($response, $template, $datosUI);
    }
}
