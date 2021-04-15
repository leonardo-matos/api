<?php
namespace API\Core\Helper;


class Image{

    
        public $base64;
		public $mime_type;

		public function __construct($base64,$mime_type) {
			$this->base64 = $base64;
			$this->mime_type = $mime_type;
		}

		public function __get($param){
			if (property_exists($this, $param)) {
				return $this->param;
			}
		}

		public function __set($param, $value){
			$this->param = $value;
		}
    
    
    
    
    	function compress_image($src, $dest , $quality){
    		$info = getimagesize($src);
    
    		if ($info['mime'] == 'image/jpeg'){
    			$image = imagecreatefromjpeg($src);
    		}else if ($info['mime'] == 'image/gif'){
    			$image = imagecreatefromgif($src);
    		}else if ($info['mime'] == 'image/png'){
    			$image = imagecreatefrompng($src);
    		}else{
    			die('Unknown image file format');
    		}
    		imagejpeg($image, $dest, $quality);
    		return $dest;
    	}

}
