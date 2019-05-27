<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\Service\BuyinSessionService;
Use \Solcre\lmsuy\Service\UserService;
Use \Solcre\lmsuy\Service\UserSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$userService = new UserService($connection);
$userSessionService = new UserSessionService($connection, $userService, $sessionService);
$buyinSessionService = new BuyinSessionService($connection, $sessionService, $userSessionService);

if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idB"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

/*$datos = $connection->getDatosSessionBuyinById($_GET["idB"]);
if (sizeof($datos)==0)
{
	die("error 404");
}*/

$buyin = $buyinSessionService->findOne($_GET["idB"]);
$buyinSessionService->delete($buyin);
$message = 'Buyin eliminado exitosamente';

//BUSQUEDA DE DATOS PARA LA UI
$session = $sessionService->findOne($_GET['id']);

$datosBuyins = $buyinSessionService->find($_GET['id']);

$buyins = array();
foreach ($datosBuyins as $buyin)  {
	$buyins[] = $buyin->toArray(); 
}

$datosUI['session'] = $session->toArray();
$datosUI['buyins'] = $buyins;
$datosUI['breadcrumb'] = 'Buyins';
$datosUI['message'] = $message;


// DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('buyins.html.twig', $datosUI);

?>