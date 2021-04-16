<?php

namespace API\deliver_teste\Controller;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
// use API\Core\Helper\String;
use API\Core\Configure\Config;
use API\deliver_teste\Controller\deliverController;
use API\deliver_teste\Model\servicoTesteModel;

use PHPMailer;

require_once 'vendor/educoder/pest/Pest.php';

set_time_limit(800);

class servicoTesteController extends deliverController
{
	public function incluirCorredor(Application $app, Request $request)
	{
	
		$this->validarRequisicao($app, '','');
		
		$model = new servicoTesteModel();
		
		$dados = json_decode($request->getContent());

		if(!isset($dados->nome) || $dados->nome == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo nome é de preenchimento Obrigatório')));

		}else if(!isset($dados->cpf) ||$dados->cpf == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo cpf é de preenchimento Obrigatório')));
			
		}else if(!isset($dados->data_nascimento)|| $dados->data_nascimento == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo data de nascimento é de preenchimento Obrigatório')));
			
		}else{
			$dadosCorredor = $model->incluirCorredor($dados);
			
			if(!$dadosCorredor){
				return $app->json(array('codigo'=>500 ,'mensagem'=>utf8_encode('Falha ao inserir dados')),500);

			}else{

				return $app->json(array('codigo'=>200,'mensagem'=>utf8_encode('Dados inseridos com sucesso')));
			}
		}	
	}
	
	public function incluirProva(Application $app, Request $request)
	{
	
		$this->validarRequisicao($app, '','');
		
		$model = new servicoTesteModel();
		
		$dados = json_decode($request->getContent());

		if(!isset($dados->tipo_prova) || $dados->tipo_prova == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo tipo prova é de preenchimento Obrigatório')));

		}else if(!isset($dados->data_prova) ||$dados->data_prova == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo data prova é de preenchimento Obrigatório')));
			
		}else{
			$dadosProva = $model->incluirProva($dados);
		
			if(!$dadosProva){
				return $app->json(array('codigo'=>500 ,'mensagem'=>utf8_encode('Falha ao inserir dados')),500);

			}else{
				
				return $app->json(array('codigo'=>200,'mensagem'=>utf8_encode('Dados inseridos com sucesso')));
			}
		}
	}
	
	public function incluirProvaCorredor(Application $app, Request $request)
	{
	
		$this->validarRequisicao($app, '','');
		
		$model = new servicoTesteModel();
		
		$dados = json_decode($request->getContent());
		
		$retornaProvas = $model->retornaProvasCorredor($dados);
		
		$retornaIdadeCorredor = $model->retornaIdadeCorredor($dados)[0][0];

		if(!isset($dados->id_prova) || $dados->id_prova == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo id prova é de preenchimento Obrigatório')));

		}else if(!isset($dados->id_corredor) ||$dados->id_corredor == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo id corredor é de preenchimento Obrigatório')));
			
		}else if($retornaProvas != null){
			return $app->json(array('codigo'=>400 ,'mensagem'=>utf8_encode('Corredor não pode ser cadastrado em mais de uma prova no mesmo dia')),400);
			
		}else if($retornaIdadeCorredor < 18){
			return $app->json(array('codigo'=>400 ,'mensagem'=>utf8_encode('Corredor é menor de idade, não pode ser vinculado a uma prova')),400);

		}else{
			$dadosProvaCorredor = $model->incluirProvaCorredor($dados);
			
			if(!$dadosProvaCorredor){
				
				return $app->json(array('codigo'=>500 ,'mensagem'=>utf8_encode('Falha ao inserir dados')),500);
			}else{
	
					return $app->json(array('codigo'=>200,'mensagem'=>utf8_encode('Dados inseridos com sucesso')));
			}
		}
	}
	
	public function incluirResultados(Application $app, Request $request)
	{
	
		$this->validarRequisicao($app, '','');
		
		$model = new servicoTesteModel();
		
		$dados = json_decode($request->getContent());

		if(!isset($dados->id_prova_corredor) || $dados->id_prova_corredor == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo id prova corredor é de preenchimento Obrigatório')));

		}else if(!isset($dados->hora_inicio_prova) ||$dados->hora_inicio_prova == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo hora de inicio da prova é de preenchimento Obrigatório')));
			
		}else if(!isset($dados->hora_conclusao_prova) ||$dados->hora_conclusao_prova == null){
			return $app->json(array('codigo'=>400,'mensagem'=>utf8_encode('Campo hora de conclusao da prova é de preenchimento Obrigatório')));
			
		}else{
			$dadosResultados = $model->incluirResultados($dados);
		
			if(!$dadosResultados){
				return $app->json(array('codigo'=>500 ,'mensagem'=>utf8_encode('Falha ao inserir dados')),500);

			}else{
				return $app->json(array('codigo'=>200,'mensagem'=>utf8_encode('Dados inseridos com sucesso')));
			}
		}
	}
	
