<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>

<?php
include "../MySQL/Connect.php";
include "../MySQL/ConnectLmsuy_db.php";
include "../Entity/UserEntity.php";
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\UserEntity;


date_default_timezone_set('America/Argentina/Buenos_Aires');
$connection = new ConnectLmsuy_db;

if ($connection->getDatosSessionById($_GET['id'])->end_at!=null)
{
	?>
	<mark> <code> La sesión ha finalizado </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="users.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	<?php
	exit;
}

//chequeo variables que deben ser no NULL.
if (empty($_POST['accumulatedPoints']))
{
	$points = 0;
} else 
{
	$points = $_POST['accumulatedPoints'];
}

if (empty($_POST['approved']))
{
	$approved = 1;
} else 
{
	$approved = $_POST['approved'];
}

if (isset($_POST['idSession']))
{
	$start_at = !empty($_POST['start']) ? $_POST['start'] : null;
	$end_at = !empty($_POST['end']) ? $_POST['end'] : null;
	$mensaje = $connection->insertUserInSession(date('c'), $points, $_POST['cashout'], $start_at, $end_at, $approved, $_POST['idSession']);
	?>
	<mark> <!--<i class="far fa-grin-alt"></i> --><code> <?php echo $mensaje ?> </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="users.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	
	<?php
	exit;
}

$datosUsers = $connection->getDatosUsers();

$users = array();

foreach ($datosUsers as $user) 
{
	$users[]= new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name, $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);
}


?>

<body>
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Nuevo Usuario</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Ingresar Usuario
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">

									<input class="form-control" name="id" id="id" type="hidden" value="null" required="true">

									<input class="form-control" name="idSession" id="idSession" type="hidden" required="true" value="<?php echo $_GET['id']; ?>">

								<input name="approved" id="approved" type="hidden">		
								
								<div class="form-group">
									<select class="custom-select" name="user_id" id="user_id" required="true">
										<option value=""> --Seleccione un Jugador--</option>
										<?php foreach ($users as $user) 
										{
											?>
										<option value="<?php echo $user->getId(); ?>"> <?php echo $user->getName()." ".$user->getLastname(); ?></option>
											<?php
										}
										?>
									</select>
								</div>							

								<input name="accumulatedPoints" id="accumulatedPoints" type="hidden">

								<input class="form-control" name="cashout" id="cashout" type="hidden">
							

								<input name="start" id="start" type="hidden" required="true" value="">

								<input name="end" id="end" type="hidden" required="true" value="">
								 <!-- =getStarbyFirstBuyin()--> 

								<div class="form-group">
									<input class="btn btn-lg btn-block btn-primary" type="submit" value="Enviar" />
								</div>
							</form>
						</article>
					</section>
				</div>
			</div>
		</div>
	</div>
	
</body>
</html>