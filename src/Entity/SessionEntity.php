<?php
Namespace Solcre\PokerApp\Entity;

use Solcre\PokerApp\Exception\UserAlreadyAddedException;
use Solcre\PokerApp\Exception\SessionFullException;
use Solcre\PokerApp\Exception\PlayerNotFoundException;
use Solcre\PokerApp\Exception\InsufficientBuyinException;
use Solcre\PokerApp\Exception\ComissionAlreadyAddedException;
use Solcre\PokerApp\Exception\PropinaDealerAlreadyAddedException;
use Solcre\PokerApp\Exception\PropinaServicioAlreadyAddedException;

class SessionEntity {

	protected $idSesion;
	protected $fecha;
	protected $titulo;
	protected $descripcion;
	protected $foto;
	protected $lugares;
	protected $lugaresEspera;
	protected $reservaEspera;
	protected $horaInicio;
	protected $horaInicioReal;
	protected $horaFin;
	protected $comision;
	
	protected $sesionPropinasDealer = array();
	protected $sesionPropinasServicio = array();

	/* punto 6 de las correcciones, las siguientes variables deben ser cada una un array con objetos del tipo SesionBuyIn, SesionComision y SesionUsuario, respectivamente. HECHO. */

	protected $sesionUsuarios = array();
	protected $sesionComisiones = array();
	protected $sesionBuyins = array();


