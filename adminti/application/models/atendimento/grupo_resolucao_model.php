<?php

/**
 * Dashboard_model
 * 
 * Classe que realiza o tratamento do grupo resolu��o
 * 
 * @package   
 * @author Tiago Silva Costa
 * @version 2014
 * @access public
 */
class Grupo_resolucao_model extends CI_Model{
	
	/**
	 * Grupo_resolucao_model::__construct()
	 * 
	 * @return
	 */
	function __construct(){
		parent::__construct();
	}
    
    /**
    * Grupo_resolucao_model::insere()
    * 
    * Fun��o que realiza a inser��o dos dados do grupo resolu��o na base de dados
    * @return O n�mero de linhas afetadas pela opera��o
    */
	public function insere(){
		
		$campo = array();
		$valor = array();
        
        #$campo[] = 'criador_grupo_resolucao';
        #$valor[] = $this->session->userdata('cd');
		foreach($_POST as $c => $v){
			
            if($c <> 'cd_grupo_resolucao'){
            
    			$valorFormatado = $this->util->removeAcentos($this->input->post($c));
    			$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
    			
    			$campo[] = $c;
    			$valor[] = $valorFormatado;
            
            }
            
		}
        
        # A senha in�cial fica definida com o CPF
        #$campo[] = 'senha_grupo_resolucao';
		#$valor[] = $this->util->formaValorBanco(md5(str_replace('-', '', str_replace('.', '',$this->input->post('cpf_funcionario')))));
		
		$campos = implode(', ', $campo);
		$valores = implode(', ', $valor);
		
        $this->db->trans_begin();
        
		$sql = "INSERT INTO atendimento.grupo_resolucao (".$campos.")\n VALUES(".$valores.");";
		$this->db->query($sql);
        $cd = $this->db->insert_id();
        
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
    * Grupo_resolucao_model::atualiza()
    * 
    * Fun��o que realiza a atualiza��o dos dados do grupo resolu��o na base de dados
    * @return O n�mero de linhas afetadas pela opera��o
    */
	public function atualiza(){
        
        #$campoValor[] = 'atualizador_grupo_resolucao = '.$this->session->userdata('cd');
        #$campoValor[] = "data_atualizacao_grupo_resolucao = '".date('Y-m-d h:i:s')."'";
        
		foreach($_POST as $c => $v){
			
			if($c != 'cd_grupo_resolucao'){
				$valorFormatado = $this->util->removeAcentos($this->input->post($c));
				$valorFormatado = strtoupper($this->util->formaValorBanco($valorFormatado));
			
				$campoValor[] = $c.' = '.$valorFormatado;
			
			}
		}
		
		$camposValores = implode(', ', $campoValor);
		
        $this->db->trans_begin();
        
		$sql = "UPDATE atendimento.grupo_resolucao SET ".$camposValores." WHERE cd_grupo_resolucao = ".$this->input->post('cd_grupo_resolucao').";";
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
        
		return $this->db->query($sql); # RETORNA O N�MERO DE LINHAS AFETADAS
		
	}
	
    /**
    * Grupo_resolucao_model::dadosGrupo_resolucao()
    * 
    * Fun��o que monta um array com todos os dados do grupo resolu��o
    * @param $cd Cd do grupo resolu��o para recupera��o de dados
    * @return Retorna todos os dados do grupo resolu��o
    */
	public function dadosGrupo_resolucao($cd){
	
		$this->db->where('cd_grupo_resolucao', $cd);
		$grupo_resolucao = $this->db->get('atendimento.grupo_resolucao')->result_array(); # TRANSFORMA O RESULTADO EM ARRAY
		
		return $grupo_resolucao[0];
	}
	
    /**
    * Grupo_resolucao_model::camposGrupo_resolucao()
    * 
    * Fun��o que pega os nomes de todos os campos existentes na tabela grupo resolu��o
    * @return Os campos da tabela grupo resolu��o
    */
	public function camposGrupo_resolucao(){
		
		$campos = $this->db->get('atendimento.grupo_resolucao')->list_fields();
		
		return $campos;
		
	}
    
    /**
     * Grupo_resolucao_model::psqGrupo_resolucaos()
     * 
     * lista os grupo resolu��os existentes de acordo com os par�metros informados
     * @param $nome do grupo resolu��o que se deseja encontrar
     * @param $pagina P�gina da pagina��o
     * @param $mostra_por_pagina P�gina corrente da pagina��o
     * 
     * @return A lista dos grupo resolu��os
     */
    public function psqGrupo_resolucaos($nome = null, $status = null, $pagina = null, $mostra_por_pagina = null){
        
        $this->db->select("
                            cd_grupo_resolucao,
                            nome_grupo_resolucao,
                            CASE WHEN status_grupo_resolucao = 'A'
                                THEN 'Ativo'
                            ELSE 'Inativo' END AS status_grupo_resolucao
                            ");       
        
        
        if($nome != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "nome_grupo_resolucao LIKE '%".strtoupper($nome)."%' OR email_grupo_resolucao LIKE '%".strtoupper($nome)."%'";
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status_grupo_resolucao = '".$status."'";
            $this->db->where($condicao);
        }
 
        $this->db->order_by("nome_grupo_resolucao", "asc");  
        
        return $this->db->get('atendimento.grupo_resolucao', $mostra_por_pagina, $pagina)->result();
    }
    
    /**
     * Grupo_resolucao_model::psqQtdGrupo_resolucaos()
     * 
     * Consulta a quantidade de grupo resolu��os da pesquisa
     * 
     * @param $nome Nome do grupo resolu��o para filtrar a consulta
     * 
     * @param $status Status do grupo resolu��o para filtrar a consulta
     * 
     * @return Retorna a quantidade
     */
    public function psqQtdGrupo_resolucaos($nome = null, $status = null){
        
        if($nome != '0'){
            $condicao = "nome_grupo_resolucao LIKE '%".strtoupper($nome)."%'";
            $this->db->where($condicao);
        }
        
        if($status != '0'){
            #$this->db->like('nome_perfil', $nome); 
            $condicao = "status_grupo_resolucao = '".$status."'";
            $this->db->where($condicao);
        }
        
        $this->db->select('count(*) as total');
        return $this->db->get('atendimento.grupo_resolucao')->result();
    }
    
    /**
     * Grupo_resolucao_model::deleteGrupo_resolucao()
     * 
     * Apaga o grupo resolu��o
     * 
     * @return Retorna o n�mero de linhas afetadas
     */
    public function deleteGrupo_resolucao(){
        
        $sql = "DELETE FROM atendimento.grupo_resolucao WHERE cd_grupo_resolucao = ".$this->input->post('apg_cd_grupo_resolucao');
        $this->db->query($sql);
        return $this->db->affected_rows();
        
    }
    
    /**
     * Grupo_resolucao_model::associaMotivosAoGrupo()
     * 
     * Associa motivos ao(s) grupo(s)
     * 
     * @return 
     */
    public function associaMotivosAoGrupo($grupoMotivos, $tipo){
        
        $this->db->trans_begin();
        
        if($tipo == 'motivo'){
        
            $sql = "DELETE FROM atendimento.grupo_resolucao_motivo WHERE cd_grupo_resolucao = ".$this->input->post('cd_grupo_resolucao');

            $this->db->query($sql);
            
            foreach($grupoMotivos as $gru){
                
                $sql = "INSERT INTO atendimento.grupo_resolucao_motivo (cd_grupo_resolucao, cd_motivo) VALUES(".$this->input->post('cd_grupo_resolucao').", ".$gru.");";
              
                $this->db->query($sql);
                
            }
        
        }elseif($tipo == 'grupo'){
            
            $sql = "DELETE FROM atendimento.grupo_resolucao_motivo WHERE cd_motivo = ".$this->input->post('cd_motivo');
            $this->db->query($sql);
            
            foreach($grupoMotivos as $gru){
                
                $sql = "INSERT INTO atendimento.grupo_resolucao_motivo (cd_grupo_resolucao, cd_motivo) VALUES(".$gru.", ".$this->input->post('cd_motivo').");";
              
                $this->db->query($sql);
                
            }
            
        }
        
        if($this->db->trans_status() === TRUE){
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }
        
    }
    
    /**
     * Grupo_resolucao_model::motivosDoGrupo()
     * 
     * Pega todos os motivos associados ao grupo
     * 
     * @return Os motivos associados ao grupo
     */
    public function motivosDoGrupo($cd_grupo){
        
        $this->db->select('motivo.cd_motivo, nome_motivo');
        $this->db->where('cd_grupo_resolucao', $cd_grupo);
        $this->db->join('atendimento.grupo_resolucao_motivo', 'grupo_resolucao_motivo.cd_motivo = motivo.cd_motivo'); 
        $this->db->order_by("nome_motivo", "asc");  
        return $this->db->get('atendimento.motivo')->result();
        
    }
    
    /**
     * Grupo_resolucao_model::motivosDoGrupo()
     * 
     * Pega os grupos que possuem o motivo
     * 
     * @return Os grupos que possuem o motivo
     */
     public function gruposPossuiMotivo($cd_motivo){
        
        $this->db->distinct();
        $this->db->select('grupo_resolucao.cd_grupo_resolucao, nome_grupo_resolucao');
        $this->db->where('cd_motivo', $cd_motivo);
        $this->db->where('status_grupo_resolucao', 'A');
        $this->db->join('atendimento.grupo_resolucao_motivo', 'grupo_resolucao_motivo.cd_grupo_resolucao = grupo_resolucao.cd_grupo_resolucao'); 
        $this->db->order_by("nome_grupo_resolucao", "asc");  
        return $this->db->get('atendimento.grupo_resolucao')->result();
        
     }
     
     /**
     * Grupo_resolucao_model::todosGruposDisponiveis()
     * 
     * Pega todos os grupos que n�o est�o associados a nenhum motivo
     * 
     * @return Os grupos que possuem o motivo
     */
     public function todosGruposDisponiveis($cd_motivo = false){
        
        $this->db->distinct();
        $this->db->select('cd_grupo_resolucao, nome_grupo_resolucao');
        
        if($cd_motivo){
            $this->db->where('cd_grupo_resolucao NOT IN (SELECT DISTINCT sec.cd_grupo_resolucao FROM atendimento.grupo_resolucao_motivo AS sec WHERE cd_motivo = '.$cd_motivo.')');
        }
        
        $this->db->where('status_grupo_resolucao', 'A');
        return $this->db->get('atendimento.grupo_resolucao')->result();
        
     }
     
     public function todosGrupos($cd_grupo = false){
        
        $this->db->distinct();
        $this->db->select('cd_grupo_resolucao, nome_grupo_resolucao');
        
        if($cd_grupo){
            $this->db->where('cd_grupo_resolucao NOT IN ('.$cd_grupo.')');
        }
        
        $this->db->where('status_grupo_resolucao', 'A');
        return $this->db->get('atendimento.grupo_resolucao')->result();
        
     }
     
     public function gruposDoUsuario($cd_usuario = false){
        
        if($cd_usuario){
            $this->db->where('cd_usuario', $cd_usuario);
        }
        
        $this->db->distinct();
        $this->db->select('grupo_resolucao.cd_grupo_resolucao,nome_grupo_resolucao');
        $this->db->join('atendimento.grupo_resolucao', 'grupo_resolucao.cd_grupo_resolucao = grupo_resolucao_usuario.cd_grupo_resolucao'); 
        return $this->db->get('atendimento.grupo_resolucao_usuario')->result();
        
     }
     
     /**
     * Grupo_resolucao_model::associaGruposAoUsuario()
     * 
     * Associa grupo(s) ao usu�rio(s)
     * 
     * @return 
     */
    public function associaGruposAoUsuario($grupos){
        
        $this->db->trans_begin();

        $sql = "DELETE FROM atendimento.grupo_resolucao_usuario WHERE cd_usuario = ".$this->input->post('cd_usuario');

        $this->db->query($sql);
        
        foreach($grupos as $gru){
            
            $sql = "INSERT INTO atendimento.grupo_resolucao_usuario (cd_usuario, cd_grupo_resolucao) VALUES(".$this->input->post('cd_usuario').", ".$gru.");";
          
            $this->db->query($sql);
            
        }

        if($this->db->trans_status() === TRUE){
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }
        
    }
    
    /**
     * Grupo_resolucao_model::associaUsuariosAoGrupo()
     * 
     * Associa usu�rio(s) ao grupo
     * 
     * @return 
     */
    public function associaUsuariosAoGrupo($usuarios){
        
        $this->db->trans_begin();

        $sql = "DELETE FROM atendimento.grupo_resolucao_usuario WHERE cd_grupo_resolucao = ".$this->input->post('cd_grupo_resolucao');

        $this->db->query($sql);
        
        foreach($usuarios as $usu){
            
            $sql = "INSERT INTO atendimento.grupo_resolucao_usuario (cd_usuario, cd_grupo_resolucao) VALUES(".$usu.", ".$this->input->post('cd_grupo_resolucao').");";
          
            $this->db->query($sql);
            
        }

        if($this->db->trans_status() === TRUE){
            $this->db->trans_commit();
        }else{
            $this->db->trans_rollback();
        }
        
    }
    
    /**
     * Grupo_resolucao_model::grupoEnviarEmail()
     * 
     * Pega o grupo de usu�rios para disparar e-mails
     * 
     * @return 
     */
    public function grupoEnviarEmail(){
        
        $this->db->distinct();
        $this->db->select('nome_usuario, email_usuario, nome_motivo, prazo_motivo');
        $this->db->join('atendimento.motivo', 'motivo.cd_motivo = grupo_resolucao_motivo.cd_motivo'); 
        $this->db->join('atendimento.grupo_resolucao_usuario', 'grupo_resolucao_motivo.cd_grupo_resolucao = grupo_resolucao_usuario.cd_grupo_resolucao');
        $this->db->join('adminti.usuario', 'grupo_resolucao_usuario.cd_usuario = adminti.usuario.cd_usuario');
        $this->db->where('motivo.cd_motivo', $this->input->post('cd_motivo'));
        return $this->db->get('atendimento.grupo_resolucao_motivo')->result();
        
    }
    
    /**
     * Grupo_resolucao_model::usuariosDisponiveis()
     * 
     * Usu�rios dispon�veis para serem adicionado no grupo
     * 
     * @return 
     */
    public function usuariosDisponiveis(){
        
        $sql = 'SELECT 
                	DISTINCT
                	usuario.cd_usuario,
                	nome_usuario
                FROM adminti.usuario 
                WHERE cd_departamento = '.$this->input->post('cd_departamento').'
                AND usuario.cd_usuario NOT IN(
                	SELECT cd_usuario FROM atendimento.grupo_resolucao_usuario WHERE cd_grupo_resolucao = '.$this->input->post('cd_grupo_resolucao').'
                ) ORDER BY nome_usuario';
        
        return $this->db->query($sql)->result();  
        
    }
    
    /**
     * Grupo_resolucao_model::usuariosDoGrupo()
     * 
     * Usu�rios associados ao grupo
     * 
     * @return 
     */
    public function usuariosDoGrupo($cd){
        
        $sql = 'SELECT 
                	DISTINCT
                	usuario.cd_usuario,
                	nome_usuario
                FROM adminti.usuario 
                INNER JOIN atendimento.grupo_resolucao_usuario ON usuario.cd_usuario = grupo_resolucao_usuario.cd_usuario
                WHERE cd_grupo_resolucao = '.$cd.'
                ORDER BY nome_usuario';

        
        return $this->db->query($sql)->result();  
        
    }

}