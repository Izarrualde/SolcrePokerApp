<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\Service\BuyinSessionService;
Use \Solcre\lmsuy\Service\UserService;
Use \Solcre\lmsuy\Service\UserSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
//Use \Solcre\PokerApp\Exception\InsufficientBuyinException;

// BUSQUEDA DE DATOS PARA LA UI
$datosUI = array();

if (!isset($_GET['id']))
{
	header('Location: ../../index_twig.php');
	exit;
}

//extraigo datos de la bdd
$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$userService = new UserService($connection);
$userSessionService = new UserSessionService($connection, $userService, $sessionService);
$buyinSessionService = new BuyinSessionService($connection, $sessionService, $userSessionService);

$session = $sessionService->findOne($_GET['id']);

$buyins = $buyinSessionService->find($_GET['id']);

$datosUI['buyins'] = array();

foreach ($buyins as $buyin)  {
	$datosUI['buyins'][] = $buyin->toArray();
}

$datosUI['session'] = [
		'idSession' => $session->getIdSession(),
		'buyins' => $buyins
];
$datosUI['breadcrumb'] = 'Buyins';

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('buyins.html.twig', $datosUI);

?>