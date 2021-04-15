<?php

namespace OAuth2\GrantType;

use OAuth2\Storage\UserCredentialsInterface;
use OAuth2\ResponseType\AccessTokenInterface;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;

/**
 *
 * @author Brent Shaffer <bshafs at gmail dot com>
 */
class UserCredentials implements GrantTypeInterface{
    private $userInfo;

    protected $storage;

    /**
     * @param OAuth2\Storage\UserCredentialsInterface $storage REQUIRED Storage class for retrieving user credentials information
     */
    public function __construct(UserCredentialsInterface $storage){
        $this->storage = $storage;
    }

    public function getQuerystringIdentifier(){
        return 'password';
    }

    public function validateRequest(RequestInterface $request, ResponseInterface $response){


        if (!$request->request("password") || !$request->request("username")) {
            $response->setError(400, 400, 'Parâmetros faltando: "username" e  "password" necessarios');

            return null;
        }

        if (!$this->storage->checkUserCredentials($request->request("username"), $request->request("password"))) {
            $response->setError(401, 400, 'Usuário ou senha inválidos');

            return null;
        }

        $userInfo = $this->storage->getUserDetails($request->request("username"));
        
        if (empty($userInfo)) {
            $response->setError(400, 400, 'Não foi possível recuperar as informações do usuário');

            return null;
        }

        if (!isset($userInfo['user_id'])) {
            throw new \LogicException("you must set the user_id on the array returned by getUserDetails");
        }

        $this->userInfo = $userInfo;

        return true;
    }

    public function getClientId() {
        return null;
    }

    public function getUserId(){
        return $this->userInfo['user_id'];
    }

    public function getScope() {
        return isset($this->userInfo['scope']) ? $this->userInfo['scope'] : null;
    }

    public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope){
        return $accessToken->createAccessToken($client_id, $user_id, $scope);
    }


}
