<?php

include "src/Entity/SessionEntity.php";
include "src/Entity/UserEntity.php";
include "src/Entity/UserSession.php";
include "src/Entity/ComisionSession.php";
include "src/Entity/BuyinSession.php";
include "src/Entity/DealerTipSession.php";
include "src/Entity/ServiceTipSession.php";

include "src/MySQL/Connect.php";
include "src/MySQL/ConnectAppPoker.php";

include "src/Exception/UserAlreadyAddedException.php";
include "src/Exception/SessionFullException.php";
include "src/Exception/InsufficientBuyinException.php";
include "src/Exception/PlayerNotFoundException.php";
include "src/Exception/ComissionAlreadyAddedException.php";
include "src/Exception/ServiceTipAlreadyAddedException.php";
include "src/Exception/DealerTipAlreadyAddedException.php";

Use \Solcre\PokerApp\Entity\SessionEntity;
Use \Solcre\PokerApp\Entity\UserSession;
Use \Solcre\PokerApp\Entity\BuyinSession;
Use \Solcre\PokerApp\Entity\ComissionSession;
Use \Solcre\PokerApp\Entity\DealerTipSession;
Use \Solcre\PokerApp\Entity\ServiceTipSession;

Use \Solcre\pokerApp\MySQL\Connect;
Use \Solcre\pokerApp\MySQL\ConnectAppPoker;

Use \Solcre\PokerApp\Exception\InsufficientBuyinException;
Use \Solcre\PokerApp\Exception\PlayerNotFoundException;
Use \Solcre\PokerApp\Exception\SessionFullException;
Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;
Use \Solcre\PokerApp\Exception\DealerTipAlreadyAddedException;
Use \Solcre\PokerApp\Exception\ServiceTipAlreadyAddedException;


$session = new ConnectAppPoker;
$datosUsers = $session->getDatosUsers();
$datosBuyinSession = $session->getDatosBuyinSession();
$datosComissionSession = $session->getDatosComissionSession();
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


?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title> SESSION </title>
	<meta name="vierwport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">	
</head>
<body>
	<div class="container">
		<div class="col-md-8">
			<nav aria-label="breadcrumb">
				 <ol class="breadcrumb">
				    <li class="breadcrumb-item active" aria-current="page">Home</li>
				  </ol>
			</nav>

		

			<div class="card">
				<div class="card-header bg-primary text-white">
					SESIONES
				</div>
				<div class="card-body">
					<section class="container row">
						<article class="col-md-12">
						<table class="table table-bordered table-hover text-center">
							<thead class="text-center bg-danger">
								<th> IdSession </th>
								<th> Fecha </th>
								<th> Inicio </th>
								<th> Fin </th>
								<th> Horas Jugadas </th>
								<th> Total Jugado </th>
							</thead>
							<tbody>
								<tr>
									<td> miercoles </td>
									<td> <a href="src/links/Session.php"> 03-04-19 </a> </td>
									<td> xxx</td>
									<td> xxx </td>
									<td> xxx</td>
									<td> xxx </td>
								</tr>
								<tr>
									<td> jueves </td>
									<td> 04-04-19 </td>
									<td> xxx </td>
									<td> xxx </td>
									<td> xxx </td>
									<td> xxx </td>							
								</tr>
								<tr>
									<td> viernes </td>
									<td> 04-04-19 </td>
									<td> xxx </td>
									<td> xxx </td>
									<td> xxx </td>
									<td> xxx </td>
								</tr>

							</tbody>
						</table>

						<br>
						<br>
						<br>

						<a href="src/links/newsession.php" class="btn btn-lg btn-block btn-dark"> <i class="fas fa-plus"></i> new session </a>								
						</article>	
					</section>
				</div>
			</div>
		</div>
	
	</div>


	<script src=”js/jquery.js”> </script>
	<script src=”js/bootstrap.min.js”> </script>		
</body>
</html>






<?php
//var_dump($session1->sessionDealerTips);
//$session1"->sessionDealerTips[] = 



//$session1->sessionServiceTips =
//$session1->sessionUsers =
//$session1->sessionComissions =
//$session1->sessionBuyins = 



