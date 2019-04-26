<?php
include "../MySQL/Connect.php";
include "../MySQL/ConnectAppPoker.php";
Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;
date_default_timezone_set('America/Argentina/Buenos_Aires');

$mensaje1 = '';
$mensaje2 = '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Nuevo Tip</title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>

<?php

if (isset($_POST['enviado']))
{
	if ((is_numeric($_POST['dealerTip'])) and (is_numeric($_POST['serviceTip'])))
	{		
		$session = new ConnectAppPoker;
		$session->insertDealerTip();
		$session->insertServiceTip();
		?>

		<mark> <i class="far fa-grin-alt"></i> <code> Los tips se ingresaron exitosamente </code></mark>
		<br> <br>
		<a class="btn btn-primary" href="tips.php?id=<?php echo $_GET['id']; ?>"> volver </a>
		<?php
		exit;
	} 
	else 
	{
		if (!is_numeric($_POST['dealerTip'])) 
		{
			$mensaje1='dealerTip debe ser un valor entero';
		}
		if (!is_numeric($_POST['serviceTip'])) 
		{
			$mensaje2='serviceTip debe ser un valor entero';
		}
	} 
}

?>

<body>
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Nuevo Tip</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Ingresar Tips
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">
									<input type="hidden" class="form-control" name="id" id="id" type="text"  required="true" value="null">
								
									<input type="hidden" class="form-control" name="idSession" id="idSession" type="text" placeholder="IdSession" required="true" value="<?php echo $_GET['id']; ?>">

								<div class="form-group">
									<label class="sr-only" for="hour"> hour: </label>
									<input class="form-control" name="hour" id="hour" type="datetime-local" required="true" value="<?php echo substr(date('c'), 0, 16); ?>">
									<small id="hour" class="form-tet text-muted"> Hora </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="tip"> dealerTip: </label>
									<input class="form-control" name="dealerTip" id="dealerTip" type="text" placeholder="DealerTip" required="true" value="<?php if ((isset($_POST['dealerTip'])) and ($mensaje1=='')) echo $_POST['dealerTip'];?>">
									<?php 
									if ($mensaje1!='')
									{
										?>
										<small id="dealerTip" class="form-tet text-muted"><div class="alert alert-danger"> <?php echo $mensaje1 ?> </div></small>
										<?php
									}
									?>
								</div>

								<div class="form-group">
									<label class="sr-only" for="tip"> serviceTip: </label>
									<input class="form-control" name="serviceTip" id="serviceTip" type="text" placeholder="ServiceTip" required="true" value="<?php if ((isset($_POST['serviceTip'])) and ($mensaje2=='')) echo $_POST['serviceTip'];?>">
									<?php 
									if ($mensaje2!='')
									{
										?>
										<small id="serviceTip" class="form-tet text-muted"><div class="alert alert-danger"> <?php echo $mensaje2 ?> </div></small>
										<?php
									}
									?>
								</div>

								<div class="form-group">
									<input type="hidden" name="enviado" value="si"/>
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
