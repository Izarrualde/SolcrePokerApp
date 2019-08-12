<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\ComissionSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Entity\ComissionSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Slim\Views\Twig;
use Psr\Container\ContainerInterface;
// use \Solcre\lmsuy\Twig\Func;
use Solcre\Pokerclub\Exception\ComissionInvalidException;
use Solcre\Pokerclub\Exception\ComissionNotFoundException;
use Exception;

class ComissionSessionController
{
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_500 = 500;

    protected $view;
    protected $comissionService;
    protected $sessionService;

    public function __construct(View $view, EntityManager $em)
    {
        $this->view                    = $view;
        $this->comissionService = new ComissionSessionService($em);
        $this->sessionService          = new SessionService($em);
    }

    public function listAll($request, $response, $args)
    {

        $idSession = $args['idSession'];
        $comissions = [];
        $datosUI    = [];

        $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));

        if (is_array($datosComissions)) {
            foreach ($datosComissions as $comissionObject) {
                $comissions[] = $comissionObject->toArray();
            }
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosUI['session']               = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['comissions'] = $comissions;
            $datosUI['breadcrumb']            = 'Comisiones';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = $comissions;
        }

        return $this->view->render($request, $response, $datosUI);
    }
 
    public function list($request, $response, $args)
    {
        $id        = $args['idcomission'];
        $idSession = $args['idSession'];
        $datosUI = null;

        $comission = $this->comissionService->fetchOne(array('id' => $id));

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session   = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosUI['session']              = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['comission'] = isset($comission) ? $comission->toArray() : [];
            $datosUI['breadcrumb']           = 'Editar Comision';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            if (isset($comission)) {
                $datosUI = $comission->toArray();
            } else {
                $response = $response->withStatus(self::STATUS_CODE_404);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $args['idSession'];
        $datosUI = [];
        $message = [];

        if (is_array($post)) {
            try {
                $comission = $this->comissionService->add($post);
                $message[] = 'la comission se ingreso exitosamente.';
            } catch (\Solcre\Pokerclub\Exception\ComissionInvalidException $e) {
                $message[] = $e->getMessage();
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'comissionSession/listAll.html.twig';
                $this->view->setTemplate($template);

                $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));
                $session         = $this->sessionService->fetchOne(array('id' => $idSession));

                $comissions = [];

                if (is_array($datosComissions)) {
                    foreach ($datosComissions as $comission) {
                        $comissions[] = $comission->toArray();
                    }
                }

                $datosUI['session']               = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['comissions'] = $comissions;
                $datosUI['breadcrumb']            = 'Comisiones';
                $datosUI['message']               = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($comission) ? [] : $comission->toArray();
                $response = $response->withStatus(self::STATUS_CODE_201);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $session   = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosUI  = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['session']    = is_null($session) ? [] : $session->toArray();
            $datosUI['breadcrumb'] = 'Nueva Comision';
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $id        = $args['idcomission'];
        $idSession = $post['idSession'];
        $datosUI   = [];
        $message   = [];

        if (is_array($post)) {
            try {
                $comission = $this->comissionService->update($post);
                $message[] = 'la comission se actualizó exitosamente.';
            // @codeCoverageIgnoreStart
            } catch (ComissionInvalidException $e) {
                $message[] = $e->getMessage();
            // @codeCoverageIgnoreEnd
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'comissionSession/listAll.html.twig';
                $this->view->setTemplate($template);

                $session         = $this->sessionService->fetchOne(array('id' => $idSession));
                $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));

                $comissions = [];
                if (isset($datosComissions)) {
                    foreach ($datosComissions as $comission) {
                        $comissions[] = $comission->toArray();
                    }
                }

                $datosUI['session']               = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['comissions'] = $comissions;
                $datosUI['breadcrumb']            = 'Comisiones';
                $datosUI['message']               = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                if (isset($comission)) {
                    $datosUI = $comission->toArray();
                } else {
                    $response = $response->withStatus(self::STATUS_CODE_404);
                }
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $id        = $args['idcomission'];
        $idSession = $args['idSession'];
        $datosUI = [];
        $message = [];

        // set status code 204 when JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(self::STATUS_CODE_204);
        }
        
        try {
            $delete = $this->comissionService->delete($id);
            $message[]  = 'La comisión se eliminó exitosamente';
        // @codeCoverageIgnoreStart
        } catch (ComissionNotFoundException $e) {
            $response = $response->withStatus(self::STATUS_CODE_404);
            $message[] = $e->getMessage();
        } catch (\Exception $e) {
            $response = $response->withStatus(self::STATUS_CODE_500);
            $message[] = $e->getMessage();
        }
        // @codeCoverageIgnoreEnd

        $datosUI  = null;

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'comissionSession/listAll.html.twig';
            $this->view->setTemplate($template);

            // Busqueda de datos para UI
            $comissions = [];
            $session         = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosComissions = $this->comissionService->fetchAll(array('session' => $idSession));

            if (isset($datosComissions)) {
                foreach ($datosComissions as $comission) {
                    $comissions[] = $comission->toArray();
                }
            }

            $datosUI['session']               = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['comissions'] = $comissions;
            $datosUI['breadcrumb']            = 'Comisiones';
            $datosUI['message']               = $message;
        }
         
        return $this->view->render($request, $response, $datosUI);
    }
}
