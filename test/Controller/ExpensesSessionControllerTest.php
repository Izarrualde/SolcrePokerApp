<?php

use PHPUnit\Framework\TestCase;
use \Solcre\Pokerclub\Service\ExpensesSessionService;
use \Solcre\Pokerclub\Service\SessionService;
use \Solcre\Pokerclub\Entity\ExpensesSessionEntity;
use \Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use \Solcre\Pokerclub\Exception\ExpensesInvalidException;
use \Solcre\Pokerclub\Exception\ExpenditureNotFoundException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\ExpensesSessionController;

class ExpensesSessionControllerTest extends TestCase
{

    public function createController($view, $expensesService, $sessionService) {
        $container = AppWrapper::getContainer();

        // Get EntityManager from container
        $entityManager = $container->get(EntityManager::class);

        $controller = new ExpensesSessionController($view, $entityManager);

        // Inject the mocked expensesService by reflection
        $reflection = new ReflectionProperty($controller, 'expensesService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $expensesService);

        // Inject the mocked sessionService by reflection
        $reflection = new ReflectionProperty($controller, 'sessionService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $sessionService);

        return $controller;
    }

    public function getAListOfExpenses($session)
    {
        return [ 
          new ExpensesSessionEntity(
            1,
            $session,
            'gasto1',
            100
          ),
          new ExpensesSessionEntity(
            2,
            $session,
            'gasto2',
            200
          ),
        ];
    }

    public function listAllSetup($view)
    {
        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
          2,
          date_create('2019-06-27 15:00:00'),
          'another test session',
          'another test description',
          'another test photo',
          10,
          date_create('2019-06-27 19:00:00'),
          date_create('2019-06-27 19:30:00'),
          date_create('2019-06-27 23:30:00')
        );

        $expectedExpenses = $this->getAListOfExpenses($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $expensesService->method('fetchAll')->willReturn($expectedExpenses);

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedExpenses as $expenditure) {
          $expectedExpensesArray[] = $expenditure->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']             = $expectedSession->toArray();
            $expectedDatosUI['session']['expenses'] = $expectedExpensesArray;
            $expectedDatosUI['breadcrumb']          = 'Gastos';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedExpensesArray;
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function listAllWithExceptionSetup($view, $exception)
    {
        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService  = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->will($this->throwException($exception));
        $expensesService->method('fetchAll')->willReturn(null);

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = null;

            if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['message']    = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb'] = 'Gastos';
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testListAll()
    {
        $view     = $this->createMock(Solcre\lmsuy\View\TwigWrapperView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $setup           = $this->listAllSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $args = [
            'idSession' => 2
        ];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    }

    public function testListAllWithJsonView()
    {
        $view     = $this->createMock(JsonView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $setup           = $this->listAllSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $args = [
            'idSession' => 2
        ];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    }


    public function testListAllWithException()
    {
        $view     = $this->createMock(Solcre\lmsuy\View\TwigWrapperView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $exception = new \Exception('Solcre\Pokerclub\Entity\ExpensesEntity' . " Entity not found", 404);

        $setup           = $this->listAllWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $args = [
            'idSession' => 2
        ];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $controller->listAll($request, $response, $args);
    }

    public function testListAllWithExceptionWithJsonView()
    {
        $view     = $this->createMock(JsonView::class);
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(404);

        $exception = new \Exception('Solcre\Pokerclub\Entity\ExpensesEntity' . " Entity not found", 404);

        $setup           = $this->listAllWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $args = [
            'idSession' => 2
        ];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->listAll($request, $response, $args);
    }

    public function listSetup($view)
    {
        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
          2,
          date_create('2019-06-27 15:00:00'),
          'another test session',
          'another test description',
          'another test photo',
          10,
          date_create('2019-06-27 19:00:00'),
          date_create('2019-06-27 19:30:00'),
          date_create('2019-06-27 23:30:00')
        );

        $expectedExpenditure = new ExpensesSessionEntity(
          1,
          $expectedSession,
          'gasto1',
          100
        );

        $sessionService->method('fetch')->willReturn($expectedSession);
        $expensesService->method('fetch')->willReturn($expectedExpenditure);

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['expenditure'] = $expectedExpenditure->toArray();
            $expectedDatosUI['breadcrumb']             = 'Editar item';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedExpenditure->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function listWithExceptionSetup($view, $exception)
    {
        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->willReturn(null);
        $expensesService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['message']    = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb'] = 'Editar item';
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testList()
    {
        $view             = $this->createMock(TwigWrapperView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $setup           = $this->listSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function testListWithJsonView()
    {
        $view             = $this->createMock(JsonView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(200);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $setup           = $this->listSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function testListWithNotFoundException()
    {
        $view             = $this->createMock(TwigWrapperView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $exception = new ExpenditureNotFoundException();

        $setup           = $this->listWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $controller->list($request, $response, $args);
    }

    public function testListWithExpenditureNotFoundExceptionWithJsonView()
    {
        $view             = $this->createMock(JsonView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(404);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $exception = new ExpenditureNotFoundException();

        $setup           = $this->listWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function testListWithException()
    {
        $view             = $this->createMock(TwigWrapperView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\ExpensesSessionEntity' . " Entity not found", 404);

        $setup           = $this->listWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $controller->list($request, $response, $args);
    }

    public function testListWithExceptionWithJsonView()
    {
        $view             = $this->createMock(JsonView::class);
        $request          = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(500);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\ExpensesSessionEntity' . " Entity not found", 404);

        $setup           = $this->listWithExceptionSetup($view, $exception);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function addSetup($view, $request)
    {
        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService  = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
          2,
          date_create('2019-06-27 15:00:00'),
          'another test session',
          'another test description',
          'another test photo',
          10,
          date_create('2019-06-27 19:00:00'),
          date_create('2019-06-27 19:30:00'),
          date_create('2019-06-27 23:30:00')
        );

        $expectedExpenditureAdded = new ExpensesSessionEntity(
            2,
            $expectedSession,
            'expenditure added',
            100,
        );

        $expectedExpenses = [
            new ExpensesSessionEntity(
                1,
                $expectedSession,
                'expenditure 1',
                $expectedSession
            ),
            $expectedExpenditureAdded
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $expensesService->method('fetchAll')->willReturn($expectedExpenses);
        $expensesService->method('add')->willReturn($expectedExpenditureAdded);

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedExpenses as $expenditure) {
            $expectedExpensesArray[] = $expenditure->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']             = $expectedSession->toArray();
            $expectedDatosUI['session']['expenses'] = $expectedExpensesArray;
            $expectedDatosUI['breadcrumb']          = 'Gastos de Sesión';
            $expectedDatosUI['message']             = ['el item se ingresó exitosamente.'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedExpenditureAdded->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI 
        ];
    }

    public function testAdd()
    {

        $view = $this->createMock(TwigWrapperView::class);
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
            [
              'description' => 'gasto2',
              'idSession'   => 2,
              'amount'      => 200
            ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession' => 2
        ];

        // metthod with data post use 2 parameters in addSetup
        $setup           = $this->addSetup($view, $request);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithJson()
    {

        $view = $this->createMock(JsonView::class);
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
            [
              'description' => 'gasto2',
              'idSession'   => 2,
              'amount'      => 200
            ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response->withStatus(201);

        $args = [
          'idSession' => 2
        ];

        // metthod with data post use 2 parameters in addSetup
        $setup           = $this->addSetup($view, $request);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->add($request, $response, $args);
    }

    public function addAndUpdateWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
            [
              'description' => 'gasto2',
              'idSession'   => 2,
              'amount'      => 200
            ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(1);

        $expectedExpenses = $this->getAListOfExpenses($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $expensesService->method('fetchAll')->willReturn($expectedExpenses);
        
        $expensesService->method('add')->will($this->throwException($exception));
        $expensesService->method('update')->will($this->throwException($exception));

        $controller = $this->createController($view, $expensesService, $sessionService);
        
        $expectedDatosUI = null;

        if (is_array($expectedExpenses)) {
             foreach ($expectedExpenses as $expenditure) {
                $expectedExpensesArray[] = $expenditure->toArray();
            }           
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']               = $expectedSession->toArray();
            $expectedDatosUI['session']['expenses'] = $expectedExpensesArray;
            $expectedDatosUI['message']               = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb']            = 'Gastos de Sesión';
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testAddWithInvalidAmount()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ExpensesInvalidException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];
        

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

      $controller->add($request, $response, $args); 
    }

    public function testAddWithInvalidAmountWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ExpensesInvalidException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];

        $expectedResponse = $response->withStatus(400);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->add($request, $response, $args); 
    }

    public function testAddWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\ExpensesSessionEntity' . " Entity not found", 404); 

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];
        
        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            // $this->contains([$exception->getMessage()]),
            $this->equalTo($expectedDatosUI),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

      $controller->add($request, $response, $args); 
    }

    public function testAddWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\ExpensesSessionEntity' . " Entity not found", 404); 

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];

        $expectedResponse = $response->withStatus(500);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->add($request, $response, $args); 
    }

    public function testForm()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $request = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'  => 2
        ];
        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
          2,
          date_create('2019-06-27 15:00:00'),
          'another test session',
          'another test description',
          'another test photo',
          10,
          date_create('2019-06-27 19:00:00'),
          date_create('2019-06-27 19:30:00'),
          date_create('2019-06-27 23:30:00')
        );

        $sessionService->method('fetch')->willReturn($expectedSession);

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = [];

        $expectedDatosUI['session']    = $expectedSession->toArray();
        $expectedDatosUI['breadcrumb'] = 'Nuevo item';

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->form($request, $response, $args);

    }

    public function testFormWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $request = $this->createMock(Slim\Psr7\Request::class);
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $args = [
          'idSession'  => 2
        ];
        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);
        $sessionService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = [];

        $expectedDatosUI['message']    = [$exception->getMessage()];
        $expectedDatosUI['breadcrumb'] = 'Nuevo item';

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $controller->form($request, $response, $args);

    }
    public function updateSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $request->method('getParsedBody')->willReturn(
            [
              'comission' => 30,
              'idSession' => 2,
              'hour'      => '2019-06-26 19:05:00'
            ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $post = $request->getParsedBody();

        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
          2,
          date_create('2019-06-27 15:00:00'),
          'another test session',
          'another test description',
          'another test photo',
          10,
          date_create('2019-06-27 19:00:00'),
          date_create('2019-06-27 19:30:00'),
          date_create('2019-06-27 23:30:00')
        );

        $expectedExpenditureUpdated = new ExpensesSessionEntity(
            2,
            $expectedSession,
            'expenditure updated',
            150,
        );


        $expectedExpenses = [
            new ExpensesSessionEntity(
                1,
                $expectedSession,
                'expenditure 1',
                $expectedSession
            ),
            $expectedExpenditureUpdated
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $expensesService->method('fetchAll')->willReturn($expectedExpenses);
        $expensesService->method('update')->willReturn($expectedExpenditureUpdated);

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedExpenses as $expenditure) {
          $expectedExpensesArray[] = $expenditure->toArray();
        }
        
        if ($view instanceof TwigWrapperView) {
          $expectedDatosUI['session']             = $expectedSession->toArray();
          $expectedDatosUI['session']['expenses'] = $expectedExpensesArray;
          $expectedDatosUI['breadcrumb']          = 'Gastos de Sesión';
          $expectedDatosUI['message']             = ['El item se actualizó exitosamente'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI  = $expectedExpenditureUpdated->toArray();
            $expectedResponse = $expectedResponse->withStatus(200);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' =>  $expectedResponse
        ];
    }

    public function testUpdate()
    {
      $view = $this->createMock(TwigWrapperView::class);

      $args = [
        'idSession'     => 2,
        'idExpenditure' => 1
      ];

        $setup            = $this->updateSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithJsonView()
    {
      $view = $this->createMock(JsonView::class);

      $args = [
        'idSession'     => 2,
        'idExpenditure' => 1
      ];

        $setup            = $this->updateSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateWithInvalidAmount()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ExpensesInvalidException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];
        

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

      $controller->update($request, $response, $args); 
    }

    public function testUpdateWithInvalidAmountWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ExpensesInvalidException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];

        $expectedResponse = $response->withStatus(400);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->update($request, $response, $args); 
    }

    public function testUpdateWithExpenditureNotFound()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ExpenditureNotFoundException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];
        

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

      $controller->update($request, $response, $args); 
    }

    public function testUpdateWithExpenditureNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ExpenditureNotFoundException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];

        $expectedResponse = $response->withStatus(404);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->update($request, $response, $args); 
    }

    public function testUpdateWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ExpenditureNotFoundException();

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];
        

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

      $controller->update($request, $response, $args); 
    }

    public function testUpdateWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);

        $setup            = $this->addAndUpdateWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];

