<?php
include "../Entity/SessionEntity.php";
include "../Entity/BuyinSession.php";
//include "src/Entity/UserEntity.php";
include "../Entity/UserSession.php";
include "../MySQL/Connect.php";
include "../MySQL/ConnectLmsuy_db.php";
//include "src/Exception/UserAlreadyAddedException.php";
//include "src/Exception/SessionFullException.php";
//include "src/Exception/PlayerNotFoundException.php";
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\UserSession;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
//Use \Solcre\PokerApp\Exception\PlayerNotFoundException;

if (!isset($_GET['id']))
{
	header('Location: ../../index.php');
	exit;
}

$connection = new ConnectLmsuy_db;
$datosUsers = $connection->getDatosSessionsUsers($_GET['id']);

$session = new SessionEntity;

foreach ($datosUsers as $user) 
{
	$session->sessionUsers[] = new UserSession($user->id, $session, $user->user_id, $user->is_approved, $user->points, $user->cashout, $user->start_at, $user->end_at);
}

$buyins = $connection->getDatosSessionBuyins($_GET['id']);

foreach ($buyins as $buyin) 
{
	$session->sessionBuyins[] = new BuyinSession($buyin->id, $_GET['id'], $buyin->session_user_id, $buyin->amount_of_cash_money, $buyin->amount_of_credit_money, $buyin->currency_id, $buyin->created_at, $buyin->approved);
}

$datosSession = $connection->getDatosSessionById($_GET['id']);

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> info users </title>
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
		<div class="col-md-12">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
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
					if (isset($_GET["m"]) and ($_GET["m"])==1)
					{
						?>
						<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">x</button>
								El usuario se ha eliminado exitosamente.
						</div>
					<?php
					}
					?>
					Usuarios 
				</div>
				<div class="card-body">
					<section class="container row" style="width: auto; margin: auto auto;">
						<article class="col-md-12">
							<table class="table table-bordered table-hover table-condensed">
								<thead class="text-center bg-dark text-white">
									<th> Jugador </th>
									<th> Hora <br>de inicio </th>
									<th> Cashin </th>
									<th> Cashout </th>
									<th> Acciones </th>
								</thead> 
								<tbody class="text-center">
									<?php 
									if (sizeof($datosUsers)==0)
									{
										?>
										<tr>
											<td colspan="9"> sin registros </td>
										</tr>
									<?php
									} else
									{
										foreach ($session->sessionUsers as $user) 
										{ 
											if (empty($user->getEnd()))
											{
												?>
													<tr class="text-center">
														<td> 
															
															<?php echo $connection->getDatosUserById($user->getIdUser())->name; echo " "; echo $connection->getDatosUserById($user->getIdUser())->last_name; ?>  </td>
														<td> <?php echo (empty($user->getStart())) ? '-':date_format(date_create($user->getStart()), 'H:i'); 
														      //date_format(date_create($user->getStart()), 'H:i'); 
															  //echo $connection->getDatosUserById($user->getIdUser())->start_at;
															  ?>  
														</td>
															
														<td> <?php echo $user->getCashin()."(".$user->getTotalCredit().")"; ?> </td>
														<td> <?php echo $user->getCashout() ?>  </td>
														<td> <a href="actions/closeSession.php?id=<?php echo $_GET['id']; /*$connection->updateUserSession($user->getAccumulatedPoints(), $user->getCashout(), $user->getStart(), date('c'), $user->getIsApproved(), $_GET['id'], $user->getIdUser(), $user->getId()); //$connection->updateUserSession($user->getAccumulatedPoints(), $user->getCashout(), $user->getStart(), date('c'), $user->getIsApproved(), $user->getIdSession(), $user->getIdUser(), $user->getIdUserSession())



														*/ ?>&idUS=<?php echo $user->getId(); ?>&idU=<?php echo $user->getIdUser(); ?>"> <i class="fas fa-sign-out-alt"></i> </a> <a href="actions/editUser.php?idU=<?php echo $user->getIdUser(); ?>&id=<?php echo $_GET['id']; ?>&idUS=<?php echo $user->getId(); ?>"> <i class="fas fa-pencil-alt"> </i> </a> <a href="actions/deleteUser.php?idU=<?php echo $user->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a> </td>
												
													</tr> 
											<?php
											}
										}
									}
											?>
												<tr>
													<td colspan="9">
													<a href="newusers.php?id=<?php echo $_GET['id']; ?>" class="btn btn-lg btn-block btn-danger"> <i class="fas fa-plus"></i></a>
													</td>
												</tr>
							
								</tbody>  
							</table>
						</article>	



					</section>
				</div>
			</div>

			<div class="card">
				<div class="card-header bg-primary text-white">
					Usuarios Inactivos
				</div>
				<div class="card-body">
					<section class="container row" style="width: auto; margin: auto auto;">
						<article class="col-md-12">
							<table class="table table-bordered table-hover table-condensed">
								<thead class="text-center bg-dark text-white">
									<th> Inactivo </th>
									<th> Hora <br> de inicio </th>
									<th> Cashin </th>
									<th> Cashout </th>
									<th> Hora <br> de fin </th>
									<th> acciones </th>
								</thead>
								<tbody class="text-center">
									<?php 
									if (sizeof($datosUsers)==0)
									{
										?>
										<tr>
											<td colspan="9"> sin registros </td>
										</tr>
									<?php
									} else
									{
										foreach ($session->sessionUsers as $user) 
										{
											if (!empty($user->getEnd()))
											{
												?>
													<tr class="text-center">
														<td> 
															
															<?php echo $connection->getDatosUserById($user->getIdUser())->name; echo " "; echo $connection->getDatosUserById($user->getIdUser())->last_name; ?>  </td>
														<td> <?php echo date_format(date_create($user->getStart()), 'H:i'); ?> </td>
														<td> <?php echo $user->getCashin()."(".$user->getTotalCredit().")"; ?> </td>
														<td> <?php echo $user->getCashout() ?>  </td>

														<td> <?php echo date_format(date_create($user->getEnd()), 'H:i');
												             ?> 
												        </td>

														<td> <a href="actions/editUser.php?idU=<?php echo $user->getIdUser(); ?>&id=<?php echo $_GET['id']; ?>&idUS=<?php echo $user->getId(); ?>"> <i class="fas fa-pencil-alt"> </i> </a> <a href="actions/deleteUser.php?idU=<?php echo $user->getId(); ?>&id=<?php echo $_GET['id']; ?>"> <i class="fas fa-trash-alt"></i> </a></td>
												
													</tr>
											<?php
											}
										}
									}
											?>

							
								</tbody>  
							</table>
						</article>	
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