/*
// variables inicializadas para crear una instancia de tipo SessionEntity
$idSession = 93;
$date = "25/03/2019";
$title = null;
$description = null;
$photo = null;
$seats = 9;
$seatsWaiting = 0;
$reserveWaiting = 0;
$startTime = "18.00";
$starTimeReal = "20.00";
$endTime = "03.00";
$comission = 0;
$dealerTip = 0;
$serviceTip = 0;

*/

/*
$session = new SessionEntity($idSession, $date, $title, $description, $photo, $seats, $seatsWaiting, $reserveWaiting, $startTime, $starTimeReal, $endTime, $comission, $dealerTip, $serviceTip);

echo "-----------------------------------------------------------------------------------------------------"."\n";



$usersSession[] = new UserSession("Destri", $session, 1, true, 0, 1500, "15-03-2019 20:00:00", "16-03-2019 02:30:00");
$usersSession[] = new UserSession("Nazar", $session, 2, true, 0, 200, "15-03-2019 20:00:00", "16-03-2019 02:30:00");
$usersSession[] = new UserSession("Zunino", $session, 3, true, 0, 1000, "15-03-2019 20:00:00", "16-03-2019 02:30:00");
$usersSession[] = new UserSession("Galle", $session, 4, true, 0, 0, "15-03-2019 20:00:00", "16-03-2019 02:30:00");
$usersSession[] = new UserSession("Cugurra", $session, 5, true, 0, 1000, "15-03-2019 20:00:00", "16-03-2019 02:30:00");
$usersSession[] = new UserSession("Altman", $session, 6, true, 0, 3500, "15-03-2019 20:00:00", "16-03-2019 02:30:00");
$usersSession[] = new UserSession("Guzman", $session, 7, true, 0, 1100, "15-03-2019 20:00:00", "16-03-2019 02:30:00");
$usersSession[] = new UserSession("Meyer", $session, 8, true, 0, 1000, "15-03-2019 20:00:00", "16-03-2019 02:30:00");



$session->addUsers($usersSession);



$buyins[] = new BuyinSession(1, 93, "Destri", 500, 50, "usd", "19.00", true);
$buyins[] = new BuyinSession(11, 93, "Destri", 500, 0, "usd", "20.00", true);
//$buyins[] = new BuyinSession(2, 93, "Destri", 2000, 0, "usd", "20.00", true);
//$buyins[] = new BuyinSession(3, 93, "Nazar", 1000, 1000, "usd", "19.00", true);
//$buyins[] = new BuyinSession(4, 93, "Zunino", 500, 0, "usd", "19.00", true);
//$buyins[] = new BuyinSession(5, 93, "Galle", 2000, 3000, "usd", "19.00", true);
//$buyins[] = new BuyinSession(6, 93, "Cugurra", 2000, 0, "usd", "19.00", true);
//$buyins[] = new BuyinSession(7, 93, "Altman", 200, 600, "usd", "19.00", true);
//$buyins[] = new BuyinSession(8, 93, "Guzman", 0, 500, "usd", "19.00", true);
//$buyins[] = new BuyinSession(9, 93, "Meyer", 1000, 0, "usd", "19.00", true);


$session->addBuyins($buyins);


//var_dump($session->sessionBuyins);


//echo $session->sessionUsers[0]->getCashin();


$comissions[] = new ComissionSession(1, 93, "18.00", 90);
$comissions[] = new ComissionSession(2, 93, "19.00", 60);
$comissions[] = new ComissionSession(3, 93, "20.00", 30);
$comissions[] = new ComissionSession(4, 93, "21.00", 40);
$comissions[] = new ComissionSession(5, 93, "22.00", 60);
$comissions[] = new ComissionSession(6, 93, "23.00", 60);
$comissions[] = new ComissionSession(7, 93, "00.00", 50);
$comissions[] = new ComissionSession(8, 93, "01.00", 70);
$comissions[] = new ComissionSession(9, 93, "02.00", 40);
$comissions[] = new ComissionSession(10, 93, "03.00", 100);


$session-> addComissions($comissions);


$dealerTips[] = new DealerTipSession(1, 93, "18.00", 30);
$dealerTips[] = new DealerTipSession(2, 93, "19.00", 40);
$dealerTips[] = new DealerTipSession(3, 93, "20.00", 40);
$dealerTips[] = new DealerTipSession(4, 93, "21.00", 15);
$dealerTips[] = new DealerTipSession(5, 93, "22.00", 20);
$dealerTips[] = new DealerTipSession(6, 93, "23.00", 10);
$dealerTips[] = new DealerTipSession(7, 93, "00.00", 30);
$dealerTips[] = new DealerTipSession(8, 93, "01.00", 30);
$dealerTips[] = new DealerTipSession(9, 93, "02.00", 10);
$dealerTips[] = new DealerTipSession(10, 93, "03.00", 50);


$session-> addDealerTips($dealerTips);

//var_dump($dealerTips);
//var_dump($session->sessionDealerTips);


$serviceTips[] = new ServiceTipSession(1, 93, "18.00", 3516);
$serviceTips[] = new ServiceTipSession(2, 93, "19.00", 0);
$serviceTips[] = new ServiceTipSession(3, 93, "20.00", 5);
$serviceTips[] = new ServiceTipSession(4, 93, "21.00", 8);
$serviceTips[] = new ServiceTipSession(5, 93, "22.00", 6);
$serviceTips[] = new ServiceTipSession(6, 93, "23.00", 9);
$serviceTips[] = new ServiceTipSession(7, 93, "00.00", 8);
$serviceTips[] = new ServiceTipSession(8, 93, "01.00", 7);
$serviceTips[] = new ServiceTipSession(9, 93, "02.00", 6);
$serviceTips[] = new ServiceTipSession(10, 93, "03.00", 10);


$session-> addServiceTips($serviceTips);

//var_dump($session->sessionServiceTips);

//var_dump($session->sessionUsers[0]->start);

//echo "-"."\n";
echo "<br/>";
$i = 1;
foreach ($session->getSessionUsers() as $jugador) {
	$date1 = date_create($jugador->getStart());
	$date2 = date_create($jugador->getEnd());
	$hours = date_diff($date1, $date2);
	echo $i.")".$jugador->getId()." Cashin: ".$jugador->getCashin()."	"."cashout: ".$jugador->getCashout()."	resultado: ".$jugador->getResult()."	hours: ".$hours->format("%d:%H:%i")."<br/>";
	$i++;
}

echo "<br/>"."Comision por hora"."<br/>";



$comissionTotal = 0;
foreach ($session->getSessionComissions() as $comission) {
	echo $comission->getHour()."	"."usd"."	".$comission->getComission()."<br/>";
	$comissionTotal += $comission->getComission();
}
echo "<br/>"."TOTAL 	".$comissionTotal."<br/>";

//validar sesion

echo "<br/>"."getTotalPlayed = ".$session->getTotalPlayed();
echo "<br/>"."getTotalCashout = ".$session->getTotalCashout();
echo "<br/>"."getComissionTotal = ".$session->getComissionTotal();
echo "<br/>"."getDealerTipTotal = ".$session->getDealerTipTotal();
echo "<br/>"."getServiceTipTotal = ".$session->getServiceTipTotal();




echo "<br/>"."Propina Dealer"."<br/>";
echo "<br/>";
foreach ($session->getSessionDealerTips() as $tip) {
	echo $tip->getHour()."	"."usd 	".$tip->getDealerTip()."<br/>";
}
echo "Total =	".$session->getDealerTipTotal()."<br/>";
//echo "\n"."TOTAL 	".$PropinaTotal."\n";

echo "<br/>"."Propina Servicio"."<br/>";
echo "<br/>";
foreach ($session->getSessionServiceTips() as $tip) {
	echo $tip->getHour()."	"."usd 	".$tip->getServiceTip()."<br/>";
}
echo "Total =	".$session->getServiceTipTotal()."<br/>";

echo "<br/>"."Validacion de Sesion:"."<br/>";
if ($session->validateSession($session)) {
	echo "sesion valida";
	}	else {
	echo "sesion no valida";
}
*/