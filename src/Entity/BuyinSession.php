<?php
Namespace Solcre\PokerApp\Entity;

class BuyinSession {
	protected $id;
	protected $idSesion;
	protected $idJugador;
	protected $montoCash;
	protected $montoCredito;
	protected $moneda;
	protected $hora;
	protected $aprobado;

	public function __construct($id=null, $idSesion=null, $idJugador=null, $montoCash=null, $montoCredito=null, $moneda=null, $hora=null, $aprobado=null){
		$this->setId($id);
		$this->setIdSesion($idSesion);
		$this->setIdJugador($idJugador);
		$this->setMontoCash($montoCash);
		$this->setMontoCredito($montoCredito);
		$this->setMoneda($moneda);
		$this->setHora($hora);
		$this->setAprobado($aprobado);
	}

	public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getIdSesion(){
		return $this->idSesion;
	}

	public function setIdSesion($idSesion){
		$this->idSesion = $idSesion;
		return $this;
	}

	public function getIdJugador() {
		return $this->idJugador;
	}

	public function setIdJugador($idJugador){
		$this->idJugador = $idJugador;
		return $this;
	}

	public function getMontoCash(){
		return $this->montoCash;
	}

	public function setMontoCash($montoCash){
		$this->montoCash = $montoCash;
		return $this;
	}

	public function getMontoCredito(){
		return $this->montoCredito;
	}

	public function setMontoCredito($montoCredito){
		$this->montoCredito = $montoCredito;
		return $this;
	}

	public function getMoneda(){
		return $this->moneda;
	}

	public function setMoneda($moneda){
		$this->moneda = $moneda;
		return $this;
	}

	public function getHora(){
		return $this->hora;
	}

	public function setHora($hora){
		$this->hora = $hora;
		return $this;
	}

	public function getAprobado(){
		return $this->aprobado;
	}

	public function setAprobado($aprobado){
		$this->aprobado = $aprobado;
		return $this;
	}

}


?>