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
define('JPATH_CORE', JPATH_BASE.DS.$baseDir);
define('JPATH_APPS', JPATH_CORE.DS.$appsDir);

// LOAD JOOMLA FRAMEWORK
define( '_JEXEC', 1 );
define( '_VALID_MOS', 1 );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );

// Quando o Joomla é carregado manualmente os caminhos passam a ser relativos
// ao diretório do arquivo no qual o joomla é carregado. Dessa forma, a class 'JURI::root()'
// não tráz os valores referente a raíz do site. A solução nesse caso, é criar variáveis que tragam
// o valor relativo à raiz do site. Assim, Caso esse arquivo seja necessário, deve-se usar as variáveis
// '$_ROOT' e '$_BASE' em substituição de 'JURI::root()' e 'JURI::root(true)' respectivamente.
$r = strpos(JURI::root(), $baseDir);
$b = strpos(JURI::root(true), $baseDir);
$_ROOT = ($r === false) ? JURI::root() : substr(JURI::root(), 0, $r);             // -> {http://.../}
$_BASE = ($b === false) ? JURI::root(true) : substr(JURI::root(true), 0, $b - 1); // -> {/...}
$_CORE = $_ROOT.$baseDir.DS;
$_APPS = $_CORE.$appsDir.DS;

?>
