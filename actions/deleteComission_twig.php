<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;

$connection = new ConnectLmsuy_db;

/*
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idC"]))	
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/
/*
$datos = $connection->getDatosSessionComissionById($_GET["idC"]);
//$datos1 = $session->getDatosSessionComissions();
if (sizeof($datos)==0)
{
	die("error 404");
}
*/
$connection->deleteComission($_GET["idC"]);
$message = 'Comisión eliminada exitosamente';


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
$datosUI['breadcrumb'] = 'Comisiones';
$datosUI['message'] = $message;

// DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('comissions.html.twig', $datosUI);

?>