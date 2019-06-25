<?php
namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\DealerTipSessionService;
use \Solcre\lmsuy\Service\ServiceTipSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Entity\DealerTipSessionEntity;
use \Solcre\lmsuy\Entity\ServiceTipSessionEntity;
use \Solcre\lmsuy\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use \Solcre\lmsuy\Exception\DealerTipInvalidException;
use \Solcre\lmsuy\Exception\ServiceTipInvalidException;

class TipSessionController
{
    protected $view;
    protected $dealerTipService;
    protected $serviceTipService;
    protected $sessionService;

    protected $entityManager;


    public function __construct(Twig $view, EntityManager $em)
    {
        $this->view                     = $view;
        $this->dealerTipService  = new DealerTipSessionService($em);
        $this->serviceTipService = new ServiceTipSessionService($em);
        $this->sessionService           = new SessionService($em);
        $this->entityManager            = $em;
    }

    public function listAll($request, $response, $args)
    {
        $idSession = $args['idSession'];

         $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
         $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));
         $session          = $this->sessionService->fetchOne(array('id' => $idSession));
 
        $template = 'tips.html.twig';

        $serviceTips = array();
        $dealerTips  = array();

        foreach ($datosDealerTips as $dealerTip) {
            $dealerTips[] = $dealerTip->toArray();
        }

        foreach ($datosServiceTips as $serviceTip) {
            $serviceTips[] = $serviceTip->toArray();
        }

        $datosUI['session']                = $session->toArray();
        $datosUI['session']['serviceTips'] = $serviceTips;
        $datosUI['session']['dealerTips']  = $dealerTips;
        $datosUI['breadcrumb']             = 'Tips';

        return $this->view->render($response, $template, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $datosUI = array();
        if (isset($args['idDealerTip'])) {
            $id        = $args['idDealerTip'];
            $dealerTip = $this->dealerTipService->fetchOne(array('id' => $id));

            $template = 'editDealerTip.html.twig';

            $datosUI['dealerTip']  = $dealerTip->toArray();
            $datosUI['breadcrumb'] = 'Editar DealerTip';
        } elseif (isset($args['idServiceTip'])) {
            $id         = $args['idServiceTip'];
            $serviceTip = $this->serviceTipService->fetchOne(array('id' => $id));

            $template = 'editServiceTip.html.twig';

            $datosUI['serviceTip'] = $serviceTip->toArray();
            $datosUI['breadcrumb'] = 'Editar ServiceTip';
        }
        return $this->view->render($response, $template, $datosUI);
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

        $datosUI = array();
        $message = array();

        if (is_array($post)) {
            try {
                $this->dealerTipService->add($postDealerTip);
                $message[] = 'El Dealer Tip se ingresó exitosamente.';
            } catch (DealerTipInvalidException $e) {
                $message[] = $e->getMessage();
            }

            try {
                $this->serviceTipService->add($postServiceTip);
                $message[] = 'El Service Tip se ingresó exitosamente.';
            } catch (ServiceTipInvalidException $e) {
                $message[] = $e->getMessage();
            }

            $template = 'tips.html.twig';

            //extraigo datos de la bdd
            $idSession        = $post['idSession'];
            $session          = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
            $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));

            $dealerTips = array();
            $serviceTips = array();

            foreach ($datosDealerTips as $dealerTip) {
                $dealerTips[] = $dealerTip->toArray();
            }

            foreach ($datosServiceTips as $serviceTip) {
                $serviceTips[] = $serviceTip->toArray();
            }

            $datosUI['session']                = $session->toArray();
            $datosUI['session']['dealerTips']  = $dealerTips;
            $datosUI['session']['serviceTips'] = $serviceTips;
            $datosUI['breadcrumb']             = 'Tips';
            $datosUI['message']                = $message;
        }
        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $session   = $this->sessionService->fetchOne(array('id' => $idSession));

        $template = 'newTips.html.twig';

        $datosUI = array();
   
        $datosUI['session']    = $session->toArray();
        $datosUI['breadcrumb'] = 'Nuevo Tip';

        return $this->view->render($response, $template, $datosUI);
    }


    public function update($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $message = array();

        if (isset($args['idDealerTip'])) {
            $this->dealerTipService->update($post);
            $message[] = 'El dealerTip se actualizó exitosamente.';
            $template  = 'tips.html.twig';
        } elseif (isset($args['idServiceTip'])) {
            $this->serviceTipService->update($post);
            $message[] = 'El serviceTip se actualizó exitosamente.';
            $template  = 'tips.html.twig';
        }

        //extraigo datos de la DB
        $idSession        = $post['idSession'];
        $session          = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
        $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));

        $dealerTips  = array();
        $serviceTips = array();

        foreach ($datosDealerTips as $dealerTip) {
            $dealerTips[] = $dealerTip->toArray();
        }

        foreach ($datosServiceTips as $serviceTip) {
            $serviceTips[] = $serviceTip->toArray();
        }

        $datosUI['session']                = $session->toArray();
        $datosUI['session']['dealerTips']  = $dealerTips;
        $datosUI['session']['serviceTips'] = $serviceTips;
        $datosUI['breadcrumb']             = 'Tips';
        $datosUI['message']                = $message;

        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $message = array();
        $datosUI = array();

        if (isset($args['idDealerTip'])) {
            $idTip =  $args['idDealerTip'];
            $dealerTip   = $this->entityManager->getReference('Solcre\lmsuy\Entity\DealerTipSessionEntity', $idTip);
            $this->dealerTipService->delete($idTip);
            
            $idSession   = $dealerTip->getSession()->getId();
            $message[]   = 'El dealerTip se eliminó exitosamente';
            $template    = 'tips.html.twig';
        } elseif (isset($args['idServiceTip'])) {
                $idServiceTip =  $args['idServiceTip'];
                $serviceTip = $this->entityManager->getReference(
                    'Solcre\lmsuy\Entity\ServiceTipSessionEntity',
                    $idServiceTip
                );
            $this->serviceTipService->delete($idServiceTip);
            
            $idSession = $serviceTip->getSession()->getId();
            $message[] = 'El serviceTip se eliminó exitosamente';
            $template  = 'tips.html.twig';
        }

        //DATOS PARA LA UI
        $session          = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
        $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));

        $dealerTips  = array();
        $serviceTips = array();

        foreach ($datosDealerTips as $dealerTip) {
            $dealerTips[] = $dealerTip->toArray();
        }

        foreach ($datosServiceTips as $serviceTip) {
            $serviceTips[] = $serviceTip->toArray();
        }

        $datosUI['session']                = $session->toArray();
        $datosUI['session']['dealerTips']  = $dealerTips;
        $datosUI['session']['serviceTips'] = $serviceTips;
        $datosUI['breadcrumb']             = 'Tips';
        $datosUI['message']                = $message;
        
        return $this->view->render($response, $template, $datosUI);
    }
}
