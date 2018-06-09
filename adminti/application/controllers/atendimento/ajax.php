<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
#date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe criada para controlar todas as buscas sicronas (Sem refresh)
*/
class Ajax extends CI_Controller
{
    
	/**
	 * Ajax::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
		parent::__construct();
        
        #$this->load->helper('url');
		#$this->load->helper('form');
    }
    
    
    /**
	 * Ajax::exibicaoDivAtendimento()
	 * 
     * Alimenta a session que exibe ou não a div de atendimento
     * 
	 */
    public function exibicaoDivAtendimento(){
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        $exibicao = $this->input->post('var_exibicao');

        $this->session->set_userdata('exibicao', $exibicao);

    }
    
    /**
	 * Ajax::atualizaGrupoResolvedores()
	 * 
     * Atualiza os grupos de resolvedores de acordo com os motivos / problemas informados
     * 
	 */
    public function atualizaGrupoResolvedores($tipo){
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        $this->load->model('grupo_resolucao_model','grupo_resolucao');
        
        if($tipo == 'motivo'){ # Condição utilizada na tela de edição de motivo
            
            foreach($_POST['motivos'] as $motivos){
            
                $extracao[] = str_replace('idMotivo_','',$motivos);
                
            }
            
            try{
            
                $this->grupo_resolucao->associaMotivosAoGrupo($extracao, 'motivo');
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            echo '<div class="alert alert-success" role="alert"><strong>Associa&ccedil;&atilde;o do motivo / problema atualizado.</strong></div>';
            
        }elseif($tipo == 'grupo'){ # Condição utilizada na tela de edição de grupos
            
            foreach($_POST['grupos'] as $grupos){
            
                $extracao[] = str_replace('idGrupo_','',$grupos);
                
            }
            
            try{
            
                $this->grupo_resolucao->associaMotivosAoGrupo($extracao, 'grupo');
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            echo '<div class="alert alert-success" role="alert"><strong>Associa&ccedil;&atilde;o do grupo atualizada.</strong></div>';
            
        }
        
    }
    
    /**
	 * Ajax::associacaoGruposUsuario()
	 * 
     * Associa o grupo ao usuário
     * 
	 */
    public function associacaoGruposUsuario(){
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        $this->load->model('atendimento/grupo_resolucao_model','grupo_resolucao');
        
        foreach($_POST['grupos'] as $grupos){
        
            $extracao[] = str_replace('idGrupo_','',$grupos);
            
        }
        
        try{
        
            $this->grupo_resolucao->associaGruposAoUsuario($extracao);
            
        }catch( Exception $e ){
                
            log_message('error', $e->getMessage());
            
        }
        
        echo '<div class="alert alert-success" role="alert"><strong>Associa&ccedil;&atilde;o do grupo atualizada.</strong></div>';
        
    }
    
    /**
	 * Ajax::atualizaGrupoUsuarios()
	 * 
     * Associa usuário ao grupo
     * 
	 */
    public function atualizaGrupoUsuarios(){
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        $this->load->model('grupo_resolucao_model','grupo_resolucao');
        
        foreach($_POST['usuarios'] as $usuarios){
        
            $extracao[] = str_replace('idUsuario_','',$usuarios);
            
        }
        
        try{
        
            $this->grupo_resolucao->associaUsuariosAoGrupo($extracao);
            
        }catch( Exception $e ){
                
            log_message('error', $e->getMessage());
            
        }
        
        echo '<div class="alert alert-success" role="alert"><strong>Associa&ccedil;&atilde;o do grupo atualizada.</strong></div>';

    }
    
    /**
	 * Ajax::verificaGuiche()
	 * 
     * Verifica se o guichê esta disponível
     * 
	 */
    public function verificaGuiche(){
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        $this->load->model('usuario_model','usuario');
        $resDados['dados'] = $this->usuario->verificaGuiche();
		$this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::registraSenha()
	 * 
     * Registra a senha (Dá entrada)
     * 
	 */
    public function registraSenha(){
        
        $this->load->model('atendimento_model','atendimento');
        
        try{
        
            $resDados['dados'] = array('total' => (bool)$this->atendimento->registraSenha());
        
        }catch( Exception $e ){
                
            log_message('error', $e->getMessage());
            
        }
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::carregaCategoria()
	 * 
     * Carrega a categoria
     * 
	 */
    public function carregaCategoria(){
        
        $this->load->model('categoria_model','categoria');
        
        $resDados['dados'] = $this->categoria->todasCategorias();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::carregaMotivo()
	 * 
     * Carrega o motivo
     * 
	 */
    public function carregaMotivo(){
        
        $this->load->model('motivo_model','motivo');
        
        $resDados['dados'] = $this->motivo->todosMotivos();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::pegaPrazoMotivo()
	 * 
     * Pega o prazo do motivo
     * 
	 */
    public function pegaPrazoMotivo(){
        
        $this->load->model('motivo_model','motivo');
        
        $resDados['dados'] = $this->motivo->pegaPrazo();
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::usuariosDepartamento()
	 * 
     * Usuários disponíveis para adicionar ao grupo
     * 
	 */
    public function usuariosDepartamento(){
        
        $this->load->model('grupo_resolucao_model','grupo_resolucao');
        
        $resDados['dados'] = $this->grupo_resolucao->usuariosDisponiveis();
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::pegaAtendentesOnline()
	 * 
     * Pega os atendentes online e offline
     * 
	 */
    public function pegaAtendentesOnline(){
        
        $this->load->model('dashboard_model','dashboard');
        $dados['dados'] = $this->dashboard->atendentesOnline();
        $this->load->view('view_json',$dados);
        
    }
                
}
