<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>

<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectLmsuy_db.php";
include "../../Entity/SessionEntity.php";
include "../../Entity/UserSession.php";
Use \Solcre\lmsuy\Entity\UserSession;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');
$connection = new ConnectLmsuy_db;

if (isset($_POST["id"]))
{
	$connection->closeUserSession($_POST['id'], $_POST['idUser'], $_POST['cashout'], $connection->getDatosSessionUsersById($_POST['id'])['0']->start_at, $_POST['end']);
	$datosUsers = $connection->getDatosSessionUsersById($_POST['id']);


	//$session = new SessionEntity;

	/*foreach ($datosUsers as $user) 
	{
		$session->sessionUsers[] = new UserSession($user->id, $session, $user->user_id, $user->is_approved, $user->points, $user->cashout, $user->start_at, $user->end_at);
		if ($user->id==$_POST['id'])
		{

		}*/
	//}

	?>
	<mark> <!--<i class="far fa-grin-alt"></i> --><code> <?php echo "El usuario ha salido de la sesión"; ?> </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="../users.php?id=<?php echo $_GET['id']; ?>"> volver </a>	
	<?php
	exit;	
}

?>

<body>
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../../index.php">Inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Cerrar Sesión </li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Cerrar Sesión de Usuario
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">
								<input name="id" type="hidden" value="<?php echo $_GET['idUS']; ?>">

								<input name="idUser" type="hidden" value="<?php echo $_GET['idU']; ?>">

								<div class="form-group">
									<label class="sr-only" for="end"> Fin: </label>
									<input class="form-control" name="cashout" id="cashout" type="text" placeholder="Cashout" required="true" value="" autofocus="true">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="end"> Fin: </label>
									<input class="form-control" name="end" id="end" type="datetime-local" value="<?php echo substr(date('c'), 0, 16); ?>">
									<small id="end" class="form-tet text-muted"> Fecha y hora de finalizacion </small>
								</div>  

								<div class="form-group">
									<input class="btn btn-lg btn-block btn-primary" type="submit" value="Enviar" />
								</div>
								<br><br><br><br>
							</form>
						</article>
					</section>
				</div>
			</div>
		</div>
	</div>

</body>
</html>