<?php
// INIT JOOMLA
// Inicializa 'manualmente' o Joomla Framework
// Obs: Utilizado em arquivos ajax

$baseDir = 'libraries/envolute';
$appsDir = 'base-apps';

// Define o caminho para a 'RAÍZ' do site
define('JPATH_BASE', substr(__DIR__, 0, strpos(__DIR__, $baseDir)));
define( 'DS', DIRECTORY_SEPARATOR );
// 'Core' da Base
define('JPATH_CORE', JPATH_BASE.$baseDir);
define('JPATH_APPS', JPATH_BASE.$appsDir);

// LOAD JOOMLA FRAMEWORK
define( '_JEXEC', 1 );
define( '_VALID_MOS', 1 );
require_once ( JPATH_BASE.DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE.DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'factory.php' );

// Quando o Joomla é carregado manualmente os caminhos passam a ser relativos
// ao diretório do arquivo no qual o joomla é carregado. Dessa forma, a class 'JURI::root()'
// não tráz os valores referente a raíz do site. A solução nesse caso, é criar variáveis que tragam
// o valor relativo à raiz do site. Assim, Caso esse arquivo seja necessário, deve-se usar as variáveis
// '$_ROOT' e '$_BASE' em substituição de 'JURI::root()' e 'JURI::root(true)' respectivamente.
// ROOT
$r = strpos(JURI::root(), $baseDir);					//	Verifica se esta em uma biblioteca
if($r === false) $r = strpos(JURI::root(), $appsDir);	//	Verifica se esta em uma aplicação
// remove, da URL, os diretórios do sistema (libs e apps) -> {http://.../}
$_ROOT = ($r === false) ? JURI::root() : substr(JURI::root(), 0, $r);
// BASE
$b = strpos(JURI::root(true), $baseDir);					//	Verifica se esta em uma biblioteca
if($b === false) $b = strpos(JURI::root(true), $appsDir);	//	Verifica se esta em uma aplicação
// remove, da URL, os diretórios do sistema (libs e apps) -> {/...}
// IMPORTANTE: Segue o besmo padrão de 'JURI::root(true)'
// Ou seja, não tem a barra '/' no fim. Ex: {raíz = ""; subdir = "/subdir"}
$_BASE = ($b === false) ? JURI::root(true) : substr(JURI::root(true), 0, $b - 1);
// CORE (libraries/...)
$_CORE = $_ROOT.$baseDir.DS;
// APPs (base-apps/...)
$_APPS = $_CORE.$appsDir.DS;

// Contants
if(!defined('_ROOT_')) define('_ROOT_', $_ROOT); //-> http://www.../joomla
if(!defined('_BASE_')) define('_BASE_', $_BASE); //-> /joomla
if(!defined('_CORE_')) define('_CORE_', $_CORE);
if(!defined('_APPS_')) define('_APPS_', $_APPS);
if(!defined('_TEMPLATE_')) define('_TEMPLATE_', _BASE_.DS.'templates'.DS.'base');

?>
