<?php

/**
 * DadosBanco_model
 * 
 * Classe que realiza consultas gen�ricas no banco
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class DadosBanco_model extends CI_Model{
	
	/**
	 * DadosBanco_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * DadosBanco_model::status()
    * 
    * Fun��o que pega o status
    * @return Retorna os status existentes
    */
	public function status(){
		return $this->db->get('status')->result();
	}
    
    /**
    * DadosBanco_model::menu()
    * 
    * Fun��o que pega os dados do menu
    * @return Retorna o menu
    */
	public function menu($permitidos = false){
	   
       if($permitidos){
            $this->db->where("cd_permissao IN (".implode(',',$permitidos).")");
        }
       
        $this->db->where("status_menu =  'A'");
        $this->db->order_by("ordem_menu", "asc"); 
		return $this->db->get('menu')->result();
	}
    
    /**
    * DadosBanco_model::paisMenu()
    * 
    * Fun��o que pega o id dos menus pai
    * @return Retorna todos os dados do menu
    */
	public function paisMenu($permitidos = false){
	
        if($permitidos){
            $this->db->where("cd_permissao IN (".implode(',',$permitidos).")");
        }
    
        $this->db->distinct();
        $this->db->select('pai_menu');
		$this->db->where('pai_menu <> 0');
        $this->db->where("status_menu =  'A'");
		$paisMenu = $this->db->get('menu')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $paisMenu;
	}
    
    /**
    * DadosBanco_model::permissoes()
    * 
    * Fun��o que pega os dados das permiss�es
    * @return Retorna as permiss�es
    */
	public function permissoes(){
	   
        $this->db->where("status_permissao =  'A'");
        $this->db->order_by("nome_permissao", "asc"); 
        #$this->db->order_by("ordem_permissao", "asc"); 
		return $this->db->get('permissao')->result();
	}
    
    /**
    * DadosBanco_model::paiPermissao()
    * 
    * Fun��o que pega o id das permiss�es pai
    * @return Retorna todos os dados das permiss�es
    */
	public function paiPermissao(){
	
        $this->db->distinct();
        $this->db->select('pai_permissao');
		$this->db->where('pai_permissao <> 0');
        $this->db->where("status_permissao =  'A'");
		$paisPemissao = $this->db->get('permissao')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $paisPemissao;
	}
    
    /**
    * DadosBanco_model::departamento()
    * 
    * Fun��o que pega os departamento
    * @return Retorna os departamentos ativos
    */
	public function departamento(){
	   
        $this->db->where("status_departamento =  'A'");
        $this->db->order_by("nome_departamento", "asc"); 
        
		return $this->db->get('departamento')->result();
        
	}
    
    /**
    * DadosBanco_model::estado()
    * 
    * Fun��o que pega as estados
    * @return Retorna as estados ativos
    */
	public function estado(){
	   
        #$this->db->where("status_cidade =  'A'");
        $this->db->order_by("nome_estado", "asc"); 
		return $this->db->get('estado')->result();
	}
    
    /**
    * DadosBanco_model::parametro()
    * 
    * Fun��o que pega os par�metros
    * @return Retorna os par�metros
    */
	public function parametro(){
	   
        $this->db->where("status_parametro =  'A'");
        #$this->db->order_by("nome_departamento", "asc"); 
        
		return $this->db->get('parametro')->result();
        
	}
    
    /**
    * DadosBanco_model::unidade()
    * 
    * Fun��o que pega as unidades
    * @return Retorna as unidades
    */
	public function unidade(){
	   
        $this->db->where("status =  'A'");
        #$this->db->order_by("nome_departamento", "asc"); 
        
		return $this->db->get('adminti.unidade')->result();
        
	}

}