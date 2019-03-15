<?php

include "src/Entity/SessionEntity.php";
include "src/Entity/UserEntity.php";
include "src/Entity/UserSession.php";
include "src/Entity/ComisionSession.php";
include "src/Entity/BuyinSession.php";
include "src/Entity/DealerTipSession.php";
include "src/Entity/ServiceTipSession.php";

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

Use \Solcre\PokerApp\Exception\InsufficientBuyinException;
Use \Solcre\PokerApp\Exception\PlayerNotFoundException;
Use \Solcre\PokerApp\Exception\SessionFullException;
Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;
Use \Solcre\PokerApp\Exception\DealerTipAlreadyAddedException;
Use \Solcre\PokerApp\Exception\ServiceTipAlreadyAddedException;

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


$session = new SessionEntity($idSession, $date, $title, $description, $photo, $seats, $seatsWaiting, $reserveWaiting, $startTime, $starTimeReal, $endTime, $comission, $dealerTip, $serviceTip);

echo "-----------------------------------------------------------------------------------------------------"."\n";




$usersSession[] = new UserSession("Destri", $session, 1, true, 0, 1000, 1500, null, null);
$usersSession[] = new UserSession("Nazar", $session, 2, true, 0, 2000, 200, null, null);
$usersSession[] = new UserSession("Zunino", $session, 3, true, 0, 1000, 1000, null, null);
$usersSession[] = new UserSession("Galle", $session, 4, true, 0, 1500, 0, null, null);
$usersSession[] = new UserSession("Cugurra", $session, 5, true, 0, 600, 1000, null, null);
$usersSession[] = new UserSession("Altman", $session, 6, true, 0, 1500, 3500, null, null);
$usersSession[] = new UserSession("Guzman", $session, 7, true, 0, 1000, 1100, null, null);
$usersSession[] = new UserSession("Meyer", $session, 8, true, 0, 1000, 1000, null, null);

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

var_dump($session->sessionBuyins);

echo $session->sessionUsers[0]->getCashin();


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



/*
//echo "-"."\n";
$i = 1;
foreach ($session->getUsers() as $jugador) {
	echo $i.")".$jugador->getId()." Cashin: ".$jugador->getCashin()."	"."cashout: ".$jugador->getCashout()."	resultado: ".$jugador->getResultado()."\n";
	$i++;
}

echo "\n"."Comision por hora"."\n\n";
*/
/*

$comissionTotal = 0;
foreach ($session->getComissions() as $comission) {
	echo $comission->getHour()."	"."usd"."	".$comission->getComission()."\n";
	$comissionTotal += $comission->getComission();
}
echo "\n"."TOTAL 	".$comissionTotal."\n";

//validar sesion

echo "\n"."getTotalPlayed = ".$session->getTotalPlayed();
echo "\n"."getTotalCashout = ".$session->getTotalCashout();
echo "\n"."getComissionTotal = ".$session->getComissionTotal();
echo "\n"."getDealerTipTotal = ".$session->getDealerTipTotal();
echo "\n"."getServiceTipTotal = ".$session->getServiceTipTotal();


*/
/*
echo "\n\n"."Propina Dealer"."\n";
echo "\n\n";
foreach ($session->getDealerTips() as $tip) {
	echo $tip->getHour()."	"."usd 	".$tip->getDealerTip()."\n";
}
echo "Total =	".$session->getDealerTipTotal()."\n";
//echo "\n"."TOTAL 	".$PropinaTotal."\n";

echo "\n\n"."Propina Servicio"."\n";
echo "\n\n";
foreach ($session->getServiceTips() as $tip) {
	echo $tip->getHour()."	"."usd 	".$tip->getServiceTip()."\n";
}
echo "Total =	".$session->getServiceTipTotal()."\n";

echo "\n"."Validacion de Sesion:"."\n\n";
if ($session->validateSession($session)) {
	echo "sesion valida";
	}	else {
	echo "sesion no valida";
}
*/
//agrego propinas de dealer
