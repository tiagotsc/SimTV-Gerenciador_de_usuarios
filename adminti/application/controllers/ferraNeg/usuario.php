<?php
#error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
#setlocale(LC_ALL, 'pt_BR.UTF-8', 'Portuguese_Brazil.1252');
/**
* Classe respons�vel pela usu�rio
*/
class Usuario extends CI_Controller
{
    
	/**
	 * Usuario::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
       
		parent::__construct();
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        $this->load->library('Util', '', 'util');
        $this->load->model('ferraNeg/dadosBanco_model','dadosBanco');
        $this->load->model('ferraNeg/permissaoPerfil_model','permissaoPerfil');
        $this->load->model('ferraNeg/usuario_model','usuario');
        $this->load->helper('url');
        $this->load->library('pagination');
		$this->load->helper('form');
        $this->load->library('table');
    }
    
    /**
     * Usuario::usuarios()
     * 
     * @return
     */
    public function usuarios(){
        
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu(false), $this->dadosBanco->paisMenu(false));
       
	    #$dados['valores'] = $this->relatorio->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'ferraNeg/usuario/view_psq_usuario');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Usuario::ficha()
     * 
     * Exibe a ficha para cadastro e atualiza��o do usu�rio
     * 
     * @param bool $cd Cd do usu�rio que quando informado carrega os dados do usu�rio
     * @return
     */
    public function ficha($cd = false){
        
        if($cd){
            
            $dados = $this->usuario->dadosUsuario($cd);
            
            $campos = array_keys($dados);
            
            foreach($campos as $campo){
			 
                #Data
                #if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $dados[$campo])){
                    #$dados[$campo] = $this->util->formataData($dados[$campo],'BR');
                #}
             
				$info[$campo] = $dados[$campo]; # ALIMENTA OS CAMPOS COM OS DADOS
			}
            
        }else{
            
            $campos = $this->usuario->camposUsuario();
            
            foreach($campos as $campo){
                $info[$campo] = '';
            }
        
        }
        
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu(false), $this->dadosBanco->paisMenu(false));
        
        $dados['departamento'] = $this->dadosBanco->departamento();
        
        $dados['estado'] = $this->dadosBanco->estado();
        
        $dados['perfil'] = $this->permissaoPerfil->perfil();
       
   	 #$dados['valores'] = $this->relatorio->arquivoRetornoDiario();
        #$dados['campos'] = ($dados['valores'][0]);
        $this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'ferraNeg/usuario/view_frm_usuario', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Usuario::salvar()
     * 
     * Cadastra ou atualiza o usu�rio
     * 
     * @return
     */
    public function salvar(){
        
        array_pop($_POST);
        
        if($this->input->post('cd_usuario')){
            
            try{
            
                $status = $this->usuario->atualiza();
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
        }else{
            
            try{
            
                $status = $this->usuario->insere();
            
            }catch( Exception $e ){
                
                log_message('error', $e->getMessage());
                
            }
            
            $_POST['cd_usuario'] = $status;
        }
        
        if($status){
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Usu&aacute;rio salvo com sucesso!</strong></div>');
            
            redirect(base_url('ferraNeg/usuario/ficha/'.$this->input->post('cd_usuario'))); 
            
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao salvar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('ferraNeg/usuario/ficha'));
            
        }
        
    }
    
    /**
     * Usuario::pesquisar()
     * 
     * Pesquisa o usu�rio
     * 
     * @param mixed $nome Nome do usu�rio para pesquisa
     * @param mixed $pagina P�gina corrente
     * @return
     */
    public function pesquisar($nome = null, $status, $pagina = null){
        
        $nome = ($nome == null)? '0': $nome;
        $status = ($status == null)? '0': $status;
        
        $this->load->library('pagination');
        
        $dados['pesquisa'] = 'sim';
        $dados['postNome'] = ($this->input->post('nome_usuario') != '')? $this->input->post('nome_usuario') : $nome;
        $dados['postStatus'] = ($this->input->post('status_usuario') != '')? $this->input->post('status_usuario') : $status;
        
        $mostra_por_pagina = 30;
        $dados['usuarios'] = $this->usuario->psqUsuarios($dados['postNome'], $dados['postStatus'], $pagina, $mostra_por_pagina);   
        $dados['qtdUsuarios'] = $this->usuario->psqQtdUsuarios($dados['postNome'], $dados['postStatus']);                     
        
        $config['base_url'] = base_url('ferraNeg/usuario/pesquisar/'.$dados['postNome'].'/'.$dados['postStatus']); 
		$config['total_rows'] = $dados['qtdUsuarios'][0]->total;
		$config['per_page'] = $mostra_por_pagina;
		$config['uri_segment'] = 5;
        $config['first_link'] = '&lsaquo; Primeiro';
        $config['last_link'] = '&Uacute;ltimo &rsaquo;';
        $config['full_tag_open'] = '<li>';
        $config['full_tag_close'] = '</li>';
        $config['first_tag_open']	= '';
       	$config['first_tag_close']	= '';
        $config['last_tag_open']		= '';
	    $config['last_tag_close']		= '';
	    $config['first_url']			= ''; // Alternative URL for the First Page.
	    $config['cur_tag_open']		= '<a id="paginacaoAtiva" class="active"><strong>';
	    $config['cur_tag_close']		= '</strong></a>';
	    $config['next_tag_open']		= '';
        $config['next_tag_close']		= '';
	    $config['prev_tag_open']		= '';
	    $config['prev_tag_close']		= '';
	    $config['num_tag_open']		= '';
		$this->pagination->initialize($config);
		$dados['paginacao'] = $this->pagination->create_links();
        
        $dados['postNome'] = ($dados['postNome'] == '0')? '': $dados['postNome'];
        $dados['postStatus'] = ($dados['postStatus'] == '0')? '': $dados['postStatus'];
        
        $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu(false), $this->dadosBanco->paisMenu(false));
        
        $this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'ferraNeg/usuario/view_psq_usuario', $dados);
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
        
        // Ent�o chama o layout que ir� exibir as views parciais...
      	$this->layout->show('layout');
        
    }
    
    /**
     * Usuario::apaga()
     * 
     * Apaga o usu�rio
     * 
     * @return
     */
    public function apaga(){
        
        try{
        
            $status = $this->usuario->deleteUsuario();  
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        if($status){
        
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success"><strong>Usu&aacute;rio apagado com sucesso!</strong></div>');
            redirect(base_url('ferraNeg/usuario/usuarios'));      
        
        }else{
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao apagar usu&aacute;rio, caso o erro persiste comunique o administrador!</div>');
            redirect(base_url('ferraNeg/usuario/usuarios'));
        
        }
    }
                
}
