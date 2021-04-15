<?php
namespace API\deliver_teste\Model;

use SqlFormatter;
use API\Core\Helper\Database;
use API\Core\Configure\Config;
use phpFastCache\CacheManager;


class deliverModel
{

	/**
	 * Conexão com o banco
	 * 
	 * @var Resource
	 */
	protected $dbResource;

	/**
	 * Afirmação SQL
	 * 
	 * @var string
	 */
	protected $strSqlQuery;

	/**
	 * retorno da consulta
	 * 
	 * @var mixed
	 */
	protected $resultSet;

	/**
	 * Desabilitar cache da Query 
	 * 
	 * @var boolean
	 */
	protected $cachedQuery = true;

	/**
	 * Tipo de retorno da consulta
	 * Fetch
	 * @var string
	 */
	protected $fetchType = 'fetchArray';

	/**
	 * Construtor da class
	 * 
	 * @param string $sql
	 * @param boolean $execute
	 */
	public function __construct()
	{
		$this->dbResource = new Database();
	}


	/**
	 * Destrutor da classe, executa a consulta ao finalizar
	 * @return void
	 */
	public function __destruct()
	{

	}


	/**
	 * Define a forma de retorno da consulta SQL
	 * Se não estiver definido o padrão será fetchArray
	 * 
	 * @param string $type Tipo de retorno (array, row)
	 * @return void
	 */
	public function setFetchType($type)
	{
		switch ($type) {
			case 'array':
				$this->fetchType = 'fetchArray';
				break;
			case 'row':
				$this->fetchType = 'fetchRow';
				break;
			default:
				$this->fetchType = 'fetchArray';
				break;
		}
	}
	
	/**
	 * Retorna a forma de retorno da consulta
	 * 
	 * @return string 
	 */
	public function getFetchType()
	{
		return $this->fetchType;
	}

	/**
	 *  Retorna os dados da consulta
	 *  
	 * @param string $sql
	 * @param boolean $execute
	 * @return  mixed retorna o resultado de uma query SQL
	 */
	public function getResultSet()
	{
		return $this->resultSet;

	}

	/**
	 *  Retorna a consulta SQL formatada
	 *  Para propositos de debug
	 *   
	 * @param string $sql
	 * @param boolean $execute
	 * @return  void
	 */
	public function getSQLQuery()
	{
		echo SqlFormatter::format($this->strSqlQuery);

	}

	/**
	 * Desabilitar cache em consulta especifica( Utilizar apenas para consultas que necessitam extremamente de tempo real)
	 * 
	 * @return void
	 */
	public function disableQueryCache()
	{
		$this->cachedQuery = false;
	}


	/**
	 * Executa uma procedure SQL
	 * 
	 * @param boolean $validate_query Apenas 
	 * @return void		  
	 */
	public function execProcedure()
	{
		$this->resultSet = $this->dbResource->queryMySql($this->strSqlQuery,'executar',$this->getFetchType());		
	}


	/**
	 * Executa a consulta sql e insere o retorno da mesma na variavel $this->resultSet
	 * 
	 * @param boolean $validate_query Apenas 
	 * @return void		  
	 */
	public function execute($onlySelect=false)
	{
		if($onlySelect){
				$cache = $this->getCache($this->strSqlQuery);
				if(!is_null($cache) && $this->cachedQuery){
					$this->resultSet = $cache;
				}else{
					$this->resultSet = $this->dbResource->queryMySql($this->strSqlQuery,'consultar',$this->getFetchType());
					$this->setCache($this->strSqlQuery,$this->resultSet);
				}
		}else{
				switch ($this->checkSQLQuerytype()) {
				    
					case 'select':
						$cache = $this->getCache($this->strSqlQuery);
						if(!is_null($cache) && $this->cachedQuery){
							$this->resultSet = $cache;
						}else{
							$this->resultSet = $this->dbResource->queryMySql($this->strSqlQuery,'consultar',$this->getFetchType());
							$this->setCache($this->strSqlQuery,$this->resultSet);
						}
						break;
						
					case 'insert':
						$this->resultSet =  $this->dbResource->queryMySql($this->strSqlQuery,'inserir');
						break;
						
					case 'update':
						$this->resultSet =  $this->dbResource->queryMySql($this->strSqlQuery,'atualizar');
						break;
						
					case 'delete':
						$this->resultSet =  $this->dbResource->queryMySql($this->strSqlQuery,'deletar');
						break;
						
					case 'invalid':
							echo 'verifique se não há espaços depois do primeiro comando SQL, de modo a pegar apenas a primeira string';
							$this->getSQLQuery();
						exit();
						break;						
				}
		}

		
	}

