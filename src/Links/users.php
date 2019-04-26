<?php
include "../Entity/SessionEntity.php";
//include "src/Entity/UserEntity.php";
include "../Entity/UserSession.php";
include "../MySQL/Connect.php";
include "../MySQL/ConnectAppPoker.php";
//include "src/Exception/UserAlreadyAddedException.php";
//include "src/Exception/SessionFullException.php";
//include "src/Exception/PlayerNotFoundException.php";
Use \Solcre\PokerApp\Entity\SessionEntity;
Use \Solcre\PokerApp\Entity\UserSession;
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;
//Use \Solcre\PokerApp\Exception\PlayerNotFoundException;

if (!isset($_GET['id']))
{
	header('Location: ../../index.php');
	exit;
}

$session = new ConnectAppPoker;
$datosUsers = $session->getDatosSessionUsers();
$session1 = new SessionEntity;

foreach ($datosUsers as $user) 
{
	$session1->sessionUsers[] = new UserSession($user->id, $session1, $user->user_id, $user->approved, $user->accumulated_points, $user->cashout, $user->start, $user->end);
}
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
									<th> id </th>
									<th> idUser </th>
									<th> approved </th>
									<th> accumulatedPoints </th>
									<th> cashout </th>
									<th> start </th>
									<th> end </th>
									<th> horas jugadas </th>
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
										foreach ($session1->sessionUsers as $thisUser) 
										{
											?>
												<tr class="text-center">
													<td> <?php echo $thisUser->getId() ?>  </td>
													<td> <?php echo $thisUser->getIdUser() ?>  </td>
													<td> <?php echo $thisUser->getApproved() ?>  </td>
													<td> <?php echo $thisUser->getAccumulatedPoints() ?>  </td>
													<td> <?php echo $thisUser->getCashout() ?>  </td>
													<td> <?php echo date_format(date_create($thisUser->getStart()), 'H:i') ?> </td>
													<td> <?php echo date_format(date_create($thisUser->getEnd()), 'H:i') ?> </td>
													<td> <?php echo date_diff(date_create($thisUser->getStart()), date_create($thisUser->getEnd()))->format('%H:%M'); ?> </td>
													<td> <a href="actions/editUser.php?id=<?php echo $thisUser->getId(); ?>"> <i class="fas fa-pencil-alt"> </i> </a> 
														<a href="javascript:void(0);" onclick="eliminar('actions/deleteUser.php?id=<?php echo $thisUser->getId(); ?>');"> <i class="fas fa-trash-alt"> </i> </a></td>
											
												</tr>
											<?php
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
		</div>
	</div>



		<script src="../../js/functions.js"></script>
		<script src=”../../js/jquery.js”> </script>
		<script src=”../../js/bootstrap.min.js”> </script>
		
</body>
</html>