	public function listarClassificacaoPorIdade(Application $app, Request $request)
	{
	
		$this->validarRequisicao($app, '','');
	
		$model = new servicoTesteModel();
	
		$model->listarClassificacao();

		$listarClassificacaoPorIdade = $model->getResultSet();

		$model->listarTiposProva();
		
		$tiposProva = $model->getResultSet();
		
		$i = 0;
		$j = 0;
		$k = 0;
		$l = 0;
		$m = 0;
		
		
		if(empty($listarClassificacaoPorIdade) || empty($tiposProva)){
			return 'VAZIO';
		}else{
			foreach($tiposProva as $dadosTipoProva){
				$a = 0;
				$b = 0;
				$c = 0;
				$d = 0;
				$e = 0;
				foreach($listarClassificacaoPorIdade as $dados){
					if($dados['tipo_prova'] == $dadosTipoProva['tipo_prova']){
						if($dados['idade'] <= 25){
							$arrRetorno[$dados['tipo_prova']]['18-25'][$i] = array('id_prova' => utf8_encode($dados['id_prova']),
															'tipo_prova' => utf8_encode($dados['tipo_prova']),
															'id_corredor' => utf8_encode($dados['id_corredor']),
															'idade' => utf8_encode($dados['idade']),
															'nome' => utf8_encode($dados['nome']),
															'classificacao' => $a+++1
														);
							$i++;
						}else if($dados['idade'] >= 26 && $dados['idade'] <= 35){
														
							$arrRetorno[$dados['tipo_prova']]['26-35'][$j] = array('id_prova' => utf8_encode($dados['id_prova']),
															'tipo_prova' => utf8_encode($dados['tipo_prova']),
															'id_corredor' => utf8_encode($dados['id_corredor']),
															'idade' => utf8_encode($dados['idade']),
															'nome' => utf8_encode($dados['nome']),
															'classificacao' => $b+++1
														);
							$j++;
						}else if($dados['idade'] >= 36 && $dados['idade'] <= 45){
							
							$arrRetorno[$dados['tipo_prova']]['36-45'][$k] = array('id_prova' => utf8_encode($dados['id_prova']),
															'tipo_prova' => utf8_encode($dados['tipo_prova']),
															'id_corredor' => utf8_encode($dados['id_corredor']),
															'idade' => utf8_encode($dados['idade']),
															'nome' => utf8_encode($dados['nome']),
															'classificacao' => $c+++1
														);
							$k++;
						}else if($dados['idade'] >= 46 && $dados['idade'] <= 55){
							$arrRetorno[$dados['tipo_prova']]['46-55'][$l] = array('id_prova' => utf8_encode($dados['id_prova']),
															'tipo_prova' => utf8_encode($dados['tipo_prova']),
															'id_corredor' => utf8_encode($dados['id_corredor']),
															'idade' => utf8_encode($dados['idade']),
															'nome' => utf8_encode($dados['nome']),
															'classificacao' => $d+++1
														);
							$l++;
						}else if($dados['idade'] < 55){
							$arrRetorno[$dados['tipo_prova']]['55'][$m] = array('id_prova' => utf8_encode($dados['id_prova']),
														'tipo_prova' => utf8_encode($dados['tipo_prova']),
														'id_corredor' => utf8_encode($dados['id_corredor']),
														'idade' => utf8_encode($dados['idade']),
														'nome' => utf8_encode($dados['nome']),
														'classificacao' => $e+++1
													);
							$m++;
						}else{
							return $app->json(array('codigo'=>500,'mensagem'=>utf8_encode('Erro ao montar array')),500);
						}
					}
				}
			}
		}
		return $app->json($arrRetorno);
	}
	public function listarClassificacaoGeral(Application $app, Request $request)
	{
	
		$this->validarRequisicao($app, '','');
	
		$model = new servicoTesteModel();
	
		$model->listarTiposProva();
		
		$tiposProva = $model->getResultSet();
		
		$model->listarClassificacao();
		
		$classificacao = $model->getResultSet();
		
		$i = 0;
		$j = 1;
		
		if(empty($tiposProva) || empty($classificacao)){
			return 'VAZIO';
		}else{
			foreach($tiposProva as $dadosTipoProva){
				foreach($classificacao as $dados){
					if($dados['tipo_prova'] == $dadosTipoProva['tipo_prova']){
						$arrRetorno[$dadosTipoProva['tipo_prova']][$i] = array('id_prova' => utf8_encode($dados['id_prova']),
														'tipo_prova' => utf8_encode($dados['tipo_prova']),
														'id_corredor' => utf8_encode($dados['id_corredor']),
														'idade' => utf8_encode($dados['idade']),
														'nome' => utf8_encode($dados['nome']),
														'classificacao' => $j++
													);
					}
					$i++;
				}
				$j = 1;
			}
		}
		return $app->json($arrRetorno);
	}
	public function teste(Application $app, Request $request)
	{
		// $this->validarRequisicao($app, '','');
		
		$model = new servicoTesteModel();
		
		$model->teste();
		
		$teste = $model->getResultSet();
		echo '<pre>';
		var_dump($teste);exit;
		
		if(empty($teste)){
			return 'VAZIO';
		}else{
			// $i = 1;
			foreach($teste as $dados){
				// var_dump($dados);
				// $arrRetorno = array('idMsgBoletoBancario' => $dados['IdMsgBoletoBancario'],
				// 					'tipoMensagem' => $dados['TipoMensagem'],
				// 					'mensagem' => $dados['Mensagem'],
				// 					'finalidadeInstrucao' => $dados['FinalidadeInstrucao'],
				// 					'idBancoSiscafW' => $dados['IdBancoSiscafW'],
				// 					'e_MensagemWEB' => $dados['E_MensagemWEB'],
				// 					'e_InstrucaoWEB' => $dados['E_InstrucaoWEB'],
				// 					'mensagemHTML' => $dados['MensagemHTML'],
				// 					'e_Terceirizado' => $dados['E_Terceirizado']
				// 					);
				// $arrRetorno[$i] = array('CodCidade' => $dados['CodCidade'],
				// 					'Nome' => $dados['Nome'],
				// 					'codSubsecao' => $dados['codSubsecao']
				// 					);
				// 					$i++;
			}
		}
		return $app->json($arrRetorno);
	}
}