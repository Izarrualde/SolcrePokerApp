<?php
namespace Solcre\lmsuy\Controller;

use \Solcre\lmsuy\Service\ExpensesSessionService;
use \Solcre\lmsuy\Service\SessionService;
use \Solcre\lmsuy\Entity\ExpensesSessionEntity;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use \Solcre\lmsuy\Exception\ExpensesInvalidException;

class ExpensesSessionController
{
    protected $view;
    protected $expensesService;
    protected $sessionService;

    public function __construct(Twig $view, EntityManager$em)
    {
        $this->view                   = $view;
        $this->expensesService = new ExpensesSessionService($em);
        $this->sessionService         = new SessionService($em);
    }

    public function listAll($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $template  = 'expenses.html.twig';

        $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));
        $session       = $this->sessionService->fetchOne(array('id' => $idSession));

        $expenses = array();
        $datosUI  = array();

        foreach ($datosExpenses as $expensesObject) {
            $expenses[] = $expensesObject->toArray();
        }

        $datosUI['session']             = $session->toArray();
        $datosUI['session']['expenses'] = $expenses;
        $datosUI['breadcrumb']          = 'Gastos';

        return $this->view->render($response, $template, $datosUI);
    }

 
    public function list($request, $response, $args)
    {
        $id        = $args['idExpenditure'];
        $idSession = $args['idSession'];
        $template  = 'editExpenses.html.twig';

        $expenditure = $this->expensesService->fetchOne(array('id' => $id));
        $session     = $this->sessionService->fetchOne(array('id' => $idSession));

        $datosUI = array();
        
        $datosUI['session']                = $session->toArray();
        $datosUI['session']['expenditure'] = $expenditure->toArray();
        $datosUI['breadcrumb']             = 'Editar item';

        return $this->view->render($response, $template, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $idSession = $args['idSession'];

        $datosUI = array();
        $message = array();
        
        if (is_array($post)) {
            try {
                $this->expensesService->add($post);
                $message[]  = 'el item se ingresó exitosamente.';
            } catch (ExpensesInvalidException $e) {
                $message[] = $e->getMessage();
            }

            $template = 'expenses.html.twig';
            

            //extraigo datos de la bdd
            $expenses = array();
            $datosUI  = array();

            $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));
            $session       = $this->sessionService->fetchOne(array('id' => $idSession));

            foreach ($datosExpenses as $expensesObject) {
                $expenses[] = $expensesObject->toArray();
            }

            $datosUI['session']             = $session->toArray();
            $datosUI['session']['expenses'] = $expenses;
            $datosUI['breadcrumb']          = 'Gastos de Sesión';
            $datosUI['message']             = $message;
        }

        return $this->view->render($response, $template, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $template  = 'newexpenses.html.twig';
        $session   = $this->sessionService->fetchOne(array('id' => $idSession));
        
        $datosUI = array();

        $datosUI['session']    = $session->toArray();
        $datosUI['breadcrumb'] = 'Nuevo item';

        return $this->view->render($response, $template, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post      = $request->getParsedBody();
        $id        = $args['idExpenditure'];
        $idSession = $post['idSession'];

        $message = array();
        $expenses = array();
        $datosUI = array();

        $this->expensesService->update($post);
        $message[]  = 'El item se actualizó exitosamente';
        $template = 'expenses.html.twig';

        //extraigo datos de la BD
        $session       = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));

        foreach ($datosExpenses as $expensesObject) {
            $expenses[] = $expensesObject->toArray();
        }

        $datosUI['session']             = $session->toArray();
        $datosUI['session']['expenses'] = $expenses;
        $datosUI['breadcrumb']          = 'Gastos de Sesión';
        $datosUI['message']             = $message;

        return $this->view->render($response, $template, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        //$idSession = $args['idSession'];
        $id        = $args['idExpenditure'];
        $idSession = $args['idSession'];

        $message = array();
        $datosUI  = array();
        $expenses = array();

        $this->expensesService->delete($id);
        $message[]  = 'El item se eliminó exitosamente';
        $template = 'expenses.html.twig';

        //BUSQUEDA DE DATOS PARA LA UI
        $session       = $this->sessionService->fetchOne(array('id' => $idSession));
        $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));

        foreach ($datosExpenses as $expensesObject) {
            $expenses[] = $expensesObject->toArray();
        }

        $datosUI['session']             = $session->toArray();
        $datosUI['session']['expenses'] = $expenses;
        $datosUI['breadcrumb']          = 'Gastos de Sesión';
        $datosUI['message']             = $message;

        return $this->view->render($response, $template, $datosUI);
    }
}
