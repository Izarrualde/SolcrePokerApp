<?php
namespace Solcre\lmsuy\Controller;

use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\pokerclub\Service\SessionService;


class SessionController
{
    protected $view;
    protected $sessionService;

    public function __construct($view, EntityManager $em)
    {
        $this->view           = $view;
        $this->sessionService = new SessionService($em);
    }

    public function listAll($request, $response, $args)
    {
        $sessions = [];
        $datosUI  = [];

        $datosSessions = $this->sessionService->fetchAll();

        if (isset($datosSessions)) {
            foreach ($datosSessions as $sessionObject) {
                $sessions[] = $sessionObject->toArray();
            }       
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['sessions'] = $sessions;
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = $sessions;
            $response = $response->withStatus(200); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $session = $this->sessionService->fetchOne(array('id' => $idSession));

        $datosUI = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['session']    = is_null($session) ? [] : $session->toArray();
            $datosUI['breadcrumb'] = 'Editar Sesión';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = is_null($session) ? [] : $session->toArray();
            $response = $response->withStatus(200); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $datosUI = [];

        if (is_array($post)) {
            $session = $this->sessionService->add($post);
            
            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'session/listAll.html.twig';
                $this->view->setTemplate($template);

                if ($session instanceof SessionEntity) {
                    $message[] = 'La sesión se agregó exitosamente';
                    $datosSessions = $this->sessionService->fetchAll();
                    $sessions = [];

                    if (isset($datosSessions)) {
                        foreach ($datosSessions as $sessionObject) {
                            $sessions[] = $sessionObject->toArray();
                        }          
                    }

                    $datosUI['sessions'] = $sessions;
                    $datosUI['message']  = $message;
                }
            } 

            // JsonView
            if ($this->view instanceof JsonView) {
                if ($session instanceof SessionEntity) {
                    $datosUI = $session->toArray();
                    $response = $response->withStatus(201); //magic number
                }
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $datosUI  = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'session/form.html.twig';
            $this->view->setTemplate($template);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $datosUI  = [];
        
        $session = $this->sessionService->update($post);
      
        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'session/listAll.html.twig';
            $this->view->setTemplate($template);
            $message[]       = 'La Sesión se actualizó exitosamente';
            $datosSessions = $this->sessionService->fetchAll();
            $sessions = [];

            if (isset($datosSessions)) {
                foreach ($datosSessions as $sessionObject) {
                    $sessions[] = $sessionObject->toArray();
                }    
            }

            $datosUI['sessions'] = $sessions;
            $datosUI['message']  = $message;
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = is_null($session) ? [] : $session->toArray();
            $response = $response->withStatus(200); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $idSession     = $args['idSession'];
        $delete = $this->sessionService->delete($idSession);
        $datosUI  = [];
var_dump($delete); die();
        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $message[]       = 'La Sesión se eliminó exitosamente';
            $datosSessions = $this->sessionService->fetchAll();
            $template = 'session/listAll.html.twig';
            $this->view->setTemplate($template);

            $sessions = [];

            if (isset($datosSessions)) { 
                foreach ($datosSessions as $sessionObject) {
                     $sessions[] = $sessionObject->toArray();
                }
            }
            
            $datosUI['sessions'] = $sessions;
            $datosUI['message']  = $message;
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(204); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function calculatePoints($request, $response, $args)
    { 
        $idSession = $args['idSession'];
        $this->sessionService->calculateRakeback($idSession);

        $message[]       = 'Puntos agregados exitosamente';
        $datosSessions = $this->sessionService->fetchAll();

        $datosUI  = [];
        $sessions = [];

        if (isset($datosSessions)) {
            foreach ($datosSessions as $sessionObject) {
                 $sessions[] = $sessionObject->toArray();
            }            
        }
        
        $datosUI['sessions'] = $sessions;
        $datosUI['message']  = $message;

        return $this->view->render($request, $response, $datosUI);
    } 
}
