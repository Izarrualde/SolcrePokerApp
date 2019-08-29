<?php

use PHPUnit\Framework\TestCase;
use \Solcre\Pokerclub\Service\DealerTipSessionService;
use \Solcre\Pokerclub\Service\ServiceTipSessionService;
use \Solcre\Pokerclub\Service\SessionService;
use \Solcre\Pokerclub\Entity\DealerTipSessionEntity;
use \Solcre\Pokerclub\Entity\ServiceTipSessionEntity;
use \Solcre\Pokerclub\Entity\SessionEntity;
use Doctrine\ORM\EntityManager;
use Solcre\lmsuy\View\TwigWrapperView;
use Solcre\lmsuy\View\JsonView;
use Solcre\lmsuy\View\View;
use \Solcre\Pokerclub\Exception\DealerTipInvalidException;
use \Solcre\Pokerclub\Exception\ServiceTipInvalidException;
use Test\AppWrapper;
use Solcre\lmsuy\Controller\TipSessionController;

class TipSessionControllerTest extends TestCase
{
    public function createController($view, $dealerTipService, $serviceTipService, $sessionService)
    {
        $container = AppWrapper::getContainer();

        // Get EntityManager from container
        $entityManager = $container->get(EntityManager::class);

        $viewMock = 
        $controller = new TipSessionController($view, $entityManager);

        // Inject the mocked dealerTipService by reflection
        $reflection = new ReflectionProperty($controller, 'dealerTipService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $dealerTipService);

        // Inject the mocked serviceTipService by reflection
        $reflection = new ReflectionProperty($controller, 'serviceTipService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $serviceTipService);

        // Inject the mocked sessionService by reflection
        $reflection = new ReflectionProperty($controller, 'sessionService');
        $reflection->setAccessible(true);
        $reflection->setValue($controller, $sessionService);

        return $controller;
    }

    public function getAListOfDealerTips($session)
    {
        return [ 
          new DealerTipSessionEntity(
            1,
            date_create('2019-06-26 19:05:00'),
            50,
            $session
          ),
          new DealerTipSessionEntity(
            2,
            date_create('2019-06-26 19:10:00'),
            60,
            $session
          ),
        ];
    }

    public function getAListOfServiceTips($session)
    {
        return [ 
          new ServiceTipSessionEntity(
            1,
            date_create('2019-06-26 19:05:00'),
            50,
            $session
          ),
          new ServiceTipSessionEntity(
            2,
            date_create('2019-06-26 19:10:00'),
            60,
            $session
          ),
        ];
    }

    public function listAllSetup($view)
    {
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

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

        $expectedDealerTips  = $this->getAListOfDealerTips($expectedSession);
        $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = [
              'dealerTips'  => $expectedDealerTipsArray, 
              'serviceTips' => $expectedServiceTipsArray
            ];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI, 
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function listAllWithExceptionSetup($view, $exception)
    {
        $request  = $this->createMock(Slim\Psr7\Request::class);
        
        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->will($this->throwException($exception));
        $dealerTipService->method('fetchAll')->willReturn(null);
        $serviceTipService->method('fetchAll')->willReturn(null);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = null;

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['message']    = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb'] = 'Tips';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI['dealerTips']  = null;
            $expectedDatosUI['serviceTips'] = null;
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI, 
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testListAll()
    {
        $view     = $this->createMock(TwigWrapperView::class);

        $setup            = $this->listAllSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

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

        $setup            = $this->listAllSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(200);

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
        $view     = $this->createMock(TwigWrapperView::class);

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);

        $setup            = $this->listAllWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $setup['expectedResponse'];

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

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);

        $setup            = $this->listAllWithExceptionSetup($view, $exception);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(404);

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

    public function listDealerTipSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

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

        $expectedDealerTip = new DealerTipSessionEntity(
          1,
          date_create('2019-06-26 19:05:00'),
          50,
          $expectedSession
        );

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetch')->willReturn($expectedDealerTip);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']              = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTip'] = $expectedDealerTip->toArray();
            $expectedDatosUI['breadcrumb']           = 'Editar DealerTip';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedDealerTip->toArray();
        }

        return [ 
            'controller'      => $controller, 
            'expectedDatosUI' => $expectedDatosUI, 
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function listServiceTipSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
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

        $expectedServiceTip = new ServiceTipSessionEntity(
          1,
          date_create('2019-06-26 19:05:00'),
          50,
          $expectedSession
        );

        $sessionService->method('fetch')->willReturn($expectedSession);
        $serviceTipService->method('fetch')->willReturn($expectedServiceTip);
        // $serviceTipService->method('fetchOne')->willReturn($expectedServiceTip);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']               = $expectedSession->toArray();
            $expectedDatosUI['session']['serviceTip'] = $expectedServiceTip->toArray();
            $expectedDatosUI['breadcrumb']            = 'Editar ServiceTip';
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = $expectedServiceTip->toArray();
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI, 
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function listDealerTipWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->willReturn(null);
        $dealerTipService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['message']    = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb'] = 'Editar DealerTip';
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI, 
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function listServiceTipWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $sessionService->method('fetch')->willReturn(null);
        $serviceTipService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['message']    = [$exception->getMessage()];
            $expectedDatosUI['breadcrumb'] = 'Editar ServiceTip';
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI, 
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testListDealerTip()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
            'idSession'   => 2,
            'idDealerTip' => 1
        ];

        $setup           = $this->listDealerTipSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
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

        $controller->list($request, $response, $args);
    }


