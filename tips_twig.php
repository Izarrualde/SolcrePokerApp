<?php
include "vendor/autoload.php";
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\DealerTipSession;
Use \Solcre\lmsuy\Entity\ServiceTipSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Service\DealerTipSessionService;
Use \Solcre\lmsuy\Service\ServiceTipSessionService;

/*if (!isset($_GET['id']))
{
	header('Location: ../../index_twig.php');
	exit;
}*/

// BUSQUEDA DE DATOS PARA LA UI
$datosUI = array();

// extraigo datos de al bdd
$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$dealerTipSessionService = new DealerTipSessionService($connection);
$serviceTipSessionService = new ServiceTipSessionService($connection);

$session = $sessionService->findOne($_GET['id']);
$dealerTipsSession = $dealerTipSessionService->find($_GET['id']);

$serviceTipsSession = $serviceTipSessionService->find($_GET['id']);


foreach ($dealerTipsSession as $dealerTip) 
{
	$dealerTips[] = $dealerTip->toArray();	
}

foreach ($serviceTipsSession as $serviceTip) 
{
	$serviceTips[] = $serviceTip->toArray();	
}

$datosUI['session'] = $session->toArray();
$datosUI['session']['serviceTips'] = $serviceTips;
$datosUI['session']['dealerTips'] = $dealerTips;

$datosUI['breadcrumb'] = 'Tips';

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('tips.html.twig', $datosUI);

?>