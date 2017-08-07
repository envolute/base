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

// SYSTEM VARIABLES
require(__DIR__.'/../_system.vars.php');

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
			if ($nome[$i] == "a" or $nome[$i] == "e" or $nome[$i] == "o" or $nome[$i] == "de" or $nome[$i] == "do" or $nome[$i] == "da" or $nome[$i] == "dos" or $nome[$i] == "das") {
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

	// RETORNA UMA FRASE NO SINGULAR OU PLURAL A PARTIR DE UM DETERMINADO VALOR
	// $count INT -> contados
	// $singular STR -> frase no singular. Ex: '%s mês'
	// $plural STR -> frase no plural. Ex: '%s meses'
	// $sufix STR -> sufixo opcional para a frase. Ex: '%s meses'.[ e].'%s dias'
	public static function pluralize($count, $singular, $plural, $sufix = '') {
		if($count > 0) return str_replace('%s', $count, ($count == 1 ? $singular : $plural)).$sufix;
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
			$image = _CORE_.'/helpers/thumbnail/thumbnail.php?file='._BASE_.DS.htmlspecialchars($img).'&w='.$w.'&h='.$h;
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
		$s = (int)$str;
		return ($s > 0 && $s <= 12) ? $m[$s] : '';

	}

	// FORMATA A DATA
	// Default -> DD/MM/YYYY
	public static function dateFormat($date, $format = 'd/m/Y', $showZero = true, $zeroFormat = '00/00/0000') {
		if(!empty($date) && $date != 0 && strpos($date, '0000') === false)
		return date($format, strtotime($date));
		else return ($showZero ? $zeroFormat : '');
	}

	// FORMATA A DATA PARA INCLUSÃO NO BANCO DE DADOS
	// De -> DD/MM/YYYY :: Para -> YYYY-MM-DD
	public static function dateToSql($date) {
		if(strpos($date, '-') === 2 || strpos($date, '/') === 2 || strpos($date, '.') === 2) :
			$dt = array();
	    if(strpos($date, '-') === 2) $dt = explode('-', $date);
	    if(strpos($date, '/') === 2) $dt = explode('/', $date);
	    if(strpos($date, '.') === 2) $dt = explode('.', $date);
	    return (!$db) ? $dt[2].'-'.$dt[1].'-'.$dt[0] : '0000-00-00';
	  else :
	    return $date;
	  endif;
	}

	// DIFERENÇA ENTRE DATAS
	public static function dateDiff($startDate, $endDate = 'now') {
		$dIni	= new DateTime($startDate);
		$dEnd	= new DateTime($endDate);
		$diff = $dIni->diff($dEnd);
		$rDiff = $dIni->diff($dEnd->sub(new DateInterval('P'.$diff->y.'Y'.$diff->m.'M')));
		// valores dependentes. Ex: '4' anos '2' meses e '8' dias
		$data = array(
			'd' => $rDiff->days, // dias sem considerar os meses. Ex: de 10 à 18 = 8 dias
			'D' => $diff->days, // todos os dias. Ex: 480 dias
			'm' => $diff->m, // meses sem considerar dos anos. Ex: de Nov à Jan = 3 meses
			'M' => ($diff->y * 12) + $diff->m, // todos os meses. Ex: 18 meses
			'y' => $diff->y // anos
		);
		return $data;
	}

	// DIFERENÇA ENTRE HORAS
	// Retorna um Array com base na hora de início e fim
	// Ex: $timer = timeDiff('08:40:00', '10:10:00');
	// $timer['time'] => '01:30:00' (o tempo do início até o fim)
	// $timer['seconds'] => '68048' (o tempo em segundos)
	// $timer['minutes'] => '428' (o tempo em minutos)
	// $timer['hours'] => '1.5' => 01:30:00 (expresão númérica do tempo, utilizada para o cálculo)
	public static function timeDiff($start, $end) {
		$sec = strtotime($end) - strtotime($start); // seconds
		$min = $sec / 60; // minutes
		$hs = $min / 60; // 1,5 hs
		$dt = array(
			'time' => date('H:i:s', mktime(0, 0, $sec)),
			'seconds' => $sec,
			'minutes' => $min,
			'hours' => $hs
		);
		return $dt;
	}

	// CALCULA A IDADE A PARTIR DE UMA DATA
	// IMPORTANTE: $date -> YYYY-MM-DD
	public static function getAge($date) {
		$age = !empty($date) ? self::dateDiff($date) : 0;
		return $age['y'];
	}

	// FORMATA O PREÇO
	// Default -> 0.000,00
	public static function priceFormat($price, $usFormat = false, $prefix = '', $showZero = true, $zeroFormat = '0,00') {
		if(!empty($price) && $price != 0 && $price != '0,00' && $price != '0.00') :
			if($usFormat) $val = $prefix.number_format($price, 2, '.', ',');
			else $val = $prefix.number_format($price, 2, ',', '.');
			return $val;
		else :
			return ($showZero ? $zeroFormat : '');
		endif;
	}

	// COMPLETA O VALOR COM CARACTERES ADICIONAIS
	// Caractere default é: char = '0'
	public static function lengthFixed($str, $length, $char = '0', $placement = 'before') {
		// length
		$strlen = strlen($str);
		if(!empty($str) && !empty($length) && $strlen > 0 && $strlen < $length) :
			$e = $length - $strlen;
			$x = '';
			for($i = 0; $i < $e; $i++) $x .= $char;
			$res = ($placement == 'before') ? ($x.$str) : ($str.$x);
			return $res;
		else :
			return $str;
		endif;
	}

	// GERADOR DE SENHA RANDOMICA
	public static function randomPassword($length = 8, $strong = false) {
		if($strong) :
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
		else :
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		endif;
		return substr(str_shuffle($chars), 0, $length);
	}

	// DOMÍNIO DO SITE
	public static function getDomain() {
		return str_replace(_BASE_, '', _ROOT_);
	}

	// ADD PARÂMETRO NA URL
	// $url: url base -> geralmente JURI::current()
	// $param: o parâmetro que será passado
	// $queryString: Caso existam outros parâmetros na URL, define de eles serão mantidos 'true' ou 'false' para não manter
	public static function setUrlParam($url, $param, $queryString = true) {
		$qStr = $amp = '';
		if($queryString) :
			if(!empty($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != $param) $amp = '&';
			$qStr = str_replace($param.$amp, '', $_SERVER['QUERY_STRING']);
		endif;
		$qStr = '?'.$param.$amp.$qStr;
		return $url.$qStr;
	}

	// ESTADOS BRASILEIROS
	public static function getBrazilianStates() {
		$states = array(
			'sigla' => array('AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'),
			'estado' => array('Acre', 'Alagoas', 'Amapá', 'Amazonas', 'Bahia', 'Ceará', 'Distrito Federal', 'Espírito Santo', 'Goiás', 'Maranhão', 'Mato Grosso', 'Mato Grosso do Sul', 'Minas Gerais', 'Pará', 'Paraíba', 'Paraná', 'Pernambuco', 'Piauí', 'Rio de Janeiro', 'Rio Grande do Norte', 'Rio Grande do Sul', 'Rondônia', 'Roraima', 'Santa Catarina', 'São Paulo', 'Sergipe', 'Tocantins')
		);
		return $states;
	}

	// MAIL TEMPLATE DEFAULT
	// template default para envio de e-mails
	// $header / $body / $container são arrays com informações sobre estilo do template
	// Ex: $header('bg' => '#fff', 'color' => '#444', border => 'border: 1px solid #ddd');
	public static function mailTemplateDefault($content, $title = '', $footer = '', $logo = '', $box = array(), $head = array(), $body = array()) {

		// Container style
		$boxBg			= '#f8f8f8';
		$boxColor		= '#555';
		$boxBorder		= 'border: 1px solid #ddd';
		if(count($box)) :
			$boxBg		= (isset($box['bg']) && !empty($box['bg'])) ? $box['bg'] : $boxBg;
			$boxColor	= (isset($box['color']) && !empty($box['color'])) ? $box['color'] : $boxColor;
			if(isset($box['border']) && !empty($box['border'])) :
				$b = (strpos($box['border'], 'border') === false) ? 'border: ' : '';
				$boxBorder = $b.$box['border'];
			else :
				$boxBorder;
			endif;
		endif;
		// Header style
		$headBg			= '#fff';
		$headColor		= '#444';
		$headBorder		= 'border-bottom: 1px solid #ddd';
		if(count($head)) :
			$headBg		= (isset($head['bg']) && !empty($head['bg'])) ? $head['bg'] : $headBg;
			$headColor	= (isset($head['color']) && !empty($head['color'])) ? $head['color'] : $headColor;
			if(isset($head['border']) && !empty($head['border'])) :
				$b = (strpos($head['border'], 'border') === false) ? 'border-bottom: ' : '';
				$headBorder = $b.$head['border'];
			else :
				$headBorder;
			endif;
		endif;
		// Body style
		$bodyBg			= '#f8f8f8';
		$bodyColor		= $boxColor;
		$bodyBorder		= 'border: 1px solid #fff';
		if(count($body)) :
			$bodyBg		= (isset($body['bg']) && !empty($body['bg'])) ? $body['bg'] : $bodyBg;
			$bodyColor	= (isset($body['color']) && !empty($body['color'])) ? $body['color'] : $bodyColor;
			if(isset($body['border']) && !empty($body['border'])) :
				$b = (strpos($body['border'], 'border') === false) ? 'border: ' : '';
				$bodyBorder = $b.$body['border'];
			else :
				$bodyBorder;
			endif;
		endif;
		// IMPORTANTE: informar apenas o nome da imagem. Ela deve estar no diretório '/images/template/logos/'
		$brand = ($logo && !empty($logo) && file_exists(_ROOT_.'/images/template/logos/'.$logo)) ? $logo : 'logo-news.png';
		$html = str_replace('<p>','<p style="margin:0 0 15px">', $content);

		$htmlFooter = !empty($footer) ? '<div style="padding:15px; text-align:center; font-size:11px; color:#aaa;">'.$footer.'</div>' : '';
		$html = '<div style="max-width:650px; margin:auto; font-family:arial;"><div style="padding:4px; background-color:'.$boxBg.';"><div style="font-size:13px; color:'.$boxColor.'; '.$boxBorder.'; box-shadow:0 0 4px rgba(0,0,0,0.1);"><div style="padding:15px; font-size:20px; color:'.$headColor.'; '.$headBorder.'; background-color:'.$headBg.';"><table style="width:100%; border-collapse:collapse; border-spacing:0;"><tbody><tr><td style="padding:0; line-height:1;"><img src="'._ROOT_.'/images/template/logos/'.$brand.'" style="vertical-align:bottom;"></td><td style="padding:0; line-height:1; text-align:right;">'.$title.'</td></tr></tbody></table></div><div style="padding:15px; color:'.$bodyColor.'; '.$bodyBorder.'; background-color:'.$bodyBg.';">'.$html.'</div></div></div>'.$htmlFooter.'</div>';
		return $html;

	}

	// CSV TO ARRAY
	public static function csvToArray($file, $separator = ';') {
		if(file_exists($file)) :
			$fp = fopen ($file,"r");
	   		while ($data = fgetcsv($fp, 1000, $separator)) {
				$array[] = $data;
			}
			return $array;
		else :
			return 0;
		endif;
	}

}

?>
