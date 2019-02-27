<?php
Namespace Solcre\PokerApp\Usuario;

class Usuario {
	protected $id;
	protected $usuario;
	protected $clave;
	protected $email;
	protected $apellido;
	protected $multiplicador;
	protected $activo;
	protected $horas;
	protected $puntos;
	protected $resultado;
	protected $cashin;

public function __construct($id=null, $usuario="", $clave="", $email="", $apellido="", $multiplicador=null, $activo=null, $horas=0, $puntos=0, $resultado=0, $cashin=0) {
	$this->id = setId($id);
	$this->usuario = setUsuario($usuario);
	$this->clave = setClave($clave);
	$this->email = setEmail($email);
	$this->apellido = setApellido($apellido);
	$this->multiplicador = setMultiplicador($multiplicador);
	$this->activo = setActivo($activo);
	$this->horas = setHoras($horas);
	$this->puntos = setpuntos($puntos);
	$this->resultado = setResultado($resultado);
	$this->cashin = setCashin($cashin);
}

public function getId() {
	return $this->id;
}

public function setId($id){
	$this->id = $id;
	return $this;
}

public function getUsuario() {
	return $this->usuario;
}

public function setUsuario($usuario){
	$this->usuario = $usuario;
	return $this;
}

public function getClave() {
	return $this->clave;
}

public function setClave($clave){
	$this->clave = $clave;
	return $this;
}

public function getEmail() {
	return $this->email;
}


public function setEmail($email){
	$this->email = $email;
	return $this;
}

public function getApellido() {
	return $this->apellido;
}

public function setApellido($apellido){
	$this->apellido = $apellido;
	return $this;
}

public function getMultiplicador() {
	return $this->multiplicador;
}

public function setMultiplicador($multiplicador){
	$this->multiplicador = $multiplicador;
	return $this;
}

public function getHoras() {
	return $this->horas;
}

public function setHoras($horas){
	$this->horas = $horas;
	return $this;
}

public function getSesiones() {
	return $this->sesiones;
}

public function setSesiones($sesiones){
	$this->sesiones = $sesiones;
	return $this;
}

public function getPuntos() {
	return $this->puntos;
}

public function setPuntos($puntos){
	$this->puntos = $puntos;
	return $this;
}

public function getCashin() {
	return $this->cashin;
}

public function setCashin($cashin){
	$this->cashin = $cashin;
	return $this;
}


}

?>