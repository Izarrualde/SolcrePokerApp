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


if (isset($_POST['idUser']))
{
	$session = new ConnectAppPoker;
	$session->insertUser();
	//header();
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> El usuario se ingresó exitosamente </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="newsession.php"> volver </a>
	
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
					<section class="container row">
						<article>
							<form class="was-validated" action="" method="post">
								<div class="form-group">
									<label class="sr-only" for="id">  Id: </label>
									<input class="form-control" name="id" id="id" type="text" placeholder="id" autofocus="true" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="idUser"> IdUser: </label>
									<input class="form-control" name="idUser" id="idUser" type="text" placeholder="IdUser" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="accumulatedPoints"> Puntos Acumulados: </label>
									<input class="form-control"name="accumulatedPoints" id="accumulatedPoints" type="text" placeholder="Puntos Acumulados" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="cashout"> Cashout: </label>
									<input class="form-control" name="cashout" id="cashout" type="text" placeholder="Cashout" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="horaInicio"> hora inicio: </label>
									<input class="form-control" name="start" id="horaInicio" type="datetime-local" placeholder="hora de inicio" required="true">
									<small id="horaInicio" class="form-tet text-muted"> Fecha y hora de inicio </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="horaFin"> hora fin: </label>
									<input class="form-control" name="end" id="horaFin" type="datetime-local" placeholder="hora de fin" required="true">
									<small id="horaFin" class="form-tet text-muted"> Fecha y hora de finalización </small>
								</div>

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