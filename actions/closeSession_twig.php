<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\Entity\UserSession;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\BuyinSession;

date_default_timezone_set('America/Argentina/Buenos_Aires');
$connection = new ConnectLmsuy_db;

if (isset($_POST["id"]))
{
	$connection->closeUserSession($_POST['id'], $_POST['idUser'], $_POST['cashout'], $connection->getDatosSessionUserById($_POST['id'])->start_at, $_POST['end']);
	$datosUsers = $connection->getDatosSessionUserById($_POST['id']);
	$message = 'El usuario ha salido de la sesión';
    $template = 'users.html.twig';	
    $datosUI['breadcrumb'] = 'Usuarios';


    // extraigo datos de la bdd necesarios para el template users.html.twig
	$datosSession = $connection->getDatosSessionById($_GET['id']);
	$datosUsers = $connection->getDatosSessionsUsers($_GET['id']);

	$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

	$buyins = $connection->getDatosSessionBuyins($_GET['id']);

	foreach ($buyins as $buyin) 
	{
		$session->sessionBuyins[] = new BuyinSession($buyin->id, $_GET['id'], $buyin->session_user_id, $buyin->amount_of_cash_money, $buyin->amount_of_credit_money, $buyin->currency_id, $buyin->created_at, $buyin->approved);
	}

	foreach ($datosUsers as $user) 
	{
		$userObject = new UserSession($user->id, $session, $user->user_id, $user->is_approved, $user->points, $user->cashout, $user->start_at, $user->end_at);

		$name = $connection->getDatosUserById($userObject->getIdUser())->name;
		$lastname = $connection->getDatosUserById($userObject->getIdUser())->last_name;

		$cashin = $userObject->getCashin();
		$totalCredit = $userObject->getTotalCredit();

		$usersSession[] = [
			'id' => $userObject->getId(),
			'idSession' => $userObject->getSession()->getIdSession(),
			'idUser' => $userObject->getIdUser(),
			'isApproved' => $userObject->getIsApproved(),
			'points' => $userObject->getAccumulatedPoints(),
			'cashout' => $userObject->getCashout(),
			'cashin' => $userObject->getCashin(),
			'startTime' => $userObject->getStart(),
			'endTime' => $userObject->getEnd(),
			'name' => $name,
			'lastname' => $lastname,
			'cashin' => $cashin,
			'totalCredit' => $totalCredit
		];
	}

	$datosUI['session'] = [
		'idSession' => $session->getIdSession(),
		'usersSession' => $usersSession
	];
	$datosUI['message'] = $message;
}
else
{
	// datos para autocomplete
	$template = 'closeUserSession.html.twig';
	$datosUI['breadcrumb'] = 'Cerrar sesión de usuario';

	$datosSession = $connection->getDatosSessionById($_GET['id']);
	$datosUser = $connection->getDatosSessionUserById($_GET['idUS']);

	$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

	$userObject = new UserSession($datosUser->id, $session, $datosUser->user_id, $datosUser->is_approved, $datosUser->points, $datosUser->cashout, $datosUser->start_at, $datosUser->end_at);

	$name = $connection->getDatosUserById($userObject->getIdUser())->name;
	$lastname = $connection->getDatosUserById($userObject->getIdUser())->last_name;

	$cashin = $userObject->getCashin();
	$totalCredit = $userObject->getTotalCredit();

	$userSession = [
		'id' => $userObject->getId(),
		'idSession' => $userObject->getSession()->getIdSession(),
		'idUser' => $userObject->getIdUser(),
		'isApproved' => $userObject->getIsApproved(),
		'points' => $userObject->getAccumulatedPoints(),
		'cashout' => $userObject->getCashout(),
		'cashin' => $userObject->getCashin(),
		'startTime' => $userObject->getStart(),
		'endTime' => $userObject->getEnd(),
		'name' => $name,
		'lastname' => $lastname,
		'cashin' => $cashin,
		'totalCredit' => $totalCredit
	];

	$datosUI['session'] = [
		'idSession' => $session->getIdSession(),
		'userSession' => $userSession
	];	
}
// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>
