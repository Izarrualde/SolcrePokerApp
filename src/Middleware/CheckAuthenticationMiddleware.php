<?php
namespace Solcre\lmsuy\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Solcre\lmsuy\Exception\UserHasNoPermissionException;

class CheckAuthenticationMiddleware
// la idea es que solo pueda acceder a las rutas que hay autenticacion
{
  const HEADER = 'Authentication';
  const PASSWORD = 'clavesecreta';
/*
	if (existe header con valor clave secreta) {
		hay respuesta, que siga viaje
	}
	else {
		tirar una excepcion USerHasNoPermissionException
	}
*/
  public function __invoke(Request $request, RequestHandler $handler): Response
  {
      if (!$request->hasHeader(self::HEADER)) {
        throw new UserHasNoPermissionException();
      } 

      $authentication = $request->getHeaderLine(self::HEADER);

      if ($authentication != self::PASSWORD) {
         throw new UserHasNoPermissionException(); 
      }
      
      return $handler->handle($request);;
  }
}