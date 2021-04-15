<?php

require '../autoloader.php';
require 'D:\DESENVOLVIMENTO_LOCAL\API\v1\src\Controller\Configure\Config.php';


use OpenBoleto\Banco\Itau;
use OpenBoleto\Agente;
use API\Core\Configure\Config;


$config = new Config();

$sacado = new Agente('Fernando Maia', '023.434.234-34', 'ABC 302 Bloco N', '72000-000', 'Brasília', 'DF');
//$cedente = new Agente('Empresa de cosméticos LTDA', '02.123.123/0001-11', 'CLS 403 Lj 23', '71000-000', 'Brasília', 'DF');

$dadosBoleto = array(
    // Parâmetros obrigatórios
    'dataVencimento' => DateTime::createFromFormat('d/m/Y','04/04/2016'),
    'valor' => '1662.51',
    'sequencial' => '293028', // 8 dígitos
    'sacado' => $sacado,
    'cedente' => $config->retornarCedente(),
    'agencia' => '0296', // 4 dígitos
    'carteira' => 175, // 3 dígitos
    'conta' => '61534', // 5 dígitos
	'contaDv' => 3,


    // Parâmetro obrigatório somente se a carteira for
    // 107, 122, 142, 143, 196 ou 198
    'numeroDocumento' => 256054, // 7 dígitos


    // Parâmetros recomendáveis
    //'logoPath' => 'http://empresa.com.br/logo.jpg', // Logo da sua empresa

    'descricaoDemonstrativo' => array( // Até 5
        'Compra de materiais cosméticos',
        'Compra de alicate',
    ),
    'instrucoes' => array( // Até 8
        'Após o dia 30/11 cobrar 2% de mora e 1% de juros ao dia.',
        'Não receber após o vencimento.',
    ),

    // Parâmetros opcionais
    //'resourcePath' => '../resources',
    'moeda' => Itau::MOEDA_REAL,
    'dataDocumento' => new DateTime(),
    'dataProcessamento' => new DateTime(),
    //'contraApresentacao' => true,
    //'pagamentoMinimo' => 23.00,
    'aceite' => 'N',
    'especieDoc' => 'DS',
    //'usoBanco' => 'Uso banco',
    //'layout' => 'layout.phtml',
    //'logoPath' => 'http://boletophp.com.br/img/opensource-55x48-t.png',
    //'sacadorAvalista' => new Agente('Antônio da Silva', '02.123.123/0001-11'),
    //'descontosAbatimentos' => 123.12,
    //'moraMulta' => 123.12,
    //'outrasDeducoes' => 123.12,
    //'outrosAcrescimos' => 123.12,
    //'valorCobrado' => 123.12,
    //'valorUnitario' => 123.12,
    //'quantidade' => 1,
);

$boleto = new Itau($dadosBoleto);

$documento = $boleto->getOutput();
echo $documento;
// $pdf = new \HTML2PDF('P','A4');
// $pdf->WriteHTML($documento);
// $pdf->Output('boleto','I');



