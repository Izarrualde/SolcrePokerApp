<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\UserSession;
Use \Solcre\lmsuy\Service\UserSessionService;
Use \Solcre\lmsuy\Service\UserService;
Use \Solcre\lmsuy\Service\SessionService;


date_default_timezone_set('America/Argentina/Buenos_Aires');
$connection = new ConnectLmsuy_db;

$sessionService = new SessionService($connection);
$userService = new UserService($connection);
$userSessionService = new UserSessionService($connection, $userService, $sessionService);

$session = $sessionService->findOne($_GET['id']);


if (isset($_POST["id"]))
{	
	var_dump($_POST);
	$userSessionObject = $userSessionService->findOne($_POST['id']);
	$userSessionService->close($userSessionObject, $_POST['cashout'], $_POST['end']);

	//$datosUsers = $connection->getDatosSessionUserById($_POST['id']);
	$message = 'El usuario ha salido de la sesión';
    $template = 'users.html.twig';	

	$datosUsersSession = $userSessionService->find($_POST['idSession']);
	$usersSession = array();
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
	// datos para autocomplete
	//proporcionar datosUI con datos de autorreleno
	$userSession = $userSessionService->findOne($_GET["idUS"]);

	$datosUI['session'] = $session->toArray();
	$datosUI['session']['userSession'] = $userSession->toArray();
	$datosUI['breadcrumb'] = 'Cerrar Sesión de Usuario';
	$template = 'closeUserSession.html.twig';

}
// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>