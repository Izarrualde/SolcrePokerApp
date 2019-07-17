<?php
namespace Solcre\lmsuy\View;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
/**
 * 
 */
class JsonView implements View
{
  public function render(ResponseInterface $response, $template, $data = [])
  {
    // copiar funcion render de twig,
    $response->getBody()->write(json_encode($data));
    return $response; 
  }
}
