<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\DealerTipSessionService;
use Solcre\Pokerclub\Service\ServiceTipSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Entity\DealerTipSessionEntity;
use Solcre\Pokerclub\Entity\ServiceTipSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Exception\DealerTipInvalidException;
use Solcre\Pokerclub\Exception\ServiceTipInvalidException;
use Solcre\Pokerclub\Exception\DealerTipNotFoundException;
use Solcre\Pokerclub\Exception\ServiceTipNotFoundException;
use Exception;

class TipSessionController extends BaseController
{
    protected $view;
    protected $dealerTipService;
    protected $serviceTipService;
    protected $sessionService;
    protected $entityManager;


    public function __construct(View $view, EntityManager $em)
    {
        $this->view              = $view;
        $this->dealerTipService  = new DealerTipSessionService($em);
        $this->serviceTipService = new ServiceTipSessionService($em);
        $this->sessionService    = new SessionService($em);
        $this->entityManager     = $em;
    }

    public function listAll($request, $response, $args)
    {
        $idSession      = $args['idSession'];
        $serviceTips    = [];
        $dealerTips     = [];
        $datosUI        = [];
        $message        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;

        try {
            $session = $this->sessionService->fetch(array('id' => $idSession));
            $status  = parent::STATUS_CODE_200;
        } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status  = parent::STATUS_CODE_404;
        }

        $datosDealerTips  = $this->dealerTipService->fetchAll(array('session' => $idSession));
        $datosServiceTips = $this->serviceTipService->fetchAll(array('session' => $idSession));

        if (is_array($datosDealerTips)) {
            foreach ($datosDealerTips as $dealerTip) {
                $dealerTips[] = $dealerTip->toArray();
            }
        }

        if (is_array($datosServiceTips)) {
            foreach ($datosServiceTips as $serviceTip) {
                $serviceTips[] = $serviceTip->toArray();
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI['dealerTips']  = $dealerTips;
            $datosUI['serviceTips'] = $serviceTips;
            $response               = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $idSession      = $args['idSession'];
        $datosUI        = [];
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;
        $message        = null;

        if (isset($args['idDealerTip'])) {
            $id = $args['idDealerTip'];

            try {
                $tip = $this->dealerTipService->fetch(array('id' => $id));
                $status    = parent::STATUS_CODE_200;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status  = parent::STATUS_CODE_404;
            }
        } elseif (isset($args['idServiceTip'])) {
            $id = $args['idServiceTip'];

            try {
                $tip = $this->serviceTipService->fetch(array('id' => $id));
                $status     = parent::STATUS_CODE_200;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI          = isset($tip) ? $tip->toArray() : [];
            $response         = $response->withStatus($status);
        }


        return $this->view->render($request, $response, $datosUI);
    }

    public function setStatusForResponse($statusDealerTip, $statusServiceTip, $expectedStatus)
    {
        if (($statusDealerTip==$expectedStatus) && ($statusServiceTip==$expectedStatus)) {
                $status = $expectedStatus;
        } elseif (($statusDealerTip==parent::STATUS_CODE_400) || ($statusServiceTip==parent::STATUS_CODE_400)) {
            $status = parent::STATUS_CODE_400;
        } else {
            $status = parent::STATUS_CODE_500;
        }

        return $status;
    }

    public function add($request, $response, $args)
    {
        $idSession        = $args['idSession'];
        $datosUI          = [];
        $message          = null;
        $statusDealerTip  = null;
        $statusServiceTip = null;
        $dealerTip        = null;
        $serviceTip       = null;
        $expectedStatus   = parent::STATUS_CODE_201;

        $post = $request->getParsedBody();
  
        if (is_array($post)) {
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

            try {
                $dealerTip       = $this->dealerTipService->add($postDealerTip);
                $message[]       = 'El Dealer Tip se ingresó exitosamente.';
                $statusDealerTip = parent::STATUS_CODE_201;
            } catch (DealerTipInvalidException $e) {
                $message[]       = $e->getMessage();
                $statusDealerTip = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $message[]       = $e->getMessage();
                $statusDealerTip = parent::STATUS_CODE_500;
            }

            try {
                $serviceTip       = $this->serviceTipService->add($postServiceTip);
                $message[]        = 'El Service Tip se ingresó exitosamente.';
                $statusServiceTip = parent::STATUS_CODE_201;
            } catch (ServiceTipInvalidException $e) {
                $message[]        = $e->getMessage();
                $statusServiceTip = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $message[]        = $e->getMessage();
                $statusServiceTip = parent::STATUS_CODE_500;
            }

            if ($this->view instanceof JsonView) {
                $datosUI['dealerTip']  = isset($dealerTip) ? $dealerTip->toArray() : [];
                $datosUI['serviceTip'] = isset($serviceTip) ? $serviceTip->toArray() : [];
                $response = $response->withStatus(
                    $this->setStatusForResponse($statusDealerTip, $statusServiceTip, $expectedStatus)
                );
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $datosUI        = [];
        $message        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;

        $post = $request->getParsedBody();

        if (is_array($post)) {
            if (isset($args['idDealerTip'])) {
                $keyTip = 'dealerTip';
                try {
                    $tip       = $this->dealerTipService->update($post);
                    $message[] = 'El dealerTip se actualizó exitosamente.';
                    $status    = parent::STATUS_CODE_200;
                } catch (DealerTipInvalidException $e) {
                    $message[] = $e->getMessage();
                    $status    = parent::STATUS_CODE_400;
                } catch (\Exception $e) {
                    $message[] = $e->getMessage();
                    $status    = parent::STATUS_CODE_500;
                }
            }

            if (isset($args['idServiceTip'])) {
                $keyTip = 'serviceTip';
                try {
                    $tip       = $this->serviceTipService->update($post);
                    $message[] = 'El serviceTip se actualizó exitosamente.';
                    $status    = parent::STATUS_CODE_200;
                } catch (ServiceTipInvalidException $e) {
                    $message[] = $e->getMessage();
                    $status    = parent::STATUS_CODE_400;
                } catch (\Exception $e) {
                    $message[] = $e->getMessage();
                    $status    = parent::STATUS_CODE_500;
                }
            }

            if ($this->view instanceof JsonView) {
                $datosUI[$keyTip]  = isset($tip) ? $tip->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $datosUI        = null;
        $message        = null;
        $status         = null;
        $get = $request->getQueryParams();

        if (isset($args['idDealerTip'])) {
            $idTip =  $args['idDealerTip'];
            try {
                $delete    = $this->dealerTipService->delete($idTip);
                $message[] = 'El dealerTip se eliminó exitosamente';
                $status    = parent::STATUS_CODE_204;
            } catch (DealerTipNotFoundException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }
        } elseif (isset($args['idServiceTip'])) {
            $idTip =  $args['idServiceTip'];
            $delete = $idServiceTip =  $idTip;
            try {
                $delete = $this->serviceTipService->delete($idServiceTip);
                $message[] = 'El serviceTip se eliminó exitosamente';
                $status    = parent::STATUS_CODE_204;
            } catch (ServiceTipNotFoundException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }
        }

        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
