<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> Revision </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<!--<script type="text/javascript" src=”js/jquery-3.4.0.min.js”> </script>-->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src=”js/bootstrap.min.js”> </script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</head>

<?php

include "../../Entity/SessionEntity.php";
include "../../Entity/UserEntity.php";
include "../../Entity/UserSession.php";
include "../../Entity/ComisionSession.php";
include "../../Entity/BuyinSession.php";
include "../../Entity/DealerTipSession.php";
include "../../Entity/ServiceTipSession.php";
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectLmsuy_db.php";
include "../../Exception/UserAlreadyAddedException.php";
include "../../Exception/SessionFullException.php";
include "../../Exception/InsufficientBuyinException.php";
include "../../Exception/PlayerNotFoundException.php";
include "../../Exception/ComissionAlreadyAddedException.php";
include "../../Exception/ServiceTipAlreadyAddedException.php";
include "../../Exception/DealerTipAlreadyAddedException.php";

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

$connection = new ConnectLmsuy_db;

$datosUsers = $connection->getDatosUsers();
$datosSession = $connection->getDatosSessionById($_GET['id']);

$users = array();

foreach ($datosUsers as $user) 
{
	$users[]= new UserEntity($user->id, $user->password, null /*mobile*/, $user->email, $user->last_name, $user->name, $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);
}

$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

$datosUsersSession = $connection->getDatosSessionsUsers($session->getIdSession());
$datosSessionComissions = $connection->getDatosSessionComissions($session->getIdSession());
$datosSessionBuyins = $connection->getDatosSessionBuyins($session->getIdSession());
$datosDealerTipSession = $connection->getDatosSessionDealerTips($session->getIdSession());
$datosServiceTipSession = $connection->getDatosSessionServiceTips($session->getIdSession());
	
foreach ($datosUsersSession as $user) 
{
	$session->sessionUsers[] = new UserSession($user->id, $session, $user->user_id, $user->is_approved, $user->points, $user->cashout, $user->start_at, $user->end_at);
}

foreach ($datosDealerTipSession as $dealerTip) 
{
	$session->sessionDealerTips[] = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->created_at, $dealerTip->dealer_tip);
}

foreach ($datosServiceTipSession as $serviceTip) 
{
	$session->sessionServiceTips[] = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->created_at, $serviceTip->service_tip);
}

foreach ($datosSessionComissions as $comission) 
{
	$session->sessionComissions[] = new ComissionSession($comission->id, $comission->session_id, $comission->created_at, $comission->comission);
}

foreach ($datosSessionBuyins as $buyin) 
{
	$session->sessionBuyins[] = new BuyinSession($buyin->id, null, $buyin->session_user_id, $buyin->amount_of_cash_money, $buyin->amount_of_credit_money, $buyin->currency_id, $buyin->created_at, $buyin->approved);
}

?>


<body>
	
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../../index.php">Inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Revision</li>
			  </ol>
			</nav>

			<div class="card bg-light mb-3">
			  <div class="card-header"><b> Datos de la sesión </b> </div>
			  <div class="card-body">
			    <h5 class="card-title"> <?php /*
			    	echo date_format(date_create($datosSession->created_at), 'l')." "; 
			    	echo date_format(date_create($datosSession->created_at), 'd-m-Y');
			    	*/?>	
			    </h5>
			    <p class="card-text"> Descripcion: </p>
			    <p class="card-text"> Inicio: </p>
			    <p class="card-text"> Jugando/Total: </p>
			    <p class="card-text"> Asientos Libres: </p>
			  </div>
			</div>

			<div class="card">
				<div class="card-header bg-primary text-white">

					Revision de la sesión
				</div>
				<div class="card-body">
					<section class="container row">
						<article class="col-md-4">
							<p> Validación de sesión: <?php echo $session->validateSession($session)?"sesión valida":"sesión no valida";
								if (!$session->validateSession($session)) echo " por una diferencia de: ".($session->getTotalPlayed() - ($session->getTotalCashout() + $session->getComissionTotal() + $session->getDealerTipTotal()+ $session-> getServiceTipTotal()))  ?>  </p>
							<p> Total Jugado: <?php echo "USD ".$session->getTotalPlayed() ?></p>

						
						</article>	
					</section>
				</div>
			</div>
		</div>
	</div>
		<script src="../../js/functions.js"></script>
		<script src=”/../../js/jquery.js”> </script>
		<script src=”/../../js/bootstrap.min.js”> </script>
		
</body>
</html>