<?php
namespace API\Core\Helper;


class Log {

        private $file;
    
		public function __construct($file){
			$this->file = $file;
		}

        public function gravaLogStr($str){
            $dt = (new \DateTime())->format('d/m/Y H:i:s'); 
            $str = $dt.' - '.$str.PHP_EOL;
            file_put_contents('log/'.$this->file.'.txt', $str, FILE_APPEND);
        }

        public function leLog(){
            $str = file_get_contents('log/'.$this->file.'.txt');
            $arrStr = array_filter(explode('\n',$str));
            return $arrStr;
        }
}
