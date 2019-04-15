<?php
include "src/Entity/SessionEntity.php";
//include "src/Entity/UserEntity.php";
include "src/Entity/UserSession.php";
include "src/Entity/ComisionSession.php";
include "src/Entity/BuyinSession.php";
include "src/Entity/DealerTipSession.php";
include "src/Entity/ServiceTipSession.php";

include "src/MySQL/Connect.php";
include "src/MySQL/ConnectAppPoker.php";

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
//Use \Solcre\PokerApp\Entity\ComissionSession;
Use \Solcre\PokerApp\Entity\DealerTipSession;
Use \Solcre\PokerApp\Entity\ServiceTipSession;

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
//$datosComissionSession = $session->getDatosComissionSession();
$datosDealerTipSession = $session->getDatosDealerTipSession();
$datosServiceTipSession = $session->getDatosServiceTipSession();
//var_dump($datos);





// hasta aca exhibi datos proveniente de mysql, pero no hidrate objetos, esa informacion no quedo incluida en mis objetos, solo en variables array que cree temporalmente.

//hidratar objetos
// quiero crear un objeto de tipo SessionEntiry y en el almacenar toda la informacion de la sesion

$session1 = new SessionEntity;

//agregar dealerTipSession a la session1, $session1->sessionDealerTips es un array de objetos del tipo DealerTipSession
//=> debo hidratar los objetos DealerTipSession, cada entrada de ese array es una linea de la tabla dealertipsession

foreach ($datosDealerTipSession as $dealerTip) 
{
	$session1->sessionDealerTips[] = new DealerTipSession($dealerTip->id, $dealerTip->idSession, $dealerTip->hour, $dealerTip->dealerTip);
}

foreach ($datosServiceTipSession as $serviceTip) 
{
	$session1->sessionServiceTips[] = new ServiceTipSession($serviceTip->id, $serviceTip->idSession, $serviceTip->hour, $serviceTip->servicetip);
}

/*
foreach ($datosComissionSession as $comission) 
{
	$session1->sessionComissions[] = new ComissionSession($comission->id, $comission->idSession, $comission->hour, $comission->comission);
}

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
	<title> info tips </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<h1> Tips </h1>

</head>
<body>
	<div class="container">
		<section class="container row">
			<article class="col-md-6">
				<p> <strong> dealer tips </strong> </p>
				<table class="table table-bordered table-hover table-condensed">
					<thead class="text-center bg-success">
						<th> id </th>
						<th> hour</th>
						<th> dealerTip </th>
					</thead>
					<tbody>
						<?php 
						foreach ($session1->sessionDealerTips as $dealerTip) 
						{
						?>
							<tr>
								<td> <center> <?php echo $dealerTip->getId() ?> </center> </td>
								<td> <center> <?php echo $dealerTip->getHour() ?> </center> </td>
								<td> <center> <?php echo $dealerTip->getDealerTip() ?> </center> </td>
							</tr>
						<?php
						}
						?>
				
					</tbody>  
				</table>
			</article>	

			<aside class="col-md-6">
				<p> <strong> service tips </strong> </p>
				<table class="table table-bordered table-hover table-condensed">
					<thead class="text-center bg-success">
						<th> id </th>
						<th> hour</th>
						<th> ServiceTip </th>
					</thead>
					<tbody>
						<?php 
						foreach ($session1->sessionServiceTips as $ServiceTip) 
						{
						?>
							<tr>
								<td> <center> <?php echo $ServiceTip->getId() ?> </center> </td>
								<td> <center> <?php echo $ServiceTip->getHour() ?> </center> </td>
								<td> <center> <?php echo $ServiceTip->getServiceTip() ?> </center> </td>
							</tr>
						<?php
						}
						?>
				
					</tbody>  
				</table>
			</aside>	


		</section>
	</div>




		<script src=”js/jquery.js”> </script>
		<script src=”js/bootstrap.min.js”> </script>
		
</body>
</html>