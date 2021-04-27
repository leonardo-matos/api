<?php
namespace API\Boleto\View;
use API\Core\Configure\Config;
use OpenBoleto\Banco\Itau;
use OpenBoleto\Agente;
use OpenBoleto\Banco\Santander;
use OpenBoleto\Banco\Sicredi;
// use API\Core\Helper\String;
use API\Core\Helper\Database;
use OpenBoleto\Exception;

class Boleto{

	/**
	* Banco do boleto
	* @var string|int
	*/
	public  $banco;

	/**
	* Numero da Agencia
	* @var int $agencia
	*/
	public $agencia;

	/**
	 * Digitor verificador da Agencia
	 * @var int $agenciaDv
	 */
	public $agenciaDv;

	/**
	 * Carteira de cobrança
	 *
	 * É um serviço contratado junto ao banco, para gerar boletos bancários.
		Através dela é feita a identificação dos pagamentos e estes são creditados direto na sua conta bancária.
		Exemplos de carteira de cobrança são: "Carteira com Registro" e a "Carteira sem Registro".
		Sem a carteira de cobrança não é possível gerar boletos,
		visto que é através dela que o banco identifica os pagamentos dos boletos.
	 * @var int $carteira
	 */
	public $carteira;

	/**
	 * Conta do cedente emissor do boleto
	 * @var int $conta
	 */
	public $conta;

	/**
	 * Status do doc
	 * Pago, Devido, Atrasado
	 * @var string $status
	 */
	public $status;

	/**
	 * Digito verificador da conta
	 * @var int $contaDv
	 */
	public $contaDv;

	/**
	 * Numero do convenio
	 * @var int $convenio
	 */
	public $convenio;

	/**
	 * Dados do pagador do boleto
	 * @var Agente
	 */
	public $sacado;

	/**
	 * Dados do gerador do boleto
	 * @var Cedente
	 */
	public $cedente;

	/**
	 * Data de geração do boleto
	 * @var DateTime
	 */
	public $dataDocumento;

	/**
	 * Data de vencimento do boleto
	 * @var string $dataVencimento
	 */
	public $dataVencimento;

	/**
	 * Seu número
	 * @var int
	 */
	public $numeroDocumento;

	/**
	 * Nosso Número
	 * É a identificação do boleto. É importante que ele não se repita para o mesmo Beneficiário
	 * @var int
	 */
	public $sequencial;
	public $sequencial_dv;

	/**
	 * descricaoDemonstrativo ou os lancamentos
	 * Descritivo do que será paago no DOC
	 * @var int
	 */
	public $descricaoDemonstrativo;

	/**
	 * Insere as instruções de pagamento do boleto
	 * @var array
	 */
	public $instrucoes;

	/**
	 * Especie do DOC
	 * @var String
	 */
	public $valor;

	/**
	 * Multa por atraso
	 * @var String
	 */
	public $multa;

	/**
	 * Exibe o desconto quando o aluno possui
	 * @var String
	 */
	public $desconto;

	/**
	 * Aceite
	 *O campo Aceite indica se o Pagador (quem recebe o boleto) aceitou o boleto,
	 * ou seja, se ele assinou o documento de cobrança que originou o boleto.
	 *O padrão é usar "N" no aceite, pois nesse caso não é necessário a autorização do Pagador para protestar o título.
	 */
	public $aceite = 'N';

	/**
	 * Especie de Doc
	 * @var string
	 */
	//customizado para o centris // valor original: DS
	public $especieDoc = '8050';

	/**
	 * Array de dados formatados para gerar o boleto
	 * @var array
	 */
	public $arrDadosBoleto;

	/**
	 * Logo do unilasalle no boleto
	 * @var string
	 */
	public $logoPath = '';

	/**
	 * Vencimento da parcela
	 * @var string
	 */
	public $vencimentoParcela;

	/**
	 * Objeto da classe dos boletos
	 * @var BoletoAbstract
	 */
	public $objBoleto;
	
	//customizado para o centris
	public $linha_digitavel;
	
    //customizado para o centris
	public $codigo_barras;
	

	public function __set($name, $value)
    {
	     throw new Exception("Variable ".$name." has not been set.", 1);
	}

	public function __get($name)
    {
	    throw new Exception("Variable ".$name." has not been declared and can not be get.", 1);
	}

	/**
	 * @param array $arrDados Dados vindos do banco de dados
	 * @return void
	 */
	

		public function setDadosObrigatorioBoleto($arrDados)
        {

		$dados = array_change_key_case($arrDados);

		// A função extract  transforma em variavel tudo que está dentro do array
		extract($dados);
		// var_dump($banco);exit;

		
		$config = new Config();
		//($nome, $documento, $endereco = null, $cep = null, $cidade = null, $uf = null)
		$sacado = new Agente(utf8_decode($nome),$cpfcnpj,$endereco,$cep,utf8_decode($cidade),$uf);
		
		// var_dump('datavencimento: ',$data_vencimento);
		// var_dump('vencimentoParcela: ',\DateTime::createFromFormat('d/m/Y',$data_vencimento));exit;
        
		//Dados da conta
		$this->banco = $banco;
		$this->agencia = $agencia;
		if(isset($agenciadv) && $agenciadv !='0'){
			$this->agenciaDv = $agenciadv;
		}
		$this->carteira = 21;
		$this->conta = $conta;
		$this->contaDv = $contadv ;
		$this->cedente = $config->retornarCedente();
		$this->convenio = '123' ;
		$this->dataDocumento = new \DateTime();
		$this->sacado = $sacado ;
		$this->numeroDocumento = $numerodocumento; 
		$this->sequencial = 555;//$sequencial; //nosso numero
		$this->sequencial_dv = $sequencial_dv ; //nosso numero digito verificador
		$this->dataVencimento = \DateTime::createFromFormat('d/m/Y',$data_vencimento);
		$this->vencimentoParcela = \DateTime::createFromFormat('d/m/Y',$vencimento_parcela);
		$this->valor = str_replace(',','.',$valor);
		$this->status = $boleto_status;
		$this->multa =  str_replace(',','.',$multa);
		$this->linha_digitavel = $linhadigitavel;
		$this->codigo_barras = $codigobarras;
		
		if(isset($desconto)){
			$this->desconto =  str_replace(',','.',$desconto);
		}
	}

