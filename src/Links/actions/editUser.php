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
include "../../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$session = new ConnectAppPoker;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idU"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $session->getDatosSessionUsersById($_GET["idU"]);

if (sizeof($datos)==0)
{
	die("error 404");
}

if (isset($_POST["id"]))
{
	$session->updateUser();
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
			    <li class="breadcrumb-item"><a href="../../../index.php">Home</a></li>
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
								<input name="id" type="hidden" value="<?php echo $datos[0]->id; ?>">
								
								<div class="form-group">
									<label class="sr-only" for="idSession"> IdUser: </label>
									<input class="form-control" name="idUser" id="idUser" type="text" autofocus="true" placeholder="IdUser" required="true" value="<?php echo $datos[0]->user_id; ?>">
								</div>

								<div class="form-group">
									<label class="sr-only" for="accumulatedPoints"> accumulatedPoints: </label>
									<input class="form-control" name="accumulatedPoints" id="accumulatedPoints" type="text" placeholder="accumulatedPoints" required="true" value="<?php echo $datos[0]->accumulated_points; ?>">
								</div>

								<input name="approved" id="approved" type="hidden" value="<?php echo $datos[0]->approved; ?>">

								<div class="form-group">
									<label class="sr-only" for="amountCash"> cashout: </label>
									<input class="form-control" name="cashout" id="cashout" type="text" placeholder="cashout" required="true" value="<?php echo $datos[0]->cashout; ?>">
								</div>

								<div class="form-group">
									<label class="sr-only" for="start"> Inicio: </label>
									<input class="form-control" name="start" id="start" type="datetime-local" required="true" value="<?php echo substr($datos[0]->start, 6, 4); echo substr($datos[0]->start, 2, 4); echo substr($datos[0]->start, 0, 2); echo "T"; echo substr($datos[0]->start, 11, 5); ?>">
									<small id="start" class="form-tet text-muted"> Fecha y hora de inicio </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="end"> Fin: </label>
									<input class="form-control" name="end" id="end" type="datetime-local" required="true" value="<?php echo substr($datos[0]->end, 6, 4); echo substr($datos[0]->end, 2, 4); echo substr($datos[0]->end, 0, 2); echo "T"; echo substr($datos[0]->end, 11, 5); ?>">
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
