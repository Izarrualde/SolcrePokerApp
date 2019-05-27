<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\Service\BuyinSessionService;
Use \Solcre\lmsuy\Service\UserService;
Use \Solcre\lmsuy\Service\UserSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$datosUI = array();
$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$userService = new UserService($connection);
$userSessionService = new UserSessionService($connection, $userService, $sessionService);
$buyinSessionService = new BuyinSessionService($connection, $sessionService, $userSessionService);

$session = $sessionService->findOne($_GET['id']);

$datosUsersSession = $userSessionService->find($_GET['id']);

$datosUI = array();

if (isset($_POST['idSession']))
{	
	// formulario fue cargado debo insertar buyin y redirigir a buyin_twig.php con el mensaje de exito
	if ((is_numeric($_POST['amountCash'])) and (is_numeric($_POST['amountCredit'])))
	{
		$buyin = new BuyinSession(null, $_GET['id'], $_POST['idUserSession'], $_POST['amountCash'], $_POST['amountCredit'], '2', date('c'), $_POST['approved']);

		$buyinSessionService->add($buyin); 


		// BUSQUEDA DE DATOS PARA LA UI

		if (!isset($_GET['id']))
		{
			header('Location: ../../index_twig.php');
			exit;
		}

		//extraigo datos de la bdd

		$buyins = array();
		$datosBuyins = $buyinSessionService->find($_GET['id']);
		foreach ($datosBuyins as $buyin)
		{
			$buyins[] = $buyin->toArray(); 
		}

		$datosUI['session'] = $session->toArray();
		$datosUI['buyins'] = $buyins;
		$datosUI['breadcrumb'] = 'Buyins';

		$template = 'buyins.html.twig';
		$datosUI['message'] = 'el buyin se ingresó exitosamente.';

	}
} 
else
{
	//envio a usuario a newbuyin.html.twig con datos necesarios para mostrar formularios
	$template = 'newbuyins.html.twig';
	$datosUI['breadcrumb'] = 'Nuevo Buyin';
	
	$usersSession = array();

	foreach ($datosUsersSession as $userSession) 
	{
		$usersSession[] = $userSession->toArray();
	}

		$datosUI['users_session'] = $usersSession;
}



// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);


echo $twig->render($template, $datosUI);
/*
		<mark> <i class="far fa-grin-alt"></i> <code> El buyin se ingresó exitosamente </code></mark>
		<br> <br>
		<a class="btn btn-primary" href="buyins.php?id=<?php echo $_GET['id']; ?>"> volver </a>
		<?php
		exit;
	} else
	{
		if (!is_numeric($_POST['amountCash']))
		{
			$mensaje1 = 'El monto en efectivo ingresado no es valido';
		}
		if (!is_numeric($_POST['amountCredit']))
		{
			$mensaje2 = 'El monto en credito ingresado no es valido';
		}
	}
}
*/
//$usersSession = $connection->getDatosSessionsUsers($_GET['id']);





/*
<!--
if (isset($_POST['idSession']))
{
	if ((is_numeric($_POST['amountCash'])) and (is_numeric($_POST['amountCredit'])))
	{

		$connection->insertBuyin($_POST['hour'], $_POST['amountCash'], $_POST['amountCredit'], $_POST['idUser'], $_POST['approved'], $_POST['currency']); 
		//header();
		?>
		<mark> <i class="far fa-grin-alt"></i> <code> El buyin se ingresó exitosamente </code></mark>
		<br> <br>
		<a class="btn btn-primary" href="buyins.php?id=<?php echo $_GET['id']; ?>"> volver </a>
		<?php
		exit;
	} else
	{
		if (!is_numeric($_POST['amountCash']))
		{
			$mensaje1 = 'El monto en efectivo ingresado no es valido';
		}
		if (!is_numeric($_POST['amountCredit']))
		{
			$mensaje2 = 'El monto en credito ingresado no es valido';
		}
	}
}

$usersSession = $connection->getDatosSessionsUsers($_GET['id']);

?>
-->
*/
?>

