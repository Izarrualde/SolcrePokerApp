<?php

include "src/Entity/SessionEntity.php";
include "src/Entity/UserEntity.php";
include "src/Entity/UserSession.php";
include "src/Entity/ComisionSession.php";
include "src/Entity/BuyinSession.php";
include "src/Entity/DealerTipSession.php";
include "src/Entity/ServiceTipSession.php";
include "src/MySQL/Connect.php";
include "src/MySQL/ConnectLmsuy_db.php";
include "src/Exception/UserAlreadyAddedException.php";
include "src/Exception/SessionFullException.php";
include "src/Exception/InsufficientBuyinException.php";
include "src/Exception/PlayerNotFoundException.php";
include "src/Exception/ComissionAlreadyAddedException.php";
include "src/Exception/ServiceTipAlreadyAddedException.php";
include "src/Exception/DealerTipAlreadyAddedException.php";

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

$connection = new ConnectLmsuy_db;

$datosUsers = $connection->getDatosUsers();
$datosSessions = $connection->getDatosSessions();


if (!empty($_POST))
{
	$connection->insertSession();
	$mensaje = "La sesion se agregó exitosamente";
}

$users = array();

foreach ($datosUsers as $user) 
{
	$users[]= new UserEntity($user->id, $user->password, null /*mobile*/, $user->email, $user->last_name, $user->name, $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);
}


$sessions = array();

foreach ($datosSessions as $session) 
{
$sessions[] = new SessionEntity($session->id, $session->created_at, $session->title, $session->description, null /*photo*/, $session->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $session->start_at, $session->real_start_at, $session->end_at);
}

foreach ($sessions as $session ) 
{
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

}

//var_dump($session->sessionUsers);




?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> lmsuy </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">	

	<script type="text/javascript" src=”js/jquery-3.4.0.min.js”> </script>
	
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>

	<script src=”js/bootstrap.min.js”> </script>

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
					} elseif (isset($_GET['m']) and $_GET['m']=='1') 
					{
						?>
						<div class="alert alert-success">
						<button type="button" class="close" data-dismiss="alert">x</button>
							<?php echo "La sesión se eliminó exitosamente"; ?>
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
								<th> Jugando/Total </th>
								<th> Asientos L </th>								
								<th> fin </th>
								<th> Acciones</th>
							</thead>
							<tbody>
									<?php 
									foreach ($sessions as $session) 
									{
									?>
									<tr>
											<td> <?php echo $session->getIdSession(); ?>  </td>
											<td> <?php echo date_format(date_create($session->getDate()), 'd-m-Y'); ?> </td>
											<td> <?php if ($session->getDate() != '0000-00-00 00:00:00') 
												       {
												       		echo date_format(date_create($session->getDate()), 'l');
												       } 
												 ?> 
											</td>
											<td> <?php echo $session->getDescription(); ?></td>
											<td> <?php 
												 if (($session->getStartTimeReal()) != '0000-00-00 00:00:00') 
												 	echo substr($session->getStartTimeReal(), 11, 5) ; 
												 ?> 
											</td>
											<td> <?php echo $session->getActivePlayers(); echo "/"; echo $session->getTotalDistinctPlayers(); ?> </td>
											<td> <?php echo ($session->getSeats()-$session->getActivePlayers()); //- getAsientosOcupados() ?> </td>	
											<td> <?php 
												 if (($session->getEndTime()) != '0000-00-00 00:00:00')
												 	echo substr($session->getEndTime(), 11, 5) ; 
												 ?> 
											</td>
											<td> 
												<a href="src/links/users.php?id=<?php echo $session->getIdSession(); ?>" class="btn btn-sm btn-info"> <i class="fas fa-users"></i></a>
												<a href="src/links/buyins.php?id=<?php echo $session->getIdSession();?>" class="btn btn-sm btn-secondary"> <i class="fas fa-money-bill"></i></a>
												<a href="src/links/tips.php?id=<?php echo $session->getIdSession(); ?> " class="btn btn-sm btn-danger"> <i class="fas fa-hand-holding-usd"></i></a> 
												<a href="src/links/comissions.php?id=<?php echo $session->getIdSession(); ?>" class="btn btn-sm btn-success"> <i class="fas fa-dollar-sign"></i></a>
												<a href="src/links/actions/editsession.php?id=<?php echo $session->getIdSession(); ?>"> <i class="fas fa-pencil-alt"> </i> </a>
												<a href="src/links/actions/deletesession.php?id=<?php echo $session->getIdSession(); ?>"> <i class="fas fa-trash-alt"></i> </a>
												<a href="src/links/actions/revisionsession.php?id=<?php echo $session->getIdSession(); ?>"> <i class="far fa-eye"></i></a>

											</td>
											<?php
										}
										?>
									</tr>
									<tr>
										<td colspan="9">
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
					Usuarios
				</div>
				<div class="card-body">
					<section class="container row"  style="width: auto; margin: auto auto;">
						<article class="col-md-12">
								<a href="src/links/viewUsers.php" class="btn btn-lg btn-block btn-info"> <i class="far fa-eye"></i></i></a>										
								<a href="src/links/adduser.php" class="btn btn-lg btn-block btn-danger"> <i class="fas fa-plus"></i></a>			
						</article>
					</section>
				</div>
			</div>
		</div>
	</div>
	<br><br><br><br><br>
</body>


</html>