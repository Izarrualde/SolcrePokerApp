<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\Service\DealerTipSessionService;
Use \Solcre\lmsuy\Service\ServiceTipSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;


$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);

$serviceTipSessionService = new ServiceTipSessionService($connection);
$dealerTipSessionService = new DealerTipSessionService($connection);

/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idT"]))
{
	die("error 404");
}

$datos = $connection->getDatosSessionServiceTipById($_GET["idT"]);

if (sizeof($datos)==0)
{
	die("error 404");
}*/
$serviceTip = $serviceTipSessionService->findOne($_GET["idT"]);

$serviceTipSessionService->delete($serviceTip);
$template = 'tips.html.twig';
$message = 'Service Tip eliminado exitosamente';


//BUSQUEDA DE DATOS PARA LA UI
$session = $sessionService->findOne($_GET['id']);

// extraigo datos de al bdd

$dealerTips = array();
$datosDealerTips = $dealerTipSessionService->find($_GET['id']);
foreach ($datosDealerTips as $dealerTip)
{
	$dealerTips[] = $dealerTip->toArray(); 
}

$serviceTips = array();
$datosServiceTips = $serviceTipSessionService->find($_GET['id']);
foreach ($datosServiceTips as $serviceTip)
{
	$serviceTips[] = $serviceTip->toArray(); 
}

$datosUI['session'] = $session->toArray();
$datosUI['session']['dealerTips'] = $dealerTips;
$datosUI['session']['serviceTips'] = $serviceTips;
$datosUI['breadcrumb'] = 'Tips';
$datosUI['message'] = $message;


// DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('tips.html.twig', $datosUI);

?>