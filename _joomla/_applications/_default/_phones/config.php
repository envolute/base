<?php
defined('_JEXEC') or die;
// SYSTEM VARIABLES
require(JPATH_BASE.'/libraries/envolute/_system.vars.php');

// App Configuration's Vars
$cfg = array();
$cfg['project'] = 'base';
// PARENT APP:
// Caso essa aplicação utilize dados de outra (PAI).
// Informe o $APPNAME da outra. Senão, deixe em branco...
$cfg['parent']	= '';
// App Define
$APPNAME		= '_phones';
$APPPATH		= !empty($cfg['parent']) ? $cfg['parent'] : $APPNAME;
$MAIN_TB		= '#__'.$cfg['project'].'_phones';
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
	$cfg['isPublic']			= false; // Público -> acesso aberto a todos
	// Restrict Access
	// $cfg['groupId']['viewer'][]  = apenas visualiza o componente
	// $cfg['groupId']['admin'][]   = administra o componente
	// ----------------------------------------------------
	$cfg['groupId']['viewer'][]	= 0; // '0' pois não pode ser vazio
	// acesso liberado sempre
	$cfg['groupId']['admin'][]	= 6; // Gerente
	$cfg['groupId']['admin'][]	= 7; // Administrador
	$cfg['groupId']['admin'][]	= 8; // Desenvolvedor
	// ----------------------------------------------------

// crud's name
	$cfg['APPNAME']				= $APPNAME;
	$cfg['APPTAG']				= $APPTAG;

// crud's main table
	$cfg['mainTable']			= $MAIN_TB;

// view

	// show app (Add Button & List)
	$cfg['showApp']				= true;
	if(isset(${$APPTAG.'ShowApp'})) $cfg['showApp'] = ${$APPTAG.'ShowApp'} ? true : false;
	// show/hidden list
	$cfg['showList']			= true;
	if(isset(${$APPTAG.'Showlist'})) $cfg['showList'] = ${$APPTAG.'Showlist'} ? true : false;
	// list modal
	$cfg['listModal']			= false;
	if(isset(${$APPTAG.'ListModal'})) $cfg['listModal'] = ${$APPTAG.'ListModal'} ? true : false;
	// list type
	$cfg['listFull']			= true;
	if(isset(${$APPTAG.'ListFull'})) $cfg['listFull'] = ${$APPTAG.'ListFull'} ? true : false;
	// toolbar position
	$cfg['staticToolbar']		= false;
	if(isset(${$APPTAG.'StaticToolbar'})) $cfg['staticToolbar'] = ${$APPTAG.'StaticToolbar'} ? true : false;
	// show/hidden 'add' button
	$cfg['showAddBtn']			= true;
	if(isset(${$APPTAG.'ShowAddBtn'})) $cfg['showAddBtn'] = ${$APPTAG.'ShowAddBtn'} ? true : false;
	// hidden button 'add' text
	$cfg['addText']				= true;
	if(isset(${$APPTAG.'AddText'})) $cfg['addText'] = ${$APPTAG.'AddText'} ? true : false;
	// show filter opened
	$cfg['showFilter']			= false;
	// list's elements default
	$cfg['pagLimit']			= 20;

	// descriptions
	$cfg['showListDesc']		= false;
	$cfg['showFormDesc']		= false;

	// date & price convertions
	$cfg['dateConvert']			= false;
	$cfg['load_UI']				= $cfg['dateConvert'];
	$cfg['priceDecimal']		= false;
	$cfg['htmlEditor']			= false;
	$cfg['htmlEditorFull']		= false;

	// crud's upload config
	$cfg['hasUpload']			= false;
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
		$cfg['uploadDir']       = JPATH_BASE.DS.'images/apps/'.$APPPATH.'/'; // IMPORTANTE: colocar a barra '/' no fim
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

// crud's relation actions

	if(!$ajaxRequest) :
		// RELATION TAG
		// evita o conflito entre sessões de diferentes carregamento do mesmo APP
		$RTAG = isset(${$APPTAG.'RelTag'}) ? $APPTAG.${$APPTAG.'RelTag'} : $APPTAG;
		// gera parâmetros da nova sessao
		// relation table Name
		$_SESSION[$RTAG.'RelTable']			= isset(${$APPTAG.'RelTable'}) ? ${$APPTAG.'RelTable'} : '';
		// relation table ID name
		$_SESSION[$RTAG.'RelNameId']		= isset(${$APPTAG.'RelNameId'}) ? ${$APPTAG.'RelNameId'} : '';
		// relation table ID default value [optional]
		$_SESSION[$RTAG.'RelId']			= isset(${$APPTAG.'RelId'}) ? ${$APPTAG.'RelId'} : '';
		// ID name of mainTable in relation table
		$_SESSION[$RTAG.'AppNameId']		= isset(${$APPTAG.'AppNameId'}) ? ${$APPTAG.'AppNameId'} : '';
		// list filter
		$_SESSION[$RTAG.'RelListId']		= isset(${$APPTAG.'RelListId'}) ? ${$APPTAG.'RelListId'} : '';
		$_SESSION[$RTAG.'RelListNameId']	= isset(${$APPTAG.'RelListNameId'}) ? ${$APPTAG.'RelListNameId'} : '';
		// show only childs
		$_SESSION[$RTAG.'OnlyChildList']	= isset(${$APPTAG.'OnlyChildList'}) ? ${$APPTAG.'OnlyChildList'} : 0;
		// UPDATE SELECT FIELD
		// select element that will be updated
		$_SESSION[$RTAG.'FieldUpdated']		= (isset(${$APPTAG.'FieldUpdated'}) && !empty(${$APPTAG.'FieldUpdated'})) ? ${$APPTAG.'FieldUpdated'} : '';
		// table field for element updated
		$_SESSION[$RTAG.'TableField']		= (isset(${$APPTAG.'TableField'}) && !empty(${$APPTAG.'TableField'})) ? ${$APPTAG.'TableField'} : '';
		// hide 'parentFieldId'
		$_SESSION[$RTAG.'HideParentField']	= isset(${$APPTAG.'HideParentField'}) ? ${$APPTAG.'HideParentField'} : 0;
	endif;

?>
