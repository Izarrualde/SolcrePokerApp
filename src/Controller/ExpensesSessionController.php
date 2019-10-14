<?php
namespace Solcre\lmsuy\Controller;

use Solcre\Pokerclub\Service\ExpensesSessionService;
use Solcre\Pokerclub\Service\SessionService;
use Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use Solcre\Pokerclub\Exception\ExpensesInvalidException;
use Solcre\Pokerclub\Exception\ExpenditureNotFoundException;
use Exception;

class ExpensesSessionController extends BaseController
{
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
        $idSession      = $args['idSession'];
        $expenses       = [];
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

        $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));

        if (is_array($datosExpenses)) {
            foreach ($datosExpenses as $expensesObject) {
                $expenses[] = $expensesObject->toArray();
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = $expenses;
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

 
    public function list($request, $response, $args)
    {
        $id             = $args['idExpenditure'];
        $idSession      = $args['idSession'];
        $datosUI        = [];
        $expenditure    = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;
        $message        = null;

        try {
            $expenditure = $this->expensesService->fetch(array('id' => $id));
            $status      = parent::STATUS_CODE_200;
        } catch (ExpenditureNotFoundException $e) {
            $message[] = $e->getMessage();
            $status  = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status  = parent::STATUS_CODE_500;
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = isset($expenditure) ? $expenditure->toArray() : [];
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function add($request, $response, $args)
    {
        $post           = $request->getParsedBody();
        $idSession      = $args['idSession'];
        $datosUI        = [];
        $message        = null;
        $status         = null;
        
        if (is_array($post)) {
            try {
                $expenditure = $this->expensesService->add($post);
                $message[]   = 'el item se ingresó exitosamente.';
                $status      = parent::STATUS_CODE_201;
            } catch (ExpensesInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI = isset($expenditure) ? $expenditure->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function form($request, $response, $args)
    {
        $idSession = $args['idSession'];
        $datosUI = [];
        $message = null;

        try {
            $session   = $this->sessionService->fetch(array('id' => $idSession));
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post           = $request->getParsedBody();
        $idSession      = $post['idSession'];
        $datosUI        = [];
        $message        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_200;

        if (is_array($post)) {
            try {
                $expenditure = $this->expensesService->update($post);
                $message[]  = 'El item se actualizó exitosamente';
                $status    = parent::STATUS_CODE_200;
            } catch (ExpensesInvalidException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_400;
            } catch (ExpenditureNotFoundException $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_404;
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
                $status    = parent::STATUS_CODE_500;
            }

            // JsonView
            if ($this->view instanceof JsonView) {
                $datosUI  = isset($expenditure) ? $expenditure->toArray() : [];
                $response = $response->withStatus($status);
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function delete($request, $response, $args)
    {
        $id             = $args['idExpenditure'];
        $idSession      = $args['idSession'];
        $datosUI        = null;
        $message        = null;
        $status         = null;
        $expectedStatus = parent::STATUS_CODE_204;

        try {
            $this->expensesService->delete($id);
            $message[]  = 'El item se eliminó exitosamente';
            $status    = parent::STATUS_CODE_204;
        } catch (ExpenditureNotFoundException $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_404;
        } catch (\Exception $e) {
            $message[] = $e->getMessage();
            $status    = parent::STATUS_CODE_500;
        }

        if ($this->view instanceof JsonView) {
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }
}
