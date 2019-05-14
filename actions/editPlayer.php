<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Editar Jugador </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>

<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))
{
	die("error 404 primero"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $connection->getDatosUsers($_GET["id"]);
//$horaInicio = $session->getHourFirstBuyin()->start;

if (sizeof($datos)==0)
{
	die("error 404 segundo");
}

if (isset($_POST["id"]))
{
	$connection->updateUser($_POST['name'], $_POST['last_name'], $_POST['username'], $_POST['email'], $_POST['id']);
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> El usuario se actualizó exitosamente </code></mark>

	<br> <br>
	<a class="btn btn-primary" href="../viewUsers.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	
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
			    <li class="breadcrumb-item active" aria-current="page">Editar Jugador </li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Editar Jugador
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">
								<input name="id" type="hidden" value="<?php echo $_GET['id']; ?>">


								<!-- usar en usuario inactivo
								<div class="form-group">
									<label class="sr-only" for="amountCash"> cashout: </label>
									<input class="form-control" name="cashout" id="cashout" type="text" placeholder="cashout" required="true" value="<?php echo $datos[0]->cashout; ?>">
								</div> -->

								<div class="form-group">
									<label class="sr-only" for="name"> Nombre: </label>
									<input class="form-control" name="name" id="start" type="text" placeholder="Nombre" required="true"value="<?php echo $datos[0]->name; ?>">
								</div>

								<div class="form-group">
									<label class="sr-only" for="name"> Apellido: </label>
									<input class="form-control" name="last_name" id="last_name" type="text" value="<?php echo $datos[0]->last_name; ?>" placeholder="Apellido" required="true" autofocus=
									>
								</div>			


								<div class="form-group">
									<label class="sr-only" for="username
									"> Apellido: </label>
									<input class="form-control" name="username" id="last_name" type="text" placeholder="Nombre de usuario" required="true" value="<?php echo $datos[0]->username; ?>">
								 </div>	

								<div class="form-group">
									<label class="sr-only" for="email
									"> Apellido: </label>
									<input class="form-control" name="email" id="last_name" type="text" placeholder="email" required="true" value="<?php echo $datos[0]->email; ?>">
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
