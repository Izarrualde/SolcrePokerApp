<?php
namespace Solcre\lmsuy\BaseController;

use Exception;
use Doctrine\ORM\EntityManager;
use Slim\Views\Twig;
use \Solcre\lmsuy\View\TwigWrapperView;
use \Solcre\lmsuy\View\JsonView;

class BuyinSessionController
{
    const STATUS_CODE_201 = 201;
    const STATUS_CODE_204 = 204;
    const STATUS_CODE_400 = 400;
    const STATUS_CODE_404 = 404;
    const STATUS_CODE_500 = 500;

    public function __construct(	)
    {

    }
}