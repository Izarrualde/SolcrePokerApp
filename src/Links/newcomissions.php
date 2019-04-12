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

if (isset($_POST['idSession']))
{
	$session = new ConnectAppPoker;
	$session->insertComission();
	//header();
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> La comisión se ingresó exitosamente </code></mark>

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
			    <li class="breadcrumb-item active" aria-current="page">Nueva Comision</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Ingresar Comision
				</div>
				<div class="card-body">
					<section class="container row">
						<article>
							<form class="was-validated" action="" method="post">
								<div class="form-group has-success">
									<label class="sr-only" for="id" class="control-label">  Id: </label>
									<input class="form-control" name="id" id="id" type="text" placeholder="id" required="true" autofocus="true">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="idSession"> IdSession: </label>
									<input class="form-control" name="idSession" id="idSession" type="text" placeholder="IdSession" required="true">

								</div>

								<div class="form-group">
									<label class="sr-only" for="hour"> hour: </label>
									<input class="form-control" name="hora" id="hour" type="datetime-local" required="true" value="<?php echo substr(date('c'), 0, 16); ?>">
									<small id="hour" class="form-tet text-muted"> Fecha y hora </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="comission"> comission: </label>
									<input class="form-control" name="comission" id="comission" type="text" placeholder="Comission" required="true">
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