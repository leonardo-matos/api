<?php
namespace API\Core\Configure;

GLOBAL $SISCONF;

$SISCONF['API']['URL_BASE'] = "http://localhost:8080";
// $SISCONF['API']['URL_BASE'] = "http://10.62.7.248/api/source";

GLOBAL $ARRLOG;
$ARRLOG	= array();

class Config
{
	// Configuração de acesso ao banco MYSQL
	public $dsn = 'mysql:dbname=deliver;host=localhost';
	public $username = 'root';
	public $password =  '';

	// Configuração de acesso ao banco SQL SERVER
	// public $dsn_sqlserver = "sqlsrv:Server=192.168.2.3;Database=Profissionais_OABRS"; // conexão no WINDOWS. Este ip não funciona quando o prjeto está no docker
	public $dsn_sqlserver = "sqlsrv:Server=192.168.254.231;Database=Implanta_OABRS"; // conexão no WINDOWS
	// public $dsn_sqlserver = "dblib:host=192.168.254.231;dbname=Implanta_OABRS"; // LINUX
	public $username_sqlserver	= "sa";
	public $password_sqlserver	= 'sdja@esdFJ*!2';

	// Configuração de acesso ao banco ORACLE
	public $dsn_oracle		= '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=ip do banco)(PORT=XXXX)))(CONNECT_DATA=(SID=XXXX)))';
	public $username_oracle	= 'XXXXX';
	public $password_oracle	= 'XXXXX';
	
	public $cacheEnabled	= true;

	public $cacheTime	= 3600;
	
	public function getRequestURL($param='')
	{
		$protocol	= (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT']	== 443) ? "https://" : "http://";
		$domainName	= $_SERVER['HTTP_HOST'];
		$url	= parse_url($_SERVER['REQUEST_URI']);
		return $protocol.$domainName.$url['path'].(!empty($param)?'/'.$param:'');
	}
	
	public function getCacheConfig()
	{
		return array(
			"storage" 	=>  "files",
			"overwrite"	=>  "files",
			"caching_method"	=> 2,
			"htaccess"	=>  true, // .htaccess protect
		);
	}

	public static function isDeveloper()
	{
		$ipAccessMachine	= $_SERVER['REMOTE_ADDR'];
		$arrIpsDevelopers	= array();
		
		// $arrIpsDevelopers[]	= '::1';
		// $arrIpsDevelopers[]	= '172.23.240.1';

		if(in_array($ipAccessMachine,$arrIpsDevelopers)){
			return true;
		}

		return false;
	}
}