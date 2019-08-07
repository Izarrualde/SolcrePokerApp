<?php
namespace Solcre\lmsuy\Controller;

use \Solcre\Pokerclub\Service\DealerTipSessionService;
use \Solcre\Pokerclub\Service\ServiceTipSessionService;
use \Solcre\Pokerclub\Service\SessionService;
use \Solcre\Pokerclub\Entity\DealerTipSessionEntity;
use \Solcre\Pokerclub\Entity\ServiceTipSessionEntity;
use \Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\View\JsonView;
use \Solcre\lmsuy\View\TwigWrapperView;
use \Solcre\Pokerclub\Exception\DealerTipInvalidException;
use \Solcre\Pokerclub\Exception\ServiceTipInvalidException;
use \Solcre\Pokerclub\Exception\DealerTipNotFoundException;
use \Solcre\Pokerclub\Exception\ServiceTipNotFoundException;
use Exception;

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
            $session = $this->sessionService->fetchOne(array('id' => $idSession));

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
        $datosUI = null;

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
                $datosUI['session']['dealerTip'] = isset($dealerTip) ? $dealerTip->toArray() : [];
                $datosUI['breadcrumb']           = 'Editar DealerTip';
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                isset($dealerTip) ? $datosUI  = $dealerTip->toArray() : $response = $response->withStatus(404);               
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
                $datosUI['session']['serviceTip'] = isset($serviceTip) ? $serviceTip->toArray() : [];
                $datosUI['breadcrumb']            = 'Editar ServiceTip';
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                isset($serviceTip) ? $datosUI  = $serviceTip->toArray() : $response = $response->withStatus(404);               
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
            'idSession' => $post['idSession'],
            'hour'      => $post['hour'],
            'dealerTip' => $post['dealerTip']
        ];

        $postServiceTip = [
            'idSession'  => $post['idSession'],
            'hour'       => $post['hour'],
            'serviceTip' => $post['serviceTip']
        ];

        $datosUI = [];
        $message = [];

        if (is_array($post)) {
            try {
                $dealerTip = $this->dealerTipService->add($postDealerTip);
                $message[] = 'El Dealer Tip se ingresó exitosamente.';
            // @codeCoverageIgnoreStart
            } catch (\Solcre\Pokerclub\Exception\DealerTipInvalidException $e) {
                $message[] = $e->getMessage();
            }
            // @codeCoverageIgnoreEnd
            try {
                $serviceTip = $this->serviceTipService->add($postServiceTip);
                $message[] = 'El Service Tip se ingresó exitosamente.';
            // @codeCoverageIgnoreStart
            } catch (\Solcre\Pokerclub\Exception\ServiceTipInvalidException $e) {
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
                $datosUI = [
                    'dealer_tip'  => is_null($dealerTip) ? [] : $dealerTip->toArray(),
                    'service_tip' => is_null($serviceTip) ? [] : $serviceTip->toArray()
                ];

                $response = $response->withStatus(201); //magic number
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
                    $dealerTip = $this->dealerTipService->update($post);
                    $message[] = 'El dealerTip se actualizó exitosamente.';    
                } catch (\Solcre\Pokerclub\Exception\DealerTipInvalidException $e) {  //add exception
                    $message[] = $e->getMessage();
                // @codeCoverageIgnoreEnd
                }
            }

            if (isset($args['idServiceTip'])) {
                try {
                    $serviceTip = $this->serviceTipService->update($post);
                    $message[] = 'El serviceTip se actualizó exitosamente.'; 
                } catch (\Solcre\Pokerclub\Exception\ServiceTipInvalidException $e) {  //add exception
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
                if (isset($dealerTip)) {
                    $datosUI[] = is_null($dealerTip) ? [] : $dealerTip->toArray();
                }
                
                if (isset($serviceTip)) {
                    $datosUI[] = is_null($serviceTip) ? [] : $serviceTip->toArray();
                }

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

        // JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(204); //magic number
        }

        if (isset($args['idDealerTip'])) {
            $idTip =  $args['idDealerTip'];
            try {
                $delete = $this->dealerTipService->delete($idTip);
                $message[]   = 'El dealerTip se eliminó exitosamente';
            } catch (DealerTipNotFoundException $e) {
                $response = $response->withStatus(404);
                $message[] = $e->getMessage();
            } catch (\Exception $e) {
                $response = $response->withStatus(500);
                $message[] = $e->getMessage();
            }
            
        } elseif (isset($args['idServiceTip'])) {
                $delete = $idServiceTip =  $args['idServiceTip'];
                try {
                    $this->serviceTipService->delete($idServiceTip);
                    $message[] = 'El serviceTip se eliminó exitosamente';
                } catch (ServiceTipNotFoundException $e) {
                    $response = $response->withStatus(404);
                    $message[] = $e->getMessage();
                } catch (\Exception $e) {
                    $response = $response->withStatus(500);
                    $message[] = $e->getMessage();
                }
        }

        $datosUI  = null;

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

        return $this->view->render($request, $response, $datosUI);
    }
}
