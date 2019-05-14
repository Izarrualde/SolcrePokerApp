<?php
include "../Entity/SessionEntity.php";
//include "src/Entity/UserEntity.php";
include "../Entity/UserSession.php";
include "../Entity/ComisionSession.php";
include "../Entity/BuyinSession.php";
include "../Entity/DealerTipSession.php";
include "../Entity/ServiceTipSession.php";

include "../MySQL/Connect.php";
include "../MySQL/ConnectLmsuy_db.php";

//include "src/Exception/UserAlreadyAddedException.php";
//include "src/Exception/SessionFullException.php";
//include "src/Exception/InsufficientBuyinException.php";
//include "src/Exception/PlayerNotFoundException.php";
//include "src/Exception/ComissionAlreadyAddedException.php";
//include "src/Exception/ServiceTipAlreadyAddedException.php";
//include "src/Exception/DealerTipAlreadyAddedException.php";

Use \Solcre\lmsuy\Entity\SessionEntity;
//Use \Solcre\PokerApp\Entity\UserSession;
//Use \Solcre\PokerApp\Entity\BuyinSession;
//Use \Solcre\PokerApp\Entity\ComissionSession;
Use \Solcre\lmsuy\Entity\DealerTipSession;
Use \Solcre\lmsuy\Entity\ServiceTipSession;

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

//Use \Solcre\PokerApp\Exception\InsufficientBuyinException;
//Use \Solcre\PokerApp\Exception\PlayerNotFoundException;
//Use \Solcre\PokerApp\Exception\SessionFullException;
//Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;
//Use \Solcre\PokerApp\Exception\DealerTipAlreadyAddedException;
//Use \Solcre\PokerApp\Exception\ServiceTipAlreadyAddedException;

/*if (!isset($_GET['id']))
{
	header('Location: ../../index.php');
	exit;
}*/


$connection = new ConnectLmsuy_db;
$datosSession = $connection->getDatosSessionById($_GET['id']);
$datosSessionDealerTips = $connection->getDatosSessionDealerTips($_GET['id']);
$datosSessionServiceTips = $connection->getDatosSessionServiceTips($_GET['id']);

$session = new SessionEntity($datosSession->id, $datosSession->created_at, $datosSession->title, $datosSession->description, null /*photo*/, $datosSession->count_of_seats, null /*seatswaiting*/ , null /*reservewainting*/, $datosSession->start_at, $datosSession->real_start_at, $datosSession->end_at);

foreach ($datosSessionDealerTips as $dealerTip) 
{
	$session->sessionDealerTips[] = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->created_at, $dealerTip->dealer_tip);
}

