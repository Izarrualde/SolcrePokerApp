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
							<form>
								<div class="form-group has-success">
									<label class="sr-only" for="id" class="control-label">  Id: </label>
									<input class="form-control" id="id" type="text" placeholder="id" required="true" autofocus="true">
								</div>
								
								<div class="form-group">
									<label class="sr-only" for="idSession"> IdSession: </label>
									<input class="form-control" id="idSession" type="text" placeholder="IdSession" required="true">
								</div>

								<div class="form-group">
									<label class="sr-only" for="hour"> hour: </label>
									<input class="form-control" id="hour" type="datetime-local" required="true">
									<small id="hour" class="form-tet text-muted"> Fecha y hora </small>
								</div>

								<div class="form-group">
									<label class="sr-only" for="comission"> comission: </label>
									<input class="form-control" id="comission" type="text" placeholder="Comission" required="true">
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