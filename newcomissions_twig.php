<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\Service\ComissionSessionService;
Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$datosUI = array();
$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);
$comissionSessionService = new ComissionSessionService($connection);

/*
if ($connection->getDatosSessionById($_GET['id'])->end_at!=null)
{
	?>
	<mark> <code> La sesión ha finalizado </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="comissions.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	<?php
	exit;
}
*/

$session = $sessionService->findOne($_GET['id']);

$datosUI = array();

if (isset($_POST['idSession'])) 
{
	if (is_numeric($_POST['comission']))
	{
		$comission = new ComissionSession($_POST['id'], $_POST['idSession'], $_POST['hour'], $_POST['comission']);

		$comissionSessionService->add($comission); 
		$template = 'comissions.html.twig';
		$message = 'la comission se ingresó exitosamente.';

		// BUSQUEDA DE DATOS PARA LA UI

		if (!isset($_GET['id']))
		{
			header('Location: ../../index_twig.php');
			exit;
		}

		//extraigo datos de la bdd

		$comissions = array();
		$datosComissions = $comissionSessionService->find($_GET['id']);
		foreach ($datosComissions as $comission)
		{
			$comissions[] = $comission->toArray(); 
		}
		$datosUI['session'] = $session->toArray();
		$datosUI['session']['comissions'] = $comissions;
		$datosUI['breadcrumb'] = 'Comissions';
		$datosUI['message'] = $message;
	}
} 
else
{
	$template = 'newcomissions.html.twig';
	$datosUI['breadcrumb'] = 'Nueva comision';
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