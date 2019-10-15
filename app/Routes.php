<?php

declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $container = $app->getContainer();

/*
    $app->group('/users', function (Group $group) use ($container) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });
*/

    $app->get('/', 'SessionController:listAll');
    $app->get('/sessions/{idSession:[0-9]+}', 'SessionController:list');
    $app->get('/sessions', 'SessionController:listAll');
    $app->post('/sessions', 'SessionController:add');
    $app->get('/sessions/form', 'SessionController:form');
    $app->get('/sessions/{idSession}/remove', 'SessionController:delete');
    $app->post('/sessions/{idSession}/update', 'SessionController:update');
    $app->get('/sessions/{idSession}/calculate', 'SessionController:CalculatePoints');
    $app->get('/sessions/{idSession}/play', 'SessionController:playSession');
    $app->get('/sessions/{idSession}/stop', 'SessionController:stopSession');

    $app->get('/sessions/{idSession}/expenses', 'ExpensesSessionController:listAll');
    $app->get('/sessions/{idSession}/expenses/{idExpenditure}', 'ExpensesSessionController:list');
    $app->get('/sessions/{idSession}/expenses/form', 'ExpensesSessionController:form');
    $app->post('/sessions/{idSession}/expenses/{idExpenditure}/update', 'ExpensesSessionController:update');
    $app->post('/sessions/{idSession}/expenses', 'ExpensesSessionController:add');
    $app->get('/sessions/{idSession}/expenses/{idExpenditure}/remove', 'ExpensesSessionController:delete');

    $app->get('/sessions/{idSession}/buyins', 'BuyinSessionController:listAll');
    $app->get('/sessions/{idSession}/buyins/{idbuyin}', 'BuyinSessionController:list');
    $app->post('/sessions/{idSession}/buyins', 'BuyinSessionController:add');
    $app->get('/{idSession}/buyins/form', 'BuyinSessionController:form');
    $app->get('/{idSession}/buyins/{idbuyin}/remove', 'BuyinSessionController:delete');
    $app->post('/buyins/{idBuyin}/update', 'BuyinSessionController:update');

    $app->get('/sessions/{idSession}/comissions', 'ComissionSessionController:listAll');
    $app->get('/sessions/{idSession}/comissions/{idcomission}', 'ComissionSessionController:list');
    $app->post('/sessions/{idSession}/comissions', 'ComissionSessionController:add');
    $app->get('/{idSession}/comissions/form', 'ComissionSessionController:form');
    $app->get('/{idSession}/comissions/{idcomission}/remove', 'ComissionSessionController:delete');
    $app->post('/comissions/{idcomission}/update', 'ComissionSessionController:update');

    $app->get('/sessions/{idSession}/tips', 'TipSessionController:listAll');
    $app->get('/sessions/{idSession}/tips/dealerTip/{idDealerTip}', 'TipSessionController:list');

    $app->get('/sessions/{idSession}/tips/serviceTip/{idServiceTip}', 'TipSessionController:list');
    $app->post('/sessions/{idSession}/tips', 'TipSessionController:add');
    $app->get('/{idSession}/tips/form', 'TipSessionController:form');
    $app->get('/tips/dealertip/{idDealerTip}/remove', 'TipSessionController:delete');
    $app->get('/tips/servicetip/{idServiceTip}/remove', 'TipSessionController:delete');
    $app->post('/tips/dealertip/{idDealerTip}/update', 'TipSessionController:update');
    $app->post('/tips/servicetip/{idServiceTip}/update', 'TipSessionController:update');

    $app->get('/sessions/{idSession}/usersSession', 'UserSessionController:listAll');
    $app->get('/sessions/{idSession}/usersSession/{idusersession}', 'UserSessionController:list');
    $app->post('/sessions/{idSession}/usersSession', 'UserSessionController:add');
    $app->get('/{idSession}/usersSession/form', 'UserSessionController:form');
    $app->get('/{idSession}/usersSession/{idusersession}/remove', 'UserSessionController:delete');
    $app->post('/usersSession/{idusersession}/update', 'UserSessionController:update');
    $app->get('/userssession/{idusersession}/formclose', 'UserSessionController:formClose');
    $app->post('/{idSession}/usersSession/{idusersession}/close', 'UserSessionController:close');

    $app->get('/users', 'UserController:listAll');
    $app->get('/users/{iduser}', 'UserController:list');
    $app->post('/users', 'UserController:add');
    $app->get('/form', 'UserController:form');
    $app->get('/users/{iduser}/remove', 'UserController:delete');
    $app->post('/users/{iduser}/update', 'UserController:update');

    $app->get('/rakeback-Algorithms', 'RakebackController:listAll');
};
