<?php
// SYSTEM VARIABLES
// Variáveis 'globais' definidas para as aplicações do sistema BASE
// Esse arquivo é carregado no arquivo 'config.php' de cada aplicação
if(!isset($baseDir)) :

  // 'Base System' Folder Name
  $baseDir = 'libraries/envolute';
  $appsDir = 'base-apps';

  // Directory Separator
  if(!defined('DS')) define( 'DS', DIRECTORY_SEPARATOR );

  // 'Site' Folder
  $_ROOT = isset($_ROOT) ? $_ROOT : JURI::root();      // -> {http://.../}
  $_BASE = isset($_BASE) ? $_BASE : JURI::root(true);  // -> {/...}
  // 'Base System' Folder
  $_CORE = isset($_CORE) ? $_CORE : $_ROOT.$baseDir.DS;
  $_APPS = isset($_APPS) ? $_APPS : $_ROOT.$appsDir.DS;
  // 'PATH': public_html/SITE/...
  if(!defined('JPATH_CORE')) define('JPATH_CORE', JPATH_BASE.DS.$baseDir);
  if(!defined('JPATH_APPS')) define('JPATH_APPS', JPATH_BASE.DS.$appsDir);

endif;

// Contants
if(!defined('_ROOT_')) define('_ROOT_', $_ROOT); //-> http://www.../joomla
if(!defined('_BASE_')) define('_BASE_', $_BASE); //-> /joomla
if(!defined('_CORE_')) define('_CORE_', $_CORE);
if(!defined('_APPS_')) define('_APPS_', $_APPS);
if(!defined('_TEMPLATE_')) define('_TEMPLATE_', _BASE_.DS.'templates'.DS.'base');
