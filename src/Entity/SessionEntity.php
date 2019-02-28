<?php
Namespace Solcre\PokerApp\Sesion;

class Sesion {
	
	protected $idSesion;
	protected $fecha;
	protected $titulo;
	protected $descripcion;
	protected $foto;
	protected $lugares;
	protected $confirmados;
	protected $lugaresEspera;
	protected $reservaEspera;
	protected $horaInicio;
	protected $horaInicioReal;
	protected $horaFin;
	protected $comision;
	protected $propinaDealer;
	protected $propinaServicio;

	/* punto 6 de las correcciones, las siguientes variables deben ser cada una un array con objetos del tipo SesionBuyIn, SesionComision y SesionUsuario, respectivamente. HECHO. */

	protected $usuarios = array('Id', 'IdSesion', 'IdUsuario', 'Aprobado', 'PuntosAcumulados', 'Cashin', 'Cashout', 'inicio', 'fin');

	protected $comisiones = array('Id', 'IdSesion', 'hora', 'comision');

	protected $buyins = array('Id', 'IdSesion', 'IdJugador', 'MontoCash', 'MontoCredito', 'Moneda', 'Hora', 'Aprobado');
}

public function __contruct($idSesion=null, $fecha=null, $titulo="", $descripcion="", $foto=null, $lugares=null, $confirmados=null, $lugaresEspera=null, $reservaEspera=null, $horaInicio=null, $horaInicioReal=null, $horaFin=null, $comision=0, $propinaDealer=0, $propinaServicio=0){
	$this->idSesion=setIdSesion($idSesion);
	$this->fecha=setFecha($fecha);
	$this->titulo=setTitulo($titulo);
	$this->descripcion=setDescripcion($descripcion);
	$this->foto=setFoto($foto);
	$this->confirmados=setCofirmados($confirmados);
	$this->lugaresEspera=setLugaresEspera($lugaresEspera);
	$this->reservaEspera=setReservaEspera($reservaEspera);
	$this->horaInicio=setHoraInicio($horaInicio);
	$this->horaInicioReal=setHoraInicioReal($horaInicioReal);
	$this->horaFin=setHoraFin($horaFin);
	$this->comision=setComision($comision);
	$this->propinaDealer=setPropinaDealer($propinaDealer);
	$this->propinaServicio=setPropinaServicio($propinaServicio);
}


public function getIdSesion(){
	return $this->IdSesion;
}

public function setIdSesion($idSesion){
	$this->idSesion=$idSesion;
	return $this;
}

public function getFecha(){
	return $this->fecha;
}


public function getIdSesion(){
	return $this->IdSesion;
}

public function getTitulo(){
	return $this->titulo;
}

public function setTitulo($titulo){
	$this->titulo=$titulo;
	return $this;
}

public function getDescripcion(){
	return $this->descripcion;
}

public function setDescripcion($descripcion){
	$this->descripcion=$descripcion;
	return $this;
}

public function getFoto(){
	return $this->foto;
}

public function setFoto($foto){
	$this->foto=$foto;
	return $this;
}

public function getLugares(){
	return $this->lugares;
}

public function setLugares($lugares){
	$this->lugares=$lugares;
	return $this;
}

public function getConfirmados(){
	return $this->confirmados;
}

public function setConfirmados($confirmados){
	$this->confirmados=$confirmados;
	return $this;
}

public function getLugaresEspera(){
	return $this->lugaresEspera;
}

public function setLugaresEspera($lugaresEspera){
	$this->lugaresEspera=$lugaresEspera;
	return $this;
}

public function getReservaEspera(){
	return $this->ReservaEspera;
}

public function setReservaEspera($ReservaEspera){
	$this->ReservaEspera=$ReservaEspera;
	return $this;
}

public function getHoraInicio(){
	return $this->horaInicio;
}

public function setHoraInicio($horaInicio){
	$this->horaInicio=$horaInicio;
	return $this;
}

public function getHoraInicioReal(){
	return $this->horaInicioReal;
}

