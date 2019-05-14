<?php

include "vendor/autoload.php";

Use \Solcre\lmsuy\Entity\UserEntity;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;


$connection = new ConnectLmsuy_db;


//$datosUsers = $session->getDatosSessionUsers();
//$datosBuyinSession = $session->getDatosSessionBuyins();
//$datosComissionSession = $session->getDatosSessionComissions();
//$datosDealerTipSession = $session->getDatosSessionDealerTips();
//$datosServiceTipSession = $session->getDatosSessionServiceTips();

$datosUsers = $connection->getDatosUsers();

$users = array();

foreach ($datosUsers as $user) 
{
	$users[]= new UserEntity($user->id, $user->password, null /*mobile*/, $user->email, $user->last_name, $user->name, $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> Usuarios </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
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
			    <li class="breadcrumb-item"><a href="../../index.php">inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
				  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					<?php
					if (isset($_GET["m"]) and ($_GET["m"])==1)
					{
						?>
						<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">x</button>
								El jugador se ha eliminado exitosamente.
						</div>
					<?php
					}
					?>
					Usuarios
				</div>
				<div class="card-body">
					<section class="container row"  style="width: auto; margin: auto auto;">
						<article class="col-md-12">
							<table class="table table-bordered table-hover text-center">
								<thead class="text-center bg-dark text-white">
									<th> Jugador </th>
									<th> Teléfono </th>
									<th> horas </th>
									<th> acciones </th>
										<tbody>
												<?php 
												foreach ($users as $user) 
												{
												?>
												<tr>
														<td> <?php echo $user->getName(); echo "  "; echo $user->getLastname(); ?></td>
														<td> <?php echo $user->getMobile(); ?></td>
														<td> <?php echo $user->getHours();?> </td>
														<td> <a href="actions/editPlayer.php?id=<?php echo $user->getId(); ?>"> <i class="fas fa-pencil-alt"> </i> </a><a href="actions/deletePlayer.php?id=<?php echo $user->getId(); ?>"> <i class="fas fa-trash-alt"></i> </a> </td>
														<?php
													}
													?>
												</tr>
										</tbody>
									</table>
						</article>
					</section>
				</div>
			</div>
		</div>
	</div>
</body>
</html>