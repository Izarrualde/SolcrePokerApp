<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Service\ComissionSessionService;

// BUSQUEDA DE DATOS PARA LA UI
$datosUI = array();

if (!isset($_GET['id']))
{
	header('Location: ../../index_twig.php');
	exit;
}

//extraigo datos de la bdd
$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$comissionSessionService = new ComissionSessionService($connection);

$session = $sessionService->findOne($_GET['id']);
$comissions = $comissionSessionService->find($_GET['id']);

foreach ($comissions as $comission) 
{
	$datosUI['comission'][] = $comission->toArray();
}

$datosUI['session'] = $session->toArray();
$datosUI['session']['comissions'] = $comissions;

$datosUI['breadcrumb'] = 'Comissions';

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('comissions.html.twig', $datosUI);

?>