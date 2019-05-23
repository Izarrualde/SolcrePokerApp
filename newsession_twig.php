<?php
include "vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$datosUI = array();
$connection = new ConnectLmsuy_db;


if (isset($_POST['id']))
{
	$connection->insertSession($_POST['date'], $_POST['title'], $_POST['description'], $_POST['seats'], $_POST['startTime'], $_POST['startTimeReal'], $_POST['end']);
	$template = 'index.html.twig'; //desplegar un mensaje de exito
}
else
{
	//envio a usaruaio a newsession.html.twig con datos necesarios para completar formulario
	$template = 'newsession.html.twig';
	$datosUI['breadcrumb'] = 'Nueva sesion';

	// BUSQUEDA DE DATOS PARA LA UI
	// no se necesitan!
}

// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);


echo $twig->render($template, $datosUI);

?>