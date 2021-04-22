<?php
namespace API\Boleto\Model;

use API\Boleto\Model\BoletoModel;

class GerarBoletoModel extends BoletoModel
{
	public function buscarDadosDoBoleto($idDebito)
	{
		$this->strSqlQuery = "SELECT p.Nome,
										p.CPF,
										p.Endereco,
										p.CEP,
										p.NomeCidade,
										p.SiglaUF,
										CodBanco as banco,
										CodAgencia AS Agencia,
										'carteira' AS carteira, -- verificar
										CodCC_Conv_Ced AS conta, -- verificar
										'contaDv' AS contaDv,-- verificar
										'cedente' AS cedente,-- verificar
										'convenio' AS convenio,-- verificar
										'sacado' AS sacado,-- verificar
										NumeroDocumento AS numero_documento,
										NossoNumero AS sequencial,
										'sequencial_dv' AS sequencial_dv,-- verificar
										CONVERT(CHAR(10), d.DataVencimento, 103) as data_vencimento,
										CONVERT(CHAR(10), d.DataVencimento, 103) as vencimento_parcela,	-- verificar
										ValorDevido AS valor,
										IdSituacaoAtual AS status,
										'multa' AS multa,-- verificar
										'linha_digitavel' AS linha_digitavel,-- verificar
										'codigo_barras' AS codigo_barras,-- verificar
										Desconto AS Desconto
								FROM 	Debitos d
										INNER JOIN Profissionais p ON d.IdProfissional = p.IdProfissional
								WHERE IdDebito = ".$idDebito;
		$this->executeSqlServer();
        
        return $this->getResultSet();
	}
}