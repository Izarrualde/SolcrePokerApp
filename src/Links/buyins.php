<?php
include "../Entity/SessionEntity.php";
include "../Entity/BuyinSession.php";
include "../MySQL/Connect.php";
include "../MySQL/ConnectLmsuy_db.php";
//include "src/Exception/InsufficientBuyinException.php";
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
//Use \Solcre\PokerApp\Exception\InsufficientBuyinException;

if (!isset($_GET['id']))
{
	header('Location: ../../index.php');
	exit;
}

$connection = new ConnectLmsuy_db;
$datosSessionBuyins = $connection->getDatosSessionBuyins($_GET['id']);


$session = new SessionEntity;

foreach ($datosSessionBuyins as $buyin) 
{
	$session->sessionBuyins[] = new BuyinSession($buyin->id, null, $buyin->session_user_id, $buyin->amount_of_cash_money, $buyin->amount_of_credit_money, $buyin->currency_id, $buyin->created_at, $buyin->approved);
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> info buyins </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

</head>
<body>
	
	<div class="container">
		<div class="col-md-10">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Buyins</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					<?php
					if (isset($_GET["m"]) and ($_GET["m"]==1))
					{
						?>
						<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">x</button>
							El buyin se ha eliminado exitosamente.
						</div>
						<?php
					}
						?>
					Buyins
				</div>
				<div class="card-body">
					<section class="container row">
						<article class="col-md-10" style="width: auto; margin: auto auto;">
							<table class="table table-bordered table-hover table-condensed">
								<thead class="text-center bg-dark text-white">
									<tr>
										<th colspan="5"> Buyins </th>
										<th colspan="3"> <?php if (isset($datosSessionBuyins[0])) 
											 {
											 	echo date_format(date_create($datosSessionBuyins[0]->created_at), 'd-m-y');
											 } ?> </th>

									</tr>
									<tr>
										<th> id </th>
										<th> sessionUserId</th>
										<th> amountCash </th>
										<th> amountCredit </th>
										<th> currency </th>
										<th> hour </th>
										<th> approved </th>
										<th> acctions </th>
									</tr>
								</thead>
								<tbody class="text-center">
									<?php 
									if (sizeof($session->sessionBuyins)==0)
									{
										?>
										<tr>
											<td colspan="8"> sin registros </td>
										</tr>
									<?php
									} else
									{
										foreach ($session->sessionBuyins as $buyin) 
										{
										?>
											<tr>
												<td> <?php echo $buyin->getId() ?>  </td>
												<td> <?php echo $buyin->getSessionUserId() ?>  </td>
												<td> <?php echo $buyin->getAmountCash() ?>  </td>
												<td> <?php echo $buyin->getAmountCredit() ?>  </td>
												<td> <?php echo $buyin->getCurrency() ?>  </td>
												<td> <?php echo date_format(date_create($buyin->getHour()), 'H:i') ?> </td>
												<td> <?php echo $buyin->getIsApproved() ?>  </td>
												<td> <a href="actions/editBuyin.php?idB=<?php echo $buyin->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-pencil-alt"> </i> </a><a href="actions/deleteBuyin.php?idB=<?php echo $buyin->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a></td> 


												<!-- <a href="javascript:void(0);" onclick="eliminar('actions/deleteBuyin.php?id= <?php echo $buyin->getId(); ?>');"> <i class="fas fa-trash-alt"> </i> </a></td> -->
											</tr>
										<?php
										}
									}
										?>
											<tr>
												<td colspan="8">
												<a href="newbuyins.php?id=<?php echo $_GET['id']; ?>" class="btn btn-lg btn-block btn-danger"> <i class="fas fa-plus"></i></a>
												</td>
											</tr>
							
								</tbody>  
							</table>
						</article>	



					</section>
				</div>
			</div>
		</div>
	</div>


		<script src="../../js/functions.js"></script>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<!--<script type="text/javascript" src=”js/jquery-3.4.0.min.js”> </script>-->
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<script src=”../../js/jquery.js”> </script>
		<script src=”../../js/bootstrap.min.js”> </script>
		
</body>
</html>