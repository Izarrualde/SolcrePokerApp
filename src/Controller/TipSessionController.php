<?php
Namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\DealerTipSessionService;
use \Solcre\lmsuy\Service\ServiceTipSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Entity\DealerTipSessionEntity;
use \Solcre\lmsuy\Entity\ServiceTipSessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;

class TipSessionController
{
    protected $view;
    protected $dealerTipSessionService;
    protected $serviceTipSessionService;
    protected $sessionService;


	public function __construct(Twig $view, EntityManager $em) {
        $this->view = $view;
    	$this->dealerTipSessionService = new DealerTipSessionService($em); 
        $this->serviceTipSessionService = new ServiceTipSessionService($em); 
        $this->sessionService = new SessionService($em); 
    }

    public function listAll($request, $response, $args) {
        $idSession = $args['idSession'];
    	// $datosDealerTips = $this->dealerTipSessionService->find($idSession);
        // $datosServiceTips = $this->serviceTipSessionService->find($idSession);

        $datosDealerTips = $this->dealerTipSessionService->fetchAll($idSession);
        $datosServiceTips = $this->serviceTipSessionService->fetchAll($idSession);


        $session = $this->sessionService->fetchOne($idSession);
        $template = 'tips.html.twig';
        $serviceTips = array();
        $dealerTips = array();
        foreach ($datosDealerTips as $dealerTip) 
        {
            $dealerTips[] = $dealerTip->toArray();  
        }

        foreach ($datosServiceTips as $serviceTip) 
        {
            $serviceTips[] = $serviceTip->toArray();    
        }

        $datosUI['session'] = $session->toArray();
        $datosUI['session']['serviceTips'] = $serviceTips;
        $datosUI['session']['dealerTips'] = $dealerTips;
        $datosUI['breadcrumb'] = 'Tips';
    	
    	return $this->view->render($response, $template, $datosUI);
    }

    public function list($request, $response, $args) {
	    $datosUI = array();
        if (isset($args['idDealerTip'])) 
        {
            $id = $args['idDealerTip'];
            $dealerTip = $this->dealerTipSessionService->fetchOne($id);
            $template = 'editDealerTip.html.twig';
            $datosUI['dealerTip'] = $dealerTip->toArray();
            $datosUI['breadcrumb'] = 'Editar DealerTip';
        }
        elseif (isset($args['idServiceTip']))
        {
            $id = $args['idServiceTip'];
            $serviceTip = $this->serviceTipSessionService->fetchOne($id);
            $template = 'editServiceTip.html.twig';
            $datosUI['serviceTip'] = $serviceTip->toArray();
            $datosUI['breadcrumb'] = 'Editar ServiceTip';
        }
    	return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args) {
        $post = $request->getParsedBody();
        $postDealerTip = [
            'id' => $post['id'],
            'idSession' => $post['idSession'],
            'hour' => $post['hour'],
            'dealerTip' => $post['dealerTip']
        ];

        $postServiceTip = [
            'id' => $post['id'],
            'idSession' => $post['idSession'],
            'hour' => $post['hour'],
            'serviceTip' => $post['serviceTip']
        ];

        $datosUI = array();
        if (is_array($post))
        {
            // $dealerTipObject = new DealerTipSession($post['id'], $post['idSession'], $post['hour'], $post['dealerTip']);
            // $serviceTipObject = new ServiceTipSession($post['id'], $post['idSession'], $post['hour'], $post['serviceTip']);

            $this->dealerTipSessionService->add($postDealerTip); 
            $this->serviceTipSessionService->add($postServiceTip); 
            $message = 'Los tips se ingresaron exitosamente.';
            $template = 'tips.html.twig';

            //extraigo datos de la bdd
            $session = $this->sessionService->fetchOne($post['idSession']);
            $datosDealerTips = $this->dealerTipSessionService->fetchAll($post['idSession']);
            $dealerTips = array();
            $datosServiceTips = $this->serviceTipSessionService->fetchAll($post['idSession']);
            $serviceTips = array();

            foreach ($datosDealerTips as $dealerTip)
            {
                $dealerTips[] = $dealerTip->toArray(); 
            }
            foreach ($datosServiceTips as $serviceTip)
            {
                $serviceTips[] = $serviceTip->toArray(); 
            }

            $datosUI['session'] = $session->toArray();
            $datosUI['session']['dealerTips'] = $dealerTips;
            $datosUI['session']['serviceTips'] = $serviceTips;
            $datosUI['breadcrumb'] = 'Tips';
            $datosUI['message'] = $message;
        }
        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args) 
    {
        $idSession = $args['idSession'];
        $session = $this->sessionService->fetchOne($idSession);
        $datosUI = array();
        $datosUI['session'] = $session->toArray();
        $datosUI['breadcrumb'] = 'Nuevo Tip';
        $template = 'newTips.html.twig';
        return $this->view->render($response, $template, $datosUI);
    }


