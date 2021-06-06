<?php

namespace Api\Ponto;

class Ponto {
    private $id_ponto = 0;
    private $id_usuario = null;
    private $dt_marcacao = null;
    private $tipo_marcacao = null;
    private $marcacao_manual = null;

    public function getId_ponto(){
		return $this->id_ponto;
	}

	public function setId_ponto($id_ponto){
		$this->id_ponto = $id_ponto;
	}

	public function getId_usuario(){
		return $this->id_usuario;
	}

	public function setId_usuario($id_usuario){
		$this->id_usuario = $id_usuario;
	}

	public function getDt_marcacao(){
		return $this->dt_marcacao;
	}

	public function setDt_marcacao($dt_marcacao){
		$this->dt_marcacao = $dt_marcacao;
	}

	public function getTipo_marcacao(){
		return $this->tipo_marcacao;
	}

	public function setTipo_marcacao($tipo_marcacao){
		$this->tipo_marcacao = $tipo_marcacao;
	}

	public function getMarcacao_manual(){
		return $this->marcacao_manual;
	}

	public function setMarcacao_manual($marcacao_manual){
		$this->marcacao_manual = $marcacao_manual;
	}
}