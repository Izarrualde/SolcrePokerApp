<?php
require_once 'vendor/autoload.php';

Use \Solcre\lmsuy\Service\SessionService;
Use \Solcre\lmsuy\Entity\SessionEntity;
Use \Solcre\lmsuy\Entity\UserEntity;
Use \Solcre\lmsuy\Entity\UserSession;
Use \Solcre\lmsuy\Entity\BuyinSession;
Use \Solcre\lmsuy\Entity\ComissionSession;
Use \Solcre\lmsuy\Entity\DealerTipSession;
Use \Solcre\lmsuy\Entity\ServiceTipSession;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Exception\InsufficientBuyinException;
Use \Solcre\lmsuy\Exception\PlayerNotFoundException;
Use \Solcre\lmsuy\Exception\SessionFullException;
Use \Solcre\lmsuy\Exception\ComissionAlreadyAddedException;
Use \Solcre\lmsuy\Exception\DealerTipAlreadyAddedException;
Use \Solcre\lmsuy\Exception\ServiceTipAlreadyAddedException;

// BUSQUEDA DE DATOS PARA LA UI
$datosUI = array();

//extraigo datos de bd
$connection = new ConnectLmsuy_db;
$sessionService = new SessionService($connection);

$sessions = $sessionService->find();

if (!empty($_POST))
{
	$connection->insertSession();
	$datosUI['mensaje'] = "La sesion se agregó exitosamente";
}

// hidrato objetos con datos de la bdd y a la vez desarrollo datosUI segun requerimientos
$datosUI['sessions'] = array();//

foreach ($sessions as $sessionObject) 
{
	$datosUI['sessions'][] = $sessionObject->toArray();
}

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates'); //especificar como cargar templates esa clase recibe parametro la carpeta, 
$twig = new \Twig\Environment($loader); //crea instancia de twig

echo $twig->render('index.html.twig', $datosUI); //te devuelve contenido de template con las variables ya interpretadas