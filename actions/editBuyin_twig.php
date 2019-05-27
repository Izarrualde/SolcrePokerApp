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

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$userService = new UserService($connection);
$userSessionService = new UserSessionService($connection, $userService, $sessionService);
$buyinSessionService = new BuyinSessionService($connection, $sessionService, $userSessionService);

$session = $sessionService->findOne($_GET['id']);

$datosUsersSession = $userSessionService->find($_GET['id']);

$datosUI = array();


/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idB"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/


if (isset($_POST["id"]))
	// ejecutar el update
{
	$buyin = new BuyinSession($_GET['idB'], $_GET['id'], $_POST['idSessionUser'], $_POST['amountCash'], $_POST['amountCredit'], '2', date('c'), $_POST['approved']);

	$buyinSessionService->update($buyin);
	$template = 'buyins.html.twig';
	$message = 'el buyin se actualizó exitosamente.';

	// BUSQUEDA DE DATOS PARA LA UI

	if (!isset($_GET['id']))
	{
		header('Location: ../../index_twig.php');
		exit;
	}

	//extraigo datos de la bdd
	$buyins = array();
	$datosBuyins = $buyinSessionService->find($_GET['id']);

	foreach ($datosBuyins as $buyin) {
		$buyins[] = $buyin->toArray(); 
	}

	$datosUI['session'] = $session->toArray();
	$datosUI['buyins'] = $buyins;
	$datosUI['breadcrumb'] = 'Buyins';
	$datosUI['breadcrumb'] = 'Buyins';
	$datosUI['message'] = $message;
}

	
else
{
	//proporcionar datosUI con datos de autorreleno
	//$buyin = $connection->getDatosSessionBuyinById($_GET["idB"]);
	$buyin = $buyinSessionService->findOne($_GET["idB"]);

	$datosUI['buyin'] = $buyin->toArray();
	$datosUI['session'] = $session->toArray();
	$datosUI['breadcrumb'] = 'Editar Buyin';
	$template = 'editBuyin.html.twig';

}

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);


/*if (sizeof($datos)==0)
{
	die("error 404");
}*/




?>