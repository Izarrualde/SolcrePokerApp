<?php
namespace Solcre\lmsuy\View;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\RequestInterface as RequestInterface;
/**
 * 
 */
class JsonView implements View
{
  public function render(RequestInterface $request, ResponseInterface $response, $data = [])
  {
    // copiar funcion render de twig,
    if (!is_null($data)) {
      $response->getBody()->write(json_encode($data));  
    }
    
    return $response; 
  }
}
