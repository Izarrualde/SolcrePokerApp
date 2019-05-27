<?php
include "../vendor/autoload.php";

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

$connection = new ConnectLmsuy_db;
/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idU"]))	
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/

/*$datos = $session->getDatosSessionUsersById($_GET["idU"]);

if (sizeof($datos)==0)
{
	die("error 404");
}*/

$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$userService = new UserService($connection);
$userSessionService = new UserSessionService($connection, $userService, $sessionService);

$userSession = $userSessionService->findOne($_GET["idUS"]);
$userSessionService->delete($userSession);
$message = 'Usuario eliminado exitosamente de la sesión';

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


// DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('users.html.twig', $datosUI);
?>