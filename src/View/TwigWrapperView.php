<?php
namespace Solcre\lmsuy\View;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as ResponseInterface;
use Psr\Http\Message\RequestInterface as RequestInterface;

class TwigWrapperView implements View
{
    protected $template;
    protected $twigView;

    public function __construct(Twig $twigView)
    {
        $this->twigView = $twigView;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function render(RequestInterface $request, ResponseInterface $response, $data = [])
    {
        $template = isset($this->template) ? $this->template : $this->getTemplateFromRequest($request);

        // setear template cuando construyo esta clase
        return $this->twigView->render($response, $template, $data);
    }

    protected function getTemplateFromRequest(RequestInterface $request)
    {
        $route = $request->getAttribute("route");

        $string = $route->getCallable();
        $folder = lcfirst(explode('Controller', $string)[0]);

        $file = explode(':', $string)[1];

        $template = $folder.'/'.$file.'.html.twig';

        return $template;
    }
}
