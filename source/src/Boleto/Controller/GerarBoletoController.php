<?php
namespace API\Boleto\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use API\Core\Helper\Mail;
// use API\Core\Helper\String;
use API\Core\Configure\Config;
use API\Boleto\View\Boleto;
use H2P\Converter\PhantomJS;
use H2P\TempFile;
use API\Boleto\Controller\BoletoController;
use API\Boleto\Model\GerarBoletoModel;

class GerarBoletoController extends BoletoController
{

	 /**
	 *
	 * Retorna informações financeiras do Aluno
	 *
	 * @author leonardo.matos <leonardo.matos@oabrs.org.br>
	 * @param Application $app
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\JsonResponse Json de retorno da chamada da API
	 */
	public function buscarInformacoesFinanceirasDoAluno(Application $app, Request $request, $matricula)
	{
		$this->validarRequisicao($app,$matricula,'aluno');
		
		//instancia o model
		$model = new FinanceiroModel();
		//instancia o config
		$config = new Config();
		$arrRetorno =  array();
		
		$anoAtu = date('Y');
		$mesAtu = date('m');
		$semestreAtu = 1;
		if($mesAtu > 6){
		    $semestreAtu = 2;
		}
		    
		$anoSemAtu = $anoAtu.'/'.$semestreAtu;
		
	    //chama funcao do model
		$model->buscarInformacoesFinanceirasDoAluno($matricula);
        
		foreach($model->getResultSet() as $linha){


			    $arrDados['id_boleto']  = (int)$linha['ID_BOLETO'];
    			$arrDados['titulo']	    = utf8_encode($linha['TITULO']);
    			$arrDados['valor' ]	    = String::formataMoeda($linha['VALOR']);
    			$arrDados['status']	    = utf8_encode($linha['BOLETO_STATUS']);
    			$arrDados['vencimento'] = $linha['VENCIMENTO'];
    			$arrDados['semestre']	= $linha['SEMESTRE'];
    			$arrDados['curso']		= utf8_encode(String::formatarNomesCompostos($linha['CURSO']));
    			$arrDados['modalidade']	= utf8_encode($linha['MODALIDADE']);
                $arrDados['linha-digitavel'] = $linha['LINHADIGITAVEL'];
                $arrDados['segunda-via'] = $config->getRequestURL($linha['ID_BOLETO'].'/2via');
    							
    			$arrRetorno[] = $arrDados;
				
				/*
    			$arrDados['id_boleto']  = (int)$linha['ID_BOLETO'];
    			$arrDados['titulo']	    = utf8_encode($linha['TITULO']);
    			$arrDados['valor' ]	    = String::formataMoeda($linha['VALOR']);
    			$arrDados['status']	    = $linha['STATUS'];
    			$arrDados['vencimento'] = $linha['VENCIMENTO'];
    			$arrDados['semestre']	= $linha['SEMESTRE'];
    			$arrDados['curso']		= String::formatarNomesCompostos($linha['CURSO']);
    			$arrDados['modalidade']	= $linha['MODALIDADE'];
    			$arrDados['linha-digitavel'] = $linha['LINHADIGITAVEL'];
    			$arrDados['segunda-via']	 = $config->getRequestURL($linha['ID_BOLETO'].'/2via');
    			
    		/*	if($linha['STATUS'] !='Pago'){
    					$financeiro = new Boleto();
    					$config 	= new Config();
    					$objBoleto = new FinanceiroModel();
    					$objBoleto->buscarDadosDoBoleto($matricula,$linha['NUMERODOCUMENTO']);
    
    					$arrBoleto = $objBoleto->getResultSet()[0];
    
    					$financeiro->setDadosObrigatorioBoleto($arrBoleto);
    					
    					$boleto = $financeiro->gerarBoleto();
    
    					$arrDados['linha-digitavel'] = $boleto->getLinhaDigitavel();
    					$arrDados['segunda-via'] = $config->getRequestURL($linha['NUMERODOCUMENTO'].'/2via');
    					unset($objBoleto);
    			}
    		
    			$arrRetorno[] = $arrDados;
    		*/		
            
            
		}
		
		return $app->json($arrRetorno);   
		
	}

	 /**
	 *
	 * Retorna informações detalhadas do boleto e envia o boleto  por email
	 *
	 * @author Leonardo.Matos <leonardo.matos@oabrs.org.br>
	 * @param Application $app
	 * @param Request $request
	 * @return JsonResponse Json de retorno da chamada da API
	 */
	public function gerarBoleto(Application $app, Request $request,$idDebito){
		// $this->validarRequisicao($app, '','');
	
		//instancia o model
		$gerarBoletoModelodel = new GerarBoletoModel();
		$financeiro = new Boleto();
		$converter = new PhantomJS();
		$arrLancamentos = array();
		
		//Dados do Boleto
		$dadosProximoNumero = $gerarBoletoModelodel->buscarDadosParaProximoNumero($idDebito);
		// $gerarBoletoModelodel->gerarProximoNossoNumero($dadosProximoNumero[0]['IdBancoSiscafw'],
		// 												$dadosProximoNumero[0]['IdContaCorrente'],
		// 												$dadosProximoNumero[0]['convenio'],
		// 												$dadosProximoNumero[0]['debitoEmConta'],
		// 												$dadosProximoNumero[0]['emissaoWeb'],
		// 												$dadosProximoNumero[0]['nossoNumero']
		// 											);
		// $gerarBoletoModelodel->gerarProximoNossoNumeroDV($dadosProximoNumeroDV[0]['codigo']);
		// echo '<pre>';
		// var_dump($dadosProximoNumero);exit;
		$arrDadosBoleto = $gerarBoletoModelodel->buscarDadosDoBoleto($idDebito)[0];

		if(strtotime($arrDadosBoleto['vencimento_parcela']) > date("d/m/Y")){

			return $app->json(array('codigo'=>400,'mensagem'=>'Atenção o Boleto está vencido.'),400);
		}

		// if($arrDadosBoleto['BOLETO_STATUS'] == 'Liquidado'){
		// 	return $app->json(array('codigo'=>400,'mensagem'=>'Este boleto já foi pago!'),400);
		// }

		//Lançamentos do boleto, (encargos, descontos etc...)
		// $model->buscarLancamentosFinanceirosDoAluno($matricula, $id_boleto);
		// $lancamentos = $model->getResultSet();
        
		//Valor do Desconto
		// $desconto = $model->buscarDescontosDoAluno($matricula,$id_boleto,$arrDadosBoleto['MODALIDADE']);
		// $desconto = $model->getResultSet();

		// $arrDadosBoleto['DESCONTO'] = trim($desconto[0]['DESCONTO']);

		$financeiro->setDadosObrigatorioBoleto($arrDadosBoleto);
		// $financeiro->adicionarLancamentos($lancamentos,$arrDadosBoleto['MODALIDADE']);
		// $financeiro->buscarInstrucoesDoBoleto($arrDadosBoleto);

		$boleto = $financeiro->gerarBoleto();
		$html = $boleto->getOutput();
		return $html;

		// return $app->json(array('codigo'=>200,'mensagem'=>utf8_encode('Boleto gerado com sucesso!')),200);
	}
}