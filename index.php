<?php
include "src/Entity/SessionEntity.php";
include "src/Entity/UserEntity.php";
include "src/Entity/UserSession.php";
include "src/Entity/ComisionSession.php";
include "src/Entity/BuyinSession.php";
include "src/Entity/PropinaDealerSession.php";
include "src/Entity/PropinaServicioSession.php";

include "src/Exception/UserAlreadyAddedException.php";
include "src/Exception/SessionFullException.php";
include "src/Exception/InsufficientBuyinException.php";
include "src/Exception/PlayerNotFoundException.php";
include "src/Exception/ComissionAlreadyAddedException.php";
include "src/Exception/PropinaDealerAlreadyAddedException.php";
include "src/Exception/PropinaServicioAlreadyAddedException.php";

Use \Solcre\PokerApp\Entity\SessionEntity;
Use \Solcre\PokerApp\Entity\UserSession;
Use \Solcre\PokerApp\Entity\BuyinSession;
Use \Solcre\PokerApp\Entity\ComisionSession;
Use \Solcre\PokerApp\Entity\PropinaDealerSession;
Use \Solcre\PokerApp\Entity\PropinaServicioSession;

Use \Solcre\PokerApp\Exception\InsufficientBuyinException;
Use \Solcre\PokerApp\Exception\PlayerNotFoundException;
Use \Solcre\PokerApp\Exception\SessionFullException;
Use \Solcre\PokerApp\Exception\ComissionAlreadyAddedException;
Use \Solcre\PokerApp\Exception\PropinaDealerAlreadyAddedException;
Use \Solcre\PokerApp\Exception\PropinaServicioAlreadyAddedException;

// variables inicializadas para crear una instancia de tipo SessionEntity
$idSesion = 93;
$fecha = "25/03/2019";
$titulo = null;
$descripcion = null;
$foto = null;
$lugares = 9;
$lugaresEspera = 0;
$reservaEspera = 0;
$horaInicio = "18.00";
$horaInicioReal = "20.00";
$horaFin = "03.00";
$comision = 0;
$propinaDealer = 0;
$propinaServicio = 0;


$sesion = new SessionEntity($idSesion, $fecha, $titulo, $descripcion, $foto, $lugares, $lugaresEspera, $reservaEspera, $horaInicio, $horaInicioReal, $horaFin, $comision, $propinaDealer, $propinaServicio);

echo "-----------------------------------------------------------------------------------------------------"."\n";




$usersSesion[] = new UserSession("Destri", $sesion, 1, true, 0, 1000, 1500, null, null);
$usersSesion[] = new UserSession("Nazar", $sesion, 2, true, 0, 2000, 200, null, null);
$usersSesion[] = new UserSession("Zunino", $sesion, 3, true, 0, 1000, 1000, null, null);
$usersSesion[] = new UserSession("Galle", $sesion, 4, true, 0, 1500, 0, null, null);
$usersSesion[] = new UserSession("Cugurra", $sesion, 5, true, 0, 600, 1000, null, null);
$usersSesion[] = new UserSession("Altman", $sesion, 6, true, 0, 1500, 3500, null, null);
$usersSesion[] = new UserSession("Guzman", $sesion, 7, true, 0, 1000, 1100, null, null);
$usersSesion[] = new UserSession("Meyer", $sesion, 8, true, 0, 1000, 1000, null, null);

$sesion->agregarUsuarios($usersSesion);

$buyins[] = new BuyinSession(1, 93, "Destri", 200, 50, "usd", "19.00", true);
$buyins[] = new BuyinSession(2, 93, "Destri", 2000, 0, "usd", "20.00", true);
$buyins[] = new BuyinSession(3, 93, "Nazar", 1000, 1000, "usd", "19.00", true);
$buyins[] = new BuyinSession(4, 93, "Zunino", 500, 0, "usd", "19.00", true);
$buyins[] = new BuyinSession(5, 93, "Galle", 2000, 3000, "usd", "19.00", true);
$buyins[] = new BuyinSession(6, 93, "Cugurra", 2000, 0, "usd", "19.00", true);
$buyins[] = new BuyinSession(7, 93, "Altman", 200, 600, "usd", "19.00", true);
$buyins[] = new BuyinSession(8, 93, "Guzman", 0, 500, "usd", "19.00", true);
$buyins[] = new BuyinSession(9, 93, "Meyer", 1000, 0, "usd", "19.00", true);


$sesion->agregarBuyins($buyins);



$comisiones[] = new ComisionSession(1, $idSesion=93, $hora="18.00", $comision=90);
$comisiones[] = new ComisionSession(2, $idSesion=93, $hora="19.00", $comision=60);
$comisiones[] = new ComisionSession(3, $idSesion=93, $hora="20.00", $comision=30);
$comisiones[] = new ComisionSession(4, $idSesion=93, $hora="21.00", $comision=40);
$comisiones[] = new ComisionSession(5, $idSesion=93, $hora="22.00", $comision=60);
$comisiones[] = new ComisionSession(6, $idSesion=93, $hora="23.00", $comision=60);
$comisiones[] = new ComisionSession(7, $idSesion=93, $hora="00.00", $comision=50);
$comisiones[] = new ComisionSession(8, $idSesion=93, $hora="01.00", $comision=70);
$comisiones[] = new ComisionSession(9, $idSesion=93, $hora="02.00", $comision=40);
$comisiones[] = new ComisionSession(10, $idSesion=93, $hora="03.00", $comision=100);

