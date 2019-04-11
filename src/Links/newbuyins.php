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

if (isset($_POST['idSession']))
{
	$session = new ConnectAppPoker;
	$session->insertBuyin(); 
	//header();
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> El buyin se ingresó exitosamente </code></mark>
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
			    <li class="breadcrumb-item active" aria-current="page">Nuevo Buyin</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Ingresar Buyin
				</div>
				<div class="card-body">
					<section class="container row">
						<article>
							<form class="was-validated" action="" method="post">
								<div class="form-group">
									<label class="sr-only" for="id">  Id: </label>
									<input class="form-control" name="id" id="id" type="text" placeholder="id" required="true" autofocus="true">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="idSession"> IdSession: </label>
									<input class="form-control" name="idSession" id="idSession" type="text" placeholder="IdSession" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="idSession"> IdSession: </label>
									<input class="form-control" name="idPlayer" id="idSession" type="text" placeholder="IdPlayer" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="amountCash"> monto cash: </label>
									<input class="form-control" name="amountCash" id="amountCash" type="text" placeholder="monto cash" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="amountCredit"> monto credito: </label>
									<input class="form-control" name="amountCredit" id="amountCredit" type="text" placeholder="monto credito" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="hour"> hora: </label>
									<input class="form-control" name="hora" id="hour" type="datetime-local" required="true">
									<small id="hour" class="form-tet text-muted"> Fecha y hora </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="currency"> currency: </label>
									<input class="form-control" name="currency" id="currency" type="text" placeholder="moneda" required="true">
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