    public function update($request, $response, $args) {
        $post = $request->getParsedBody();

        if (isset($args['idDealerTip']))
        {
            // $dealerTipObject = new DealerTipSession($post['id'], $post['idSession'], $post['hour'], $post['dealerTip']);
            $this->dealerTipSessionService->update($post);
            $message = 'El dealerTip se actualizó exitosamente';
            $template = 'tips.html.twig';
        }
        elseif (isset($args['idServiceTip'])) 
        {
            //$serviceTipObject = new ServiceTipSession($post['id'], $post['idSession'], $post['hour'], $post['serviceTip']);
            $this->serviceTipSessionService->update($post);
            $message = 'El serviceTip se actualizó exitosamente';
            $template = 'tips.html.twig';  
        }

        //extraigo datos de la DB
        $session = $this->sessionService->fetchOne($post['idSession']);
        $datosDealerTips = $this->dealerTipSessionService->fetchAll($post['idSession']);
        $dealerTips = array();
        $datosServiceTips = $this->serviceTipSessionService->fetchAll($post['idSession']);
        $serviceTips = array();
        foreach ($datosDealerTips as $dealerTip)
        {
            $dealerTips[] = $dealerTip->toArray(); 
        }
        foreach ($datosServiceTips as $serviceTip)
        {
            $serviceTips[] = $serviceTip->toArray(); 
        }
        $datosUI['session'] = $session->toArray();
        $datosUI['session']['dealerTips'] = $dealerTips;
        $datosUI['session']['serviceTips'] = $serviceTips;
        $datosUI['breadcrumb'] = 'Tips';
        $datosUI['message'] = $message;

        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args) {    
        if (isset($args['idDealerTip']))
        {
            $idDealerTip =  $args['idDealerTip'];
            $idSession = $this->dealerTipSessionService->fetchOne($idDealerTip)->getIdSession();
            $idDealerTip = $args['idDealerTip'];
            $this->dealerTipSessionService->delete($idDealerTip);
            $message = 'El dealerTip se eliminó exitosamente';
            $template = 'tips.html.twig';
            $datosUI = array();
        }
        elseif (isset($args['idServiceTip']))
        {
            $idServiceTip =  $args['idServiceTip'];
            $idSession = $this->serviceTipSessionService->fetchOne($idServiceTip)->getIdSession();
            $this->serviceTipSessionService->delete($idServiceTip);
            $message = 'El serviceTip se eliminó exitosamente';
            $template = 'tips.html.twig';
            $datosUI = array();
        }
            //extraigo datos de la DB
            $session = $this->sessionService->fetchOne($idSession);
            $datosDealerTips = $this->dealerTipSessionService->fetchAll($idSession);
            $dealerTips = array();
            $datosServiceTips = $this->serviceTipSessionService->fetchAll($idSession);
            $serviceTips = array();
            foreach ($datosDealerTips as $dealerTip)
            {
                $dealerTips[] = $dealerTip->toArray(); 
            }
            foreach ($datosServiceTips as $serviceTip)
            {
                $serviceTips[] = $serviceTip->toArray(); 
            }
            $datosUI['session'] = $session->toArray();
            $datosUI['session']['dealerTips'] = $dealerTips;
            $datosUI['session']['serviceTips'] = $serviceTips;
            $datosUI['breadcrumb'] = 'Tips';
            $datosUI['message'] = $message;
        
            return $this->view->render($response, $template, $datosUI);
    }
}