$sesion-> agregarComisiones($comisiones);

$propinasDealer[] = new PropinaDealerSession(1, $idSesion=93, $hora="18.00", $propina=30);
$propinasDealer[] = new PropinaDealerSession(2, $idSesion=93, $hora="19.00", $propina=30);
$propinasDealer[] = new PropinaDealerSession(3, $idSesion=93, $hora="20.00", $propina=40);
$propinasDealer[] = new PropinaDealerSession(4, $idSesion=93, $hora="21.00", $propina=15);
$propinasDealer[] = new PropinaDealerSession(5, $idSesion=93, $hora="22.00", $propina=20);
$propinasDealer[] = new PropinaDealerSession(6, $idSesion=93, $hora="23.00", $propina=10);
$propinasDealer[] = new PropinaDealerSession(7, $idSesion=93, $hora="00.00", $propina=30);
$propinasDealer[] = new PropinaDealerSession(8, $idSesion=93, $hora="01.00", $propina=30);
$propinasDealer[] = new PropinaDealerSession(9, $idSesion=93, $hora="02.00", $propina=10);
$propinasDealer[] = new PropinaDealerSession(10, $idSesion=93, $hora="03.00", $propina=50);


$sesion-> agregarPropinasDealer($propinasDealer);


$propinasServicio[] = new PropinaServicioSession(1, $idSesion=93, $hora="18.00", $propina=3519);
$propinasServicio[] = new PropinaServicioSession(2, $idSesion=93, $hora="19.00", $propina=7);
$propinasServicio[] = new PropinaServicioSession(3, $idSesion=93, $hora="20.00", $propina=5);
$propinasServicio[] = new PropinaServicioSession(4, $idSesion=93, $hora="21.00", $propina=8);
$propinasServicio[] = new PropinaServicioSession(5, $idSesion=93, $hora="22.00", $propina=6);
$propinasServicio[] = new PropinaServicioSession(6, $idSesion=93, $hora="23.00", $propina=9);
$propinasServicio[] = new PropinaServicioSession(7, $idSesion=93, $hora="00.00", $propina=8);
$propinasServicio[] = new PropinaServicioSession(8, $idSesion=93, $hora="01.00", $propina=7);
$propinasServicio[] = new PropinaServicioSession(9, $idSesion=93, $hora="02.00", $propina=6);
$propinasServicio[] = new PropinaServicioSession(10, $idSesion=93, $hora="03.00", $propina=10);


$sesion-> agregarPropinasServicio($propinasServicio);


//var_dump($comisiones);


//echo "-"."\n";
$i = 1;
foreach ($sesion->getUsers() as $jugador) {
	echo $i.")".$jugador->getId()." Cashin: ".$jugador->getCashin()."	"."cashout: ".$jugador->getCashout()."	resultado: ".$jugador->getResultado()."\n";
	$i++;
}

echo "\n"."Comision por hora"."\n\n";

$comisionTotal = 0;
foreach ($sesion->getComissions() as $comision) {
	echo $comision->getHora()."	"."usd"."	".$comision->getComision()."\n";
	$comisionTotal += $comision->getComision();
}
echo "\n"."TOTAL 	".$comisionTotal."\n";

//validar sesion

echo "\n"."getTotalJugado = ".$sesion->getTotalJugado();
echo "\n"."getTotalCashout = ".$sesion->getTotalCashout();
echo "\n"."getComisionTotal = ".$sesion->getComisionTotal();
echo "\n"."getPropinaDealerTotal = ".$sesion->getPropinaDealerTotal();
echo "\n"."getPropinaServicio = ".$sesion->getPropinaServicioTotal();



echo "\n\n"."Propina Dealer"."\n";
echo "\n\n";
foreach ($sesion->getPropinasDealer() as $propina) {
	echo $propina->getHora()."	"."usd 	".$propina->getPropinaDealer()."\n";
}
echo "Total =	".$sesion->getPropinaDealerTotal()."\n";
//echo "\n"."TOTAL 	".$PropinaTotal."\n";

echo "\n\n"."Propina Servicio"."\n";
echo "\n\n";
foreach ($sesion->getPropinasServicio() as $propina) {
	echo $propina->getHora()."	"."usd 	".$propina->getPropinaServicio()."\n";
}
echo "Total =	".$sesion->getPropinaServicioTotal()."\n";

echo "\n"."Validacion de Sesion:"."\n\n";
if ($sesion->validarSesion($sesion)) {
	echo "sesion valida";
	}	else {
	echo "sesion no valida";
}

//agrego propinas de dealer