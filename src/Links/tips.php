<?php
include "../Entity/SessionEntity.php";
//include "src/Entity/UserEntity.php";
include "../Entity/UserSession.php";
include "../Entity/ComisionSession.php";
include "../Entity/BuyinSession.php";
include "../Entity/DealerTipSession.php";
include "../Entity/ServiceTipSession.php";

include "../MySQL/Connect.php";
include "../MySQL/ConnectAppPoker.php";

//include "src/Exception/UserAlreadyAddedException.php";
//include "src/Exception/SessionFullException.php";
//include "src/Exception/InsufficientBuyinException.php";
//include "src/Exception/PlayerNotFoundException.php";
//include "src/Exception/ComissionAlreadyAddedException.php";
//include "src/Exception/ServiceTipAlreadyAddedException.php";
//include "src/Exception/DealerTipAlreadyAddedException.php";

Use \Solcre\PokerApp\Entity\SessionEntity;
//Use \Solcre\PokerApp\Entity\UserSession;
//Use \Solcre\PokerApp\Entity\BuyinSession;
//Use \Solcre\PokerApp\Entity\ComissionSession;
Use \Solcre\PokerApp\Entity\DealerTipSession;
Use \Solcre\PokerApp\Entity\ServiceTipSession;

Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

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


$session = new ConnectAppPoker;
$datosSessionDealerTips = $session->getDatosSessionDealerTips();
$datosSessionServiceTips = $session->getDatosSessionServiceTips();

$session1 = new SessionEntity;

foreach ($datosSessionDealerTips as $dealerTip) 
{
	$session1->sessionDealerTips[] = new DealerTipSession($dealerTip->id, $dealerTip->session_id, $dealerTip->hour, $dealerTip->dealer_tip);
}

foreach ($datosSessionServiceTips as $serviceTip) 
{
	$session1->sessionServiceTips[] = new ServiceTipSession($serviceTip->id, $serviceTip->session_id, $serviceTip->hour, $serviceTip->service_tip);
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
									<tr class="text-center bg-secondary">
										<th colspan="3"> DEALER </th>
										<th> <?php if (isset($datosSessionDealerTips[0])) 
											 {
											 	echo date_format(date_create($datosSessionDealerTips[0]->hour), 'd-m-y');
											 }  
											 ?> 
										</th>
									</tr>
									<tr class="text-center bg-success">
										<th> id </th>
										<th> hora</th>
										<th> DealerTips </th>
										<th> acciones </th>
									</tr>
								</thead>
								<tbody class="text-center">
									<?php 
									if (sizeof($session1->sessionDealerTips)==0)
									{
										?>
										<tr>
											<td colspan="4"> sin registros </td>
										</tr>
									<?php
									} else
									{ 
										foreach ($session1->sessionDealerTips as $dealerTip) 
										{
										?>
										<tr class="text-center">
											<td> <?php echo $dealerTip->getId() ?>  </td>
											<td> <?php echo date_format(date_create($dealerTip->getHour()), 'H:i') ?>  </td>
											<td> <?php echo $dealerTip->getDealerTip() ?>  </td>
											<td> <a href="actions/editDealerTip.php?idT=<?php echo $dealerTip->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-pencil-alt"> </i> </a> <a href="actions/deleteDealerTip.php?idT=<?php echo $dealerTip->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a></td>
												<!--<i class="fas fa-pencil-alt"> </i> </a> <a href="javascript:void(0);" 
												onclick="eliminar('actions/deleteDealerTip.php?id=<?php echo $dealerTip->getId(); ?>');"> <i class="fas fa-trash-alt"></i> </a></td>-->
										</tr>
										<?php
										}
										?>
										<tr class="text-center bg-dark text-white">
											<th> TOTAL </th>
											<th> </th>
											<th> <?php echo $session1->getDealerTipTotal() ?> </th>
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
									<tr class="text-center">
										<th colspan="3"> SERVICE </th>
										<th> <?php if (isset($datosSessionServiceTips[0])) 
											 {
											 	echo date_format(date_create($datosSessionServiceTips[0]->hour), 'd-m-y');
											 }
											 ?> 
										</th>
									</tr>
									<tr class="text-center bg-success">
										<th> id </th>
										<th> hora</th>
										<th> ServiceTip </th>
										<th> acciones </th>
									</tr>
								</thead>
								<tbody class="text-center">
									<?php 
									if (sizeof($session1->sessionServiceTips)==0)
									{
										?>
										<tr>
											<td colspan="4"> sin registros </td>

										</tr>
										<?php
									} else
									{
										foreach ($session1->sessionServiceTips as $serviceTip) 
										{
										?>
											<tr>
												<td> <?php echo $serviceTip->getId() ?>  </td>
												<td> <?php echo date_format(date_create($serviceTip->getHour()), 'H:i') ?>  </td>
												<td> <?php echo $serviceTip->getServiceTip() ?>  </td>
												<td> <a href="actions/editServiceTip.php?idT=<?php echo $serviceTip->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-pencil-alt"> </i> </a> <a href="actions/deleteServiceTip.php?idT=<?php echo $serviceTip->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a></td>


													<!--<a href="javascript:void(0);" onclick="eliminar('actions/deleteServiceTip.php?id= <?php echo $serviceTip->getId(); ?>');"> <i class="fas fa-trash-alt"> </i> </a></td>-->
											</tr>
										<?php
										}
										?>
											<tr class="text-center bg-dark text-white">
												<th> TOTAL </th>
												<th> </th>
												<th> <?php echo $session1->getServiceTipTotal(); ?> </th>
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