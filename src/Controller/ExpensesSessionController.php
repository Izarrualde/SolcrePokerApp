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
        $expenses       = null;
        $datosUI        = null;
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

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if ($status == $expectedStatus) {
                $datosUI['session']             = isset($session) ? $session->toArray() : [];
                $datosUI['session']['expenses'] = $expenses;            
            }

            $datosUI['breadcrumb'] = 'Gastos';
            
            if (isset($message)) {
                $datosUI['message'] = $message;
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
        $datosUI        = null;
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

        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            if ($status == $expectedStatus) {
                $session     = $this->sessionService->fetch(array('id' => $idSession));
                $datosUI['session']                = isset($session) ?  $session->toArray() : [];
                $datosUI['session']['expenditure'] = isset($expenditure) ? $expenditure->toArray() : [];
            }

            $datosUI['breadcrumb'] = 'Editar item';
            
            if (isset($message)) {
                $datosUI['message'] = $message;
            }
        }

        // JsonView
        if ($this->view instanceof JsonView) {
            $datosUI  = is_null($expenditure) ? [] : $expenditure->toArray();
            $response = $response->withStatus($status);
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function loadData($idSession, $message)
    {
        $data = null;

        // TwigWrapperView 
        if ($this->view instanceof TwigWrapperView) {
            $template = 'expensesSession/listAll.html.twig';
            $this->view->setTemplate($template);


            $datosExpenses = $this->expensesService->fetchAll(array('session' => $idSession));
            // @codeCoverageIgnoreStart
            try {
                $session = $this->sessionService->fetch(array('id' => $idSession));    
            } catch (\Exception $e) {
                $message[] = $e->getMessage();
            }
            // @codeCoverageIgnoreEnd

            $expenses = [];                
                
            if (is_array($datosExpenses)) {
                foreach ($datosExpenses as $expensesObject) {
                    $expenses[] = $expensesObject->toArray();
                }
            }

            $data['session']             = isset($session) ? $session->toArray() : [];
            $data['session']['expenses'] = $expenses;
            $data['breadcrumb']          = 'Gastos de Sesión';
            $data['message']             = $message;    
        }

        return $data;
    }

    public function add($request, $response, $args)
    {
        $post           = $request->getParsedBody();
        $idSession      = $args['idSession'];
        $datosUI        = null;
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
            
            $datosUI  = $this->view instanceof JsonView ? 
                (isset($expenditure) ? $expenditure->toArray() : null) : 
                $this->loadData($idSession, $message);

            if ($this->view instanceof JsonView) {
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
        
        // TwigWrapperView
        if ($this->view instanceof TwigWrapperView) {
            $datosUI['session']    = isset($session) ? $session->toArray() : null;
            $datosUI['breadcrumb'] = 'Nuevo item';
            
            if (isset($message)) {
                $datosUI['message']    = $message;                
            }
        }

        return $this->view->render($request, $response, $datosUI);
    }

    public function update($request, $response, $args)
    {
        $post           = $request->getParsedBody();
        $idSession      = $post['idSession'];
        $datosUI        = null;
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

            $datosUI  = $this->view instanceof JsonView ? 
                (isset($expenditure) ? $expenditure->toArray() : null) : 
                $this->loadData($idSession, $message);

            if ($this->view instanceof JsonView) {
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

            if ($this->view instanceof TwigWrapperView) {
                $datosUI = $this->loadData($idSession, $message);
            }

            if ($this->view instanceof JsonView) {
                $response = $response->withStatus($status);
            }

        return $this->view->render($request, $response, $datosUI);
    }
}
