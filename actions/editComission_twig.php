<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\Service\ComissionSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$comissionSessionService = new ComissionSessionService($connection);
/*
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idC"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/

//$datos = $connection->getDatosSessionComissionById($_GET["idC"]);
/*
if (sizeof($datos)==0)
{
	die("error 404");
}*/
$session = $sessionService->findOne($_GET['id']);

$datosUI = array();

if (isset($_POST["id"]))
{
	$comission = new ComissionSession($_GET['idC'], $_GET['id'], $_POST['hour'], $_POST['comission']);

	$comissionSessionService->update($comission);

	$template = 'comissions.html.twig';
	$message = 'La comisión se actualizó exitosamente.';
	// BUSQUEDA DE DATOS PARA LA UI

	if (!isset($_GET['id']))
	{
		header('Location: ../../index_twig.php');
		exit;
	}

	//extraigo datos de la bdd
	$comissions = array();
	$datosComissions = $comissionSessionService->find($_GET['id']);

	foreach ($datosComissions as $comission) {
		$comissions[] = $comission->toArray(); 
	}

	$datosUI['session'] = $session->toArray();
	$datosUI['session']['comissions'] = $comissions;
	$datosUI['breadcrumb'] = 'Comisiones';
}
else
{
	//proporcionar datosUI con datos de autorreleno
	//$buyin = $connection->getDatosSessionBuyinById($_GET["idB"]);
	$comission = $comissionSessionService->findOne($_GET["idC"]);

	$datosUI['session'] = $session->toArray();
	$datosUI['session']['comission'] = $comission->toArray();
	$datosUI['breadcrumb'] = 'Editar Comisión';
	$template = 'editComission.html.twig';
}	


// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>