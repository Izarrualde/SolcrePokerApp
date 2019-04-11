<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
</head>
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
							<form>
								<div class="form-group">
									<label class="sr-only" for="id">  Id: </label>
									<input class="form-control" id="id" type="text" placeholder="id" required="true" autofocus="true">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="idSession"> IdSession: </label>
									<input class="form-control" id="idSession" type="text" placeholder="IdSession" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="amountCash"> monto cash: </label>
									<input class="form-control" id="amountCash" type="text" placeholder="monto cash" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="amountCredit"> monto credito: </label>
									<input class="form-control" id="amountCredit" type="text" placeholder="monto credito" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="hour"> hora: </label>
									<input class="form-control" id="hour" type="datetime-local" required="true">
									<small id="hour" class="form-tet text-muted"> Fecha y hora </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="currency"> currency: </label>
									<input class="form-control" id="currency" type="text" placeholder="moneda" required="true">
								</div>

								<a href="" class="btn btn-lg btn-block btn-primary"> enviar </a>
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