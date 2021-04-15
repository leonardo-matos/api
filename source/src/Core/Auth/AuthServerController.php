<?php
namespace API\Core\Auth;

use Silex\Application;
use API\Core\Configure\Config;
use OAuth2\Server as OAuth2Server;
use OAuth2\Storage\Pdo;
use OAuth2\Request;
use OAuth2\HttpFoundationBridge\Response as BridgeResponse;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\OpenID\GrantType\AuthorizationCode;
use OAuth2\GrantType\UserCredentials;
use OAuth2\GrantType\RefreshToken;
use OAuth2\HttpFoundationBridge\Response;

/**
 * Classe de Autentica��o baseada no Protocolo Oauth v2.0
 * @since V1
 */
class AuthServerController
{

	private $storage;
	private $server;
	private $response;

	public function __construct()
	{
		$config = new Config();

		$this->storage = new Pdo(array('dsn' 	 => $config->dsn,
										  'username' => $config->username,
										  'password' => $config->password)
		);

		$this->server = new OAuth2Server($this->storage,
										 array('always_issue_new_refresh_token' => true,
										 	   'refresh_token_lifetime'=> 2419200));
		$this->response = new BridgeResponse();
		
		$this->addGrantTypes();
	}

	/**
	 * Gera o Access Token
	 * 
	 * /oauth/token
	 * @sin
	 * @since V1
	 * @author Leonardo
	 * @return array
	 */
	public function generateAccessToken()
	{

		return $this->server->handleTokenRequest(Request::createFromGlobals(),$this->response);

	}
	
	/**
	 * Invalida(revoke) o Access Token
	 * 
	 * /oauth/revoke
	 * @sin
	 * @since V1
	 * @author Leonardo
	 * @return array
	 */
	public function revokeAccessToken()
	{
		return $this->server->handleRevokeRequest(Request::createFromGlobals(),$this->response);
	}

	/**
	 *  Adiciona os tipos de Grant Flows que ser�o ser possiveis utilizar na API
	 * 
	 *  @return void
	 */
	private function addGrantTypes()
	{
		$this->server->addGrantType(new ClientCredentials($this->storage));
		$this->server->addGrantType(new AuthorizationCode($this->storage));
		$this->server->addGrantType(new UserCredentials($this->storage));
		$this->server->addGrantType(new RefreshToken($this->storage,array('always_issue_new_refresh_token' => true)));
	}

	/**
	 *
	 * FUN��O DISPONIBILIZADA PARA IMPLEMENTA��O FUTURA
	 *  SER� COMO AQUELES SITES QUE UTILIZANDO O LOGAR COM "FACEBOOK" E ETC...
	 * /oauth/authorize
	 * 
	 * @param Request $request
	 * @param Application $app
	 * @return Response
	 */
	public function generateAuthorizationCode()
	{
		if (!$this->server->validateAuthorizeRequest(Request::createFromGlobals(), $this->response)) {
			return $this->response;
		}

		// display an authorization form
		if (empty($_POST['authorized'])) {
			return('
			<form method="post">
			  <label>Do You Authorize TestClient?</label><br />
			  <input type="submit" name="authorized" value="yes">
			  <input type="submit" name="authorized" value="no">
			</form>');
		}
		$is_authorized = ($_POST['authorized'] === 'yes');
		$this->server->handleAuthorizeRequest(Request::createFromGlobals(), $this->response, $is_authorized);

		return $this->response;
	}

	/**
	 *
	 * Valida se o usu�rio utilizou o token para acessar a API e se este � valido
	 * @since V1
	 * @return Response
	 */
	
	public function validateUserRequest($scope)
	{
		if (!$this->server->verifyResourceRequest(Request::createFromGlobals(), $this->response,$scope)) {
			return $this->response;
		}
		return $this->response;
	}

	/**
	 *Recupera os dados do Access token(Client_id,User_id) etc...
	 * @since V1
	 * @return Response
	 */
	public function getAccessTokenData()
	{	
		
		return $this->server->getAccessTokenData(Request::createFromGlobals(),$this->response);
	}
}