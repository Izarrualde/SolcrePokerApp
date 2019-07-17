<?php
namespace Solcre\lmsuy\View;

use Psr\Http\Message\ResponseInterface as ResponseInterface;

interface View
{
  public function render(ResponseInterface $response, $template, $data = []);
}
