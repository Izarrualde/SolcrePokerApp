<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
/*
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idC"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/

//$datos = $connection->getDatosSessionComissionById($_GET["idC"]);
/*
if (sizeof($datos)==0)
{
	die("error 404");
}*/

if (isset($_POST["id"]))
{
	$connection->updateComission($_POST["idSession"], $_POST["hour"], $_POST["comission"], $_POST["id"]);

	$template = 'comissions.html.twig';
	$datosUI['breadcrumb'] = 'Comisiones';
	$message = "Comisión actualizada exitosamente";
		//extraigo datos de la bdd
	
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
	$datosUI['message'] = $message;
}
else
{
	//proporcionar datosUI con datos de autorreleno

	$com = $connection->getDatosSessionComissionById($_GET["idC"]);

	$comissionObject = new ComissionSession($com[0]->id, $com[0]->session_id, $com[0]->created_at, $com[0]->comission);

		$datosUI['comission'] = [
			'id' => $comissionObject->getId(),
			'idSession' => $comissionObject->getIdSession(),
			'hour' => $comissionObject->getHour(),
			'comission' => $comissionObject->getComission(),
		];
/*
		$datosUI['comission'] = [
			'id' => $
			'idSession' =>
			'comission' => $comission
		];*/



		$template = 'editComission.html.twig';
		$datosUI['breadcrumb'] = 'Editar Comision';
}	


// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>