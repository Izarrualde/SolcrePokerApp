<?php
include "../vendor/autoload.php";

Use \Solcre\lmsuy\Service\UserService;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;


$connection = new ConnectLmsuy_db;
$userService = new UserService($connection);

$datosUI = array();

$user = $userService->findOne($_GET["id"]);
$userService->delete($user);
$message = 'Usuario eliminado exitosamente';

//BUSQUEDA DE DATOS PARA LA UI
$users = $userService->find();


$datosUI['users'] = $users;
$datosUI['breadcrumb'] = 'Usuarios';
$datosUI['message'] = $message;

//DISPLAY DE LA UI

$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('viewUsers.html.twig', $datosUI);

?>