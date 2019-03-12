<?php
Namespace Solcre\PokerApp\Entity;

class ComisionSession{
	protected $id;
	protected $idSesion;
	protected $hora;
	protected $comision;

	public function __construct($id=null, $idSesion=null, $hora=null, $comision=null){
		$this->setId($id);
		$this->setIdSesion($idSesion);
		$this->setHora($hora);
		$this->setComision($comision);
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

	public function getComision(){
		return $this->comision;
	}

	public function setComision($comision){
		$this->comision = $comision;
		return $this;
	}
}

?>