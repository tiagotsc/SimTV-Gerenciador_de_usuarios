<?php
error_reporting(0);
if(!defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Sao_Paulo');
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
        
        if(!$this->session->userdata('session_id') || !$this->session->userdata('logado')){
			redirect(base_url('home'));
		}
        #$this->load->helper('url');
		#$this->load->helper('form');
    }
    
    /*function index($product_id = 2)
    {
        $this->load->model('Product_model', 'product');
        $data['json'] = $this->product->get($product_id);
        if (!$data['json']) show_404();

        $this->load->view('json_view', $data);
    }*/
    
    /**
	 * Ajax::pegaQtdArquivosDiarios()
	 * 
     * Pega a quantidade diária de arquivos que foram processados
     * 
	 */
    public function pegaQtdArquivosDiarios(){
        
        $this->load->model('ArquivoCobranca_model','arquivoCobranca');
        $resDados['dados'] = $this->arquivoCobranca->qtdArquivosDiarios();
        $this->load->view('view_json',$resDados);
    }
    
    /**
     * Ajax::statusProcesValidacaoRetorno()
     * 
     * @return
     */
    public function statusProcesValidacaoRetorno(){
        
        #echo $_SESSION['totalRetornos']; exit();
        /*$dados['dados'] = array('processados'=>$this->session->userdata('retornoProcessado'));
        $this->session->unset_userdata('retornoProcessado');
        #$dados['totalRetornos'] = $_SESSION['totalRetornos'];
        
        $this->load->view('cobrancaFaturamento/arquivos/view_ajax',$dados);*/
        
    }
    
    /**
    * Ajax::pesquisaBoleto()
    * Função que pesquisa boleto
    */
    public function pesquisaBoleto(){
	
		$this->load->model('ArquivoCobranca_model','arquivoCobranca');
		#$this->load->helper('url');
		#$this->load->helper('form');
		$this->load->helper('text');
		
		$resDados['dados'] = $this->arquivoCobranca->buscaBoleto();
        #$teste[0] = array('conteudo_arquivo'=>$this->input->post('boleto'));
        #$resDados['dados'] = $teste;
		
		$this->load->view('view_json',$resDados);
	
	}
    
    /**
	 * Ajax::graficoLinhaRetornoAno()
	 * 
     * Monta a estrutura de informações para alimentar o gráfico de linhas do dashboard
     * 
	 */
    public function graficoLinhaRetornoAno($ano, $cdBanco = null){
        
        $this->load->model('Dashboard_model','dashboard');
        
        $resTitulo = $this->dashboard->qtdTitulos($ano, $cdBanco);
        
        if($resTitulo){
   
            foreach($resTitulo as $rB){
                
                if($rB->qtd_baixado > 0){
                    $qtdBaixados = $rB->qtd_baixado;
                }else{
                    $qtdBaixados = 0;
                }
                
                if($rB->qtd_rejeitado > 0){
                    $qtdRejeitado = $rB->qtd_rejeitado;
                }else{
                    $qtdRejeitado = 0;
                }
                
                $resTitulos[] = array('meses'=>$rB->mes, 'Quantidade baixado'=>$qtdBaixados, 'Quantidade rejeitado'=>$qtdRejeitado);
            }
        
        }else{
            
            $resTitulos[] = array('meses'=>'Nenhum', 'Quantidade baixado'=>0, 'Quantidade rejeitado'=>0);
            
        }
        
        $resDados['dados'] = $resTitulos;
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::graficoBarraColadaRetornoAno()
	 * 
     * Monta a estrutura de informações para alimentar o gráfico de barras coladas do dashboard
     * 
	 */
    public function graficoBarraColadaRetornoAno($ano, $cdBanco = null){
        
        $this->load->model('Dashboard_model','dashboard');
        
        $resTitulo = $this->dashboard->valorTotalTitulos($ano, $cdBanco);
        
        if($resTitulo){
        
            foreach($resTitulo as $rB){
                
                if($rB->total_baixado > 0){
                    $valorBaixado = $rB->total_baixado;
                }else{
                    $valorBaixado = 0;
                }
                
                $resBaixados[] = $valorBaixado;
                
                if($rB->total_rejeitado > 0){
                    $valorRejeitado = $rB->total_rejeitado;
                }else{
                    $valorRejeitado = 0;
                }
                
                $resRejeitados[] = $valorRejeitado;
                
                $meses[] = $rB->mes;
                
            }
        
            $resDados['dados'] = array('meses'=>$meses, 'Valor baixado'=>$resBaixados, 'Valor rejeitado'=>$resRejeitados);
        
        }else{
            
            $resDados['dados'] = array('meses'=>'Nenhum', 'Valor baixado'=>0, 'Valor rejeitado'=>0);
            
        }

        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::parametrosRelatorio()
	 * 
     * Pega os parâmetros do relatório para consulta
     * 
     * @param $cd_relatorio Cd do relatório
     * 
	 */
    public function parametrosRelatorio($cd_relatorio){
        
        $this->load->model('Relatorio_model','relatorio'); 
        $resDados['dados'] = $this->relatorio->parametrosDoRelatorio($cd_relatorio);
        /*
        $resDados['dados'] = Array(
                                array(
                                    "nome"=>"João",
                                    "sobreNome"=>"Silva",
                                    "cidade"=>"Maringá"
                                ),
                                array(
                                    "nome"=>"Ana",
                                    "sobreNome"=>"Rocha",
                                    "cidade"=>"Londrina"
                                ),
                                array(
                                    "nome"=>"Véra",
                                    "sobreNome"=>"Valério",
                                    "cidade"=>"Cianorte"
                                ));
         */                       
        $this->load->view('view_json',$resDados);                        
        
    }
    
    /**
	 * Ajax::rentabilizacaoTela1()
	 * 
     * Pega os dados do gráfico tela 1 da rentabilização
     * 
     * @param $mesAno Parâmetro mês ano para consulta
     * 
	 */
    public function rentabilizacaoTela1($mesAno){
        
        $this->load->model('Dashboard_model','dashboard');
        
        $resDados['dados'] = $this->dashboard->rentabilizacaoTela1($mesAno);
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::rentabilizacaoTela2()
	 * 
     * Pega os dados do gráfico tela 2 da rentabilização
     * 
     * @param $mesAno Parâmetro mês ano para consulta
     * 
	 */
    public function rentabilizacaoTela2($mesAno){
        
        $this->load->model('Dashboard_model','dashboard');
        
        $resDados['dados'] = $this->dashboard->rentabilizacaoTela2($mesAno);
        
        $this->load->view('view_json',$resDados);
        
    }
    
    /**
	 * Ajax::rentabilizacaoTela3()
	 * 
     * Pega os dados do gráfico tela 3 da rentabilização
     * 
     * @param $mesAno Parâmetro mês ano para consulta
     * 
	 */
    public function rentabilizacaoTela3($mesAno){
        
        $this->load->model('Dashboard_model','dashboard');
        
        $resDados['dados'] = $this->dashboard->rentabilizacaoTela3($mesAno);
        
        $this->load->view('view_json',$resDados);
        
    }
                
}
