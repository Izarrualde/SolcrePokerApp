<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Editar Sesion</title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>

<?php
include "../../MySQL/Connect.php";
include "../../MySQL/ConnectLmsuy_db.php";
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');
$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]))
{
	die("error 404"); 
}

$datos = $connection->getDatosSessionById($_GET["id"]);

if (empty($datos))
{
	die("error 404");
}

if (isset($_POST["id"]))
{
	$connection->updateSession($_POST['created_at'], $_POST['title'], $_POST['description'], $_POST['count_of_seats'], $_POST['real_start_at'], $_POST['end_at'], $_POST['id']);
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> La Sesión se actualizó exitosamente </code></mark>

	<br> <br>
	<a class="btn btn-primary" href="../../../index.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	
	<?php
	exit;
}

//echo substr($datos->end_time, 0, 10); echo "T"; echo substr($datos->end_time, 11, 5);
	?>
<body>
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../../index.php">Inicio</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Editar Sesión</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Editar Sesión
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">
								
								<input name="id" type="hidden" value="<?php echo $datos->id; ?>">

								<input name="count_of_seats" type="hidden" value="<?php echo $datos->count_of_seats; ?>">
								
								<input class="form-control" name="idSession" id="idSession" type="hidden" required="true" value="<?php echo $datos->id; ?>">

								<div class="form-group">
									<label class="sr-only" for="created_at"> Fecha: </label>
									<input class="form-control" name="created_at" id="created_at" type="date" required="true" value="<?php echo date_format(date_create($datos->created_at), 'Y-m-d'); ?>"> 
									
								</div>

								<div class="form-group">
									<label class="sr-only" for="title"> title: </label>
									<input class="form-control" name="title" id="title" placeholder="title" type="text" required="true" value="<?php echo $datos->title; ?>">
									
								</div>


								<div class="form-group">
									<label class="sr-only" for="description"> Descripcion: </label>
									<input class="form-control" name="description" id="description" placeholder="Descripcion" type="text" value="<?php echo $datos->description; ?>">
									
								</div>

								<div class="form-group">
									<label class="sr-only" for="real_start_at"> Hora de Inicio: </label>
									<input class="form-control" name="real_start_at" id="real_start_at" type="datetime-local" value="<?php echo substr($datos->real_start_at, 0, 10); echo "T"; echo substr($datos->real_start_at, 11, 5); ?>">
									
								</div>

								<div class="form-group">
									<label class="sr-only" for="end_at"> Hora de Fin: </label>
									<input class="form-control" name="end_at" id="end_at" type="datetime-local" value="<?php echo substr($datos->end_at, 0, 10); echo "T"; echo substr($datos->end_at, 11, 5); ?>">
									
								</div>

								<div class="form-group">
									<input class="btn btn-lg btn-block btn-primary" type="submit" value="Editar" />
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