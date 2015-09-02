<?php
/**
 * @copyright  Copyright (C) 2012 Open Source Matters. All rights reserved.
 * @license    GNU/GPL, see LICENSE.php
 * Developed by Ivo Junior.
 */

// no direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();
$doc = JFactory::getDocument();

// contants
define('_ROOT_',JURI::root());
define('_PATH_',JURI::root(true)); //-> /joomla
define('_TPLBASE_',_PATH_.'/templates/'.$app->getTemplate());
define('_CORE_',_TPLBASE_.'/core');

class baseHelper {
	
	// REMOVE ACENTOS DAS PALAVRAS
	public static function removeAcentos($str, $enc = 'UTF-8'){
 
		$acentos = array(
			'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
		        'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
		        'C' => '/&Ccedil;/',
		        'c' => '/&ccedil;/',
		        'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
		        'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
		        'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
		        'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
		        'N' => '/&Ntilde;/',
		        'n' => '/&ntilde;/',
		        'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
		        'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
		        'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
		        'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
		        'Y' => '/&Yacute;/',
		        'y' => '/&yacute;|&yuml;/',
		        'a.' => '/&ordf;/',
		        'o.' => '/&ordm;/'
		);

        	return preg_replace($acentos, array_keys($acentos), htmlentities($str,ENT_NOQUOTES, $enc));
	}	
	
	// FORMATA OS NOMES DE PESSOAS
	public static function nameFormat($nome,$limite = NULL) {
		$nome = mb_strtolower($nome, 'UTF-8'); // Converter o nome todo para minúsculo
		$nome = explode(" ", $nome); // Separa o nome por espaços
		$saida = "";
		for ($i=0; $i < count($nome); $i++) {
		
			// Tratar cada palavra do nome
			if ($nome[$i] == "e" or $nome[$i] == "de" or $nome[$i] == "do" or $nome[$i] == "da" or $nome[$i] == "dos" or $nome[$i] == "das") {
				// Se a palavra estiver dentro das complementares mostrar toda em minúsculo
				$saida .= $nome[$i].' ';
			}else if ($nome[$i] == "ii" or $nome[$i] == "iii") {
				// Se a palavra estiver dentro das complementares mostrar toda em maiúsculo
				$saida .= strtoupper($nome[$i]).' ';
			}else {
				// Se for um nome, mostrar a primeira letra maiúscula
				$saida .= ucfirst($nome[$i]).' ';
			}
		
		}
		
		// tamanho da string
		$saida = self::textLimit($saida,$limite);
		
		// IMPORTANTE
		// palavras entre chaves "[]" ficam sempre maiúsculas -> ex.: siglas [adsl] = ADSL
		// obs: as chaves "[]" são retiradas, para mantê-los adicione parenteses internos. ex: [[ADSL]]
		preg_match('/\\[(.*)\\]/s', $saida ,$matches);
		
		if(isset($matches[0]) && isset($matches[1]))
		$saida = str_replace($matches[0],strtoupper($matches[1]),$saida);
		
		return $saida;
	}
	
	// LIMITA O TAMANHO DA STRING E ACRESCENTA '...' CASO A STRING SEJA MAIOR QUE O TAMANHO PERMITIDO.
	public static function textLimit($str,$max = NULL) {
		$txt = trim($str);
		// se 'max' não for passado, retorna a string inteira...
		if(!is_null($max)) {
			$txt = (mb_strlen($txt, 'UTF-8') > $max) ? mb_substr($txt,0,$max).'...' : $txt;
		}
		return $txt;
	}	
	
	// FORMATA UMA STRING PARA SERVIR COMO NOME DE CLASSE
	public static function strToClassName($str) {
		$str = mb_strtolower($str, 'UTF-8'); // Converter o nome todo para minúsculo
		$str = self::removeAcentos($str); // remove acentos
		$str = str_replace(" ", "", $str); // remove os espaços
		
		return $str;
	}

	// GERA O THUMBNAIL
	public static function thumbnail($img,$w,$h) {
		
		$image = htmlspecialchars($img);
		
		// Se for informada a largura e altura, será criado um thumbnail. Senão, carrega a imagem original...
		// verifica também se NÃO é uma imagem remota, ou acessada em outro servidor
		if($w && $h && strpos($img,'://') === false) :
			$image = _CORE_.'/helpers/thumbnail/thumbnail.php?file='._PATH_.'/'.htmlspecialchars($img).'&w='.$w.'&h='.$h;
		endif;
		
		return $image;
	}	
	
	// FORMATA OS NOMES DOS MESES
	public static function getMonthName($str,$full = true) {
		
		if($full) {
			$m = array('','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
		} else {
			$m = array('','Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez');
		}
		
		switch($str) {
			case '01':
				$saida = $m[1];
				break;
			case '02':
				$saida = $m[2];
				break;
			case '03':
				$saida = $m[3];
				break;
			case '04':
				$saida = $m[4];
				break;
			case '05':
				$saida = $m[5];
				break;
			case '06':
				$saida = $m[6];
				break;
			case '07':
				$saida = $m[7];
				break;
			case '08':
				$saida = $m[8];
				break;
			case '09':
				$saida = $m[9];
				break;
			case '10':
				$saida = $m[10];
				break;
			case '11':
				$saida = $m[11];
				break;
			case '12':
				$saida = $m[12];
				break;
		}
		
		return $saida;
	}	
	
	// CALCULA A IDADE A PARTIR DE UMA DATA
	// IMPORTANTE: $date -> DD/MM/YYYY
	public static function getAge($date) {
		
		$dt = str_replace('-','/',$date);
		$dt = str_replace('.','/',$dt);
		$pos = strpos($dt, '/');
		if($pos == 4) {
			$d = explode('/',$dt);
			$dt = $d[2].'/'.$d[1].'/'.$d[0];
		}
		if(!empty($dt) && $pos !== false && $dt != '00/00/0000' && $dt != '0000/00/00') :
			$birthDate = explode("/", $dt);
			//get age from date or birthdate
			$age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
		endif;
		
		return $age;
	}

}

?>