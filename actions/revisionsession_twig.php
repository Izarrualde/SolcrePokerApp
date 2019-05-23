<?php

include "../vendor/autoload.php";

Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\UserEntity;
Use \Solcre\lmsuy\Entity\UserSession;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\Entity\ComissionSession;
Use \Solcre\lmsuy\Entity\DealerTipSession;
Use \Solcre\lmsuy\Entity\ServiceTipSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Exception\InsufficientBuyinException;
Use \Solcre\lmsuy\Exception\PlayerNotFoundException;
Use \Solcre\lmsuy\Exception\SessionFullException;
Use \Solcre\lmsuy\Exception\ComissionAlreadyAddedException;
Use \Solcre\lmsuy\Exception\DealerTipAlreadyAddedException;
Use \Solcre\lmsuy\Exception\ServiceTipAlreadyAddedException;

//Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;

if (!isset($_GET['id']))
{
	header('Location: ../../index.php');
	exit;
}

// BUSQUEDA DE DATOS PARA LA UI
$datosUI = array();

//extraigo datos de la bdd
$connection = new ConnectLmsuy_db;

$datosUsers = $connection->getDatosUsers();
$datosSession = $connection->getDatosSessionById($_GET['id']);


// hidrato objetos con datos de la bdd y a la vez desarrollo UI segun requerimientos.

$users = array();

foreach ($datosUsers as $user) 
{
	$usersObject = new UserEntity($user->id, $user->password, null /*mobile*/, $user->email, $user->last_name, $user->name, $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);

	$users[] = [
		'id'  => $usersObject->getId(),
		'password' => $usersObject->getPassword(),
		'email' => $usersObject->getEmail(),
		'lastname' => $usersObject->getLastname(),
		'name' => $usersObject->getName(),
		'username' => $usersObject->getUsername(),
		'multiplier' => $usersObject->getMultiplier(),
		'isActive' => $usersObject->getIsActive(),
		'hours' => $usersObject->getHours(),
		'points' => $usersObject->getPoints(),
		'results' => $usersObject->getResults(),
		'cashin' => $usersObject->getCashin()
	];
}

$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

$datosUsersSession = $connection->getDatosSessionsUsers($session->getIdSession());
foreach ($datosUsersSession as $user) 
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

$datosComissionsSession = $connection->getDatosSessionComissions($session->getIdSession());
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

$datosSessionBuyins = $connection->getDatosSessionBuyins($session->getIdSession());
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

$datosSessionDealerTips = $connection->getDatosSessionDealerTips($session->getIdSession());
foreach ($datosSessionDealerTips as $dealerTip) 
{
	$dealerTipObject = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->created_at, $dealerTip->dealer_tip);

	$dealerTips[] = [
		'id' => $dealerTipObject->getId(),
		'idSession' => $dealerTipObject->getIdSession(),
		'dealerTip' => $dealerTipObject->getDealerTip(),
		'hour' => $dealerTipObject->getHour()
	];	
}

$datosSessionServiceTips = $connection->getDatosSessionServiceTips($session->getIdSession());
foreach ($datosSessionServiceTips as $serviceTip) 
{
	$serviceTipsObject = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->created_at, $serviceTip->service_tip);

	$serviceTips[] = [
		'id' => $serviceTipsObject->getId(),
		'idSession' => $serviceTipsObject->getIdSession(),
		'serviceTip' => $serviceTipsObject->getServiceTip(),
		'hour' => $serviceTipsObject->getHour()
	];
}

$datosUI['session'] = [
		'idSession' => $session->getIdSession(),
		'validateSession' => $session->validateSession($session),
		'totalPlayed' => $session->getTotalPlayed(),
		'totalCashout' => $session->getTotalCashout(),
		'totalDealerTips' => $session->getDealerTipTotal(),
		'totalServiceTips' => $session->getServiceTipTotal(),
		'totalComission' => $session->getComissionTotal(),
		'usersSession' => $usersSession,
		'comissionsSession' => $comissions,
		'buyinsSession' => $buyins,
		'dealerTips' => $dealerTips,
		'serviceTips' => $serviceTips
];



// DISPLAY DE LA UI
echo getcwd();
echo "<br>";
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('revisionsession.html.twig', $datosUI);

?>