<?php
namespace Solcre\lmsuy\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class ViewSelectorMiddleware
{
    protected $container;
    protected $viewMap;

    public function __construct($container, Array $viewMap)
    {
        $this->container = $container;
        $this->viewMap   = $viewMap;
    }

    public function __get($property)
    {
        if ($this->container->{$property}) {
          return $this->container->{$property};
        }
    }

    /**
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $type = $request->getHeaderLine('Accept');
        if (isset($type)){
            $this->container->set('view', $this->viewMap[$type]);
        }
        
        return $handler->handle($request);;
    }

}
