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

	/* punto 6 de las correcciones, las siguientes variables deben ser cada una un array con objetos del tipo SesionBuyIn, SesionComision y SesionUsuario, respectivamente. */

	protected $usuarios = array('Id' => , 'IdSesion' =>, 'IdUsuario' =>, 'Aprobado' =>, 'PuntosAcumulados' =>, 'Cashin' =>, 'Cashout' =>, 'inicio' =>, 'fin' =>,  );

	protected $comisiones = array('Id' => , 'IdSesion' => 'hora' => 'comision' => );

	protected $buyins = array'Id' => , 'IdSesion' =>, 'IdJugador' =>, 'MontoCash' =>, 'MontoCredito' =>, 'Moneda' =>, 'Hora' =>, 'Aprobado' => );

/* duda de si puedo darle valor al array usando una funcion ej array (IdSesion' => getIdSesion($sesion)) */

	/* las siguientes no sé si deberían ser variables o funciones 

	protected $comision;
	protected $propinaDealer;
	protected $propinaServicio;
*/

}

public function __contruct($idSesion=null, $fecha=null, $titulo="", $descripcion="", $foto=null, $lugares=null, $confirmados=null, $lugaresEspera=null, $reservaEspera=null, $horaInicio=null, $horaInicioReal=null, $horaFin=null){
	$this->idSesion=$idSesion;
	$this->fecha=$fecha;
	$this->titulo=$titulo;
	$this->descripcion=$descripcion;
	$this->foto=$foto;
	$this->confirmados=$confirmados;
	$this->lugaresEspera=$lugaresEspera
	$this->reservaEspera=$reservaEspera;
	$this->horaInicio=$horaInicio;
	$this->horaInicioReal=$horaInicioReal;
	$this->horaFin=$horaFin;
}

public function setIdSesion($idSesion){
	$this->idSesion=$idSesion;
	return $this;
}

public function getIdSesion(){
	return $this->IdSesion;
}

public function setFecha($fecha){
	$this->fecha=$fecha;
	return $this;
}

public function getFecha(){
	return $this->fecha;
}

public function setTitulo($titulo){
	$this->titulo=$titulo;
	return $this;
}

public function getTitulo(){
	return $this->titulo;
}

public function setDescripcion($descripcion){
	$this->descripcion=$descripcion;
	return $this;
}

public function getDescripcion(){
	return $this->descripcion;
}


public function setFoto($foto){
	$this->foto=$foto;
	return $this;
}

public function getFoto(){
	return $this->foto;
}

public function setLugares($lugares){
	$this->lugares=$lugares;
	return $this;
}

public function getLugares(){
	return $this->lugares;
}


public function setConfirmados($confirmados){
	$this->confirmados=$confirmados;
	return $this;
}

public function getConfirmados(){
	return $this->confirmados;
}

public function setLugaresEspera($lugaresEspera){
	$this->lugaresEspera=$lugaresEspera;
	return $this;
}

public function getLugaresEspera(){
	return $this->lugaresEspera;
}

public function setReservaEspera($ReservaEspera){
	$this->ReservaEspera=$ReservaEspera;
	return $this;
}

public function getReservaEspera(){
	return $this->ReservaEspera;
}

public function setHoraInicio($horaInicio){
	$this->horaInicio=$horaInicio;
	return $this;
}

public function getHoraInicio(){
	return $this->horaInicio;
}

public function setHoraInicioReal($horaInicioReal){
	$this->horaInicioReal=$horaInicioReal;
	return $this;
}

public function getHoraInicioReal(){
	return $this->horaInicioReal;
}

public function setHoraFin($horaFin){
	$this->horaFin=$horaFin;
	return $this;
}

public function getHoraFin(){
	return $this->horaFin;
}

/* punto 7 de correcciones */
public function getResultado($cashin, $cashout){
$resultado = $cashin - $cashout;
return $resultado;
}

public function getHorasJugadas($horaInicio, $horaFin){
/* $HorasJugadas = ;   falta implementación */
return $horasJugadas;
}


/* 9- En la clase Sesion tenés que tener un método calcularComision() que lo que haga es iterar en la colección de SesionComision e ir sumando lo que se comisonó hora por hora y devolver el total.  */

public function calcularComision($sesionComision){
/* dudas en que es la coleccion SesionComision */
}

/* 10- En la clase Sesión tenes que agregar un método getTotalJugado() que lo que haga es iterar en los buyins y devolver la suma de todos los buyins para saber cuanto se jugó en total. */ 

public function getTotalJugado($buyins){
/* duda con implemetación, iterar en los buyins? de distintos arreglos? */
}


/* 11- En la clase Sesión tenes que agregar un método que sea getTotalCashout() que lo haga es iterar en todos los jugadores que jugaron la sesión y devolver la suma total del cashout. */

public function getTotalCashout(){
/* implementarla */
}

/* 12- En la clase Sesión tenés que agregar un método que sea validarSesion() que lo que haga es una serie de comprobaciones y devolver true si la sesión es válida o false si la sesión tiene errores.  

getTotalJugado() = getTotalCashout() + calcularComision()  + getPropinaDealer()  + getPropinaServicio()

Si esa ecuación se cumple, no diferencias en la venta de fichas, si no se cumple validarSesion tiene que dar error para nosotros saber que el Rodri le dio fichas de más o de menos a alguien o se mezcló comisión o propina con fichas que no estaban en juego o e perdieron fichas. */

public function getTotalJugado($sesion){
Foreach ($usuario->cashin as $cashin) 
/* implementarla */
}

public function validarSesion($sesion){
If (getTotalJugado() = getTotalCashout() + calcularComision()  + getPropinaDealer()  + getPropinaServicio())

}

?>