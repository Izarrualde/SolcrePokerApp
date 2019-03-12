<?php
Namespace Solcre\PokerApp\Entity;

Class UserSession {
	protected $id;
	protected $session;
	protected $idUsuario;
	protected $aprobado;
	protected $puntosAcumulados;
	protected $cashout;
	protected $inicio;
	protected $fin;

	public function __construct($id=null, SessionEntity $session = null, $idUsuario=null, $aprobado=null, $puntosAcumulados=0, $cashout=0, $inicio=null, $fin=null){
		$this->setId($id);
		$this->setSession($session);
		$this->setIdUsuario($idUsuario);
		$this->setAprobado($aprobado);
		$this->setPuntosAcumulados($puntosAcumulados);
		$this->setCashout($cashout);
		$this->setInicio($inicio);
		$this->setFin($fin);
	}

	public function getId() {
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
		return $this;
	}

	public function getSession() {
		return $this->session;
	}
    public function setSession(SessionEntity $session = null) {
		return $this->session = $session;
	}

	public function getIdSesion() {
		if ($this->session instanceof SessionEntity) {
			return $this->session->getId();
		}
		return null;
	}

	public function getIdUsuario() {
		return $this->idUsuario;
	}

	public function setIdUsuario($idUsuario){
		$this->idUsuario = $idUsuario;
		return $this;
	}

	public function getAprobado() {
		return $this->aprobado;
	}

	public function setAprobado($aprobado){
		$this->aprobado = $aprobado;
		return $this;
	}


	public function getPuntosAcumulados() {
		return $this->puntosAcumulados;
	}

	public function setPuntosAcumulados($puntosAcumulados){
		$this->puntosAcumulados = $puntosAcumulados;
		return $this;
	}

	public function getCashin() {
		$cashin = 0;
		$session = $this->getSession();
		if ($session instanceof SessionEntity) {
			foreach($session->getBuyins() as $buyin) {
				if ($buyin->getIdJugador() == $this->getId()) {
					$cashin += $buyin->getMontoCash() + $buyin->getMontoCredito();
				}
			}
		} 
		return $cashin;
	}

	public function getCashout() {
		return $this->cashout;
	}

	public function setCashout($cashout){
		$this->cashout = $cashout;
		return $this;
	}

	public function getInicio() {
		return $this->inicio;
	}

	public function setInicio($inicio){
		$this->inicio = $inicio;
		return $this;
	}

	public function getFin() {
		return $this->fin;
	}

	public function setFin($fin){
		$this->fin = $fin;
		return $this;
	}


	public function getResultado(){
		return $this->getCashout() - $this->getCashin();
	}

	protected function getHorasJugadas(){
		return dateDiff($this->getFin(), $this->getInicio());
	}

	protected function dateDiff($horaInicio, $horaFin){
	/* esta funcion recibe hora de inicio y hora de fin de sesión y devuelve la cantidad de horas jugadas en un formato determinado */
	}



}

?>