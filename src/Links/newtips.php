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
$mensaje2 = '';
echo "<br>";
var_dump($_POST);
echo "<br>";

echo "<br>"."valor de mensaje1=".$mensaje1;
echo "<br>"."valor de mensaje2=".$mensaje2;


if (isset($_POST['enviado']))
{
	if ((is_numeric($_POST['dealerTip'])) and (is_numeric($_POST['servicetip'])))
	{
		
		echo "<br>"."dealerTip es visto como entero"."<br>";
		echo "<br>"."serviceTip es visto como entero"."<br>";

		$session = new ConnectAppPoker;
		$session->insertDealerTip();
		$session->insertServiceTip();
		//header();
		?>

		<mark> <i class="far fa-grin-alt"></i> <code> Los tips se ingresaron exitosamente </code></mark>

		<br> <br>
		<a class="btn btn-primary" href="newsession.php"> volver </a>
	
		<?php
		exit;
	} 
	else 
	{
		switch (!is_numeric($_POST['dealerTip'])) 
		{
			case 'false':
				$mensaje1='dealerTip debe ser un valor entero';
				break;
		}
		switch (!is_numeric($_POST['servicetip'])) 
		{
			case 'false':
				$mensaje2='serviceTip debe ser un valor entero';
				break;	
		}
	} 
}

echo "<br>";
echo $mensaje1;
echo "<br>";
echo $mensaje2;
echo "<br>";
?>
	



<body>
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Nueva Sesion</li>
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
								<div class="form-group">
									<label class="sr-only" for="id">  Id: </label>
									<input class="form-control" name="id" id="id" type="text" placeholder="id" autofocus="true" required="true" value="<?php if (($mensaje1!='') or ($mensaje1!='')) echo $_POST['id'];?>">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="idSession"> IdSession: </label>
									<input class="form-control" name="idSession" id="idSession" type="text" placeholder="IdSession" required="true" value="<?php if (($mensaje1!='') or ($mensaje1!='')) echo $_POST['idSession'];?>" >
								</div>

								<div class="form-group">
									<label class="sr-only" for="hour"> hour: </label>
									<input class="form-control" name="hora" id="hour" type="datetime-local" required="true" value="<?php echo substr(date('c'), 0, 16); ?>">
									<small id="hour" class="form-tet text-muted"> Hora </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="tip"> dealerTip: </label>
									<input class="form-control" name="dealerTip" id="dealerTip" type="text" placeholder="DealerTip" required="true" value="<?php if ($mensaje2!='') echo $_POST['dealerTip'];?>">
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
									<input class="form-control" name="servicetip" id="servicetip" type="text" placeholder="ServiceTip" required="true" value="<?php if ($mensaje1!='') echo $_POST["servicetip"];?>">
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