foreach ($datosSessionServiceTips as $serviceTip) 
{
	$session->sessionServiceTips[] = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->created_at, $serviceTip->service_tip);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> info tips </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<!--<script type="text/javascript" src=”js/jquery-3.4.0.min.js”> </script>-->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<!--<script src=”js/bootstrap.min.js”> </script>-->
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</head>
<body>
	<div class="container">
		<div class="col-md-12">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Tips</li>
			  </ol>
			</nav>
			<div class="card bg-light mb-3">
			  <div class="card-header"><b> Datos de la Sesión </b> </div>
			  <div class="card-body">
			    <p> <i><?php 
			    	echo date_format(date_create($session->getDate()), 'l')." "; 
			    	echo date_format(date_create($session->getDate()), 'd-m-Y');
			    	?>	</i>
			    </p>
			    <p class="card-text"> Descripcion: <?php echo $session->getDescription() ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Inicio: <?php echo substr($session->getStartTimeReal(), 11, 5) ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Jugando/Total: <?php echo $session->getActivePlayers()."/".$session->getTotalDistinctPlayers() ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Asientos Libres: <?php echo ($session->getSeats()-$session->getActivePlayers()) ?>
			    </p>

			  </div>
			</div>
			<div class="card">
				<div class="card-header bg-primary text-white">
					<?php
					if (isset($_GET["m"]))
					{
						switch ($_GET["m"]) 
						{
							case '1':
								?>
								<div class="alert alert-success">
								<button type="button" class="close" data-dismiss="alert">x</button>
									El dealerTip se ha eliminado exitosamente.
								</div>
								<?php
								break;
							case '2':
								?>
								<div class="alert alert-success">
								<button type="button" class="close" data-dismiss="alert">x</button>
									El serviceTip se ha eliminado exitosamente.
								</div>
								<?php
								break;
						}
					}
					?>
					(Dealer & Service) Tips
				</div>
				<div class="card-body">
					<section class="container row"  style="width: auto; margin: auto auto;">
						<article class="col-md-6">
							<table class="table table-bordered table-hover table-condensed">
								<thead class="thead-dark">

									<tr class="text-center bg-success">
										<th> Hora</th>
										<th> Propina </th>
										<th> Acciones </th>
									</tr>
								</thead>
								<tbody class="text-center">
									<?php 
									if (sizeof($session->sessionDealerTips)==0)
									{
										?>
										<tr>
											<td colspan="3"> sin registros </td>
										</tr>
									<?php
									} else
									{ 
										foreach ($session->sessionDealerTips as $dealerTip) 
										{
										?>
										<tr class="text-center">
											<td> <?php echo date_format(date_create($dealerTip->getHour()), 'H:i') ?>  </td>
											<td> <?php echo "USD ".$dealerTip->getDealerTip() ?>  </td>
											<td> <a href="actions/editDealerTip.php?idT=<?php echo $dealerTip->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-pencil-alt"> </i> </a> <a href="actions/deleteDealerTip.php?idT=<?php echo $dealerTip->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a></td>
												<!--<i class="fas fa-pencil-alt"> </i> </a> <a href="javascript:void(0);" 
												onclick="eliminar('actions/deleteDealerTip.php?id=<?php echo $dealerTip->getId(); ?>');"> <i class="fas fa-trash-alt"></i> </a></td>-->
										</tr>
										<?php
										}
										?>
										<tr class="text-center bg-dark text-white">
											<th> TOTAL </th>
											<th> <?php echo "USD ".$session->getDealerTipTotal() ?> </th>
											<th> </th>											
										</tr>
									<?php
									}
									?>
								</tbody>  
							</table>
						</article>	

						<aside class="col-md-6">
							<table class="table table-bordered table-hover table-condensed">
								<thead class="thead-dark">
									<tr class="text-center bg-success">
										<th> Hora</th>
										<th> Propina </th>
										<th> Acciones </th>
									</tr>
								</thead>
								<tbody class="text-center">
									<?php 
									if (sizeof($session->sessionServiceTips)==0)
									{
										?>
										<tr>
											<td colspan="4"> sin registros </td>

										</tr>
										<?php
									} else
									{
										foreach ($session->sessionServiceTips as $serviceTip) 
										{
										?>
											<tr>
												<td> <?php echo date_format(date_create($serviceTip->getHour()), 'H:i') ?>  </td>
												<td> <?php echo "USD ".$serviceTip->getServiceTip() ?>  </td>
												<td> <a href="actions/editServiceTip.php?idT=<?php echo $serviceTip->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-pencil-alt"> </i> </a> <a href="actions/deleteServiceTip.php?idT=<?php echo $serviceTip->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a></td>


													<!--<a href="javascript:void(0);" onclick="eliminar('actions/deleteServiceTip.php?id= <?php echo $serviceTip->getId(); ?>');"> <i class="fas fa-trash-alt"> </i> </a></td>-->
											</tr>
										<?php
										}
										?>
											<tr  class="text-center bg-dark text-white">
												<th> TOTAL </th>
												<th> <?php echo "USD ".$session->getServiceTipTotal(); ?> </th>
												<th> </th>												

											</tr>		
										<?php
									}
										?>								
								</tbody>  
							</table>
						</aside>	

					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<td colspan="8">
								<a href="newtips.php?id=<?php echo $_GET['id']; ?>" class="btn btn-lg btn-block btn-danger"> <i class="fas fa-plus"></i></a>
							</td>
						</thead>
					</table>
					</section>
				</div>
			</div>
		</div>
	</div>



		<script src="../../js/functions.js"></script>
		<script src=”../../js/jquery.js”> </script>
		<script src=”../../js/bootstrap.min.js”> </script>
		
</body>
</html>