<?php
include "../Entity/SessionEntity.php";
//include "src/Entity/UserEntity.php";
include "../Entity/UserSession.php";
include "../Entity/ComisionSession.php";
include "../Entity/BuyinSession.php";
include "../Entity/DealerTipSession.php";
include "../Entity/ServiceTipSession.php";

include "../MySQL/Connect.php";
include "../MySQL/ConnectAppPoker.php";

//include "src/Exception/UserAlreadyAddedException.php";
//include "src/Exception/SessionFullException.php";
//include "src/Exception/InsufficientBuyinException.php";
//include "src/Exception/PlayerNotFoundException.php";
//include "src/Exception/ComissionAlreadyAddedException.php";
//include "src/Exception/ServiceTipAlreadyAddedException.php";
//include "src/Exception/DealerTipAlreadyAddedException.php";

Use \Solcre\PokerApp\Entity\SessionEntity;
//Use \Solcre\PokerApp\Entity\UserSession;
//Use \Solcre\PokerApp\Entity\BuyinSession;
Use \Solcre\PokerApp\Entity\ComissionSession;
//Use \Solcre\PokerApp\Entity\DealerTipSession;
//Use \Solcre\PokerApp\Entity\ServiceTipSession;

Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

//Use \Solcre\PokerApp\Exception\InsufficientBuyinException;
//Use \Solcre\PokerApp\Exception\PlayerNotFoundException;
//Use \Solcre\PokerApp\Exception\SessionFullException;
//Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;
//Use \Solcre\PokerApp\Exception\DealerTipAlreadyAddedException;
//Use \Solcre\PokerApp\Exception\ServiceTipAlreadyAddedException;


$session = new ConnectAppPoker;
//$datosUsers = $session->getDatosUsers();
//$datosBuyinSession = $session->getDatosBuyinSession();
$datosComissionSession = $session->getDatosComissionSession();
//$datosDealerTipSession = $session->getDatosDealerTipSession();
//$datosServiceTipSession = $session->getDatosServiceTipSession();
//var_dump($datos);





// hasta aca exhibi datos proveniente de mysql, pero no hidrate objetos, esa informacion no quedo incluida en mis objetos, solo en variables array que cree temporalmente.

//hidratar objetos
// quiero crear un objeto de tipo SessionEntiry y en el almacenar toda la informacion de la sesion

$session1 = new SessionEntity;

//agregar dealerTipSession a la session1, $session1->sessionDealerTips es un array de objetos del tipo DealerTipSession
//=> debo hidratar los objetos DealerTipSession, cada entrada de ese array es una linea de la tabla dealertipsession

/*
foreach ($datosDealerTipSession as $dealerTip) 
{
	$session1->sessionDealerTips[] = new DealerTipSession($dealerTip->id, $dealerTip->idSession, $dealerTip->hour, $dealerTip->dealerTip);
}

foreach ($datosServiceTipSession as $serviceTip) 
{
	$session1->sessionServiceTips[] = new ServiceTipSession($serviceTip->id, $serviceTip->idSession, $serviceTip->hour, $serviceTip->servicetip);
}
*/

foreach ($datosComissionSession as $comission) 
{
	$session1->sessionComissions[] = new ComissionSession($comission->id, $comission->idSession, $comission->hour, $comission->comission);
}

/*
foreach ($datosBuyinSession as $buyin) 
{
	$session1->sessionBuyins[] = new BuyinSession($buyin->id, $buyin->idSession, $buyin->idPlayer, $buyin->amountCash, $buyin->amountCredit, $buyin->currency, $buyin->hour, $buyin->approved);
}

foreach ($datosUsers as $user) 
{
	$session1->sessionUsers[] = new UserSession($user->id, $session1, $user->idUser, $user->approved, $user->accumulatedPoints, $user->cashout, $user->start, $user->end);
}
*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> info comissions </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="../../css/bootstrap.min.css">

</head>
<body>
	
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			    <li class="breadcrumb-item"><a href="../../index.php">Home</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Comissions</li>
			  </ol>
			</nav>
			<div class="card">
				<div class="card-header bg-primary text-white">
					Comissions
				</div>
				<div class="card-body">
					<section class="container row">
						<article class="col-md-6">
							
							<table class="table table-bordered table-hover table-condensed">
								<thead class="text-center bg-secondary">
									<tr>
										<th colspan="2"> Comission </th>
										<th> <?php echo date_format(date_create($datosComissionSession[0]->hour), 'd-m-y'); ?>
									</tr>
									<tr class="bg-success">
										<th> id </th>
										<th> hour</th>
										<th> comisision </th>							
									</tr>

								</thead>
								<tbody class="text-center">
									<?php 
									foreach ($session1->sessionComissions as $comission) 
									{
									?>
										<tr>
											<td> <?php echo $comission->getId() ?> </td>
											<td> <?php echo date_format(date_create($comission->getHour()), 'H:i') ?> </td>
											<td> <?php echo $comission->getComission() ?> </td>
										</tr>
									<?php
									}
									?>
										<tr class="text-center bg-secondary">
											<th> TOTAL </th>
											<th> </th>
											<th> <?php echo $session1->getComissionTotal() ?></th>
										</tr>			
								</tbody>  
							</table>
						
						</article>	



					</section>
				</div>
			</div>
		</div>
	</div>




		<script src=”/../../js/jquery.js”> </script>
		<script src=”/../../js/bootstrap.min.js”> </script>
		
</body>
</html>