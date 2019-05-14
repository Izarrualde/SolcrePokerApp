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
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
if (!isset($_GET["id"]) or !is_numeric($_GET["id"]) or !isset($_GET["idB"]))
{
	die("error 404"); //porque esa id no existe, no tiene ninguna comission asociada.
}

$datos = $connection->getDatosSessionBuyinById($_GET["idB"]);

if (sizeof($datos)==0)
{
	die("error 404");
}

if (isset($_POST["id"]))
{
	$connection->updateBuyin($_POST['amountCash'], $_POST['amountCredit'], $_POST['currency'], $_POST['hour'], $_POST['approved'], $_POST['id']);
	?>
	<mark> <i class="far fa-grin-alt"></i> <code> El buyin se actualizó exitosamente </code></mark>

	<br> <br>
	<a class="btn btn-primary" href="../buyins.php?id=<?php echo $_GET['id']; ?>"> volver </a>
	
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
			    <li class="breadcrumb-item active" aria-current="page">Editar Buyin</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Editar Buyin
				</div>
				<div class="card-body">
					<section class="container row justify-content-center">
						<article>
							<form class="was-validated" action="" method="post">
								<input name="id" type="hidden" value="<?php echo $datos[0]->id; ?>">
								
								<input name="idSessionUser" id="idSessionUser" type="hidden" required= "true" value="<?php echo $datos[0]->session_user_id; ?>">


								<div class="form-group">
									<label class="sr-only" for="amountCash"> amountCash: </label>
									<input class="form-control" name="amountCash" id="amountCash" type="text" autofocus="true" placeholder="amountCash" required="true" value="<?php echo $datos[0]->amount_of_cash_money; ?>">
								</div>

								<div class="form-group">
									<label class="sr-only" for="amountCredit"> amountCredit: </label>
									<input class="form-control" name="amountCredit" id="amountCredit" type="text" placeholder="amountCredit" required="true" value="<?php echo $datos[0]->amount_of_credit_money; ?>">
								</div>

								<div class="form-group">
									<label class="sr-only" for="currency"> currency: </label>
									<input class="form-control" name="currency" id="currency" type="text" placeholder="Currency" required="true" value="<?php echo $datos[0]->currency_id; ?>">
								</div>

								<div class="form-group">
									<label class="sr-only" for="hour"> hora: </label>
									<input class="form-control" name="hour" id="hour" type="datetime-local" required="true" value="<?php echo substr($datos[0]->created_at, 6, 4); echo substr($datos[0]->created_at, 2, 4); echo substr($datos[0]->created_at, 0, 2); echo "T"; echo substr($datos[0]->created_at, 11, 5); ?>">
									<small id="hour" class="form-tet text-muted"> Fecha y hora </small>
								</div>

									<input name="approved" id="approved" type="hidden" required="true" value="<?php echo $datos[0]->approved; ?>">

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
