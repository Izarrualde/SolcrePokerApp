<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\DealerTipSession;
Use \Solcre\lmsuy\Entity\ServiceTipSession;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idT"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/

$datos = $connection->getDatosSessionDealerTipById($_GET["idT"]);
/*
if (sizeof($datos)==0)
{
	die("error 404");
}*/

if (isset($_POST["id"]))
{
	$connection->updateDealerTip($_POST["idSession"], $_POST["hour"], $_POST["dealerTip"], $_POST["id"]);
	$message = "DealerTip actualizado exitosamente";
	$template = 'tips.html.twig';	
	$datosUI['breadcrumb'] = 'Tips';

	// extraigo datos de al bdd
	$datosSession = $connection->getDatosSessionById($_GET['id']);
	$datosSessionDealerTips = $connection->getDatosSessionDealerTips($_GET['id']);
	$datosSessionServiceTips = $connection->getDatosSessionServiceTips($_GET['id']);

	// hidrato objetos con datos de la bdd y a la vez desarrollo datosUI segun requierimientos.

	$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

	foreach ($datosSessionDealerTips as $dealerTip) 
	{
		$dealerTipObject = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->created_at, $dealerTip->dealer_tip);

		$dealerTips[] = [
			'id' => $dealerTipObject->getId(),
			'idSession' => $dealerTipObject->getIdSession(),
			'dealerTip' => $dealerTipObject->getDealerTip(),
			'hour' => $dealerTipObject->getHour()
		];	
	}


	foreach ($datosSessionServiceTips as $serviceTip) 
	{
		$serviceTipsObject = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->created_at, $serviceTip->service_tip);

		$serviceTips[] = [
			'id' => $serviceTipsObject->getId(),
			'idSession' => $serviceTipsObject->getIdSession(),
			'serviceTip' => $serviceTipsObject->getServiceTip(),
			'hour' => $serviceTipsObject->getHour()
		];
	}

	$datosUI['session'] = [
		'idSession' => $session->getIdSession(),
		'serviceTips' => $serviceTips,
		'dealerTips' => $dealerTips
	];
	$datosUI['message'] = $message;
}
else
{
	//proporcionar datosUI con datos de autorreleno
	$dealerTip = $connection->getDatosSessionDealerTipById($_GET["idT"]);

	$dealerTipObject = new DealerTipSession($dealerTip[0]->id, $dealerTip[0]->session_id, $dealerTip[0]->created_at, $dealerTip[0]->dealer_tip);

	$dealerTip = [
		'id' => $dealerTipObject->getId(),
		'idSession' => $dealerTipObject->getIdSession(),
		'dealerTip' => $dealerTipObject->getDealerTip(),
		'hour' => $dealerTipObject->getHour()
	];	
	

		$datosUI['session'] = [
			'dealerTip' => $dealerTip,
			'idSession' => $dealerTipObject->getIdSession()
		];

	$template = 'editDealerTip.html.twig';
	$datosUI['breadcrumb'] = 'Editar Dealer Tip';
}


// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>