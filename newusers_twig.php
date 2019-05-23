<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\UserEntity;
Use \Solcre\lmsuy\Entity\UserSession;
Use \Solcre\lmsuy\Entity\BuyinSession;


date_default_timezone_set('America/Argentina/Buenos_Aires');

$datosUI = array();
$connection = new ConnectLmsuy_db;

/*
if ($connection->getDatosSessionById($_GET['id'])->end_at!=null)
{
	?>
	<mark> <code> La sesión ha finalizado </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="users.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	<?php
	exit;
}
*/

/*
//chequeo variables que deben ser no NULL.
if (empty($_POST['accumulatedPoints']))
{
	$points = 0;
} else 
{
	$points = $_POST['accumulatedPoints'];
}

if (empty($_POST['approved']))
{
	$approved = 1;
} else 
{
	$approved = $_POST['approved'];
}
*/

if (isset($_POST['idSession']))
{
	$start_at = !empty($_POST['start']) ? $_POST['start'] : null;
	$end_at = !empty($_POST['end']) ? $_POST['end'] : null;
	$connection->insertUserInSession(date('c'), $_POST['accumulatedPoints'], $_POST['cashout'], $start_at, $end_at, $_POST['approved'], $_POST['idSession']);
	$template = 'users.html.twig';
	$message = 'Se agregó el usuario exitosamente a la sesión.';
	$datosUI['breadcrumb'] = 'Usuarios de Sesion';
	$datosUI['message'] = $message;

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
}
else
{
	//envio a usaruaio a newusers_twig.php con datos necesarios para mostrar formularios
	$template = 'newusers.html.twig';
	$datosUI['breadcrumb'] = 'Nuevo usuario de sesion';

	//extraigo datos de la bdd
	$datosSession = $connection->getDatosSessionById($_GET['id']);
	$datosUsers = $connection->getDatosUsers();

	$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

	foreach ($datosUsers as $user) 
	{
		$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name, $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);

		$users[] = [
			'id' => $userObject->getId(),
			'name' => $userObject->getName(),
			'lastname' => $userObject->getLastname()
		];
	}

		$datosUI['session'] = [
		'idSession' => $session->getIdSession(),
		'users' => $users
		];
}

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);


echo $twig->render($template, $datosUI);


?>
