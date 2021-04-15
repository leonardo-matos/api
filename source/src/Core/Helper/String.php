<?php
namespace API\Core\Helper;

class String{

	/**
	 * Esta fun��o substitui caracteres especiais pelos seus equivalentes
	 *
	 * @param string $str
	 * @return string
	 */
	public static function normalizeString($str){
 		$a = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', '�', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', '?', '?', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', '?', '?', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', '?', 'O', 'o', 'O', 'o', 'O', 'o', '�', '�', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', '�', '�', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', '�', 'Z', 'z', 'Z', 'z', '�', '�', '?', '�', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');
 		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', '?', 'a', '?', 'e', '?', '?', 'O', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');
 		return str_replace($a, $b, $str);
	}

	/**
	 * Esta fun��o coloca as conjun��es em caixa baixa de nomes compostos
	 *
	 * ex: JO�O DA SILVA para  Jo�o da Silva
	 *
	 * outro exemplo:
	 * CI�NCIA DA COMPUTA��O para Ci�ncia da Computa��o
	 *
	 * @param string $str
	 * @return string
	 */
	public static function formatarNomesCompostos($str){
		//$str = ucwords(mb_strtolower($str,'UTF-8')); //windows
		$str = ucwords(mb_strtolower($str,'ISO-8859-1')); //linux
		$a = array(' Em ',' Da ',' De ', ' E ',' Dos ',' Das ',' Do ',' A ',' Nas ',' Na ',' No ',' Com ',' O ', ' Ii ',' Para ',' Iii ');
		$b = array(' em ',' da ',' de ', ' e ',' dos ',' das ',' do ',' a ',' nas ',' na ',' no ',' com ',' o ', ' II ',' para ', 'III' );
		$str = str_replace($a, $b, $str);
		return $str;
	}

	/**
	 * Esta fun��o formata os numeros para Real (R$)
	 *
	 *
	 * @param string $str
	 * @return string
	 */
	public static function formataMoeda($str){

		return 'R$ '.number_format((double)str_replace(',','.',$str), 2,',', '.');
	}

	/**
	 * Esta fun��o formata os locais do sistema
	 * Pr�dio 1 | Sala 604
	 *
	 * @param string $str
	 * @return string
	 */
	public static function formataPredioSala($str){
			$string = '';
			if(!empty($str)){
				$local =  explode('-',$str);

				if(!empty($local[1])){
					$string .= "Pr�dio: $local[1] |";
				}

				if(!empty($local[0])){
					$string .= "Sala: ".utf8_encode(self::formatarNomesCompostos($local[0]))."";
				}

				if(!empty($local[1]) && !empty($local[0])){
					$string = "Pr�dio: $local[1] | Sala: ".utf8_encode($local[0])."";
				}

			}else{
				$string = 'Sala n�o definida';
			}
			return $string;

	}


}