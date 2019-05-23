<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\BuyinSession;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;


/*if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idB"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}*/


if (isset($_POST["id"]))
	// ejecutar el update
{
	$connection->updateBuyin($_POST['amountCash'], $_POST['amountCredit'], '2', $_POST['hour'], $_POST['approved'], $_POST['id']);

	$message = 'Buyin actualizado exitosamente';
    $template = 'buyins.html.twig';
    $datosUI['breadcrumb'] = 'Buyins';


	//extraigo datos de la bdd necesarios para el template buyins.html.twig
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
	$datosUI['message'] = $message;
}
else
{
	//proporcionar datosUI con datos de autorreleno
	$buyin = $connection->getDatosSessionBuyinById($_GET["idB"]);

	$buyinObject = new BuyinSession($buyin[0]->id, $_GET['id'], $buyin[0]->session_user_id, $buyin[0]->amount_of_cash_money, $buyin[0]->amount_of_credit_money, $buyin[0]->currency_id, $buyin[0]->created_at, $buyin[0]->approved);

		$name = $connection->getDatosUserById($connection->getIdUserByUserSessionId($buyinObject->getSessionUserId()))->name;
		$lastname = $connection->getDatosUserById($connection->getIdUserByUserSessionId($buyinObject->getSessionUserId()))->last_name;

		$buyin = [
			'id' => $buyinObject->getId(),
			'idSession' => $buyinObject->getIdSession(),
			'amountCash' => $buyinObject->getAmountCash(),
			'amountCredit' => $buyinObject->getAmountCredit(),
			'hour' => $buyinObject->getHour(),
			'name' => $name,
			'lastname' => $lastname
		];
		$datosUI['session'] = [
			'buyin' => $buyin
		];

		$template = 'editBuyin.html.twig';
		$datosUI['breadcrumb'] = 'Editar Buyin';
}

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);


/*if (sizeof($datos)==0)
{
	die("error 404");
}*/




?>