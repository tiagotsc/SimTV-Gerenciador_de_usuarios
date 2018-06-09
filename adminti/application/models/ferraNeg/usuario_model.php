<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do usuário
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class Usuario_model extends CI_Model{
	
	/**
	 * Usuario_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * Usuario_model::insere()
    * 
    * Função que realiza a inserção dos dados do usuário na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        $campo[] = 'criador_usuario';
        $valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_usuario' and $c <> 'cd_perfil' and $c <> 'status_usuario'){
            
    			$valorFormatado = $this->util->removeAcentos($this->input->post($c));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
    			
    			$campo[] = $c;
    			$valor[] = $valorFormatado;
            
            }
            
		}
        
        # A senha inícial fica definida com o CPF
        #$campo[] = 'senha_usuario';
		#$valor[] = $this->util->formaValorBanco(md5(str_replace('-', '', str_replace('.', '',$this->input->post('cpf_funcionario')))));
		
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
		
        $this->db->trans_begin();
        
		$sql = "INSERT INTO adminti.usuario (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
        # Registra Perfil
        $perfil = $this->util->formaValorBanco($this->input->post('cd_perfil'));
        $sql = "INSERT INTO sistema.config_usuario (cd_usuario, cd_perfil, status_config_usuario)\n VALUES(".$cd.",".$perfil.",'".$this->input->post('status_usuario')."');";
        $this->db->query($sql);
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            
            return $cd;
        }
        
	}
	
    /**
    * Usuario_model::atualiza()
    * 
    * Função que realiza a atualização dos dados do usuário na base de dados
    * @return O número de linhas afetadas pela operação
    */
	public function atualiza(){
        
        $campoValor[] = 'atualizador_usuario = '.$this->session->userdata('cd');
        $campoValor[] = "data_atualizacao_usuario = '".date('Y-m-d h:i:s')."'";
        
		foreach($_POST as $c => $v){
			
			if($c != 'cd_usuario' and $c != 'cd_perfil' and $c != 'status_usuario'){
				$valorFormatado = $this->util->removeAcentos($this->input->post($c));
				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE adminti.usuario SET ".$camposValores." WHERE cd_usuario = ".$this->input->post('cd_usuario').";";
		$this->db->query($sql);
        
        # Registra Perfil
        /*$perfil = $this->util->formaValorBanco($this->input->post('cd_perfil'));
        $sql = "UPDATE sistema.config_usuario SET cd_perfil = ".$perfil.", status_config_usuario = '".$this->input->post('status_usuario')."' WHERE cd_usuario = ".$this->input->post('cd_usuario');
        $this->db->query($sql);*/
        
        # Registra Perfil
        $sql = "DELETE FROM sistema.config_usuario WHERE cd_usuario = ".$this->input->post('cd_usuario');
        $this->db->query($sql);
        
        $perfil = $this->util->formaValorBanco($this->input->post('cd_perfil'));
        $sql = "INSERT INTO sistema.config_usuario (cd_usuario, cd_perfil, status_config_usuario)\n VALUES(".$this->input->post('cd_usuario').",".$perfil.",'".$this->input->post('status_usuario')."');";
        $this->db->query($sql);
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
        
		return $this->db->query($sql); # RETORNA O NÚMERO DE LINHAS AFETADAS
		
	}
	
    /**
    * Usuario_model::dadosUsuario()
    * 
    * Função que monta um array com todos os dados do usuário
    * @param $cd Cd do usuário para recuperação de dados
    * @return Retorna todos os dados do usuário
    */
	public function dadosUsuario($cd){
	   
		$this->db->where('usuario.cd_usuario', $cd);
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
		$usuario = $this->db->get('adminti.usuario')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $usuario[0];
	}
	
    /**
    * Usuario_model::camposUsuario()
    * 
    * Função que pega os nomes de todos os campos existentes na tabela usuário
    * @return Os campos da tabela usuário
    */
	public function camposUsuario(){
		
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
		$campos = $this->db->get('adminti.usuario')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Usuario_model::psqUsuarios()
     * 
     * lista os usuários existentes de acordo com os parâmetros informados
     * @param $nome do usuário que se deseja encontrar
     * @param $pagina Página da paginação
     * @param $mostra_por_pagina Página corrente da paginação
     * 
     * @return A lista dos usuários
     */
    public function psqUsuarios($nome = null, $status = null, $pagina = null, $mostra_por_pagina = null){
        
        $this->db->select("
                            usuario.cd_usuario,
                            login_usuario,
                            nome_usuario,
                            email_usuario,
                            CASE WHEN status_config_usuario = 'A'
                                THEN 'Ativo'
                            ELSE 'Inativo' END AS status_usuario,
                            nome_estado,
                            nome_departamento,
                            nome_perfil
                            ");       
        
        
        if($nome != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "nome_usuario LIKE '%".strtoupper($nome)."%' OR email_usuario LIKE '%".strtoupper($nome)."%'";
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status_usuario = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->join('adminti.departamento', 'adminti.departamento.cd_departamento = adminti.usuario.cd_departamento', 'left');      
        $this->db->join('adminti.estado', 'adminti.estado.cd_estado = adminti.usuario.cd_estado', 'left');    
        $this->db->join('sistema.config_usuario', 'usuario.cd_usuario = sistema.config_usuario.cd_usuario', 'left');
        $this->db->join('sistema.perfil', 'sistema.perfil.cd_perfil = sistema.config_usuario.cd_perfil', 'left'); 
        $this->db->order_by("nome_usuario", "asc");  
        
        return $this->db->get('adminti.usuario', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * Usuario_model::psqQtdUsuarios()
     * 
     * Consulta a quantidade de usuários da pesquisa
     * 
     * @param $nome Nome do usuário para filtrar a consulta
     * 
     * @param $status Status do usuário para filtrar a consulta
     * 
     * @return Retorna a quantidade
     */
    public function psqQtdUsuarios($nome = null, $status = null){
        
        if($nome != '0'){
            $condicao = "nome_usuario LIKE '%".strtoupper($nome)."%'";
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status_usuario = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->select('count(*) as total');
        return $this->db->get('adminti.usuario')->result();
    }
    
    /**
     * Usuario_model::deleteUsuario()
     * 
     * Apaga o usuário
     * 
     * @return Retorna o número de linhas afetadas
     */
    public function deleteUsuario(){
        
        $sql = "DELETE FROM adminti.usuario WHERE cd_usuario = ".$this->input->post('apg_cd_usuario');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    /**
     * Usuario_model::autenticaUsuario()
     * 
     * Autentica o usuário
     * 
     * @return Retorna os dados do usuário caso ele exista
     */
    public function autenticaUsuario(){
        
        $this->db->select('cd_usuario, login_usuario, nome_usuario, email_usuario, cd_perfil');
        $this->db->where('login_usuario', $this->input->post('login'));
        return $this->db->get('adminti.usuario')->result();
        
    }

}