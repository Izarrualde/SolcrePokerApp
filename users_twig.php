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
//Use \Solcre\PokerApp\Exception\PlayerNotFoundException;

if (!isset($_GET['id']))
{
	header('Location: ../../index.php');
	exit;
}

$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$userService = new UserService($connection);
$userSessionService = new UserSessionService($connection, $userService, $sessionService);

$session = $sessionService->findOne($_GET['id']);
$datosUsersSession = $userSessionService->find($_GET['id']);

//$datosUI['buyins'] = array();
$usersSession = array();
foreach ($datosUsersSession as $userSession)  {
	$usersSession[] = $userSession->toArray(); 
}

$datosUI['session'] = $session->toArray();
$datosUI['session']['usersSession'] = $usersSession;
$datosUI['breadcrumb'] = 'Usuarios de Sesión';

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('users.html.twig', $datosUI);

?>