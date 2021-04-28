<?php
    require_once __DIR__ . '/vendor/autoload.php';
    use Silex\Application;
    set_time_limit (9999);
    $app = new Silex\Application();

    $app['debug'] = true;

        //Cria o token
        $app->match('/oauth2/token','API\Core\Auth\AuthServerController::generateAccessToken');
        
        /************************************************************************************************************
	    * 							        Serviços relacionados a geração do boleto         		       		    *
	    ************************************************************************************************************/
        $app->GET('oab/gerarBoleto/{idDebito}','API\Boleto\Controller\GerarBoletoController::gerarBoleto');

$app->run();