<?php
$tmpl = strpos(__DIR__, 'templates/base');
$root = substr(__DIR__, 0, $tmpl);
define('JPATH_BASE', $root);
define('JPATH_SOURCE', $tmpl.'/source');

// LOAD JOOMLA FRAMEWORK
define( '_JEXEC', 1 );
define( '_VALID_MOS', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .DS.'libraries'.DS.'joomla'.DS.'factory.php' );

$__pos = strpos(JURI::root(), 'templates/base');
$_root = ($__pos === false) ? JURI::root() : substr(JURI::root(), 0, $__pos); // 'http://.../base/...'
$__pos = strpos(JURI::root(true), 'templates/base');
$_base = ($__pos === false) ? JURI::root(true) : substr(JURI::root(true), 0, $__pos - 1); // '/base/...'

?>
