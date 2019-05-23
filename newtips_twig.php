<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\DealerTipSession;
Use \Solcre\lmsuy\Entity\ServiceTipSession;
date_default_timezone_set('America/Argentina/Buenos_Aires');

$mensaje1 = '';
$mensaje2 = '';


$datosUI = array();
$connection = new ConnectLmsuy_db;

/*
if ($connection->getDatosSessionById($_GET['id'])->end_at!=null)
{
	?>
	<mark> <code> La sesión ha finalizado </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="tips.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	<?php
	exit;
}*/

if (isset($_POST['dealerTip']))
{
	if ((is_numeric($_POST['dealerTip'])) and (is_numeric($_POST['serviceTip'])))
	{		
		$connection = new ConnectLmsuy_db;
		$connection->insertDealerTip($_POST['hour'], $_POST['dealerTip'], $_POST['idSession']);
		$connection->insertServiceTip($_POST['hour'], $_POST['serviceTip'], $_POST['idSession']);
		$template = 'tips.html.twig';
		$datosUI['message'] = 'Las propinas se agregaron exitosamente';
		$datosUI['breadcrumb'] = 'Tips';

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
	}
} 
else
{
	$template = 'newtips.html.twig';
	$datosUI['breadcrumb'] = 'Nuevo tip';
		// BUSQUEDA DE DATOS PARA LA UI


	//extraigo datos de la bdd
		$datosUI['session'] = [
		'idSession' => $_GET['id']
		];
}

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);


echo $twig->render($template, $datosUI);


?>