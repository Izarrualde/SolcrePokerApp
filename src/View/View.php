<?php
namespace Solcre\lmsuy\View;

use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\RequestInterface as RequestInterface;

interface View
{
    public function render(RequestInterface $request, ResponseInterface $response, $data = []);
}