	/**
	 * Insere a descricao de lancamentos no boleto
	 *
	 * @param array $arrayLancamentos
	 */
	public function adicionarLancamentos($arrayLancamentos, $modalidade)
    {

		if(empty($arrayLancamentos)){return '';	}

		foreach($arrayLancamentos as $key=>$lancamento){
			
		    if($lancamento['modalidade'] == $modalidade)
		    {
			
			     $this->descricaoDemonstrativo[] = utf8_decode($lancamento['tipo_sinal']).' '.String::formataMoeda($lancamento['valor']).' -- '.utf8_decode($lancamento['descricao']);
		    }
			
		}
		if($this->status == 'Atrasado'){
			$this->descricaoDemonstrativo[] = utf8_decode($lancamento['tipo_sinal']).' '.String::formataMoeda($this->multa).' -- '.'Juros e Acréscimos';

		}
	}

	/**
	 * Retorna as informações do boleto
	 * @return array
	 */
	public function getDadosObrigatorioBoleto()
    {
		return $this->arrDadosBoleto;
	}

	/**
	 *
	 *@return  BoletoAbstract
	 */
	public function gerarBoleto()
    {
        // var_dump('aqui no boleto: ',$this->banco);exit;

        // $this->banco = '748';

		switch($this->banco){
			// Itau
			case '341':
				$this->objBoleto = new Itau($this);
				break;
				//Santander
			case '033':
				$this->objBoleto = new Santander($this);
				break;
			case '748':
				$this->objBoleto = new Sicredi($this);
				break;
		}
		return $this->objBoleto;
	}

	/**
	 * Retorna as instrucoes do boleto
	 * @return array
	 */
	public function buscarInstrucoesDoBoleto($arrDados)
    {

		$db = new Database();
		$arrRetorno  = array();
		$strSQL = '';
		$this->instrucoes = array('');
		if($this->status != 'Atrasado'){
		    
		    $desc = $this->desconto;
		    $desc = str_replace(',','.',$desc);
		    
		   // if((float)$desc > 0){
		   //   $this->instrucoes[] = 'At� o vencimento, desconto de '.String::formataMoeda($this->desconto);    
		  //  }
			
			//$this->instrucoes[] = utf8_decode('Em qualquer banco at� o vencimento');
			
			$strSQL = "SELECT ecm.PERCENTUAL AS ind_multa,
						eci.PROPORCIONAL AS ind_comissao,
						eci.DATAVIGOR
						FROM GVDASA.FIN_ESTRUTURCORRECAO ec
						INNER JOIN GVDASA.FIN_TIPOTITULO tti ON tti.CODIGOESTRUTURACORRECAO = ec.CODIGOESTRUTURACORRECAO
						INNER JOIN GVDASA.FIN_TITULO titu ON tti.CODIGOTIPOTITULO = titu.CODIGOTIPOTITULO
						INNER JOIN GVDASA.FIN_ESTRMULTA ecm ON ecm.CODIGOESTRUTURACORRECAO = ec.CODIGOESTRUTURACORRECAO
						INNER JOIN GVDASA.FIN_ESTRINDICE eci ON eci.CODIGOESTRUTURACORRECAO = ec.CODIGOESTRUTURACORRECAO
						where titu.CODIGOTITULO = ".$arrDados['TITULO'];
			// Executa a consulta
			$arrRetorno = $db->querySqlServer($strSQL,'consultar','fetchRow');

			$valorMulta = number_format((($this->valor * (int)$arrRetorno['ind_multa']) / 100),2); 
			$jurosDiario = number_format(((((int)$arrRetorno['ind_comissao']/30) * $this->valor)/100),2);
			
			// Monta mensagem a ser enviada
			$msg = "Após vencimento cobrar multa de R$ ".$valorMulta." + juros diário de R$ ".$jurosDiario." + IGPM";
			$msg .= "<br>";
			$msg .= "Este título está disponível no portal acadêmico, acesse https://portal.unilasalle.edu.br";
			$msg .= "<br>";
			$msg .= "Pelo portal acadêmico efetue a emissão e atualização do boleto. Maiores informações (51) 3476-8500";
			$msg .= "<br>";
			$msg .= "Sr CAIXA não aceitar este título após ".$arrDados['DATA_LIMITE_PAG'];
			//$msg = utf8_decode($msg);
			// Insere no Array
			array_push($this->instrucoes,$msg);
		}

		if($this->status == 'Atrasado'){
			// Monta mensagem a ser enviada
			$msg = "Não receber após ".$this->dataVencimento->format('d/m/Y');
			$msg .= "<br>";
			$msg .= "Vencimento original: ".$this->vencimentoParcela->format('d/m/Y');
			//$msg = utf8_decode($msg);
			// Insere no Array
			array_push($this->instrucoes,$msg);
		}	
	}
}