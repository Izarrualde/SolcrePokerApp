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
//include "src/Exception/PlayerNotFoundException.php";
//include "src/Exception/ComissionAlreadyAddedException.php";
Use \Solcre\PokerApp\Entity\SessionEntity;
Use \Solcre\PokerApp\Entity\ComissionSession;
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

//Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;

if (!isset($_GET['id']))
{
	header('Location: ../../index.php');
	exit;
}

$session = new ConnectAppPoker;

$datosComissionsSession = $session->getDatosSessionComissions();

$session1 = new SessionEntity;

foreach ($datosComissionsSession as $comission) 
{
	$session1->sessionComissions[] = new ComissionSession($comission->id, $comission->session_id, $comission->hour, $comission->comission);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> info comissions </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
	<!--<script type="text/javascript" src=”js/jquery-3.4.0.min.js”> </script>-->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script src=”js/bootstrap.min.js”> </script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

</head>
<body>
	
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Comisiones</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					<?php
					if (isset($_GET["m"]) and $_GET["m"]==1)
					{
						?>
						<div class="alert alert-success">
						<button type="button" class="close" data-dismiss="alert">x</button>
						La comisión se ha eliminado exitosamente.
						</div>
					<?php
					}
					?>
					Comisiones
				</div>
				<div class="card-body">
					<section class="container row">
						<article class="col-md-9"  style="width: auto; margin: auto auto;">
							<table class="table table-bordered table-hover table-condensed">
								<thead class="thead-dark text-center bg-secondary">
									<tr>
										<th colspan="3"> Comisiones </th>
										<th> <?php if (isset($datosComissionsSession[0])) 
											 {
											 	echo date_format(date_create($datosComissionsSession[0]->hour), 'd-m-y');
											 } ?> </th>
									</tr>
									<tr class="bg-success">
										<th> id </th>
										<th> hora</th>
										<th> comision </th>	
										<th> accciones </th>						
									</tr>

								</thead>
								<tbody class="text-center">
									<?php 
									if (sizeof($session1->sessionComissions)==0)
									{
										?>
										<tr>
											<td colspan="4"> sin registros </td>
										</tr>
									<?php
									} else
									{ 
										foreach ($session1->sessionComissions as $comission) 
										{
										?>
											<tr>
												<td> <?php echo $comission->getId() ?> </td>
												<td> <?php echo date_format(date_create($comission->getHour()), 'H:i') ?> </td>
												<td> <?php echo $comission->getComission() ?> </td>
												<td> <a href="actions/editComission.php?idC=<?php echo $comission->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-pencil-alt"> </i> </a> <a href="actions/deleteComission.php?idC=<?php echo $comission->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a></td> 


													<!-- <a href="javascript:void(0);" onclick="eliminar('actions/deleteComission.php?id= <?php echo $comission->getId(); ?>');"> <i class="fas fa-trash-alt"> </i> </a></td>-->
											</tr>
										<?php
										}
										?>
											<tr class="text-center bg-dark text-white">
												<th> TOTAL </th>
												<th> </th>
												<th> <?php echo $session1->getComissionTotal() ?></th>
												<th> </th>
											</tr>	
									<?php
									}
									?>		
											<tr>
												<td colspan="8">
												<a href="newcomissions.php?id=<?php echo $_GET['id']; ?>" class="btn btn-lg btn-block btn-danger"> <i class="fas fa-plus"></i></a>
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
		<script src=”/../../js/jquery.js”> </script>
		<script src=”/../../js/bootstrap.min.js”> </script>
		
</body>
</html>