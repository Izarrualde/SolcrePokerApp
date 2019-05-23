<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Service\SessionService;

date_default_timezone_set('America/Argentina/Buenos_Aires');
$connection = new ConnectLmsuy_db;
/*if (!isset($_GET["id"]))
{
	die("error 404"); 
}*/

$datos = $connection->getDatosSessionById($_GET["id"]);
/*
if (empty($datos))
{
	die("error 404");
}*/

$sessionService = new SessionService($connection);

if (isset($_POST["idSession"]))
{
	$connection->updateSession($_POST['created_at'], $_POST['title'], $_POST['description'], $_POST['count_of_seats'], $_POST['real_start_at'], $_POST['end_at'], $_POST['idSession']);
	$mensaje = 'sesion actualizada';
	$template = 'index.html.twig';

//$datosUsers = $connection->getDatosUsers();
//$datosSessions = $connection->getDatosSessions();
	$sessions = $sessionService->find();
	//$connection->insertSession();
	$datosUI['mensaje'] = "La sesion se agregó exitosamente";
	$datosUI['sessions'] = array();//

	foreach ($sessions as $sessionObject)
	{
		$datosUI['sessions'][] = $sessionObject->toArray();
	}
	$datosUI['css'] = '../css/bootstrap.min.css';
}
else
{
	//datos para rellenar
	$session = $sessionService->findOne($_GET["id"]);
	$datosUI['session'] = $session->toArray();
	$template = 'editSession.html.twig';
}


// hidrato objetos con datos de la bdd y a la vez desarrollo datosUI segun requerimientos



// DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates'); 
$twig = new \Twig\Environment($loader);
echo $twig->render($template, $datosUI); 


?>