    public function testListDealerTipWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
            'idSession'   => 2,
            'idDealerTip' => 1
        ];

        $setup            = $this->listDealerTipSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(200);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }


    public function testListServiceTip()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
            'idSession'   => 2,
            'idServiceTip' => 1
        ];

        $setup           = $this->listServiceTipSetup($view);
        $controller      = $setup['controller'];
        $expectedDatosUI = $setup['expectedDatosUI'];
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

        $controller->list($request, $response, $args);
    }

    public function testListServiceTipWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
            'idSession'   => 2,
            'idServiceTip' => 1
        ];

        $setup            = $this->listServiceTipSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(200);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->list($request, $response, $args);
    }

    public function testListDealerTipWithException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
            'idSession'   => 2,
            'idDealerTip' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\DealerTipSessionEntity' . " Entity not found", 404);

        $setup            = $this->listDealerTipWithExceptionSetup($view, $exception);
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

        $controller->list($request, $response, $args);
    }


    public function testListDealerTipWithExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
            'idSession'   => 2,
            'idDealerTip' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\DealerTipessionEntity' . " Entity not found", 404);

        $setup            = $this->listDealerTipWithExceptionSetup($view, $exception);
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

        $controller->list($request, $response, $args);
    }

    public function testListServiceTipWithException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
            'idSession'   => 2,
            'idServiceTip' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\ServiceTipSessionEntity' . " Entity not found", 404);

        $setup            = $this->listServiceTipWithExceptionSetup($view, $exception);
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

        $controller->list($request, $response, $args);
    }


    public function testListServiceTipWithExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
            'idSession'   => 2,
            'idServiceTip' => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\ServiceTipessionEntity' . " Entity not found", 404);

        $setup            = $this->listServiceTipWithExceptionSetup($view, $exception);
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

        $controller->list($request, $response, $args);
    }

    public function addSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'id'         => 1,
            'idSession'  => 2,
            'hour'       => '2019-06-26 19:05:00',
            'dealerTip'  => 50,
            'serviceTip' => 60
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $expectedDealerTipAdded = new DealerTipSessionEntity(
            2,
            date_create($post['hour']),
            $post['dealerTip'],
            $expectedSession,
        );

        $expectedServiceTipAdded = new ServiceTipSessionEntity(
            2,
            date_create($post['hour']),
            $post['serviceTip'],
            $expectedSession,
        );

        $expectedDealerTips = [
            new DealerTipSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            $expectedDealerTipAdded
        ];

        $expectedServiceTips = [
            new ServiceTipSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            $expectedServiceTipAdded
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $dealerTipService->method('add')->willReturn($expectedDealerTipAdded);
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);
        $serviceTipService->method('add')->willReturn($expectedServiceTipAdded);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = ['El Dealer Tip se ingresó exitosamente.', 'El Service Tip se ingresó exitosamente.'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = [
              'dealerTip'  => $expectedDealerTipAdded->toArray(), 
              'serviceTip' => $expectedServiceTipAdded->toArray()
            ];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }


    public function addWithDealerTipExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'id'         => 1,
            'idSession'  => 2,
            'hour'       => '2019-06-26 19:05:00',
            'dealerTip'  => 50,
            'serviceTip' => 60
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $expectedServiceTipAdded = new ServiceTipSessionEntity(
            2,
            date_create($post['hour']),
            $post['serviceTip'],
            $expectedSession,
        );

        $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);

        $expectedServiceTips = [
            new ServiceTipSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            $expectedServiceTipAdded
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $dealerTipService->method('add')->will($this->throwException($exception));
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);
        $serviceTipService->method('add')->willReturn($expectedServiceTipAdded);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = [$exception->getMessage(), 'El Service Tip se ingresó exitosamente.'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = [
              'serviceTip' => $expectedServiceTipAdded->toArray()
          ];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function addWithServiceTipExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'id'         => 1,
            'idSession'  => 2,
            'hour'       => '2019-06-26 19:05:00',
            'dealerTip'  => 50,
            'serviceTip' => 60
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $expectedDealerTipAdded = new DealerTipSessionEntity(
            2,
            date_create($post['hour']),
            $post['dealerTip'],
            $expectedSession,
        );

        $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

        $expectedDealerTips = [
            new DealerTipSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            $expectedDealerTipAdded
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $serviceTipService->method('add')->will($this->throwException($exception));
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);
        $dealerTipService->method('add')->willReturn($expectedDealerTipAdded);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = ['El Dealer Tip se ingresó exitosamente.', $exception->getMessage()];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = [
              'dealerTip'  => $expectedDealerTipAdded->toArray()
            ];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'id'         => 1,
            'idSession'  => 2,
            'hour'       => '2019-06-26 19:05:00',
            'dealerTip'  => 50,
            'serviceTip' => 60
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

        $post = $request->getParsedBody();

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

        $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);

        $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $dealerTipService->method('add')->will($this->throwException($exceptionDealerTip));
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);
        $serviceTipService->method('add')->will($this->throwException($exceptionServiceTip));

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = [$exceptionDealerTip->getMessage(), $exceptionServiceTip->getMessage()];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testAdd()
    {
        $view    = $this->createMock(TwigWrapperView::class);


        $args = [
          'idSession' => 2
        ];

        $setup            = $this->addSetup($view);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        // metthod with data post use 2 parameters in addSetup
        $setup            = $this->addSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(201);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithInvalidDealerTipException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new DealerTipInvalidException();

        $setup            = $this->addWithDealerTipExceptionSetup($view, $exception);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithInvalidDealerTipExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new DealerTipInvalidException();

        $setup            = $this->addWithDealerTipExceptionSetup($view, $exception);
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

    public function testAddWithDealerTipWithException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\DealerTipSessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipExceptionSetup($view, $exception);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithDealerTipWithExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipExceptionSetup($view, $exception);
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

    public function testAddWithInvalidServiceTipException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ServiceTipInvalidException();

        $setup            = $this->addWithServiceTipExceptionSetup($view, $exception);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithInvalidServiceTipExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new ServiceTipInvalidException();

        $setup            = $this->addWithServiceTipExceptionSetup($view, $exception);
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

    public function testAddWithServiceTipWithException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipExceptionSetup($view, $exception);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithServiceTipWithExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipExceptionSetup($view, $exception);
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

    public function testAddWithInvalidDealerAndInvalidServiceTipExceptions()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionDealerTip  = new DealerTipInvalidException();
        $exceptionServiceTip = new ServiceTipInvalidException();

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }
 

    public function testAddWithInvalidDealerAndInvalidServiceTipExceptionsWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionDealerTip  = new DealerTipInvalidException();
        $exceptionServiceTip = new ServiceTipInvalidException();

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
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



    public function testAddWithInvalidDealerAndServiceTipException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionDealerTip  = new DealerTipInvalidException();
        $exceptionServiceTip = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }


    public function testAddWithInvalidDealerAndServiceTipExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionDealerTip  = new DealerTipInvalidException();
        $exceptionServiceTip = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->WithStatus(400);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->add($request, $response, $args);
    }



    public function testAddWithInvalidServiceTipAndDealerTipException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionServiceTip = new DealerTipInvalidException();
        $exceptionDealerTip  = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithInvalidServiceTipAndDealerTipExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionServiceTip = new ServiceTipInvalidException();
        $exceptionDealerTip  = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
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

    public function testAddWithDealerTipAndServiceTipExceptions()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionServiceTip = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);
        $exceptionDealerTip  = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
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

        $controller->add($request, $response, $args);
    }

    public function testAddWithDealerTipAndServiceTipException()
    {
        $view    = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionServiceTip = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 
        $exceptionDealerTip  = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->add($request, $response, $args);
    }

    public function testAddWithDealerAndServiceTipExceptionWithJsonView()
    {
        $view    = $this->createMock(JsonView::class);

        $args = [
          'idSession' => 2
        ];

        $exceptionServiceTip = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 
        $exceptionDealerTip  = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->addWithDealerTipAndServiceTipExceptionsSetup($view, $exceptionDealerTip, $exceptionServiceTip);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->WithStatus(500);

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

        $dealerTipService = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $args = [
          'idSession'  => 2
        ];

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

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
        $request    = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $expectedDatosUI = [];

        $expectedDatosUI['session']    = $expectedSession->toArray();
        $expectedDatosUI['breadcrumb'] = 'Nuevo Tip';

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
        
        $dealerTipService = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService = $this->createMock(SessionService::class);

        $args = [
          'idSession'  => 2
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404);
        $sessionService->method('fetch')->will($this->throwException($exception));

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);
        $request    = $this->createMock(Slim\Psr7\Request::class);

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $expectedDatosUI = [];

        $expectedDatosUI['message']    = [$exception->getMessage()];
        $expectedDatosUI['session']    = [];
        $expectedDatosUI['breadcrumb'] = 'Nuevo Tip';

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
        );

        $controller->form($request, $response, $args);
    }

    public function updateDealerTipSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'id'         => 1,
            'idSession'  => 2,
            'hour'       => '2019-06-26 19:05:00',
            'dealerTip'  => 50,
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $post = $request->getParsedBody();

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

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

        $expectedDealerTipUpdated = new DealerTipSessionEntity(
            2,
            date_create($post['hour']),
            $post['dealerTip'],
            $expectedSession,
        );

        $expectedDealerTips = [
            new DealerTipSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            $expectedDealerTipUpdated
        ];

        $expectedServiceTips = [
            new ServiceTipSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            new ServiceTipSessionEntity(
                2,
                date_create('2019-06-26 19:30:00'),
                60,
                $expectedSession
            ),
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $dealerTipService->method('update')->willReturn($expectedDealerTipUpdated);
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = ['El dealerTip se actualizó exitosamente.'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = [
              'dealerTip'  => $expectedDealerTipUpdated->toArray()
            ];
            $expectedResponse = $expectedResponse->withStatus(200);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function updateDealerTipWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'id'         => 1,
            'idSession'  => 2,
            'hour'       => '2019-06-26 19:05:00',
            'dealerTip'  => 50,
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $post = $request->getParsedBody();

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

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

        $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);

        $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $dealerTipService->method('update')->will($this->throwException($exception));
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = [$exception->getMessage()];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function updateServiceTipSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'id'         => 1,
            'idSession'  => 2,
            'hour'       => '2019-06-26 19:05:00',
            'dealerTip'  => 50,
            'serviceTip' => 60
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $post = $request->getParsedBody();

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

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

        $expectedServiceTipUpdated = new ServiceTipSessionEntity(
            2,
            date_create($post['hour']),
            $post['serviceTip'],
            $expectedSession,
        );

        $expectedDealerTips = [
            new DealerTipSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            new DealerTipSessionEntity(
                2,
                date_create('2019-06-26 19:30:00'),
                60,
                $expectedSession
            ),
        ];

        $expectedServiceTips = [
            new ServiceTipSessionEntity(
                1,
                date_create('2019-06-26 19:00:00'),
                50,
                $expectedSession
            ),
            $expectedServiceTipUpdated,
        ];

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $serviceTipService->method('update')->willReturn($expectedServiceTipUpdated);
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = [];

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = ['El serviceTip se actualizó exitosamente.'];
        }

        if ($view instanceof JsonView) {
            $expectedDatosUI = [
              'serviceTip'  => $expectedServiceTipUpdated->toArray()
            ];
            $expectedResponse = $expectedResponse->withStatus(200);
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function updateServiceTipWithExceptionSetup($view, $exception)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getParsedBody')->willReturn(
          [
            'id'         => 1,
            'idSession'  => 2,
            'hour'       => '2019-06-26 19:05:00',
            'dealerTip'  => 50,
            'serviceTip' => 60
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;

        $post = $request->getParsedBody();

        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

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

        $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);

        $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $serviceTipService->method('update')->will($this->throwException($exception));
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = [$exception->getMessage()];
        }

        return [ 
            'controller'       => $controller, 
            'expectedDatosUI'  => $expectedDatosUI,
            'request'          => $request,
            'response'         => $response,
            'expectedResponse' => $expectedResponse
        ];
    }

    public function testUpdateDealerTip()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'    => 2,
          'idDealerTip'  => 1
        ];

        $setup            = $this->updateDealerTipSetup($view);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
  }


    public function testUpdateDealerTipWithJson()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'    => 2,
          'idDealerTip'  => 1
        ];

        $setup            = $this->updateDealerTipSetup($view);
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

    public function testUpdateServiceTip()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'    => 2,
          'idServiceTip'  => 1
        ];

        $setup            = $this->updateServiceTipSetup($view);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateServiceTipWithJson()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'    => 2,
          'idServiceTip'  => 1
        ];

        $setup            = $this->updateServiceTipSetup($view);
        $controller       = $setup['controller'];
        $expectedDatosUI  = $setup['expectedDatosUI'];
        $request          = $setup['request'];
        $response         = $setup['response'];
        $expectedResponse = $response->withStatus(200);

        $view->expects($this->once())
        ->method('render')
        ->with(
            $this->equalTo($request),
            $this->equalTo($expectedResponse),
            $this->equalTo($expectedDatosUI),
          );

        $controller->update($request, $response, $args);
    }

    public function testUpdateInvalidDealerTip()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'    => 2,
          'idDealerTip'  => 1
        ];

        $exception = new DealerTipInvalidException();

        $setup            = $this->updateDealerTipWithExceptionSetup($view, $exception);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateInvalidDealerTipWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'    => 2,
          'idDealerTip'  => 1
        ];

        $exception = new DealerTipInvalidException();

        $setup            = $this->updateDealerTipWithExceptionSetup($view, $exception);
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

    public function testUpdateDealerTipWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'    => 2,
          'idDealerTip'  => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->updateDealerTipWithExceptionSetup($view, $exception);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateDealerTipWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'    => 2,
          'idDealerTip'  => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->updateDealerTipWithExceptionSetup($view, $exception);
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

    public function testUpdateInvalidServiceTip()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'    => 2,
          'idServiceTip'  => 1
        ];

        $exception = new ServiceTipInvalidException();

        $setup            = $this->updateServiceTipWithExceptionSetup($view, $exception);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateInvalidServiceipWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'    => 2,
          'idServiceTip'  => 1
        ];

        $exception = new ServiceTipInvalidException();

        $setup            = $this->updateServiceTipWithExceptionSetup($view, $exception);
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

    public function testUpdateServiceTipWithException()
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'    => 2,
          'idServiceTip'  => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->updateServiceTipWithExceptionSetup($view, $exception);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->update($request, $response, $args);
    }

    public function testUpdateServiceTipWithExceptionWithJsonView()
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'    => 2,
          'idServiceTip'  => 1
        ];

        $exception = new Exception('Solcre\Pokerclub\Entity\SessionEntity' . " Entity not found", 404); 

        $setup            = $this->updateServiceTipWithExceptionSetup($view, $exception);
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


    public function deleteDealerTipSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getQueryParams')->willReturn(
          [
            'idSession' => 2
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;


        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

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

        $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);
        $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $dealerTipService->method('delete')->willReturn(true);
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = ['El dealerTip se eliminó exitosamente'];
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


    public function deleteServiceTipSetup($view)
    {
        $request = $this->createMock(Slim\Psr7\Request::class);
        $request->method('getQueryParams')->willReturn(
          [
            'idSession' => 2
          ]
        );

        $response         = new Slim\Psr7\Response();
        $expectedResponse = $response;


        $dealerTipService  = $this->createMock(DealerTipSessionService::class);
        $serviceTipService = $this->createMock(ServiceTipSessionService::class);
        $sessionService    = $this->createMock(SessionService::class);

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

        $expectedDealerTips = $this->getAListOfDealerTips($expectedSession);
        $expectedServiceTips = $this->getAListOfServiceTips($expectedSession);

        $sessionService->method('fetch')->willReturn($expectedSession);
        $dealerTipService->method('fetchAll')->willReturn($expectedDealerTips);
        $serviceTipService->method('delete')->willReturn(true);
        $serviceTipService->method('fetchAll')->willReturn($expectedServiceTips);

        $controller = $this->createController($view, $dealerTipService, $serviceTipService, $sessionService);

        $expectedDatosUI = null;

        foreach ($expectedDealerTips as $dealerTip) {
          $expectedDealerTipsArray[] = $dealerTip->toArray();
        }

        foreach ($expectedServiceTips as $serviceTip) {
          $expectedServiceTipsArray[] = $serviceTip->toArray();
        }

        if ($view instanceof TwigWrapperView) {
            $expectedDatosUI['session']                = $expectedSession->toArray();
            $expectedDatosUI['session']['dealerTips']  = $expectedDealerTipsArray;
            $expectedDatosUI['session']['serviceTips'] = $expectedServiceTipsArray;
            $expectedDatosUI['breadcrumb']             = 'Tips';
            $expectedDatosUI['message']                = ['El serviceTip se eliminó exitosamente'];
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


    public function testDeleteDealerTip() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'    => 2,
          'idDealerTip'  => 1
        ];

        $setup            = $this->deleteDealerTipSetup($view);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    }

    public function testDeleteDealerTipWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'    => 2,
          'idDealerTip'  => 1
        ];

        $setup            = $this->deleteDealerTipSetup($view);
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

    public function testDeleteServiceTip() 
    {
        $view = $this->createMock(TwigWrapperView::class);

        $args = [
          'idSession'    => 2,
          'idServiceTip'  => 1
        ];

        $setup            = $this->deleteServiceTipSetup($view);
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
            $this->equalTo('tipSession/listAll.html.twig'),
        );

        $controller->delete($request, $response, $args);
    } 

    public function testDeleteServiceTipWithJsonView() 
    {
        $view = $this->createMock(JsonView::class);

        $args = [
          'idSession'    => 2,
          'idServiceTip'  => 1
        ];

        $setup            = $this->deleteServiceTipSetup($view);
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
}