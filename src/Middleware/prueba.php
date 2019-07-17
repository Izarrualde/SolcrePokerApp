<?php

// crear middleware global
$a = function($request, $response, $next) {

// $next es la encargada de ir acumulando y haciendo el callback de cada middleware.

  $response->getBody()->write(' ANTES ');

  $response = $next($request, $response);
  // con esto le decimos que haga un callback en caso que existiese otro middleware y lo almacene en la variable $response

  $response->getBody()->write( 'DESPUES ');
  return $response;
}

$app->add($a);

// lo anterior es un middleware globlal, se asigna a cualquier ruta

// veremos como hacer un middleware para una ruta especifica

$b = function($request, $response, $next) {
  $response->getBody()->write(' PRIMERO ');
  $response = $next($request, $response);
  $response->getBody()->write( 'ULTIMO ');
  return $response;
}

//crear middleware especifico para una ruta
// vamos a especificar este middleware y enlazarlo solamente a una ruta de nuestro router
// en index tenemos

$app->get('/test', 'UserController:show')->setName('mt');
$app->get('/users', 'UserController:users')->setName('users');

y agregamos ->add($b) a la ruta que queramos, por ej:
$app->get('/test', 'UserController:show')->setName('mt');
$app->get('/users', 'UserController:users')->setName('users')->add($b);
*/

// Clase que nos pueda llamar este middleware

creamos carpeta middleware y luego creamos una base middleware y luego creamos nuestras clases para que se vayan llamando

