<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\ExpensesSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\Pokerclub\Exception\ExpensesInvalidException;
use Solcre\Pokerclub\Exception\ExpenditureNotFoundException;
use Exception;

class ExpensesSessionController
{
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_500 = 500;
    
    protected $view;
    protected $expensesService;
    protected $sessionService;

    public function __construct(View $view, EntityManager$em)
    {
        $this->view            = $view;
        $this->expensesService = new ExpensesSessionService($em);
        $this->sessionService  = new SessionService($em);
    }

    public function listAll($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $expenses = [];
        $datosUI  = [];

        $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));

        if (isset($datosExpenses)) {
            foreach ($datosExpenses as $expensesObject) {
                $expenses[] = $expensesObject->toArray();
            }
        }

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosUI['session'] = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['expenses'] = $expenses;
            $datosUI['breadcrumb']          = 'Gastos';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI = $expenses;
        }

        return $this->view->render($request, $response, $datosUI);
    }

 
    public function list($request, $response, $args)
    {
        $id        = $args['idExpenditure'];
        $idSession = $args['idSession'];
        $datosUI = null;

        $expenditure = $this->expensesService->fetchOne(array('id' => $id));

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $session     = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosUI['session']                = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['expenditure'] = isset($expenditure) ? $expenditure->toArray() : [];
            $datosUI['breadcrumb']             = 'Editar item';
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            if (isset($expenditure)) {
                $datosUI = $expenditure->toArray();
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
        $message = array();
        
        if (is_array($post)) {
            try {
                $expenditure = $this->expensesService->add($post);
                $message[]  = 'el item se ingresó exitosamente.';
            } catch (\Solcre\Pokerclub\Exception\ExpensesInvalidException $e) {
                $message[] = $e->getMessage();
            }

            // TwigWrapperView 
            if ($this->view instanceof TwigWrapperView) {
                $template = 'expensesSession/listAll.html.twig';
                $this->view->setTemplate($template);

                if ($expenditure instanceof ExpensesSessionEntity) {
                    //extraigo datos de la bdd
                    $expenses = [];

                    $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));
                    $session       = $this->sessionService->fetchOne(array('id' => $idSession));
                    if (isset($datosExpenses)) {
                        foreach ($datosExpenses as $expensesObject) {
                            $expenses[] = $expensesObject->toArray();
                        }
                    }

                    $datosUI['session']             = is_null($session) ? [] : $session->toArray();
                    $datosUI['session']['expenses'] = $expenses;
                    $datosUI['breadcrumb']          = 'Gastos de Sesión';
                    $datosUI['message']             = $message;
                }
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($expenditure) ? [] : $expenditure->toArray();
                $response = $response->withStatus(STATUS_CODE_201);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $session   = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosUI = [];

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['session']    = is_null($session) ? [] : $session->toArray();
            $datosUI['breadcrumb'] = 'Nuevo item';
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $id        = $args['idExpenditure'];
        $idSession = $post['idSession'];
        $datosUI = [];
        $message = [];

        if (is_array($post)) {
            try {
                $expenditure = $this->expensesService->update($post);
                $message[]  = 'El item se actualizó exitosamente';
            } catch (\Solcre\Pokerclub\Exception\ExpensesInvalidException $e) {
                $message[] = $e->getMessage();
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
            }

            // TwigWrapperView
            if ($this->view instanceof TwigWrapperView) {
                $template = 'expensesSession/listAll.html.twig';
                $this->view->setTemplate($template);

                //extraigo datos de la BD
                $session       = $this->sessionService->fetchOne(array('id' => $idSession));
                $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));

                if (isset($datosExpenses)) {
                    foreach ($datosExpenses as $expensesObject) {
                        $expenses[] = $expensesObject->toArray();
                    }
                }

                $datosUI['session']             = is_null($session) ? [] : $session->toArray();
                $datosUI['session']['expenses'] = $expenses;
                $datosUI['breadcrumb']          = 'Gastos de Sesión';
                $datosUI['message']             = $message;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = is_null($expenditure) ? [] : $expenditure->toArray();
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        //$idSession = $args['idSession'];
        $id        = $args['idExpenditure'];
        $idSession = $args['idSession'];
        $datosUI  = [];
        $message = [];

        // JsonView
        if ($this->view instanceof JsonView) {
            $response = $response->withStatus(STATUS_CODE_204);
        }

        try {
            $this->expensesService->delete($id);
            $message[]  = 'El item se eliminó exitosamente';
        } catch (\Solcre\Pokerclub\Exception\ExpenditureNotFoundException $e) {
            $response = $response->withStatus(self::STATUS_CODE_404);
            $message[] = $e->getMessage();
        } catch (\Exception $e) {
            $response = $response->withStatus(self::STATUS_CODE_500);
            $message[] = $e->getMessage();
        }

        $datosUI  = null;

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $template = 'expensesSession/listAll.html.twig';
            $this->view->setTemplate($template);

            //BUSQUEDA DE DATOS PARA LA UI
            $expenses = [];

            $session       = $this->sessionService->fetchOne(array('id' => $idSession));
            $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));

            if (isset($datosExpenses)) {
                foreach ($datosExpenses as $expensesObject) {
                    $expenses[] = $expensesObject->toArray();
                }
            }

            $datosUI['session']             = is_null($session) ? [] : $session->toArray();
            $datosUI['session']['expenses'] = $expenses;
            $datosUI['breadcrumb']          = 'Gastos de Sesión';
            $datosUI['message']             = $message;
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
