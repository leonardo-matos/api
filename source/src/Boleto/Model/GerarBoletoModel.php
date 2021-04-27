<?php
namespace API\Boleto\Model;

use API\Boleto\Model\BoletoModel;

class GerarBoletoModel extends BoletoModel
{
	public function buscarDadosParaProximoNumero($idDebito)
	{	
		$this->disableQueryCache();
		
		$this->strSqlQuery ="SELECT bs.IdBancoSiscafw,
									cc.IdContaCorrente,
									0 AS convenio,-- OAB não possui sempre será zero
									iif(d.AutorizaDebitoConta IS NULL, 0,d.AutorizaDebitoConta) AS debitoEmConta,
									0 AS emissaoWeb, -- este campo vai ser recebido via parametro por backend, no caso de geração pelo SGI será 0
									cc.ByteGeracao AS nossoNumero,
									/* Dados para executar a função dbo.ufn_CalcularModulo11 que gera o DV do nosso numero*/
									CodigoAgencia AS agencia,
									DVAgencia AS posto, -- seria esse o valor referente ??
									right(year(GETDATE()),2),
									CodigoAgencia AS beneficiario,
									0 AS nossoNumeroGeradoPeloBeneficiario,
									SequencialNossoNumero,
									concat(CodigoAgencia,DVAgencia,right(year(GETDATE()),2),CodigoAgencia,0,SequencialNossoNumero) AS codigo

							FROM	Debitos d
									INNER JOIN Profissionais p ON d.IdProfissional = p.IdProfissional
									LEFT JOIN PessoasJuridicas pj ON pj.IdPessoaJuridica = d.IdPessoaJuridica
									LEFT JOIN Profissionais_OABRS.dbo.Sociedade s ON s.CodSociedade = d.IdPessoaJuridica
									INNER JOIN ContasCorrentes_TiposDeb cctd ON cctd.IdTipoDebito = d.IdTipoDebito
									INNER JOIN ContasCorrentes cc ON cc.IdContaCorrente = cctd.IdContaCorrente
									INNER JOIN BancosSiscafw bs ON bs.IdBancoSiscafw = cc.IdBancoSiscafw

							WHERE d.IdDebito = ".$idDebito."
							AND d.IdSituacaoAtual in (1,3,10,15)";
		
		$this->executeSqlServer();
		return $this->getResultSet();
	}
	
	public function buscarDadosDoBoleto($idDebito)
	{
		// $bm = new BoletoModel();
		// $bm->disableQueryCache();
		$this->disableQueryCache();
		
		$this->strSqlQuery = "SELECT IIF(pj.Nome IS NULL,IIF(p.Nome IS NULL,s.DenominacaoSocial COLLATE Latin1_General_CI_AS,p.Nome),pj.Nome) AS nome,
										IIF(pj.CNPJ IS NULL,IIF(p.CPF IS NULL,s.CNPJ COLLATE Latin1_General_CI_AS,p.CPF),pj.CNPJ) AS cpfcnpj,
										IIF(pj.Endereco IS NULL,IIF(p.Endereco IS NULL,s.Endereco COLLATE Latin1_General_CI_AS,p.Endereco),pj.Nome) as endereco,
										IIF(pj.CEP IS NULL,IIF(p.CEP IS NULL,s.CEP COLLATE Latin1_General_CI_AS,p.CEP),pj.CEP) AS cep,
										IIF(pj.NomeCidade IS NULL,IIF(p.NomeCidade IS NULL,s.NomeCidade COLLATE Latin1_General_CI_AS,p.NomeCidade),pj.Nome) AS NomeCidade,
										IIF(pj.SiglaUf IS NULL,IIF(p.SiglaUf IS NULL,s.SiglaUf COLLATE Latin1_General_CI_AS,p.SiglaUf),pj.Nome) AS NomeCidade,
										bs.CodigoBanco AS banco,
										cc.CodigoAgencia AS Agencia,
										cc.Carteira AS carteira, --na OAB não é utilizado
										cc.ContaCorrente AS conta,
										cc.DVContaCorrente AS contaDv,
										cc.CodigoCedente AS cedente,
										cc.IdConvenioPadrao AS convenio,
										d.NumeroDocumento AS numeroDocumento,
										cc.SequencialNossoNumero as sequencial, AS sequencial,
										'sera gerado separado a partir da dbo.ufn_CalcularModulo11' AS sequencial_dv,-- dbo.ufn_CalcularModulo11 @codigo = 
										CONVERT(CHAR(10), d.DataVencimento, 103) AS data_vencimento,
										CONVERT(CHAR(10), d.DataVencimento, 103) AS vencimento_parcela,
										d.ValorDevido AS valor,
										d.IdSituacaoAtual AS status,-- select * from SituacoesDebito
										cc.ValorMulta AS multa,
										'linha_digitavel' AS linha_digitavel,-- verificar com anna
										'codigo_barras' AS codigo_barras,-- verificar com anna
										d.Desconto AS Desconto

								FROM	Debitos d
										INNER join Profissionais p ON d.IdProfissional = p.IdProfissional
										LEFT JOIN PessoasJuridicas pj ON pj.IdPessoaJuridica = d.IdPessoaJuridica
										LEFT JOIN Profissionais_OABRS.dbo.Sociedade s ON s.CodSociedade = d.IdPessoaJuridica
										INNER join ContasCorrentes_TiposDeb cctd ON cctd.IdTipoDebito = d.IdTipoDebito
										INNER join ContasCorrentes cc ON cc.IdContaCorrente = cctd.IdContaCorrente
										INNER join BancosSiscafw bs ON bs.IdBancoSiscafw = cc.IdBancoSiscafw
										/* MULTAS FAZER ESSA RELAÇÃO
										inner join Emissoes e on e.IdProfissional = p.IdProfissional or e.IdPessoaJuridica = pj.IdPessoaJuridica
										inner join DetalhesEmissao de on de.IdEmissao = e.IdEmissao
										inner join DetalhesEmissaoConfig deco on deco.IdDetalheEmissao = de.IdDetalheEmissao
										*/
								WHERE d.IdDebito = ".$idDebito."
								AND d.IdSituacaoAtual IN (1,3,10,15)";
		
		// $bm->setSQLQuery($strSQL);
		// $bm->executeSqlServer();
		// return $bm->getResultSet();
		
		$this->executeSqlServer();
		return $this->getResultSet();
	}

	public function gerarProximoNossoNumero($idBanco,$idContaCorrente,$idConvenio,$debitoEmConta,$emissaoWeb,$nossoNumero){
	// $idBanco = 14,
	// $IdContaCorrente = 9,
	// $IdConvenio = 0,
	// $DebitoEmConta = 0,
	// $EmissaoWeb = 0,
	// $NossoNumero = 8
		$this->strSqlQuery = "EXECUTE dbo.usp_GetProximoNossoNumero(@idBanco=". $idBanco.",
																	@IdContaCorrente=". $idContaCorrente.",
																	@IdConvenio=". $idConvenio.",
																	@DebitoEmConta=". $debitoEmConta.",
																	@EmissaoWeb=". $emissaoWeb.",
																	@NossoNumero=". $nossoNumero."
																)";

		$this->executeSqlServer();

		return $this->getResultSet();
	}

	public function gerarProximoNossoNumeroDV($codigo){
		
		$this->strSqlQuery = "EXECUTE dbo.usp_GetProximoNossoNumero(@codigo=".$codigo.")";

		$this->executeSqlServer();

		return $this->getResultSet();
	}
}