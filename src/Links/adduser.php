<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Agregar Usuario</title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>

<?php
include "../MySQL/Connect.php";
include "../MySQL/ConnectLmsuy_db.php";
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

print_r($_POST);
echo "<br>";
foreach ($_POST as $key => $value) {
	echo $key; echo "-->"; echo $value;
	echo "<br>";
}

if (isset($_POST['id']))
{
	$connection = new ConnectLmsuy_db;
	$mensaje = $connection->addUser(date('c'), $_POST['lastname'], $_POST['firstname'], $_POST['username'], $_POST['mobile'], $_POST['email'], $_POST['password'], $_POST['multiplier'], $_POST['active'], $_POST['hours'], $_POST['points'], $_POST['results'], $_POST['cashin'], null, null);



	?>
	<mark> <!--<i class="far fa-grin-alt"></i> --><code> <?php echo $mensaje ?> </code></mark>
	<br> <br>
	<a class="btn btn-primary" href="viewusers.php"> volver </a>
	
	<?php
	exit;
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

									<input class="form-control" name="id" id="id" type="hidden" value="" required="true">

								<div class="form-group">
									<label class="sr-only" for="lastname"> Apellido </label>
									<input class="form-control"name="lastname" id="lastname" type="text" placeholder="Apellido" required="true" autofocus="true"> 
								</div>		

								<div class="form-group">
									<label class="sr-only" for="firstname"> Nombre </label>
									<input class="form-control"name="firstname" id="firstname" type="text" placeholder="Nombre" required="true" autofocus="true"> 
								</div>		

								<div class="form-group">
									<label class="sr-only" for="username"> Jugador </label>
									<input class="form-control"name="username" id="username" type="text" placeholder="Nombre de usuario" required="true" autofocus="true"> 
								</div>		

								<div class="form-group">
									<label class="sr-only" for="mobile"> Celular </label>
									<input class="form-control"name="mobile" id="mobile" type="text" placeholder="Teléfono" required="true"> 
								</div>

								<div class="form-group">
									<label class="sr-only" for="email"> E-mail </label>
									<input class="form-control"name="email" id="email" type="text" placeholder="E-mail" required="true"> 
								</div>

									<input class="form-control" name="password" id="password" type="hidden" required="true" value="">
									<input class="form-control" name="multiplier" id="multiplier" type="hidden" required="true" value="">
									<input class="form-control" name="active" id="active" type="hidden" required="true" value="">
									<input class="form-control" name="hours" id="hours" type="hidden" required="true" value="">
									<input class="form-control" name="points" id="points" type="hidden" required="true" value="">
									<input class="form-control" name="results" id="results" type="hidden" required="true" value="">
									<input class="form-control" name="cashin" id="cashin" type="hidden" required="true" value="">


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





