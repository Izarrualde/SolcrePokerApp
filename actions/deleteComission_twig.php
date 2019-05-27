<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\Service\ComissionSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);

$comissionSessionService = new ComissionSessionService($connection);

/*
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idC"]))	
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/
/*
$datos = $connection->getDatosSessionComissionById($_GET["idC"]);
//$datos1 = $session->getDatosSessionComissions();
if (sizeof($datos)==0)
{
	die("error 404");
}
*/
$comission = $comissionSessionService->findOne($_GET["idC"]);

$comissionSessionService->delete($comission);
$message = 'Comisión eliminada exitosamente';


//BUSQUEDA DE DATOS PARA LA UI
$session = $sessionService->findOne($_GET['id']);

$datosComissions = $comissionSessionService->find($_GET['id']);

$comissions = array();
foreach ($datosComissions as $comission)  {
	$comissions[] = $comission->toArray(); 
}

$datosUI['session'] = $session->toArray();
$datosUI['session']['comissions'] = $comissions;
$datosUI['breadcrumb'] = 'Comisiones';
$datosUI['message'] = $message;

// DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('comissions.html.twig', $datosUI);

?>