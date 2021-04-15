<?php
namespace API\Core\Helper;
use API\Core\Configure\Config;
use PHPMailer;

class Mail{


	/**
	 * @var PHPMailer $this->mail
	 */
	public $mail;

	/**
	 * The Mail function itself sends email
	 *
	 * @param string $to
	 * @param string $subject
	 * @param string $body
	 * @param string $attachment
	 * @param string $from
	 * @return boolean
	 */

	public function __construct($to,$subject,$body,$from,$fromName,$attachment=''){
		$config = new Config();

		$this->mail = new PHPMailer;
		$this->mail->Host = $config->smtp_host;
		$this->mail->Username = $config->smtp_user;
		$this->mail->Password = $config->smtp_pwd;
		$this->mail->SMTPAuth = $config->stmp_auth;
		$this->mail->isSMTP();
		$this->mail->setFrom($from,$fromName);

		//if($config->isDeveloper()){
			//$this->mail->addAddress('leonard-matos@hotmail.com');
		//}else{
			$this->mail->addAddress($to);
		//}
		if(!empty($attachment)){
			$this->mail->addAttachment($attachment);
		}
		$this->mail->isHTML(true);
		$this->mail->Subject = $subject;
		$this->mail->Body    = $body;
		if(!$this->mail->send()) {
			return false;
		}
		return true;
	}


}

