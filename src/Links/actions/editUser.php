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
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');


$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idU"]))
{
	die("error 404 primero"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $connection->getDatosSessionUsersById($_GET["idUS"]);
var_dump($datos);
//$horaInicio = $session->getHourFirstBuyin()->start;

if (sizeof($datos)==0)
{
	die("error 404 segundo");
}

echo "<br>";
echo "var_dump de post";
echo "<br>";
var_dump($_POST);
echo "<br>";

if (isset($_POST["idSession"]))
{
	$connection->updateUserSession($_POST['accumulatedPoints'], $_POST['cashout'], $_POST['start'], $_POST['end'] , $_POST['approved'] , $_POST['idSession'], $_POST['idUser'], $_GET['idUS']);
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> El usuario se actualizó exitosamente </code></mark>

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
			    <li class="breadcrumb-item active" aria-current="page">Editar Usuario</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Editar Usuario
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">
								<input name="id" type="hidden" value="<?php echo $_GET['idUS']; ?>">
								
								<input name="idUser" id="idUser" type="hidden" value="<?php echo $datos['0']->user_id; ?>">

								<input name="idSession" id="idSession" type="hidden" value="<?php echo $datos['0']->session_id; ?>">

								<div class="form-group">
									<label class="sr-only" for="accumulatedPoints"> accumulatedPoints: </label>
									<input class="form-control" name="accumulatedPoints" id="accumulatedPoints" type="text" placeholder="accumulatedPoints" required="true" value="<?php echo $datos[0]->points; ?>">
								</div>

								<input name="approved" id="approved" type="hidden" value="<?php echo $datos[0]->is_approved; ?>">

								<div class="form-group">
									<label class="sr-only" for="amountCash"> cashout: </label>
									<input class="form-control" name="cashout" id="cashout" type="text" placeholder="cashout" required="true" value="<?php echo $datos[0]->cashout; ?>">
								</div>

								<div class="form-group">
									<label class="sr-only" for="start"> Inicio: </label>
									<input class="form-control" name="start" id="start" type="datetime-local" value="<?php echo substr($datos[0]->start_at, 0, 10); echo "T"; echo substr($datos[0]->start_at, 11, 5); ?>">
									<small id="start" class="form-tet text-muted"> Fecha y hora de inicio </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="end"> Fin: </label>
									<input class="form-control" name="end" id="end" type="datetime-local" value="<?php echo substr($datos[0]->end_at, 6, 4); echo substr($datos[0]->end_at, 2, 4); echo substr($datos[0]->end_at, 0, 2); echo "T"; echo substr($datos[0]->end_at, 11, 5); ?>">
									<small id="end" class="form-tet text-muted"> Fecha y hora de finalizacion </small>
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
