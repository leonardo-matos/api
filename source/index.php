<?php
    require_once __DIR__ . '/vendor/autoload.php';
    use Silex\Application;
    set_time_limit (9999);
    $app = new Silex\Application();

    $app['debug'] = true;

        //Cria e renova o token
        $app->match('/oauth2/token','API\Core\Auth\AuthServerController::generateAccessToken');

        $app->POST('deliver/incluirCorredor','API\deliver_teste\Controller\servicoTesteController::incluirCorredor');
        $app->POST('deliver/incluirProva','API\deliver_teste\Controller\servicoTesteController::incluirProva');
        $app->POST('deliver/incluirProvaCorredor','API\deliver_teste\Controller\servicoTesteController::incluirProvaCorredor');
        $app->POST('deliver/incluirResultados','API\deliver_teste\Controller\servicoTesteController::incluirResultados');
        $app->GET('deliver/listarClassificacaoPorIdade','API\deliver_teste\Controller\servicoTesteController::listarClassificacaoPorIdade');
        $app->GET('deliver/listarClassificacaoGeral','API\deliver_teste\Controller\servicoTesteController::listarClassificacaoGeral');
        $app->GET('oab/teste','API\deliver_teste\Controller\servicoTesteController::teste');

$app->run();