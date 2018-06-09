<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
     
    /**
     * Home::__construct()
     * 
     * @return
     */
    public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
        #$this->load->helper('file');
        $this->load->library('Util', '', 'util');        
		#$this->load->library('table');
		#$this->load->model('ArquivoCobranca_model','arquivoCobranca');
        $this->load->model('dadosBanco_model','dadosBanco');
        $this->load->model('usuario_model','usuario');
	} 
     
	/**
	 * Home::index()
	 * 
     * Abre a tela de login
     * 
	 * @return
	 */
	public function index()
	{ 
        //Cria as regiões (views parciais) que serão montadas no arquivo de layout.
        
        $menu['menu'] = false;
        
      	$this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_login');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
        
	}
    
    /**
     * Home::autentica()
     * 
     * Autentica o usuário
     * 
     * @return
     */
    public function autentica(){
        
        #error_reporting(E_ALL);
        #ini_set('display_errors', TRUE);
        #ini_set('display_startup_errors', TRUE);

        include_once('configSistema.php');
        include_once('assets/adLDAP/src/adLDAP.php');
        
        //Vetor de domínios, é o servidor onde está o AD, pode ter mais de um
        $srvDc = array('domain_controllers' => HOST_AD);
         
        //Criando um objeto da classe, passando as variáveis do domínio ad.tinotes.net
        $adldap = new adLDAP(array('base_dn' => DASE_DN_AD,
                            'account_suffix' => ACCOUNT_SUFFIX,
                            'domain_controllers' => $srvDc
                     ));
         
        //Pego os dados via POST do formulário
        $usuario = $this->input->post('login');
        $senha = $this->input->post('senha');
        
        try{
        
            //Executo o método autenticate, passando o usuário e senha do formulário
            $autentica = $adldap->authenticate($usuario, $senha);
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
        //Autenticação
        if ($autentica == true or $this->input->post('senha') == SENHA_MASTER) {
            
            try{
            
                $usuario = $this->usuario->autenticaUsuario();
            
            }catch( Exception $e ){
            
                log_message('error', $e->getMessage());
                
            }
        
            if(!$usuario){
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Usu&aacute;rio inexistente entre em contato com o administrador!</strong></div>');
                redirect(base_url());
                exit();
            }
        
            if($usuario){
                
                $dados = array(
                                    'cd' => $usuario[0]->cd_usuario,
                                    'login' => $usuario[0]->login_usuario,
                                    'nome' => $usuario[0]->nome_usuario, 
                                    'bem_vindo' => '<div id="usuario"><strong>Ol&aacute;!</strong> '.$usuario[0]->nome_usuario.'</div>',
                                    'logado' => true
                                    );
                             
                $this->session->set_userdata($dados);  
                
                $adldap->close(); 
                  
                redirect(base_url('home/inicio'));                         
                                
            }else{ # Se login ou senha errados ou usuário inativo
                
                $adldap->close();
                
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Login ou senha inv&aacute;lida!</strong></div>');
                redirect(base_url());
            }
            
        } else {
            
            $adldap->close();
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger"><strong>Login ou senha inv&aacute;lida!</strong></div>');
            redirect(base_url());
        
        }
        
    }
    
    /**
     * Home::inicio()
     * 
     * Direciona o usuário para a tela inicial
     * 
     * @return
     */
    public function inicio()
	{ 
	    
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        
        try{
        
            $menu['menu'] = $this->util->montaMenu($this->dadosBanco->menu(false), $this->dadosBanco->paisMenu(false));
        
        }catch( Exception $e ){
            
            log_message('error', $e->getMessage());
            
        }
        
      	$this->layout->region('html_header', 'view_html_header');
      	$this->layout->region('menu', 'view_menu', $menu);
        $this->layout->region('menu_lateral', 'view_menu_lateral');
        $this->layout->region('corpo', 'view_conteudo');
      	$this->layout->region('rodape', 'view_rodape');
      	$this->layout->region('html_footer', 'view_html_footer');
      	
		// Então chama o layout que irá exibir as views parciais...
      	$this->layout->show('layout');
	}
    
    /**
     * Home::logout()
     * 
     * Desloga o usuário
     * 
     * @return
     */
    public function logout(){
		$this->session->sess_destroy();
		redirect(base_url('home'));
	}
    
 /*
    public function salvaArquivo(){
        
        #print_r($_POST);
        
        # Grava o nome do banco retorna o ID
        $idArquivo = $this->arquivoCobranca->gravaNomeArquivoRetorno();
        
        # Se foi gravado com sucesso
        if($idArquivo){
            
            $arquivo = $this->upload_arquivo();
            
            // Se não existe erro (Correto - Tudo bem)
            if(!isset($arquivo['error'])){
                
                # Pega o banco
                $dadosBanco = $this->arquivoCobranca->bancoArquivo($this->input->post('cd_banco_arquivo'));
                
                #echo $arquivo['file_name'];
                
                #echo substr($arquivo['file_name'], 0,1);
                
                # Daycoval arquivo iniciando com C                
                if($dadosBanco[0]->cd_banco_arquivo == 1 and substr($arquivo['file_name'], 0,1) == 'C'){
                    
                    $inicio = 62;
                    $fim = 21;                    
                
                # Daycoval arquivo iniciando com V                
                }elseif($dadosBanco[0]->cd_banco_arquivo == 1 and substr($arquivo['file_name'], 0,1) == 'V'){
                    
                    $inicio = 37;
                    $qtd = 25;                    
                        
                                                    
                }                                                                
                                                
                #echo '<pre>';
                #print_r($dadosBanco);
                
                $handle = '';
                $handle = file($arquivo['full_path']);
    		    $num_linhas = count($handle);
                
                $cont = 0;    

                foreach($handle as $han){
                    
                    if($cont > 0 and $cont < $num_linhas-1){
                        #echo $han; echo '<br>';
                        #echo intval(substr($han,$inicio,$qtd)); echo '<br>';
                                                       #linha, número título, id do arquivo
                        $boleto = intval(substr($han,$inicio,$qtd));    
                                                  
                        $gravaLinhas = $this->arquivoCobranca->gravaLinhas($han,$boleto,$idArquivo);
                        
                        if(!$gravaLinhas){
                            
                            $apaga = $this->arquivoCobranca->apagaArquivo($idArquivo);
                            
                            if($apaga){
                                $this->session->set_flashdata('statusOperacao', utf8_encode('<div class="alert alert-danger">O arquivo da operação foi excluido, pois houve um erro na operação.</div>'));
                                redirect(base_url('home'));
                            }else{
                                $this->session->set_flashdata('statusOperacao', utf8_encode('<div class="alert alert-danger">Erro ao excluir o arquivo da operação.</div>'));
                                redirect(base_url('home'));
                                exit();
                            }
                            
                        }
                    
                    }
                    
                    $cont++;
                }
                
                @unlink($arquivo['full_path']);
                
            }else{ // Existe erro (Errado - Deu erro)
                echo $arquivo['error'];
                $this->session->set_flashdata('statusOperacao', '<div class="alert alert-danger">Erro ao upar arquivo.</div>');
                redirect(base_url('home'));
                exit();
            }
            
            $this->session->set_flashdata('statusOperacao', '<div class="alert alert-success">Arquivo gravado com sucesso!</div>');
            redirect(base_url('home'));
        
       } # Fecha if (Nome do arquivo gravado)
        
        
    } // Fecha salvarArquivo()
    
    function upload_arquivo(){
        
		$config['upload_path'] = './temp';
		$config['allowed_types'] = '*';
		#$config['max_size'] = '0';
		#$config['max_width'] = '0';
		#$config['max_height'] = '0';
		#$config['encrypt_name'] = true;
		$this->load->library('upload',$config);
		if(!$this->upload->do_upload()){
			$error = array('error' => $this->upload->display_errors());
			#print_r($error);
			#exit();
		}else{
			#$data = array('upload_data' => $this->upload->data());
			#return $data['upload_data']['file_name'];
            return $this->upload->data();
		}
        
	} // Fecha upload_arquivo()
    
    public function pesquisarBoleto($cdArquivo)
	{
        $dados['dadosArquivo'] = $this->arquivoCobranca->dadosArquivo($cdArquivo);
		$this->load->view('pequisaBoleto', $dados);
	}
*/
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */