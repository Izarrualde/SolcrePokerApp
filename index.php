<?php

include "src/Entity/SessionEntity.php";
include "src/Entity/UserEntity.php";
include "src/Entity/UserSession.php";
include "src/Entity/ComisionSession.php";
include "src/Entity/BuyinSession.php";
include "src/Entity/DealerTipSession.php";
include "src/Entity/ServiceTipSession.php";
include "src/MySQL/Connect.php";
include "src/MySQL/ConnectAppPoker.php";
include "src/Exception/UserAlreadyAddedException.php";
include "src/Exception/SessionFullException.php";
include "src/Exception/InsufficientBuyinException.php";
include "src/Exception/PlayerNotFoundException.php";
include "src/Exception/ComissionAlreadyAddedException.php";
include "src/Exception/ServiceTipAlreadyAddedException.php";
include "src/Exception/DealerTipAlreadyAddedException.php";

Use \Solcre\PokerApp\Entity\SessionEntity;
Use \Solcre\PokerApp\Entity\UserSession;
Use \Solcre\PokerApp\Entity\BuyinSession;
Use \Solcre\PokerApp\Entity\ComissionSession;
Use \Solcre\PokerApp\Entity\DealerTipSession;
Use \Solcre\PokerApp\Entity\ServiceTipSession;
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;
Use \Solcre\PokerApp\Exception\InsufficientBuyinException;
Use \Solcre\PokerApp\Exception\PlayerNotFoundException;
Use \Solcre\PokerApp\Exception\SessionFullException;
Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;
Use \Solcre\PokerApp\Exception\DealerTipAlreadyAddedException;
Use \Solcre\PokerApp\Exception\ServiceTipAlreadyAddedException;

$session = new ConnectAppPoker;
$datosUsers = $session->getDatosSessionUsers();
$datosBuyinSession = $session->getDatosSessionBuyins();
$datosComissionSession = $session->getDatosSessionComissions();
$datosDealerTipSession = $session->getDatosSessionDealerTips();
$datosServiceTipSession = $session->getDatosSessionServiceTips();
$datosSessions = $session->getDatosSessions();


if (!empty($_POST))
{
	$session->insertSession();
	$mensaje = "La sesion se agregó exitosamente";
}

$session1 = new SessionEntity;

/*
foreach ($datosDealerTipSession as $dealerTip) 
{
	$session1->sessionDealerTips[] = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->hour, $dealerTip->dealer_tip);
}

foreach ($datosServiceTipSession as $serviceTip) 
{
	$session1->sessionServiceTips[] = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->hour, $serviceTip->service_tip);
}

foreach ($datosComissionSession as $comission) 
{
	$session1->sessionComissions[] = new ComissionSession($comission->id, $comission->session_id, $comission->hour, $comission->comission);
}

foreach ($datosBuyinSession as $buyin) 
{
	$session1->sessionBuyins[] = new BuyinSession($buyin->id, $buyin->session_id, $buyin->player_id, $buyin->amount_cash, $buyin->amount_credit, $buyin->currency, $buyin->hour, $buyin->approved);
}

foreach ($datosUsers as $user) 
{
	$session1->sessionUsers[] = new UserSession($user->id, $session1, $user->user_id, $user->approved, $user->accumulated_points, $user->cashout, $user->start, $user->end);
}
*/

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> SESSION </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">	

	<!--<script type="text/javascript" src=”js/jquery-3.4.0.min.js”> </script>-->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<!--<script src=”js/bootstrap.min.js”> </script>-->
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	<script src="js/functions.js"></script>

</head>
<body>
	<div class="container">
		<div class="col-md-12">
			<nav aria-label="breadcrumb">
				 <ol class="breadcrumb">
				    <li class="breadcrumb-item active" aria-current="page">Inicio</li>
				  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					<?php
					if ((isset($_POST["id"])) and (isset($mensaje)))
					{
					?>
					<div class="alert alert-success">
						<button type="button" class="close" data-dismiss="alert">x</button>
							<?php echo $mensaje; ?>
						</div>
						<?php
					}
					?>


					Listado Sesiones
				</div>
				<div class="card-body">
					<section class="container row"  style="width: auto; margin: auto auto;">
						<article class="col-md-12">
						<table class="table table-bordered table-hover text-center">
							<thead class="text-center bg-dark text-white">
								<th> Id </th>
								<th> Fecha </th>
								<th> Dia </th>
								<th> Descrip. </th>
								<th> inicio </th>
								<th> fin </th>
								<th> Acciones</th>
							</thead>
							<tbody>
									<?php 
									foreach ($datosSessions AS $thisSession) 
									{
									?>
									<tr>
											<td> <?php echo $thisSession->id; ?>  </td>
											<td> <?php echo date_format(date_create($thisSession->date), 'd-m-Y'); ?> </td>
											<td> <?php if ($thisSession->date != '0000-00-00 00:00:00') 
												       {
												       		echo date_format(date_create($thisSession->date), 'l');
												       } 
												 ?> 
											</td>
											<td> <?php echo $thisSession->description; ?> </td>
											<td> <?php 
												 if (($thisSession->start_time_real) != '0000-00-00 00:00:00') 
												 	echo substr($thisSession->start_time_real, 11, 5) ; 
												 ?> 
											</td>
											<td> <?php 
												 if (($thisSession->start_time_real) != '0000-00-00 00:00:00')
												 	echo substr($thisSession->end_time, 11, 5) ; 
												 ?> 
											</td>
											<td> 
											
												<a href="src/links/tips.php?id=<?php echo $thisSession->id; ?> " class="btn btn-sm btn-danger"> <i class="fas fa-hand-holding-usd"></i></a> 
												<a href="src/links/comissions.php?id=<?php echo $thisSession->id; ?>" class="btn btn-sm btn-success"> <i class="fas fa-dollar-sign"></i></a>
												<a href="src/links/buyins.php?id=<?php echo $thisSession->id; ?>" class="btn btn-sm btn-secondary"> <i class="fas fa-money-bill"></i></a>
												<a href="src/links/users.php?id=<?php echo $thisSession->id; ?>" class="btn btn-sm btn-info"> <i class="fas fa-users"></i></a>

											</td>
											<?php
										}
										?>
									</tr>
									<tr>
										<td colspan="7">
										<a href="src/links/newsession.php" class="btn btn-lg btn-block btn-danger"> <i class="fas fa-plus"></i></a>
										</td>
									</tr>
							</tbody>
						</table>
					    </article>
					</section>
				</div>
			</div>
		<br>
		<br>
		<br>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Bloque Datos Sesion
				</div>
				<div class="card-body">
					<section class="container row"  style="width: auto; margin: auto auto;">
						<article class="col-md-12">

						</article>
					</section>
				</div>
			</div>
		</div>
	</div>
</body>


</html>