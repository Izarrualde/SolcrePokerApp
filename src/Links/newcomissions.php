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

$mensaje1 = '';
if (isset($_POST['idSession'])) 
{
	if (is_numeric($_POST['comission']))
	{
		$session = new ConnectAppPoker;
		$session->insertComission();
		?>
		<mark> <i class="far fa-grin-alt"></i> <code> La comisión se ingresó exitosamente </code></mark>
		<br> <br>
		<a class="btn btn-primary" href="comissions.php?id=<?php echo $_GET['id']; ?>"> volver </a>
		<?php
		exit;
	}else
	{
		$mensaje1='Comisión debe ser un valor entero';
	}
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
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">

									<input type="hidden" class="form-control" name="id" id="id" type="text" required="true" value="null">
								
									<input class="form-control" name="idSession" id="idSession" type="hidden" required="true" value="<?php echo $_GET['id']; ?>">

								<div class="form-group">
									<label class="sr-only" for="hour"> hour: </label>
									<input class="form-control" name="hour" id="hour" type="datetime-local" required="true" value="<?php echo substr(date('c'), 0, 16); ?>">
									<small id="hour" class="form-tet text-muted"> Fecha y hora </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="comission"> comission: </label>
									<input class="form-control" name="comission" id="comission" type="text" placeholder="Comission" required="true">
									<?php 
									if ($mensaje1!='')
									{
										?>
										<small id="comission" class="form-tet text-muted"><div class="alert alert-danger"> <?php echo $mensaje1 ?> </div></small>
										<?php
									}	 
										?>
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