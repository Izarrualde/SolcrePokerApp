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
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idT"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $session->getDatosSessionServiceTipById($_GET["idT"]);

if (sizeof($datos)==0)
{
	die("error 404");
}

if (isset($_POST["id"]))
{
	$session->updateServiceTip();
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> El serviceTip se actualizó exitosamente </code></mark>

	<br> <br>
	<a class="btn btn-primary" href="../tips.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	
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
			    <li class="breadcrumb-item"><a href="../tips.php?id=<?php echo $_GET['id']; ?>">Tips</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Editar serviceTip</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Editar serviceTip
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">
								
								<input name="id" type="hidden" value="<?php echo $datos[0]->id; ?>">
								
								<input name="idSession" type="hidden" id="idSession" type="text" required="true" value="<?php echo $datos[0]->session_id; ?>">

								<div class="form-group">
									<label class="sr-only" for="hour"> hora: </label>
									<input class="form-control" name="hour" id="hora" type="datetime-local" required="true" value="<?php echo substr($datos[0]->hour, 6, 4); echo substr($datos[0]->hour, 2, 4); echo substr($datos[0]->hour, 0, 2); echo "T"; echo substr($datos[0]->hour, 11, 5); ?>">
									<small id="hour" class="form-tet text-muted"> Fecha y hora </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="servicetip"> serviceTip: </label>
									<input class="form-control" name="serviceTip" id="serviceTip" type="text" placeholder="serviceTip" required="true" value="<?php echo $datos[0]->service_tip; ?>">
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