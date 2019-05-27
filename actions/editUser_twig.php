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
//$buyinSessionService = new BuyinSessionService($connection, $sessionService, $userSessionService);

$session = $sessionService->findOne($_GET['id']);

$datosUsersSession = $userSessionService->find($_GET['id']);

$datosUI = array();
$session = $sessionService->findOne($_GET['id']);

if (isset($_POST["idSession"]))
{
	$userSession = new UserSession($_POST['id'], $session, $_POST['idUser'], $_POST['approved'], $_POST['accumulatedPoints'], $_POST['cashout'], $_POST['start'], $_POST['end']);


	$userSessionService->update($userSession);
	$message = 'El usuario fue actualizado exitosamente';
    $template = 'users.html.twig';	

	//BUSQUEDA DE DATOS PARA LA UI
	$session = $sessionService->findOne($_GET['id']);
	$datosUsersSession = $userSessionService->find($_GET['id']);

	//$datosUI['buyins'] = array();
	$usersSession = array();
	foreach ($datosUsersSession as $userSession)  {
		$usersSession[] = $userSession->toArray(); 
	}

	$datosUI['session'] = $session->toArray();
	$datosUI['session']['usersSession'] = $usersSession;
	$datosUI['message'] = $message;
	$datosUI['breadcrumb'] = 'Usuarios de Sesión';
}
else
{
	//proporcionar datosUI con datos de autorreleno
	$userSession = $userSessionService->findOne($_GET["idUS"]);

	$datosUI['userSession'] = $userSession->toArray();
	$datosUI['session'] = $session->toArray();
	$datosUI['breadcrumb'] = 'Editar UserSession';
	$template = 'editUser.html.twig';

}
// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);



?>