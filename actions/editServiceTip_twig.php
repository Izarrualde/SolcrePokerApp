<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\Service\DealerTipSessionService;
Use \Solcre\lmsuy\Service\ServiceTipSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ServiceTipSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$dealerTipSessionService = new DealerTipSessionService($connection);
$serviceTipSessionService = new ServiceTipSessionService($connection);
/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idT"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/
$session = $sessionService->findOne($_GET['id']);

$datosUI = array();

/*
if (sizeof($datos)==0)
{
	die("error 404");
}*/

if (isset($_POST["id"]))
{
	$serviceTip = new ServiceTipSession($_GET['idT'], $_GET['id'], $_POST['hour'], $_POST['serviceTip']);

	$serviceTipSessionService->update($serviceTip);

	$template = 'tips.html.twig';
	$message = 'El Service Tip se actualizó exitosamente.';

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
}
else
{
	//proporcionar datosUI con datos de autorreleno
	$serviceTip = $serviceTipSessionService->findOne($_GET["idT"]);

	$datosUI['session'] = $session->toArray();
	$datosUI['session']['serviceTip'] = $serviceTip->toArray();
	$datosUI['breadcrumb'] = 'Editar Service Tip';
	$template = 'editServiceTip.html.twig';
}	


// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>