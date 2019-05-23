<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\BuyinSession;

$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idB"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

/*$datos = $connection->getDatosSessionBuyinById($_GET["idB"]);
if (sizeof($datos)==0)
{
	die("error 404");
}*/

$connection->deleteBuyin($_GET["idB"]);
$message = 'Buyin eliminado exitosamente';

// $datosUI cargarlo con ..
//extraigo datos de la bdd
$datosSession = $connection->getDatosSessionById($_GET['id']);
$datosSessionBuyins = $connection->getDatosSessionBuyins($_GET['id']);

$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

foreach ($datosSessionBuyins as $buyin) 
{
	$buyinObject = new BuyinSession($buyin->id, $_GET['id'], $buyin->session_user_id, $buyin->amount_of_cash_money, $buyin->amount_of_credit_money, $buyin->currency_id, $buyin->created_at, $buyin->approved);

	$name = $connection->getDatosUserById($connection->getIdUserByUserSessionId($buyinObject->getSessionUserId()))->name;
	$lastname = $connection->getDatosUserById($connection->getIdUserByUserSessionId($buyinObject->getSessionUserId()))->last_name;

	$buyins[] = [
		'id' => $buyinObject->getId(),
		'idSession' => $buyinObject->getIdSession(),
		'amountCash' => $buyinObject->getAmountCash(),
		'amountCredit' => $buyinObject->getAmountCredit(),
		'hour' => $buyinObject->getHour(),
		'name' => $name,
		'lastname' => $lastname
	];

}
$datosUI['session'] = [
		'idSession' => $session->getIdSession(),
		'buyins' => $buyins
];
$datosUI['breadcrumb'] = 'Buyins';
$datosUI['message'] = $message;


// DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('buyins.html.twig', $datosUI);

?>