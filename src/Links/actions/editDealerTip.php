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
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $session->getDatosDealerTipSessionForId($_GET["id"]);

if (sizeof($datos)==0)
{
	die("error 404");
}

if (isset($_POST["id"]))
{
	$session->updateDealerTip();
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> El dealerTip se actualizó exitosamente </code></mark>

	<br> <br>
	<a class="btn btn-primary" href="../tips.php"> volver </a>
	
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
			    <li class="breadcrumb-item"><a href="../tips.php">Tips</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Editar DealerTip</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Editar DealerTip
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post" >
								
								<input name="id" type="hidden" value="<?php echo $datos[0]->id; ?>">

								<div class="form-group">
									<label class="sr-only" for="idSession"> IdSession: </label>
									<input class="form-control" name="idSession" id="idSession" type="text" autofocus="true" placeholder="IdSession" required="true" value="<?php echo $datos[0]->idSession; ?>">
							</div>

								<div class="form-group">
									<label class="sr-only" for="hour"> hora: </label>
									<input class="form-control" name="hora" id="hora" type="datetime-local" required="true" value="<?php echo substr($datos[0]->hour, 6, 4); echo substr($datos[0]->hour, 2, 4); echo substr($datos[0]->hour, 0, 2); echo "T"; echo substr($datos[0]->hour, 11, 5); ?>">
									<small id="hour" class="form-tet text-muted"> Fecha y hora </small>
								</div>

								<div class="form-group" >
									<label class="sr-only" for="dealerTip"> dealerTip: </label>
									<input class="form-control" name="dealerTip" id="dealerTip" type="text" placeholder="dealerTip" required="true" value="<?php echo $datos[0]->dealerTip; ?>">
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