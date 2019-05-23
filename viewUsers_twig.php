<?php

include "vendor/autoload.php";

Use \Solcre\lmsuy\Entity\UserEntity;
Use \Solcre\lmsuy\MySQL\Connect;
Use \Solcre\lmsuy\MySQL\ConnectLmsuy_db;

// BUSQUEDA DE DATOS PARA LA UI
$datosUI = array();

//extraigo datos de la bdd
$connection = new ConnectLmsuy_db;
$datosUsers = $connection->getDatosUsers();

// hidrato objetos con datos de la bdd y a la vez desarrollo datosUI segun requierimientos.


foreach ($datosUsers as $user) 
{

	$userObject = new UserEntity($user->id, $user->password, null, $user->email, $user->last_name, $user->name,  $user->username, $user->multiplier, $user->is_active, $user->hours, $user->points, $user->results, $user->cashin);


	$users[] = [
		'id' => $userObject->getId(),
		'name' => $userObject->getName(),
		'lastname' => $userObject->getLastname(), 
		'hours' => $userObject->getHours()
	];
}

//$datosUI[] = ['users' => $users];

/*$datosUI['users'] = [
		'users' => $users
];*/

$datosUI['users'] = $users;
$datosUI['breadcrumb'] = 'Usuarios';


// DISPLAY DE LA UI
$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('viewUsers.html.twig', $datosUI);



?>