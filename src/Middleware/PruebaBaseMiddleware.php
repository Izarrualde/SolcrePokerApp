<?php 

/*  tanto en el modelo como en la vista habiamos creado un constructor para pasar el container, y luego teniamos un metodo magico get para ir llamando a nuestras propiedades
  entonces primero compiamos el contenido que tenimos en base controller y lo traemos aqui. 
*/

class BaseMiddleware
{
  protected $container;

  public function __construct($container)
  {
    $this->container = $container;
  }

  public function __get($property)
  {
    if ($this->container->{$property}) {
      return $this->container->{$property};
    }
  }
}

// ahora creamos en otro archivo una clase que va a extender de esta
// ExampleMiddleware.php

class ExampleMiddleware extends BaseMiddleware
{
  public __invoke($request, $response, $next)
  // este metodo magico lo que hace es llamar a una clase como si fuera una funcion
    $response->getBody()->write(' class 1 ');
    $response = $next($request, $response);
    $response->getBody()->write( 'class 2 ');
    return $response;
}

------------------------------------------------------------------
//luego en el router de la ruta /users podemos poner un middleware y concatenar otro

$app->get('/test', 'UserController:show')->setName('mt');
$app->get('/users', 'UserController:users')
  ->setName('users')
  ->add($b)
  ->add( new App\Middleware\ExampleMiddleware($container));
    //aqui es donde el metodo magico invoke al crear la clase es como si llamara a la funcion invoke()
-------------------------------------------------------------------

// ahora estabamos instanciando a nuestra clase, que tal si hacemos uso de nuestro contenedor de dependencias? para no escribir todo esto.
// vamos a dependencias.php

dependencias.php
<?php

$container = $app->getContainer();

//...

$container['view'] = ...

$container['UserController'] = function($c) {
  return new \App\Controllers\UserController($c);
}

// y agregamos
$container['ExampleMiddleware'] = function($c) {
  return new App\Middleware\ExampleMiddleware($c);
}

//entonces luego en el rutado (router) solo tendrias que pasar el string 'ExampleMiddleware' y entonces el propio Slim buscara dentro del contenedor de inyecccion de dependencias a ExampleMiddleware y se encargara de hacer todo el proceso.

$app->get('/test', 'UserController:show')->setName('mt');
$app->get('/users', 'UserController:users')
  ->setName('users')
  ->add($b)
  ->add( 'ExampleMiddleware');

