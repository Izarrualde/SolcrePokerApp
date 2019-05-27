<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;
Use \Solcre\lmsuy\Entity\UserEntity;
Use \Solcre\lmsuy\Service\UserService;

date_default_timezone_set('America/Argentina/Buenos_Aires');

$connection = new ConnectLmsuy_db;
$userService = new UserService($connection);

$datosUI = array();

$datos = $connection->getDatosUsers($_GET["id"]);


if (isset($_POST["id"]))
{
	$userObject = new UserEntity($_POST['id'], $_POST['password'], null, $_POST['email'], $_POST['lastname'], $_POST['name'],  $_POST['username'], $_POST['multiplier'], $_POST['isActive'], $_POST['hours'], $_POST['points'], $_POST['results'], $_POST['cashin']);

	$userService->update($userObject);

	$message = 'Usuario actualizado exitosamente';
	$template = 'viewUsers.html.twig';	
    $datosUI['breadcrumb'] = 'Usuarios';

	//BUSQUEDA DE DATOS PARA LA UI
	$users = $userService->find();

	$datosUI['users'] = $users;
	$datosUI['breadcrumb'] = 'Usuarios';
}
else
{
	// datos para autocomplete
	$template = 'editPlayer.html.twig';
	$datosUI['breadcrumb'] = 'Editar usuario';

	$userObject = $userService->findOne($_GET['id']);
	var_dump($userObject);

	$datosUI['user'] = $userObject->toArray();
	$datosUI['breadcrumb'] = 'Editar Jugador';
	$template = 'editPlayer.html.twig';


}


// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render($template, $datosUI);

?>