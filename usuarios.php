<?php
Namespace Solcre\PokerApp\Usuario;

class Usuario {
	protected $id;
	protected $usuario;
	protected $clave;
	protected $email;
	protected $apellido;
	protected $multiplicador;

public function __construct($id=null, $usuario="", $clave="", $email="", $apellido="", $multiplicador= null) {
	$this->id = setId($id);
	$this->usuario = setUsuario($usuario);
	$this->clave = setClave($clave);
	$this->email = setEmail($email);
	$this->apellido = setApellido($apellido);
	$this->multiplicador = setMultiplicador($multiplicador);
}

public function setId($id){
	$this->id = $id;
	return $this;
}

public function getId($id) {
	return $this->id;
}

public function setUsuario($usuario){
	$this->usuario = $usuario;
	return $this;
}

public function getUsuario($usuario) {
	return $this->usuario;
}

public function setClave($clave){
	$this->clave = $clave;
	return $this;
}

public function getClave($clave) {
	return $this->clave;
}

public function setEmail($email){
	$this->email = $email;
	return $this;
}

public function getEmail($email) {
	return $this->email;
}

public function setApellido($apellido){
	$this->apellido = $apellido;
	return $this;
}

public function getApellido($apellido) {
	return $this->apellido;
}

protected function activo(){};
protected function horas(){};
protected function puntos(){};
protected function resultado(){};
protected function cashin(){};

}

?>