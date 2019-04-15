<?php

//recibo por GET el numero de la ultima session
//idSession= $_GET(pasar a entero) + 1   la variable idSession en las tablas debe ser un integer
// cuando doy click en tip paso tips.php?idSession


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
</head>
<body>
	<div class="container-fluid">
	<div class="row">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Nueva Sesion</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Ingresar datos
				</div>
				<div class="card-body">

					<section class="container row">

						<article class="col-md-8">
							<ul>
								<br>
								<br>
								<br>
								<a href="../../src/links/newtips.php" class="btn btn-lg btn-block btn-danger"> <b>+<b>  <i class="fas fa-hand-holding-usd"></i> tips </a> 
								<br>
								<a href="../../src/links/newcomissions.php" class="btn btn-lg btn-block btn-success"> <b>+<b>  <i class="fas fa-dollar-sign"></i> comissions </a> 
								<br>
								<a href="../../src/links/newbuyins.php" class="btn btn-lg btn-block btn-secondary"> <b>+<b>  <i class="fas fa-money-bill"></i> buyins </a> 
								<br>
								<a href="../../src/links/newusers.php" class="btn btn-lg btn-block btn-info"> <b>+<b>  <i class="fas fa-user-plus"></i> users </a>
								<br>
							</ul>
						</article>	
					</section>
				</div>
			</div>
		</div>
	</div>
	<br><br><br>

	
	
</body>
</html>