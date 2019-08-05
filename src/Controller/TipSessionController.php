<?php
namespace Solcre\lmsuy\Controller;

//use \Solcre\lmsuy\Service\DealerTipSessionService;
//use \Solcre\lmsuy\Service\ServiceTipSessionService;
//use \Solcre\lmsuy\Service\SessionService;
//use \Solcre\lmsuy\Entity\DealerTipSessionEntity;
//use \Solcre\lmsuy\Entity\ServiceTipSessionEntity;
//use \Solcre\lmsuy\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use \Solcre\lmsuy\View\TwigWrapperView;
//use \Solcre\lmsuy\Exception\DealerTipInvalidException;
//use \Solcre\lmsuy\Exception\ServiceTipInvalidException;

class TipSessionController
{
    protected $view;
    protected $dealerTipService;
    protected $serviceTipService;
    protected $sessionService;
    protected $entityManager;


    public function __construct($view, EntityManager $em)
    {
        $this->view              = $view;
        $this->dealerTipService  = new DealerTipSessionService($em);
        $this->serviceTipService = new ServiceTipSessionService($em);
        $this->sessionService    = new SessionService($em);
        $this->entityManager     = $em;
    }

    public function listAll($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $serviceTips = [];
        $dealerTips  = [];
        $datosUI = [];

        $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
        $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));
        
        if (isset($datosDealerTips)) {
            foreach ($datosDealerTips as $dealerTip) {
                $dealerTips[] = $dealerTip->toArray();
            }
        }

        if (isset($datosServiceTips)) {
            foreach ($datosServiceTips as $serviceTip) {
                $serviceTips[] = $serviceTip->toArray();
            }
        }        

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session          = $this->sessionService->fetchOne(array('id' => $idSession));

            $datosUI['session']                = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['serviceTips'] = $serviceTips;
            $datosUI['session']['dealerTips']  = $dealerTips;
            $datosUI['breadcrumb']             = 'Tips';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI['dealerTips']  = $dealerTips;
            $datosUI['serviceTips'] = $serviceTips;
            
            $response = $response->withStatus(200); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $datosUI = [];

        if (isset($args['idDealerTip'])) {
            $id        = $args['idDealerTip'];
            $dealerTip = $this->dealerTipService->fetchOne(array('id' => $id));


            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $idSession = $args['idSession'];
                $session   = $this->sessionService->fetchOne(array('id' => $idSession));

                $template = 'tipSession/listDealerTip.html.twig';
                $this->view->setTemplate($template);

                $datosUI['session']              = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['dealerTip'] = is_null($dealerTip) ? [] : $dealerTip->toArray();
                $datosUI['breadcrumb']           = 'Editar DealerTip';
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($dealerTip) ? [] : $dealerTip->toArray();
                $response = $response->withStatus(200); //magic number
            }

        } elseif (isset($args['idServiceTip'])) {
            $id         = $args['idServiceTip'];
            $serviceTip = $this->serviceTipService->fetchOne(array('id' => $id));

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $idSession = $args['idSession'];
                $session   = $this->sessionService->fetchOne(array('id' => $idSession));

                $template = 'tipSession/listServiceTip.html.twig';
                $this->view->setTemplate($template);

                $datosUI['session']               = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['serviceTip'] = is_null($serviceTip) ? [] : $serviceTip->toArray();
                $datosUI['breadcrumb']            = 'Editar ServiceTip';
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($serviceTip) ? [] : $serviceTip->toArray();
                $response = $response->withStatus(200); //magic number
            }

        }

        if ($this->view instanceof TwigWrapperView) {      
            $this->view->setTemplate($template); 
        } 

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $postDealerTip = [
            'id'        => $post['id'],
            'idSession' => $post['idSession'],
            'hour'      => $post['hour'],
            'dealerTip' => $post['dealerTip']
        ];

        $postServiceTip = [
            'id'         => $post['id'],
            'idSession'  => $post['idSession'],
            'hour'       => $post['hour'],
            'serviceTip' => $post['serviceTip']
        ];

        $datosUI = [];
        $message = [];

        if (is_array($post)) {
            try {
                $this->dealerTipService->add($postDealerTip);
                $message[] = 'El Dealer Tip se ingresó exitosamente.';
            // @codeCoverageIgnoreStart
            } catch (DealerTipInvalidException $e) {
                $message[] = $e->getMessage();
            }
            // @codeCoverageIgnoreEnd
            try {
                $this->serviceTipService->add($postServiceTip);
                $message[] = 'El Service Tip se ingresó exitosamente.';
            // @codeCoverageIgnoreStart
            } catch (ServiceTipInvalidException $e) {
                $message[] = $e->getMessage();
            // @codeCoverageIgnoreEnd
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'tipSession/listAll.html.twig';
                $this->view->setTemplate($template); 

                $datosUI = $this->loadSessionAndTips($post['idSession']);
                $datosUI['breadcrumb']             = 'Tips';
                $datosUI['message']                = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $idSession = $post['idSession'];
                $serviceTips = [];
                $dealerTips  = [];

                $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
                $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));
                
                if (isset($datosDealerTips)) {
                    foreach ($datosDealerTips as $dealerTip) {
                        $dealerTips[] = $dealerTip->toArray();
                    }
                }

                if (isset($datosServiceTips)) {
                    foreach ($datosServiceTips as $serviceTip) {
                        $serviceTips[] = $serviceTip->toArray();
                    }
                } 

                $datosUI['dealerTips']  = $dealerTips;
                $datosUI['serviceTips'] = $serviceTips;
                
                $response = $response->withStatus(200); //magic number
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    private function loadSessionAndTips($idSession)
    {
        $data = [];

        $session          = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
        $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));

        $dealerTips = [];
        $serviceTips = [];

        foreach ($datosDealerTips as $dealerTip) {
            $dealerTips[] = $dealerTip->toArray();
        }

        foreach ($datosServiceTips as $serviceTip) {
            $serviceTips[] = $serviceTip->toArray();
        }

        $data['session']                = $session->toArray();
        $data['session']['dealerTips']  = $dealerTips;
        $data['session']['serviceTips'] = $serviceTips;

        return $data;
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $session   = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosUI = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'tipSession/form.html.twig';
            $this->view->setTemplate($template); 

            $datosUI['session']    = $session->toArray();
            $datosUI['breadcrumb'] = 'Nuevo Tip';
        }

        return $this->view->render($request, $response, $datosUI);
    }


    public function update($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $message = [];

        if (is_array($post)) {
            if (isset($args['idDealerTip'])) {
                try {
                    $this->dealerTipService->update($post);
                    $message[] = 'El dealerTip se actualizó exitosamente.';    
                } catch (DealerTipInvalidException $e) {  //add exception
                    $message[] = $e->getMessage();
                // @codeCoverageIgnoreEnd
                }
            }

            if (isset($args['idServiceTip'])) {
                try {
                    $this->serviceTipService->update($post);
                    $message[] = 'El serviceTip se actualizó exitosamente.'; 
                } catch (ServiceTipInvalidException $e) {  //add exception
                    $message[] = $e->getMessage();
                // @codeCoverageIgnoreEnd
                }
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = $template  = 'tipSession/listAll.html.twig';
                $this->view->setTemplate($template);

                $datosUI = isset($post['idSession']) ? $this->loadSessionAndTips($post['idSession']) : [];
                $datosUI['breadcrumb']             = 'Tips';
                $datosUI['message']                = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $idSession = $post['idSession'];
                $serviceTips = [];
                $dealerTips  = [];

                $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
                $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));
                
                if (isset($datosDealerTips)) {
                    foreach ($datosDealerTips as $dealerTip) {
                        $dealerTips[] = $dealerTip->toArray();
                    }
                }

                if (isset($datosServiceTips)) {
                    foreach ($datosServiceTips as $serviceTip) {
                        $serviceTips[] = $serviceTip->toArray();
                    }
                } 

                $datosUI['dealerTips']  = $dealerTips;
                $datosUI['serviceTips'] = $serviceTips;
                
                $response = $response->withStatus(200); //magic number
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $get = $request->getQueryParams();
        $message = [];
        $datosUI = [];

        $template    = 'tipSession/listAll.html.twig';

        if (isset($args['idDealerTip'])) {
            $idTip =  $args['idDealerTip'];
            try {
                $this->dealerTipService->delete($idTip);
                $message[]   = 'El dealerTip se eliminó exitosamente';
            } catch (DealerTipInvalidException $e) {  //add exception
                $message[] = $e->getMessage();
            }
            
        } elseif (isset($args['idServiceTip'])) {
                $idServiceTip =  $args['idServiceTip'];
                try {
                    $this->serviceTipService->delete($idServiceTip);
                    $message[] = 'El serviceTip se eliminó exitosamente';
                } catch (DealerTipInvalidException $e) {  //add exception
                    $message[] = $e->getMessage();
                }
        }
        
        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'tipSession/listAll.html.twig';
            $this->view->setTemplate($template); 
            if (isset($get['idSession'])) {
                $idSession = $get['idSession'];
                $datosUI = $this->loadSessionAndTips($idSession);
            }
            $datosUI['breadcrumb']             = 'Tips';
            $datosUI['message']                = $message;      
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $idSession = $post['idSession'];
            $serviceTips = [];
            $dealerTips  = [];

            $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
            $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));
                
            if (isset($datosDealerTips)) {
                foreach ($datosDealerTips as $dealerTip) {
                    $dealerTips[] = $dealerTip->toArray();
                }
            }

            if (isset($datosServiceTips)) {
                foreach ($datosServiceTips as $serviceTip) {
                    $serviceTips[] = $serviceTip->toArray();
                }
            } 

            $datosUI['dealerTips']  = $dealerTips;
            $datosUI['serviceTips'] = $serviceTips;
                
            $response = $response->withStatus(200); //magic number
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
