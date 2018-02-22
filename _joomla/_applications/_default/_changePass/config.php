<?php
defined('_JEXEC') or die;
// SYSTEM VARIABLES
require(JPATH_BASE.'/libraries/envolute/_system.vars.php');

// App Configuration's Vars
$cfg = array();
// App Define
$APPNAME		= '_changePass';
$MAIN_TB		= '#__users';
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
	$cfg['isPublic']			= 0; // Público -> acesso aberto a todos
	// Restrict Access
	// $cfg['groupId']['viewer'][]  = apenas visualiza o componente
	// $cfg['groupId']['admin'][]   = administra o componente
	// OBS: Se o acesso não for público e os dois grupos abaixo forem '0'
	// O acesso é permitido para qualquer usuário LOGADO
	// ----------------------------------------------------
	$cfg['groupId']['viewer'][]	= 0; // '0' pois não pode ser vazio
	// acesso liberado sempre
	$cfg['groupId']['admin'][]	= 0; // '0' pois não pode ser vazio
	// ----------------------------------------------------

// crud's name
	$cfg['APPNAME']				= $APPNAME;
	$cfg['APPTAG']				= $APPTAG;

// crud's main table
	$cfg['mainTable']			= $MAIN_TB;

// Save Function
// fuction called after save action
	$cfg['saveTrigger']			= '';

// view
	// show app (Add Button & List)
	$isModal = isset(${$APPTAG.'Modal'}) ? ${$APPTAG.'Modal'} : true;

?>
