<?php
Namespace Solcre\PokerApp\Entity;

class PropinaDealerSession{
	protected $id;
	protected $idSesion;
	protected $hora;
	protected $propinaDealer;

	public function __construct($id=null, $idSesion=null, $hora="", $propina=null){
		$this->setId($id);
		$this->setIdSesion($idSesion);
		$this->setHora($hora);
		$this->setPropinaDealer($propina);
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

	public function getHora(){
		return $this->hora;
	}

	public function setHora($hora){
		$this->hora = $hora;
		return $this;
	}

	public function getPropinaDealer(){
		return $this->propinaDealer;
	}

	public function setPropinaDealer($propina){
		$this->propinaDealer = $propina;
		return $this;
	}
}

?>