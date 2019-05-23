<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\UserSession;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$datosUI = array();
$connection = new ConnectLmsuy_db;

if (isset($_POST['idSession']))
{
	// formulario fue cargado debo insertar buyin y redirigir a buyin_twig.php con el mensaje de exito
	if ((is_numeric($_POST['amountCash'])) and (is_numeric($_POST['amountCredit'])))
	{

	    $idUserSession = $connection->getIdUserSessionByIdUser($_POST['idUser']);
		$connection->insertBuyin($_POST['hour'], $_POST['amountCash'], $_POST['amountCredit'], $idUserSession, $_POST['approved'], '2'); 
		$template = 'buyins.html.twig';
		$datosUI['breadcrumb'] = 'Buyins';
		$datosUI['message'] = 'el buyin se ingresó exitosamente.';
		$connection = new ConnectLmsuy_db;
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
	}
} 
else
{
	//envio a usaruaio a newbuyin.html.twig con datos necesarios para mostrar formularios
	$template = 'newbuyins.html.twig';
	$datosUI['breadcrumb'] = 'Nuevo Buyin';


	//public function insertBuyin($hour, $amountCash, $amountCredit, $IdSessionUser, $approved, $currency)

	/*if ($connection->getDatosSessionById($_GET['id'])->end_at!=null)
	{
		?>
		<mark> <code> La sesión ha finalizado </code></mark>
		<br> <br>
		<a class="btn btn-primary" href="buyins.php?id=<?php echo $_GET['id']; ?>"> volver </a>
		<?php
		exit;
	}*/

	// BUSQUEDA DE DATOS PARA LA UI

	//extraigo datos de la bdd
	$datosSession = $connection->getDatosSessionById($_GET['id']);
	$datosUsers = $connection->getDatosSessionsUsers($_GET['id']);

	$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

	foreach ($datosUsers as $user) 
	{
		$userObject = new UserSession($user->id, $session, $user->user_id, $user->is_approved, $user->points, $user->cashout, $user->start_at, $user->end_at);

		$name = $connection->getDatosUserById($userObject->getIdUser())->name;
		$lastname = $connection->getDatosUserById($userObject->getIdUser())->last_name;

		$usersSession[] = [
			'id' => $userObject->getId(),
			'idSession' => $userObject->getSession()->getIdSession(),
			'idUser' => $userObject->getIdUser(),
			'name' => $name,
			'lastname' => $lastname,
			'endTime' => $userObject->getEnd()
		];
	}

		$datosUI['session'] = [
		'idSession' => $session->getIdSession(),
		'usersSession' => $usersSession
		];
		var_dump($datosUI);
}



// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);


echo $twig->render($template, $datosUI);
/*
		<mark> <i class="far fa-grin-alt"></i> <code> El buyin se ingresó exitosamente </code></mark>
		<br> <br>
		<a class="btn btn-primary" href="buyins.php?id=<?php echo $_GET['id']; ?>"> volver </a>
		<?php
		exit;
	} else
	{
		if (!is_numeric($_POST['amountCash']))
		{
			$mensaje1 = 'El monto en efectivo ingresado no es valido';
		}
		if (!is_numeric($_POST['amountCredit']))
		{
			$mensaje2 = 'El monto en credito ingresado no es valido';
		}
	}
}
*/
//$usersSession = $connection->getDatosSessionsUsers($_GET['id']);





/*
<!--
if (isset($_POST['idSession']))
{
	if ((is_numeric($_POST['amountCash'])) and (is_numeric($_POST['amountCredit'])))
	{

		$connection->insertBuyin($_POST['hour'], $_POST['amountCash'], $_POST['amountCredit'], $_POST['idUser'], $_POST['approved'], $_POST['currency']); 
		//header();
		?>
		<mark> <i class="far fa-grin-alt"></i> <code> El buyin se ingresó exitosamente </code></mark>
		<br> <br>
		<a class="btn btn-primary" href="buyins.php?id=<?php echo $_GET['id']; ?>"> volver </a>
		<?php
		exit;
	} else
	{
		if (!is_numeric($_POST['amountCash']))
		{
			$mensaje1 = 'El monto en efectivo ingresado no es valido';
		}
		if (!is_numeric($_POST['amountCredit']))
		{
			$mensaje2 = 'El monto en credito ingresado no es valido';
		}
	}
}

$usersSession = $connection->getDatosSessionsUsers($_GET['id']);

?>
-->
*/
?>