	public function executeSqlServer($onlySelect=false) {
		if($onlySelect){
			$cache = $this->getCache($this->strSqlQuery);
			if(!is_null($cache) && $this->cachedQuery){
				$this->resultSet = $cache;
			}else{
				$this->resultSet = $this->dbResource->querySqlServer($this->strSqlQuery,'consultar',$this->getFetchType());
				$this->setCache($this->strSqlQuery,$this->resultSet);
			}
		}else{
			switch ($this->checkSQLQuerytype()) {
				
				case 'select':
				$cache = $this->getCache($this->strSqlQuery);
				if(!is_null($cache) && $this->cachedQuery){
					$this->resultSet = $cache;
				}else{
					$this->resultSet = $this->dbResource->querySqlServer($this->strSqlQuery,'consultar',$this->getFetchType());
					$this->setCache($this->strSqlQuery,$this->resultSet);
				}
				break;
				
				case 'insert':
				$this->resultSet =  $this->dbResource->querySqlServer($this->strSqlQuery,'inserir');
				break;
				
				case 'update':
				$this->resultSet =  $this->dbResource->querySqlServer($this->strSqlQuery,'atualizar');
				break;
				
				case 'delete':
				$this->resultSet =  $this->dbResource->querySqlServer($this->strSqlQuery,'deletar');
				break;

				case 'execute':
				$cache = $this->getCache($this->strSqlQuery);
				if(!is_null($cache) && $this->cachedQuery){
					$this->resultSet = $cache;
				}else{
					$this->resultSet = $this->dbResource->querySqlServer($this->strSqlQuery,'consultar',$this->getFetchType());
					$this->setCache($this->strSqlQuery,$this->resultSet);
				}
				break;
				
				case 'invalid':
				echo 'verifique se não há espaços depois do primeiro comando SQL, de modo a pegar apenas a primeira string';
				$this->getSQLQuery();
				exit();
				break;						
			}
		}

		
	}



	/**
	 *  Verifica o tipo da  consulta SQL: SELECT /UPDATE /DELETE
	 *
	 *  @return string tipo da consulta sql
	 * 
	 */
	public function checkSQLQuerytype()
	{	
		$query = explode(" ",trim($this->strSqlQuery));
		if(strtoupper(trim($query[0])) == 'SELECT'){
			return 'select';
		}else if(strtoupper(trim($query[0])) == 'UPDATE'){
			return 'update';
		}else if(strtoupper(trim($query[0])) == 'DELETE'){
			return 'delete';
		}else if(strtoupper(trim($query[0])) == 'INSERT'){
			return 'insert';
		}


		return 'invalid';
	}


	/**
	 * Guarda as informações em cache
	 * 
	 * @param string $identifier | ID unico do cache
	 * @param mixed $data | Dados para armazenar
	 */
	public function setCache($identifier,$data)
	{
		$config = new Config();
		if($config->cacheEnabled){
			CacheManager::setup($config->getCacheConfig());
			$cache = CacheManager::getInstance();
			return $cache->set(md5($identifier),$data,$config->cacheTime);
		}
		return null;
	}


	/**
	 * Recebe as informações guardadas no cache
	 * 
	 * @param string $identifier | ID unico do cache
	 * @param mixed $data | Dados para armazenar
	 * @return  mixed Retorna os dados em cache caso exista, senão, retorna Null
	 */
	public function getCache($identifier)
	{
		$config = new Config();
		if($config->cacheEnabled){
			CacheManager::setup($config->getCacheConfig());
			$cache = CacheManager::getInstance();
			return $cache->get(md5($identifier));
		}
		return null;
	}
}