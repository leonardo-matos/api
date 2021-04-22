<?php
namespace API\Boleto\Controller;
use Silex\Application;
use API\Core\Auth\AuthServerController;
use API\Core\Configure\Config;

class BoletoController
{

	protected function validarRequisicao($app,$id='',$origem,$scope=null)
	{	
		// Verifica se é desenvolvedor para não ficar solicitando acesso ao token toda hora
		if(!Config::isDeveloper()){
			$auth = new AuthServerController();
			$tokenRequest = $auth->validateUserRequest($scope);
			if($tokenRequest->getStatusCode() != 200){
				$tokenRequest->send();
				exit ;
			}
		}
		return true;
	}
}