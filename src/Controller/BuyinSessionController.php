<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\BuyinSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Service\UserSessionService;
use Solcre\Pokerclub\Service\UserService;
use Solcre\Pokerclub\Entity\BuyinSessionEntity;
use Solcre\Pokerclub\Entity\SessionEntity;
use Solcre\Pokerclub\Exception\BuyinInvalidException;
use Solcre\Pokerclub\Exception\BuyinNotFoundException;
use Exception;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;

class BuyinSessionController
{
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_500 = 500;

    protected $view;
    protected $buyinSessionService;
    protected $sessionService;
    protected $userSessionService;
    protected $userService;

    public function __construct(View $view, EntityManager $em)
    {
        $this->view                = $view;
        $this->sessionService      = new SessionService($em);
        $this->userService         = new UserService($em);
        $this->userSessionService  = new UserSessionService($em);
        $this->buyinSessionService = new BuyinSessionService($em, $this->userSessionService);
    }

    public function listAll($request, $response, $args)
    {
        $idSession   = $args['idSession'];
        $buyins      = [];
        $datosUI     = [];

        $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);
        if (is_array($datosBuyins)) {
            foreach ($datosBuyins as $buyinObject) {
                $buyins[] = $buyinObject->toArray();
            }
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosUI['session']           = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['buyins'] = $buyins;
            $datosUI['breadcrumb']        = 'Buyins';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = $buyins;
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function list($request, $response, $args)
    {
        $id        = $args['idbuyin'];
        $idSession = $args['idSession'];
        $datosUI   = [];

        $buyin    = $this->buyinSessionService->fetchOne(array('id' => $id));

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosUI['session']          = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['buyin'] = is_null($buyin) ? [] : $buyin->toArray();
            $datosUI['breadcrumb'] = 'Editar Buyin';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = is_null($buyin) ? [] : $buyin->toArray();
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post = $request->getParsedBody();
        $idSession = $args['idSession'];
        $datosUI = null;
        $buyin = null;
        $message = [];

        if (is_array($post)) {
            try {
                $buyin = $this->buyinSessionService->add($post);
                $message[] = 'El buyin se agregó exitosamente';
            } catch (BuyinInvalidException $e) {
                $message[] = $e->getMessage();
                $response = $response->withStatus(400);
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'buyinSession/listAll.html.twig';
                $this->view->setTemplate($template);
                
                // BUSQUEDA de datos para la UI
                $buyins      = [];
                $session     = $this->sessionService->fetchOne(array('id' => $post['idSession']));
                $datosBuyins = $this->buyinSessionService->fetchAllBuyins($post['idSession']);

                if (isset($datosBuyins)) {
                    foreach ($datosBuyins as $buyin) {
                        $buyins[] = $buyin->toArray();
                    }
                }

                $datosUI['session']           = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['buyins'] = $buyins;
                $datosUI['breadcrumb']        = 'Buyins';
                $datosUI['message']           = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = isset($buyin) ? $buyin->toArray() : [];
                if ($response->getStatusCode()==200) {
                    $response = $response->withStatus(self::STATUS_CODE_201);
                }
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $session           = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosUsersSession = $this->userSessionService->fetchAll(array('session' => $idSession));
        $datosUI      = [];
        $usersSession = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if (isset($datosUsersSession)) {
                foreach ($datosUsersSession as $userSessionObject) {
                    $usersSession[] = $userSessionObject->toArray();
                }
            }

            $datosUI['session']                 = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['usersSession'] = $usersSession;
            $datosUI['breadcrumb']              = 'Nuevo Buyin';
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $post['idSession'];
        $datosUI = null;
        $message = [];

        if (is_array($post)) {
            try {
                $buyin = $this->buyinSessionService->update($post);
                $message[] = 'El buyin se actualizó exitosamente';
            // @codeCoverageIgnoreStart
            } catch (BuyinInvalidException $e) {
                $message[] = $e->getMessage();
            // @codeCoverageIgnoreEnd
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'buyinSession/listAll.html.twig';
                $this->view->setTemplate($template);

                //BUSQUEDA DE DATOS PARA LA UI
                $session     = $this->sessionService->fetchOne(array('id' => $idSession));
                $datosBuyins = $this->buyinSessionService->fetchAllBuyins($post['idSession']);
                $buyins  = [];

                if (isset($datosBuyins)) {
                    foreach ($datosBuyins as $buyin) {
                        $buyins[] = $buyin->toArray();
                    }
                }
                
                $datosUI['session'] = $session instanceof \Solcre\Pokerclub\Entity\SessionEntity ?
                $session->toArray() : [];
                $datosUI['session']['buyins'] = $buyins;
                $datosUI['breadcrumb']        = 'Buyins';
                $datosUI['message']           = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = isset($buyin) ? $buyin->toArray():  [];
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }


    public function delete($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $id        = $args['idbuyin'];
        $datosUI = null;
        $message = [];

        // JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(self::STATUS_CODE_204);
        }

        try {
            $delete = $this->buyinSessionService->delete($id);
            $message[]  = 'El buyin se eliminó exitosamente';
        // @codeCoverageIgnoreStart
        } catch (BuyinNotFoundException $e) {
            $response = $response->withStatus(self::STATUS_CODE_404);
            $message[] = $e->getMessage();
        } catch (\Exception $e) {
            $response = $response->withStatus(self::STATUS_CODE_500);
            $message[] = $e->getMessage();
        }
        // @codeCoverageIgnoreEnd

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'buyinSession/listAll.html.twig';
            $this->view->setTemplate($template);
            // Busqueda de datos para UI
            $buyins  = [];
            $session     = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosBuyins = $this->buyinSessionService->fetchAllBuyins($idSession);

            if (isset($datosBuyins)) {
                foreach ($datosBuyins as $buyin) {
                    $buyins[] = $buyin->toArray();
                }
            }

            $datosUI['session']           = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['buyins'] = $buyins;
            $datosUI['breadcrumb']        = 'Buyins';
            $datosUI['message']           = $message;
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
