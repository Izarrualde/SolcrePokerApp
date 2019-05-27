<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\Service\BuyinSessionService;
Use \Solcre\lmsuy\Service\UserService;
Use \Solcre\lmsuy\Service\UserSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\UserSession;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\Entity\UserEntity;

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$userService = new UserService($connection);
$userSessionService = new UserSessionService($connection, $userService, $sessionService);

$session = $sessionService->findOne($_GET['id']);


$datosUI = array();


if (isset($_POST['idSession']))
{
	$start_at = !empty($_POST['start']) ? $_POST['start'] : null;
	$end_at = !empty($_POST['end']) ? $_POST['end'] : null;
	$userSession = new UserSession(null, /*$sessionService->findOne($connection->getIdSessionbyIdUserSession($id)) */$session, $_POST['id'], $_POST['approved'], $_POST['accumulatedPoints'], $_POST['cashout'], $start_at, $end_at);
	$userSessionService->add($userSession);

	$template = 'users.html.twig';
	$message = 'Se agregó el usuario exitosamente a la sesión.';



	//BUSQUEDA DE DATOS PARA LA UI
	$usersSession = array();
	$session = $sessionService->findOne($_GET['id']);
	$datosUsersSession = $userSessionService->find($_GET['id']);
	foreach ($datosUsersSession as $userSession)  
	{
		$usersSession[] = $userSession->toArray(); 
	}

	$datosUI['session'] = $session->toArray();
	$datosUI['session']['usersSession'] = $usersSession;
	$datosUI['breadcrumb'] = 'Usuarios de Sesión';
	$datosUI['message'] = $message;

}
else
{
	//envio a usaruaio a newusers_twig.php con datos necesarios para mostrar formularios
	$template = 'newusers.html.twig';
	$datosUI['breadcrumb'] = 'Nuevo usuario de sesion';

	//extraigo datos de la bdd

	$usersSession = array();
	$users = array();
	$session = $sessionService->findOne($_GET['id']);
	$datosUsersSession = $userSessionService->find($_GET['id']);
	$datosUsers = $userService->find();

	foreach ($datosUsersSession as $userSession) 
	{
		$usersSession[] = $userSession->toArray();
	}

	foreach ($datosUsers as $user) 
	{
		$users[] = $user->toArray();
	}


	$datosUI['session'] = $session->toArray();
	$datosUI['session']['usersSession'] = $usersSession;
	$datosUI['users'] = $users;
}

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);


echo $twig->render($template, $datosUI);


?>