	public function __construct($idSesion=null, $fecha=null, $titulo="", $descripcion="", $foto=null, $lugares=null, $lugaresEspera=null, $reservaEspera=null, $horaInicio=null, $horaInicioReal=null, $horaFin=null){
		$this->setIdSesion($idSesion);
		$this->setFecha($fecha);
		$this->setTitulo($titulo);
		$this->setDescripcion($descripcion);
		$this->setFoto($foto);
		$this->setLugares($lugares);
		$this->setLugaresEspera($lugaresEspera);
		$this->setReservaEspera($reservaEspera);
		$this->setHoraInicio($horaInicio);
		$this->setHoraInicioReal($horaInicioReal);
		$this->setHoraFin($horaFin);
	

	
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


	public function setFecha($fecha){
		$this->fecha=$fecha;
		return $this;
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
		return count($this->sesionUsuarios);
	}

	public function getLugaresEspera(){
		return $this->lugaresEspera;
	}

	public function setLugaresEspera($lugaresEspera){
		$this->lugaresEspera=$lugaresEspera;
	return $this;
	}

	public function getReservaEspera(){
		return $this->reservaEspera;
	}

	public function setReservaEspera($ReservaEspera){
		$this->reservaEspera=$ReservaEspera;
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

	public function getPropinasDealer(){
		return $this->sesionPropinasDealer;
	}

	public function setPropinasDealer($propinaDealer){
		$this->sesionPropinasDealer=$propinaDealer;
		return $this;
	}

	public function getPropinasServicio(){
		return $this->sesionPropinasServicio;
	}

	public function setPropinasServicio($propinaServicio){
		$this->sesionPropinasServicio=$propinaServicio;
		return $this;
	}

    public function getUsers(){
		return $this->sesionUsuarios;
	}
    public function getBuyins(){
		return $this->sesionBuyins;
	}
	public function getComissions(){
		return $this->sesionComisiones;
	}

/*--------------------------------------------------------------------------------------------------------------*/

	//11- En la clase Sesión tenes que agregar un método que sea getTotalCashout() que lo haga es iterar en todos los jugadores que jugaron la sesión y devolver la suma total del cashout.
	public function getTotalCashout(){
		$cashout = 0;
		foreach ($this->sesionUsuarios as $usuario) {
			/** @var UserSession $usuario */
			$cashout +=  $usuario->getCashout(); 
		}
		return $cashout;
	}

// --------------------------------------------------------------------------------------------------

	public function getPropinaDealerTotal (){
		$propinaDealerTotal = 0;
		foreach ($this->sesionPropinasDealer as $propinaHora) {
			$propinaDealerTotal += $propinaHora->getPropinaDealer();  //getComision() aparece en lista pero estaria usando el de esta clase no el de UserSesion !!!
		}
		return $propinaDealerTotal;
	}  


	protected function getPropinaDealerIds() {
		return array_map (function(PropinaDealerSession $propina) {
			return $propina->getId();

		}, $this->sesionPropinasDealer);
	}


	protected function isAddedPropinaDealer(PropinaDealerSession $propina){
		$idsPropinasDealer = $this->getPropinaDealerIds();
		return in_array($propina->getId(), $idsPropinasDealer);
	}

	public function agregarPropinasDealer(array $propinas){
		foreach ($propinas as $propina) {
			$this->agregarPropinaDealer($propina);
		}
	}

	public function agregarPropinaDealer(PropinaDealerSession $propina) {
		if ($this->isAddedPropinaDealer($propina)) {
			throw new PropinaDealerAlreadyAddedException();	
		} elseif (isset($this->sesionPropinasDealer[$propina->getHora()]) && $this->sesionPropinasDealer[$propina->getHora()] instanceof PropinaDealerSession) {
			throw new PropinaDealerAlreadyAddedException();
		}
		$this->sesionPropinasDealer[$propina->getHora()] = $propina;
	}

//------------------------------------------------------------------------------------------------------

	public function getPropinaServicioTotal (){
		$propinaServicioTotal = 0;
		foreach ($this->sesionPropinasServicio as $propinaHora) {
			$propinaServicioTotal += $propinaHora->getPropinaServicio();  //getComision() aparece en lista pero estaria usando el de esta clase no el de UserSesion !!!
		}
		return $propinaServicioTotal;
	}  


	protected function getPropinaServicioIds() {
		return array_map (function(PropinaServicioSession $propina) {
			return $propina->getId();

		}, $this->sesionPropinasServicio);
	}


	protected function isAddedPropinaServicio(PropinaServicioSession $propina){
		$idsPropinasServicio = $this->getPropinaServicioIds();
		return in_array($propina->getId(), $idsPropinasServicio);
	}

	public function agregarPropinasServicio(array $propinas){
		foreach ($propinas as $propina) {
			$this->agregarPropinaServicio($propina);
		}
	}

	public function agregarPropinaServicio(PropinaServicioSession $propina) {
		if ($this->isAddedPropinaServicio($propina)) {
			throw new PropinaServicioAlreadyAddedException();	
		} elseif (isset($this->sesionPropinasServicio[$propina->getHora()]) && $this->sesionPropinasServicio[$propina->getHora()] instanceof PropinaServicioSession) {
			throw new PropinaServicioAlreadyAddedException();
		}
		$this->sesionPropinasServicio[$propina->getHora()] = $propina;
	}




//------------------------------------------------------------------------------------------------------
	public function getComisionTotal (){
		$comisionTotal = 0;
		foreach ($this->sesionComisiones as $comisionHora) {
			$comisionTotal += $comisionHora->getComision();  //getComision() aparece en lista pero estaria usando el de esta clase no el de UserSesion !!!
		}
		return $comisionTotal;
	}

	protected function getComissionIds() {
		return array_map (function(ComisionSession $comision) {
			return $comision->getId();

		}, $this->sesionComisiones);
	}


	protected function isAddedComission(ComisionSession $comision){
		$idsComisiones = $this->getComissionIds();
		return in_array($comision->getId(), $idsComisiones);
	}

	public function agregarComisiones(array $comisiones){
		foreach ($comisiones as $comision) {
			$this->agregarComision($comision);
		}
	}

	protected function agregarComision(ComisionSession $comision) {
		if ($this->isAddedComission($comision)) {
			throw new ComissionAlreadyAddedException();	
		} elseif (isset($this->sesionComisiones[$comision->getHora()]) && $this->sesionComisiones[$comision->getHora()] instanceof ComisionSession) {
			throw new ComissionAlreadyAddedException();
		}
		$this->sesionComisiones[$comision->getHora()] = $comision;
	}

	private function getUserIds() {
		return array_map(function (UserSession $usuario) {  
		    		return $usuario->getId();
		}, $this->sesionUsuarios);
	}

	protected function isAdded(UserSession $usuario) {
		$idsUsuarios = $this->getUserIds();
		return in_array($usuario->getId(), $idsUsuarios);

	}
		
	public function agregarUsuario(UserSession $usuario) {
		if (($this->getLugares() - $this->getConfirmados()) == 0) {
			throw new SessionFullException();
		} elseif ($this->isAdded($usuario, $this->sesionUsuarios)) {
			throw new UserAlreadyAddedException();
		}  
		$this->sesionUsuarios[] = $usuario;
	}

	public function agregarUsuarios(array $usuarios) {
	//hacer un bloque foreach que para cada item del array llame a agregarUsuario
		foreach ($usuarios as $usuario) {
			$this->agregarUsuario($usuario);
		}
	}


	public function isPlayer($id){
		return in_array($id, $this->getUserIds());
	}


	public function agregarBuyin(BuyinSession $buyin){
		if (!$this->isPlayer($buyin->getIdJugador())) {
			throw new PlayerNotFoundException();
		}
		elseif (($buyin->getMontoCash() + $buyin->getMontoCredito()) < 100) {
			throw new InsufficientBuyinException();
		}
		$this->sesionBuyins[] = $buyin;
	}

	public function agregarBuyins(array $buyins){
		foreach ($buyins as $buyin){
			$this->agregarBuyin($buyin);
		}
	}





	/* 10- En la clase Sesión tenes que agregar un método getTotalJugado() que lo que haga es iterar en los buyins y devolver la suma de todos los buyins para saber cuanto se jugó en total. */

	public function getTotalJugado(){
	/* esta funcion recibe el buyin de los jugadores de la sesion y devuelve el total jugado */
		$montoTotal = 0;
		foreach ($this->sesionBuyins as $buyin) {
			$montoTotal += $buyin->getMontoCash() + $buyin->getMontoCredito();
		}
		return $montoTotal;
	}



	public function validarSesion($sesion){
		If ($sesion->getTotalJugado() == $sesion->getTotalCashout() + $sesion->getComisionTotal() + $sesion->getPropinaDealerTotal()+ $sesion-> getPropinaServicioTotal()){
			return true;
		} else {
			return false;
		}

	}
}
?>