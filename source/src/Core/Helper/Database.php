<?php
namespace API\Core\Helper;

use FluentPDO;
use PDO;
use API\Core\Configure\Config;

class Database{

	public $mysql;

	public $sqlserver;

	// conexão com o mysql
	public function connectDb(){
		$config = new \API\Core\Configure\Config();
		$pdo = new PDO($config->dsn, $config->username, $config->password);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->mysql = $pdo;
	}

	public function queryMySql($sqlText, $type='consultar', $fetchType='fetchArray'){
		$arrReturn = array();
		$config    = new Config();
		$this->connectDb();
		
		switch($type){
			case 'consultar':
				$statement = $this->mysql->prepare($sqlText);
				$statement->execute();
				
				switch($fetchType){
					case 'fetchArray':
						return $statement->fetchAll();
					break;
					
					case 'fetchRow':
						return $statement->fetch();
					break;
				}
			break;
			
			case 'executar':
			case 'inserir':
			case 'atualizar':
			case 'deletar':
				
				$statement = $this->mysql->beginTransaction();
				
				try{
					$statement = $this->mysql->prepare($sqlText);
					$statement->execute();
					$this->mysql->commit();
				}
				catch(Exception $e){
					$this->mysql->rollBack();
					$error = $e->getMessage();
					if($config->isDeveloper()){
						print_r($error);
					}
					return false;
				}
				return true;
			break;
		}
	}

	// conexão com o sql server
	public function connectDbSqlServer(){
		$config = new \API\Core\Configure\Config();
		$pdo = new PDO($config->dsn_sqlserver,$config->username_sqlserver,$config->password_sqlserver);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->sqlserver = $pdo;
	}

	public function querySqlServer($sqlText,$type='consultar',$fetchType='fetchArray'){ 
		$arrReturn = array();
		$config    = new Config();
		$this->connectDbSqlServer();
		
		switch($type){
			case 'consultar':
				$statement = $this->sqlserver->prepare($sqlText);
				$statement->execute();
				
				switch($fetchType){
					case 'fetchArray':
						return $statement->fetchAll();
					break;
					
					case 'fetchRow':
						return $statement->fetch();
					break;
				}
			break;
			
			case 'executar':
			case 'inserir':
			case 'atualizar':
			case 'deletar':
				$statement = $this->sqlserver->beginTransaction();
				try{

					$statement = $this->sqlserver->prepare($sqlText);


					$statement->execute();
					$this->sqlserver->commit();
				}
				catch(Exception $e){
					$this->sqlserver->rollBack();


					$error = $e->getMessage();
					
					if($config->isDeveloper()){
						print_r($error);
					}
					return false;
				}
				
				return true;
			break;
		}
	}

	public function connectDbOracle(){
		$config = new \API\Core\Configure\Config();
		$this->oracle = @oci_connect($config->username_oracle,$config->password_oracle,$config->dsn_oracle,'WE8ISO8859P1');
	}
	
	// conexão com o sql server
	public function queryOracle($sqlText,$type='consultar',$fetchType='fetchArray'){
		$arrReturn = array();
		$config    = new Config();
		$this->connectDbOracle();
		$idQuestao = 0;

		if($type == 'executar'){
			$statement	  = oci_parse($this->oracle,'begin '.$sqlText.' ; end;');
			$executeState = oci_execute($statement,OCI_NO_AUTO_COMMIT);
		}else if($type == 'execProcedureComRetorno'){
			$statement	  = oci_parse($this->oracle, $sqlText);
			oci_bind_by_name($statement,":p_idQuestao",$idQuestao,80,SQLT_CHR);
			$executeState = oci_execute($statement,OCI_NO_AUTO_COMMIT);
		}else if($type == 'execBlocoLivre'){
			$statement = oci_parse($this->oracle,$sqlText);
			$executeState = oci_execute($statement,OCI_NO_AUTO_COMMIT);
		}else{
			$statement = oci_parse($this->oracle, $sqlText);
			$executeState = oci_execute($statement,OCI_NO_AUTO_COMMIT);
		}
		if(!$executeState){
			$error 	  =  oci_error($statement);

			if($config->isDeveloper()){
				print_r($error);
			}
			return false;
		}else{
			switch($type){
				case 'consultar':
					switch($fetchType){
						case 'fetchArray':
							$arrReturn = array();
							while($row = oci_fetch_array($statement,OCI_ASSOC)){
								$arrReturn[] = $row;
							}
							return $arrReturn;
						break;
						case 'fetchRow':
							return @oci_fetch_assoc($statement);
						break;
					}
				break;
				
				case 'execProcedureComRetorno':
					if(!$executeState){
						oci_rollback($this->oracle);
						oci_close($this->oracle);
						$error =  oci_error($statement);
						if($config->isDeveloper()){
							print_r($error);
						}
						return false;
					}
					oci_commit($this->oracle);
					oci_close($this->oracle);
					return $idQuestao;
				break;
				
				case 'execBlocoLivre':
					if(!$executeState){
						oci_rollback($this->oracle);
						oci_close($this->oracle);
						$error =  oci_error($statement);
						if($config->isDeveloper()){
							print_r($error);
						}
						return false;
					}
					oci_commit($this->oracle);
					oci_close($this->oracle);
					return true;
				break;
				
				case 'executar':
				case 'inserir':
				case 'deletar':
				case 'atualizar':
				case 'atualizar':
					if(!$executeState){
						oci_rollback($this->oracle);
						oci_close($this->oracle);
						$error =  oci_error($statement);
						if($config->isDeveloper()){
							print_r($error);
						}
						return false;
					}
					oci_commit($this->oracle);
					oci_close($this->oracle);
					return true;
				break;
			}
		}
	}
}