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
//include "src/Exception/PlayerNotFoundException.php";
//include "src/Exception/ComissionAlreadyAddedException.php";
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\ComissionSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

//Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;

if (!isset($_GET['id']))
{
	header('Location: ../../index.php');
	exit;
}

$connection = new ConnectLmsuy_db;

$datosComissionsSession = $connection->getDatosSessionComissions($_GET['id']);

$session = new SessionEntity;

foreach ($datosComissionsSession as $comission) 
{
	$session->sessionComissions[] = new ComissionSession($comission->id, $comission->session_id, $comission->created_at, $comission->comission);
}

$datosSession = $connection->getDatosSessionById($_GET['id']);
var_dump($datosSession);

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

			<div class="card bg-light mb-3">
			  <div class="card-header"><b> Datos de la Sesión </b> </div>
			  <div class="card-body">
			    <p> <i><?php 
			    	echo date_format(date_create($datosSession->created_at), 'l')." "; 
			    	echo date_format(date_create($datosSession->created_at), 'd-m-Y');
			    	?>	</i>
			    </p>
			    <p class="card-text"> Descripcion:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Inicio: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Jugando/Total:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  Asientos Libres:</p>

			  </div>
			</div>

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
									<tr class="bg-success">
										<th> Hora </th>
										<th> Comision </th>	
										<th> Accciones </th>						
									</tr>

								</thead>
								<tbody class="text-center">
									<?php 
									if (sizeof($session->sessionComissions)==0)
									{
										?>
										<tr>
											<td colspan="3"> sin registros </td>
										</tr>
									<?php
									} else
									{ 
										foreach ($session->sessionComissions as $comission) 
										{
										?>
											<tr>
												<td> <?php echo date_format(date_create($comission->getHour()), 'H:i') ?> </td>
												<td> <?php echo "USD ".$comission->getComission() ?> </td>
												<td> <a href="actions/editComission.php?idC=<?php echo $comission->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-pencil-alt"> </i> </a> <a href="actions/deleteComission.php?idC=<?php echo $comission->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a></td> 


													<!-- <a href="javascript:void(0);" onclick="eliminar('actions/deleteComission.php?id= <?php echo $comission->getId(); ?>');"> <i class="fas fa-trash-alt"> </i> </a></td>-->
											</tr>
										<?php
										}
										?>
											<tr class="text-center bg-dark text-white">
												<th> TOTAL </th>
												<th> <?php echo "USD ".$session->getComissionTotal() ?></th>
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