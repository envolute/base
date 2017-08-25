<?php
defined('_JEXEC') or die;
// SYSTEM VARIABLES
require(JPATH_BASE.'/libraries/envolute/_system.vars.php');

// App Configuration's Vars
$cfg = array();
$cfg['project'] = 'agecefpb';
// App Define
$APPNAME  = 'clientsRegistrations';
$MAIN_TB  = '#__'.$cfg['project'].'_clients';
$APPTAG   = isset(${$APPNAME.'AppTag'}) ? ${$APPNAME.'AppTag'} : $APPNAME;
$newInstance = ($APPTAG != $APPNAME) ? true : false;

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

// get user type default in registration
$_SESSION[$APPTAG.'newUsertype'] = 2; // default 'Associado'

// get user type default in registration
$_SESSION[$APPTAG.'cardLimit'] = '300,00'; // default

// form action config
	$cfg['isEdit']				= isset(${$APPTAG.'isEdit'}) ? ${$APPTAG.'isEdit'} : false;

// Crud's permissions
	$cfg['isPublic']			= true; // Público -> acesso aberto a todos

// crud's name
	$cfg['APPNAME']				= $APPNAME;
	$cfg['$APPTAG']				= $APPTAG;

// crud's main table
	$cfg['mainTable']			= $MAIN_TB;

	// date & price convertions
	$cfg['dateConvert']			= true;
	$cfg['load_UI']				= $cfg['dateConvert'];
	$cfg['priceDecimal']		= false;
	$cfg['htmlEditor']			= false;
	$cfg['htmlEditorFull']		= false;

// crud's upload config

	$cfg['hasUpload']			= true;
	$cfg['hasUpload'] = isset(${$APPTAG.'hasUpload'}) ? ${$APPTAG.'hasUpload'} : $cfg['hasUpload'];
	// habilita a adição dinamica de novos campos do tipo 'file'
	$cfg['dinamicFiles']		= false;
	// valor inicial do index do arquivo... considerando '0' o campo estático
	// caso exitam outros campos estáticos, o index será igual ao número de itens estáticos
	// Ex: 4 itens estáticos => $cfg['indexFileInit'] = 4;
	$cfg['indexFileInit']		= 1;

	if($cfg['hasUpload']) :
		$cfg['fileField']		= 'file'; // upload's field name
		$cfg['fileTable']		= $cfg['mainTable'].'_files'; // upload's database table
		// upload params
		$cfg['maxFileSize']		= 5242880; // 5MB
		$cfg['uploadDir']       = JPATH_BASE.DS.'images/apps/'.$APPNAME.'/'; // IMPORTANTE: colocar a barra '/' no fim
		// file types enabled
		$cfg['fileTypes']	= array();
		$cfg['fileTypes']['image'][]	= 'image/png';
		$cfg['fileTypes']['image'][]	= 'image/gif';
		$cfg['fileTypes']['image'][]	= 'image/jpeg';
		$cfg['fileTypes']['image'][]	= 'image/pjpeg';
		$cfg['fileTypes']['file'][]		= 'text/plain';
		$cfg['fileTypes']['file'][]		= 'text/html';
		$cfg['fileTypes']['file'][]		= 'application/x-zip-compressed';
		$cfg['fileTypes']['file'][]		= 'application/x-compressed';
		$cfg['fileTypes']['file'][]		= 'application/zip';
		$cfg['fileTypes']['file'][]		= 'application/x-zip';
		$cfg['fileTypes']['file'][]		= 'application/pdf';
		$cfg['fileTypes']['file'][]		= 'application/msword';
		$cfg['fileTypes']['file'][]		= 'application/excel';
		$cfg['fileTypes']['file'][]		= 'application/vnd.ms-excel';
		$cfg['fileTypes']['file'][]		= 'application/x-excel';
		$cfg['fileTypes']['file'][]		= 'application/x-msexcel';
	endif;

?>