public function setHoraInicioReal($horaInicioReal){
	$this->horaInicioReal=$horaInicioReal;
	return $this;
}

public function getHoraFin(){
	return $this->horaFin;
}

public function setHoraFin($horaFin){
	$this->horaFin=$horaFin;
	return $this;
}

public function getComision(){
	return $this->comision;
}

public function setComision($comision){
	$this->comision=$comision;
	return $this;
}

public function getPropinaDealer(){
	return $this->propinaDealer;
}

public function setPropinaDealer($propinaDealer){
	$this->propinaDealer=$propinaDealer;
	return $this;
}

public function getPropinaServicio(){
	return $this->propinaServicio;
}

public function setPropinaServicio($propinaServicio){
	$this->$propinaServicio=$propinaServicio;
	return $this;
}


/*--------------------------------------------------------------------------------------------------------------*/

/* punto 7 de correcciones */
protected function getResultado($sesionUsuario){
	/* esta función recibe un array del tipo $usuarios y devuelve el resultado del usuario en esa sesión  */ 
$resultado = $sesionUsuario[6] - $sesionUsuario[5];
return $resultado;
}

protected function getHorasJugadas($sesionUsuario){
	/* esta función recibe un array del tipo $usuarios y devuelve la cantidad de horas jugadas del usuario en esa sesión  */ 
$horasJugadas = dateDiff($sesionUsuario[7], $sesionUsuario[8])
return $horasJugadas;
}

protected function dateDiff($horaInicio, $horaFin){
	/* esta funcion recibe hora de inicio y hora de fin de sesión y devuelve la cantidad de horas jugadas en un formato determinado */
}


/* ---------------------------------------------------------------------------------------------------------- */
/* 9- En la clase Sesion tenés que tener un método calcularComision() que lo que haga es iterar en la colección de SesionComision e ir sumando lo que se comisonó hora por hora y devolver el total.  */

protected function calcularComision($sesionComision){
/* Observacion: en SesionComision los elementos "hora" y "comision" deben ser arrays, pudiendo agregarle elementos mediante array_push en cada nueva hora de la sesion */
}


/* -------------------------------------------------------------------------------------------------------------*/

/* 10- En la clase Sesión tenes que agregar un método getTotalJugado() que lo que haga es iterar en los buyins y devolver la suma de todos los buyins para saber cuanto se jugó en total. */ 

protected function agregarUsuario($usuario){
$usuario = $this->usuarios;
}

protected function getTotalJugado($usuarios){
/* esta funcion recibe el buyin de los jugadores de la sesion y devuelve el total jugado */
$buyins = $this-> getSesion()->getBuyins();
$montoTotal = 0;
foreach ($buyins as $buyin) {
If($buyin->getUsuario()->getId() == $this->getUsuario()->getId()) {
$montoTotal = $montoTotal + $buyin->getMontoTotal();
}

/* DUDAS */ 
}

/* -----------------------------------------------------------------------------------------------------------*/





/* 11- En la clase Sesión tenes que agregar un método que sea getTotalCashout() que lo haga es iterar en todos los jugadores que jugaron la sesión y devolver la suma total del cashout. */

protected function getTotalCashout(){
/* implementarla */
}

/* 12- En la clase Sesión tenés que agregar un método que sea validarSesion() que lo que haga es una serie de comprobaciones y devolver true si la sesión es válida o false si la sesión tiene errores.  

getTotalJugado() = getTotalCashout() + calcularComision()  + getPropinaDealer()  + getPropinaServicio()

Si esa ecuación se cumple, no diferencias en la venta de fichas, si no se cumple validarSesion tiene que dar error para nosotros saber que el Rodri le dio fichas de más o de menos a alguien o se mezcló comisión o propina con fichas que no estaban en juego o e perdieron fichas. */


protected function validarSesion($sesion){
If (getTotalJugado() = getTotalCashout() + calcularComision()  + getPropinaDealer()  + getPropinaServicio())

}

?>