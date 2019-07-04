<?php
namespace Solcre\lmsuy\Controller;

use Psr\Container\ContainerInterface;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
use \Solcre\lmsuy\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;

class SessionController
{
    protected $view;
    protected $sessionService;

    public function __construct(Twig $view, EntityManager $em)
    {
        $this->view           = $view;
        $this->sessionService = new SessionService($em);
    }

    public function listAll($request, $response, $args)
    {

        $datosSessions = $this->sessionService->fetchAll();

        $sessions = array();
        $datosUI  = array();

        foreach ($datosSessions as $sessionObject) {
            $sessions[] = $sessionObject->toArray();
        }

        $datosUI['sessions'] = $sessions;


        return $this->view->render($response, 'index.html.twig', $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idSession = $args['idSession'];
        
        $session = $this->sessionService->fetchOne(array('id' => $idSession));

        $datosUI = array();

        $datosUI['session']    = $session->toArray();
        $datosUI['breadcrumb'] = 'Editar Sesión';

        return $this->view->render($response, 'editSession.html.twig', $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post = $request->getParsedBody();

        $datosUI = array();
        $template = 'index.html.twig';
        if (is_array($post)) {
            $this->sessionService->add($post);
            
            $message[] = 'La sesión se agregó exitosamente';
            
            $datosSessions = $this->sessionService->fetchAll();

            $sessions = array();
            $datosUI  = array();

            foreach ($datosSessions as $sessionObject) {
                $sessions[] = $sessionObject->toArray();
            }

            $datosUI['sessions'] = $sessions;
            $datosUI['message']  = $message;
        }

        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $datosUI  = [];
        $template = 'newsession.html.twig';

        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post = $request->getParsedBody();

        $this->sessionService->update($post);
        $message[]       = 'La Sesión se actualizó exitosamente';
        $datosSessions = $this->sessionService->fetchAll();

        $sessions = array();
        $datosUI  = array();

        foreach ($datosSessions as $sessionObject) {
            $sessions[] = $sessionObject->toArray();
        }

        $datosUI['sessions'] = $sessions;
        $datosUI['message']  = $message;
        
        return $this->view->render($response, 'index.html.twig', $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idSession     = $args['idSession'];
        $this->sessionService->delete($idSession);
        $message[]       = 'La Sesión se eliminó exitosamente';
        $datosSessions = $this->sessionService->fetchAll();

        $datosUI  = array();
        $sessions = array();

        foreach ($datosSessions as $sessionObject) {
             $sessions[] = $sessionObject->toArray();
        }
        
        $datosUI['sessions'] = $sessions;
        $datosUI['message']  = $message;

        return $this->view->render($response, 'index.html.twig', $datosUI);
    }
}
