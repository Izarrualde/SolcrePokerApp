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
			    <li class="breadcrumb-item active" aria-current="page">Nuevo Usuario</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Ingresar Usuario
				</div>
				<div class="card-body">
					<section class="container row">
						<article>
							<form>
								<div class="form-group">
									<label class="sr-only" for="id">  Id: </label>
									<input class="form-control" id="id" type="text" placeholder="id" autofocus="true">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="idSession"> IdUser: </label>
									<input class="form-control" id="idUser" type="text" placeholder="IdUser">
								</div>

								<div class="form-group">
									<label class="sr-only" for="acumulatedPoints"> Puntos Acumulados: </label>
									<input class="form-control" id="acumulatedPoints" type="text" placeholder="Puntos Acumulados">
								</div>

								<div class="form-group">
									<label class="sr-only" for="cashout"> Cashout: </label>
									<input class="form-control" id="cashout" type="text" placeholder="Cashout">
								</div>

								<div class="form-group">
									<label class="sr-only" for="horaInicio"> hora inicio: </label>
									<input class="form-control" id="horaInicio" type="datetime-local" placeholder="hora de inicio">
									<small id="horaInicio" class="form-tet text-muted"> Fecha y hora de inicio </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="horaFin"> hora fin: </label>
									<input class="form-control" id="horaFin" type="datetime-local" placeholder="hora de fin">
									<small id="horaFin" class="form-tet text-muted"> Fecha y hora de finalización </small>
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