<?php
include "../MySQL/Connect.php";
include "../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

//recibo por GET el numero de la ultima session
//idSession= $_GET(pasar a entero) + 1   la variable idSession en las tablas debe ser un integer
// cuando doy click en tip paso tips.php?idSession
date_default_timezone_set('America/Argentina/Buenos_Aires');

/*
if (!empty($_POST))
{
	header('Location: ../../index.php');
}
*/



?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>
<body>
	<?php
	if (isset($_POST['id']))
	{
		$session = new ConnectAppPoker;
		$session->insertSession();
		//header();
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> La sesión se agregó exitosamente </code></mark>
	<br>
	<br> <br>
	<a class="btn btn-primary" href="../../index.php"> volver </a>
	
	<?php
	exit;
	}
	?>

	<div class="container-fluid">
	<div class="row">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Nueva Sesion</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Ingresar datos
				</div>
				<div class="card-body">
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">
								
								<input type="hidden" class="form-control" name="id" id="id">

								<div class="form-group">
									<label class="sr-only" for="title"> Title: </label>
									<input class="form-control" name="title" id="title" type="text" placeholder="title" value="Mesa Mixta" required="true" autofocus="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="description"> Description: </label>
									<input class="form-control"name="description" id="description" type="text" placeholder="description">
								</div>
								<!--<textarea rows="4" cols="50" name="description" form="">
								Descripcion...</textarea> -->


								<div class="form-group">
									<label class="sr-only" for="date">Fecha: </label>
									<input class="form-control" name="date" id="date" type="date" placeholder="date" required="true" value="<?php echo date('Y-m-d'); ?>">
									<small id="end" class="form-tet text-muted"> Fecha </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="cashout"> Seats: </label>
									<input class="form-control" name="seats" id="seats" type="text" placeholder="seats" required="true" value="9">
								</div>

								<div class="form-group">
									<label class="sr-only" for="horaInicio"> hora inicio: </label>
									<input class="form-control" name="startTime" id="startTime" type="datetime-local" placeholder="hora de inicio" required="true" value="<?php echo substr(date('c'), 0, 16); ?>">
									<small id="end" class="form-tet text-muted"> Fecha y hora de inicio </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="horaInicio"> hora inicio real: </label>
									<input class="form-control" name="startTimeReal" id="startTimeReal" type="datetime-local" placeholder="hora de inicio real">
									<small id="end" class="form-tet text-muted"> Fecha y hora de inicio real </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="end"> hora fin: </label>
									<input class="form-control" name="end" id="end" type="datetime-local" placeholder="hora de fin">
									<small id="end" class="form-tet text-muted"> Fecha y hora de finalización </small>
								</div>

								<div class="form-group">
									<input class="btn btn-lg btn-block btn-primary" type="submit" value="Enviar" />
								</div>
							</form>
						</article>
					</section>
				</div>

					<section class="container row">

						<article class="col-md-8">
							<ul>
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<br>
								<a href="../../src/links/newtips.php" class="btn btn-lg btn-block btn-danger"> <b>+<b>  <i class="fas fa-hand-holding-usd"></i> tips </a> 
								<br>
								<a href="../../src/links/newcomissions.php" class="btn btn-lg btn-block btn-success"> <b>+<b>  <i class="fas fa-dollar-sign"></i> comissions </a> 
								<br>
								<a href="../../src/links/newbuyins.php" class="btn btn-lg btn-block btn-secondary"> <b>+<b>  <i class="fas fa-money-bill"></i> buyins </a> 
								<br>
								<a href="../../src/links/newusers.php" class="btn btn-lg btn-block btn-info"> <b>+<b>  <i class="fas fa-user-plus"></i> users </a>
								<br>
							</ul>
						</article>	
					</section>
				</div>
			</div>
		</div>
	</div>
	<br><br><br>

	
	
</body>
</html>