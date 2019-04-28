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
include "../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

date_default_timezone_set('America/Argentina/Buenos_Aires');

if (isset($_POST['idUser']))
{
	$session = new ConnectAppPoker;
	echo "<br>";
	var_dump($_POST);
	echo "<br>";
	$session->insertUser();
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> El usuario se ingresó exitosamente </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="users.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	
	<?php
	exit;
}

?>

<body>
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
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

									<input class="form-control" name="idUser" id="idUser" type="hidden" placeholder="IdUser" required="true" value="12" > <!-- poner un getidUserbyNickname -->

								<div class="form-group">
									<label class="sr-only" for="nickname"> Jugador </label>
									<input class="form-control"name="nickname" id="nickname" type="text" placeholder="nickname" required="true" autofocus="true"> 
								</div>									

								<input name="accumulatedPoints" id="accumulatedPoints" type="hidden" placeholder="Puntos Acumulados" required="true">

								<input class="form-control" name="cashout" id="cashout" type="hidden">
							

								<div class="form-group">
									<label class="sr-only" for="start"> hora inicio: </label>
									<input class="form-control" name="start" id="start" type="datetime-local" placeholder="hora de inicio" required="true" value="<?php echo substr(date('c'), 0, 16); ?>">
									<small id="inicio" class="form-tet text-muted"> Fecha y hora de Incio </small>
								</div>

								<input name="end" id="end" type="hidden" required="true" value="<?php echo substr(date('c'), 0, 16); ?>">
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