        $expectedResponse = $response->withStatus(500);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

      $controller->update($request, $response, $args); 
    } 

    public function deleteSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
          2,
          date_create('2019-06-27 15:00:00'),
          'another test session',
          'another test description',
          'another test photo',
          10,
          date_create('2019-06-27 19:00:00'),
          date_create('2019-06-27 19:30:00'),
          date_create('2019-06-27 23:30:00')
        );

        $expectedExpenses = $this->getAListOfExpenses($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $expensesService->method('fetchAll')->willReturn($expectedExpenses);
        $expensesService->method('delete')->willReturn(true);

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedExpenses as $expenditure) {
          $expenses[] = $expenditure->toArray();
        }

        if ($view instanceof TwigWrapperView) {
          $expectedDatosUI['session']             = $expectedSession->toArray();
          $expectedDatosUI['session']['expenses'] = $expenses;
          $expectedDatosUI['breadcrumb']          = 'Gastos de Sesión';
          $expectedDatosUI['message']             = ['El item se eliminó exitosamente'];
        }

        if ($view instanceof JsonView) {
            $expectedResponse = $expectedResponse->withStatus(204);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' =>  $expectedResponse
        ];
    }

    public function deleteWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $expensesService = $this->createMock(ExpensesSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $expectedSession = new SessionEntity(
          2,
          date_create('2019-06-27 15:00:00'),
          'another test session',
          'another test description',
          'another test photo',
          10,
          date_create('2019-06-27 19:00:00'),
          date_create('2019-06-27 19:30:00'),
          date_create('2019-06-27 23:30:00')
        );

        $expectedExpenses = $this->getAListOfExpenses($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $expensesService->method('fetchAll')->willReturn($expectedExpenses);
        $expensesService->method('delete')->will($this->throwException($exception));

        $controller = $this->createController($view, $expensesService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedExpenses as $expenditure) {
          $expenses[] = $expenditure->toArray();
        }

        if ($view instanceof TwigWrapperView) {
          $expectedDatosUI['session']             = $expectedSession->toArray();
          $expectedDatosUI['session']['expenses'] = $expenses;
          $expectedDatosUI['breadcrumb']          = 'Gastos de Sesión';
          $expectedDatosUI['message']             = [$exception->getMessage()];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testDelete()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $setup            = $this->deleteSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }


    public function testDeleteWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $setup            = $this->deleteSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteWithExpenditureNotFound()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $exception = new ExpenditureNotFoundException();

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteWithExpenditureNotFoundWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $exception = new ExpenditureNotFoundException();

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];

        $expectedResponse = $response->withStatus(404);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\ExpensesSessionEntity' . " Entity not found", 404);

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->contains([$exception->getMessage()]),
        );

        $view->expects($this->once())
        ->method('setTemplate')
        ->with(
            $this->equalTo('expensesSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'     => 2,
          'idExpenditure' => 1
        ];

        $exception = new \Exception('Solcre\Pokerclub\Entity\ExpensesSessionEntity' . " Entity not found", 404); 

        $setup            = $this->deleteWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];

        $expectedResponse = $response->withStatus(500);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->delete($request, $response, $args);
    }

}