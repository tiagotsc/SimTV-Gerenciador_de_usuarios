<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.validate.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery.mask.min.js") ?>"></script>
    <!-- INÍCIO Modal Apaga registro de telecom -->
    <div class="modal fade" id="apaga" tabindex="-1" role="dialog" aria-labelledby="apaga" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="myModalLabel">Deseja apagar o usu&aacute;rio?</h4>
                </div>
                <div class="modal-body">
                    <?php              
                        $data = array('class'=>'pure-form','id'=>'apagaRegistro');
                        echo form_open('usuario/apaga',$data);
                        
                            echo form_label('Nome', 'apg_nome_usuario');
                    		$data = array('id'=>'apg_nome_usuario', 'name'=>'apg_nome_usuario', 'class'=>'form-control data');
                    		echo form_input($data,'');
                        
                    ?>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="apg_cd_usuario" name="apg_cd_usuario" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">N&atilde;o</button>
                    <button type="submit" class="btn btn-primary">Sim</button>
            </div>
                    <?php
                    echo form_close();
                    ?>
        </div>
      </div>
    </div>
    <!-- FIM Modal Apaga registro de telecom -->
            <!--<div class="col-md-9 col-sm-8"> USADO PARA SIDEBAR -->
            <div class="col-lg-12">
                
                <ol class="breadcrumb">
                    <li><a href="<?php echo base_url('home/inicio'); ?>">Principal</a>
                    </li>
                    <li class="active">Pesquisar usu&aacute;rio</li>
                </ol>
                <div id="divMain">
                    <?php
                        
                        echo $this->session->flashdata('statusOperacao');
                        $data = array('class'=>'pure-form','id'=>'pesquisa_usuario');
                    	echo form_open('usuario/pesquisar',$data);
                            $attributes = array('id' => 'address_info', 'class' => 'address_info');
                            
                            $botaoCadastrar = "<a href='".base_url('usuario/ficha')."' class='linkDireita'>Cadastrar&nbsp<span class='glyphicon glyphicon-plus'></span></a>";
                            
                    		echo form_fieldset("Pesquisar usu&aacute;rio".$botaoCadastrar, $attributes);
                    		  
                                echo '<div class="row">';
                                
                                    echo '<div class="col-md-12">';
                                    #print_r(array_keys($listaBancos));
                                    echo form_label('Nome ou e-mail do usu&aacute;rio', 'nome_usuario');
                        			$data = array('name'=>'nome_usuario', 'value'=>$this->input->post('nome_usuario'),'id'=>'nome_usuario', 'placeholder'=>'Digite o nome', 'class'=>'form-control');
                        			echo form_input($data);
                                    echo '</div>';
                                    /*
                                    echo '<div class="col-md-4">';
                                    $options = array(''=>'', 'A' => 'Ativo', 'I' => 'Inativo');		
                            		echo form_label('Status', 'status_usuario');
                            		echo form_dropdown('status_usuario', $options, $postStatus, 'id="status_usuario" class="form-control"');
                                    echo '</div>';
                                     */  
                                echo '</div>';                      
                                                                
                                echo '<div class="actions">';
                                echo form_submit("btn_cadastro",utf8_encode("Pesquisar usuário"), 'class="btn btn-primary pull-right"');
                                echo '</div>';
                                                            
                    		echo form_fieldset_close();
                    	echo form_close(); 
                    
                    ?>        
                </div>
                
                <div class="row">&nbsp</div>
                <?php
                if($pesquisa == 'sim'){
                ?>
                <div class="well">
                
                <?php 
                $this->table->set_heading('Login', 'Nome', 'Cidade', 'Departamento', 'Acesso sistema', 'Status', 'A&ccedil;&atilde;o');
                            
                foreach($usuarios as $usu){
                    
                    $cell1 = array('data' => $usu->login_usuario);
                    $cell2 = array('data' => html_entity_decode($usu->nome_usuario));
                    
                    $cell3 = array('data' => html_entity_decode($usu->nome_estado));
                    $cell4 = array('data' => html_entity_decode($usu->nome_departamento));
                    $cell5 = array('data' => $acesso = ($usu->acesso_usuario == 'S')? 'SIM': 'N&Atilde;O');
                    $cell6 = array('data' => $usu->status_usuario);
                    $botaoEditar = '<a title="Editar" href="'.base_url('usuario/ficha/'.$usu->cd_usuario).'" class="glyphicon glyphicon glyphicon-pencil"></a>';
                    $botaoExcluir = '<a title="Apagar" href="#" onclick="apagarRegistro('.$usu->cd_usuario.',\''.$usu->nome_usuario.'\')" data-toggle="modal"  data-target="#apaga" class="glyphicon glyphicon glyphicon glyphicon-remove"></a>';
                    
                    $cell7 = array('data' => $botaoEditar.$botaoExcluir);
                        
                    $this->table->add_row($cell1, $cell2, $cell3, $cell4, $cell5, $cell6, $cell7);
                    
                }
                
            	$template = array('table_open' => '<table class="table table-bordered">');
            	$this->table->set_template($template);
            	echo $this->table->generate();
                echo "<ul class='pagination pagination-lg'>" . utf8_encode($paginacao) . "</ul>"; 
                ?>
                </div>
                <?php
                }
                ?>
                
            </div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->
    
<script type="text/javascript">

function apagarRegistro(cd, nome){
    $("#apg_cd_usuario").val(cd);
    $("#apg_nome_usuario").val(nome);
}

$(document).ready(function(){
    
    $(".data").mask("00/00/0000");
    
    $(".actions").click(function() {
        $('#aguarde').css({display:"block"});
    });
});


/*
CONFIGURA O CALENDÁRIO DATEPICKER NO INPUT INFORMADO
*/
$("#data,#data2").datepicker({
	dateFormat: 'dd/mm/yy',
	dayNames: ['Domingo','Segunda','Ter&ccedil;a','Quarta','Quinta','Sexta','S&aacute;bado','Domingo'],
	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b','Dom'],
	monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	nextText: 'Pr&oacute;ximo',
	prevText: 'Anterior',
    
    // Traz o calendário input datepicker para frente da modal
    beforeShow :  function ()  { 
        setTimeout ( function (){ 
            $ ( '.ui-datepicker' ). css ( 'z-index' ,  99999999999999 ); 
        },  0 ); 
    } 
});

$(document).ready(function(){
    
    // Valida o formulário
	$("#relatorios").validate({
		debug: false,
		rules: {
			data: {
                required: true
            }
		},
		messages: {
			data: {
                required: "Informe uma data."
            }
	   }
   });   
   
});

</script>