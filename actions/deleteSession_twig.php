<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;

$session = new ConnectLmsuy_db;
/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))	
{
	die("error 404");
}*/

$session->deleteSession($_GET["id"]);
$mensaje = 'sesion eliminada';

//extraigo datos de bd
$connection = new ConnectLmsuy_db;

$datosSessions = $connection->getDatosSessions();

foreach ($datosSessions as $session) 
{
	$sessionObject = new SessionEntity($session->id, $session->created_at, $session->title, $session->description, null /*photo*/, $session->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $session->start_at, $session->real_start_at, $session->end_at);
	
	//$sessions[] = $sessionObject; // para que sirve este paso?

	$datosUI['sessions'][] = [
		'idSession' => $sessionObject->getIdSession(),
		'created_at' => $sessionObject->getDate(),
		'description' => $sessionObject->getDescription(),
		'startTime' => $sessionObject->getStartTimeReal(),
		'activePlayers' => $sessionObject->getActivePlayers(),
		'distinctPlayers' => $sessionObject->getTotalDistinctPlayers(),
		'seats' => $sessionObject->getSeats(),
		'endTime' => $sessionObject->getEndTime()
	];
	$datosUI['mensaje'] = $mensaje;
}
// DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('index.html.twig', $datosUI);
?>