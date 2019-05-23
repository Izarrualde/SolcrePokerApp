<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$datosUI = array();
$connection = new ConnectLmsuy_db;

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

$mensaje1 = '';

if (isset($_POST['idSession'])) 
{
	if (is_numeric($_POST['comission']))
	{
		$connection = new ConnectLmsuy_db;
		$connection->insertComission($_POST['hour'], $_POST['comission'], $_POST['idSession']);
		$template = 'comissions.html.twig';
		
		//extraigo datos de la bdd
		$connection = new ConnectLmsuy_db;
		$datosSession = $connection->getDatosSessionById($_GET['id']);
		$datosComissionsSession = $connection->getDatosSessionComissions($_GET['id']);


		// hidrato objetos con datos de la bdd y a la vez desarrollo datosUI segun requierimientos.

		$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);


		foreach ($datosComissionsSession as $comission) 
		{

			$comissionObject = new ComissionSession($comission->id, $comission->session_id, $comission->created_at, $comission->comission);


			$comissions[] = [
				'id' => $comissionObject->getId(),
				'idSession' => $comissionObject->getIdSession(), //quisiera no tener este aca pero en el template al iterar en comission no logro acceder a session.id
				'date' => $comissionObject->getHour(),
				'comission' => $comissionObject->getComission(),
			];
		}

		$datosUI['session'] = [
				'idSession' => $session->getIdSession(),
				'comissions' => $comissions
		];
		$datosUI['breadcrumb'] = 'Comisiones';
		$datosUI['message'] = 'La comisión se agregó exitosamente';
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