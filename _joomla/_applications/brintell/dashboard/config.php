<?php
defined('_JEXEC') or die;
// SYSTEM VARIABLES
require(JPATH_BASE.'/libraries/envolute/_system.vars.php');

// App Configuration's Vars
$cfg = array();
$cfg['project']	= 'brintell';
$cfg['parent']	= '';
// App Define
$APPNAME		= 'dashboard';
$APPPATH		= !empty($cfg['parent']) ? $cfg['parent'] : $APPNAME;
$MAIN_TB		= '';
$APPTAG			= isset(${$APPNAME.'AppTag'}) ? ${$APPNAME.'AppTag'} : $APPNAME;
$newInstance	= ($APPTAG != $APPNAME) ? true : false;

// URL's {http://...}
$URL_APP        = $_APPS.$APPNAME;
$URL_APP_FILE   = $URL_APP.DS.$APPNAME;
// PATH's {public_html/SITE/...}
$PATH_APP       = JPATH_APPS.DS.$APPNAME;               // Caminho do APP {../docs}
$PATH_APP_FILE  = JPATH_APPS.DS.$APPNAME.DS.$APPNAME;   // Caminho do arquivo default do app {../docs/docs...}

// carrega o arquivo de tradução
$lang = JFactory::getLanguage();
$lang->load('base_apps', JPATH_BASE, $lang->getTag(), true);
$lang->load('base_'.$APPNAME, JPATH_BASE, $lang->getTag(), true);
// GLOBAL VARS
if(!$ajaxRequest && (!isset($_SESSION[$APPTAG.'langDef']) || (isset($_SESSION[$APPTAG.'langDef']) && $_SESSION[$APPTAG.'langDef'] != $lang->getTag()))) :
	if(isset($_SESSION[$APPTAG.'langDef'])) unset($_SESSION[$APPTAG.'langDef']);
	$_SESSION[$APPTAG.'langDef'] = $lang->getTag(); // define a language
endif;

// Crud's permissions

	$cfg['isPublic']			= 1; // Público -> acesso aberto a todos

// Restrict Access

	$cfg['groupId']['manager'][]			= 11; // Brintell Manager
	$cfg['groupId']['analyst'][]			= 12; // Brintell Analyst
	$cfg['groupId']['developer'][]			= 13; // Client Developer
	$cfg['groupId']['external'][]			= 14; // External
	$cfg['groupId']['clientManager'][]		= 15; // Client Manager
	$cfg['groupId']['client'][]				= 16; // Client Developer

?>
