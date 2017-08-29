<?php
// SYSTEM VARIABLES
// Variáveis 'globais' definidas para as aplicações do sistema BASE
// Esse arquivo é carregado no arquivo 'config.php' de cada aplicação

// Definição de datas em português
setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
date_default_timezone_set('America/Recife');

if(!isset($baseDir)) :

	// 'Base System' Folder Name
	$baseDir = 'libraries/envolute';
	$appsDir = 'base-apps';

	// Directory Separator
	if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

	// ROOT
	$r = strpos(JURI::root(), $baseDir);					//	Verifica se esta em uma biblioteca
	if($r === false) $r = strpos(JURI::root(), $appsDir);	//	Verifica se esta em uma aplicação
	// remove, da URL, os diretórios do sistema (libs e apps) -> {http://.../}
	$_ROOT = ($r === false) ? JURI::root() : substr(JURI::root(), 0, $r);
	// BASE
	$b = strpos(JURI::root(true), $baseDir);				//	Verifica se esta em uma biblioteca
	if($b === false) $b = strpos(JURI::root(), $appsDir);	//	Verifica se esta em uma aplicação
	// remove, da URL, os diretórios do sistema (libs e apps) -> {/...}
	$_BASE = ($b === false) ? JURI::root(true) : substr(JURI::root(true), 0, $b - 1);
	// CORE (libraries/...)
	$_CORE = isset($_CORE) ? $_CORE : $_ROOT.$baseDir.DS;
	if(!defined('JPATH_CORE')) define('JPATH_CORE', JPATH_BASE.DS.$baseDir); // 'PATH': public_html/SITE/...
	// APPs (base-apps/...)
	$_APPS = isset($_APPS) ? $_APPS : $_ROOT.$appsDir.DS;
	if(!defined('JPATH_APPS')) define('JPATH_APPS', JPATH_BASE.DS.$appsDir); // 'PATH': public_html/SITE/...

	// Contants
	if(!defined('_ROOT_')) define('_ROOT_', $_ROOT); //-> http://www.../joomla
	if(!defined('_BASE_')) define('_BASE_', $_BASE); //-> /joomla
	if(!defined('_CORE_')) define('_CORE_', $_CORE);
	if(!defined('_APPS_')) define('_APPS_', $_APPS);
	if(!defined('_TEMPLATE_')) define('_TEMPLATE_', _BASE_.DS.'templates'.DS.'base');

endif;
