<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\DealerTipSession;
Use \Solcre\lmsuy\Entity\ServiceTipSession;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Service\DealerTipSessionService;
Use \Solcre\lmsuy\Service\ServiceTipSessionService;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$datosUI = array();
$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$dealerTipSessionService = new DealerTipSessionService($connection);
$serviceTipSessionService = new ServiceTipSessionService($connection);

$session = $sessionService->findOne($_GET['id']);
//$mensaje1 = '';
//$mensaje2 = '';

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
		$dealerTip = new DealerTipSession($_POST['id'], $_POST['idSession'], $_POST['hour'], $_POST['dealerTip']);

		$serviceTip = new ServiceTipSession($_POST['id'], $_POST['idSession'], $_POST['hour'], $_POST['serviceTip']);

		$dealerTipSessionService->add($dealerTip); 
		$serviceTipSessionService->add($serviceTip);
		$template = 'tips.html.twig';
		$message = 'los tips se ingresaron exitosamente.';

		// BUSQUEDA DE DATOS PARA LA UI

		if (!isset($_GET['id']))
		{
			header('Location: ../../index_twig.php');
			exit;
		}

		//extraigo datos de la bdd

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

} 
else
{
	$template = 'newtips.html.twig';
	$datosUI['breadcrumb'] = 'Nuevos Tips';
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