<?php
defined('_JEXEC') or die;
// SYSTEM VARIABLES
require(JPATH_BASE.'/libraries/envolute/_system.vars.php');

// App Configuration's Vars
$cfg = array();
$cfg['project'] = 'apcefpb';
// Caso essa aplicação utilize dados de outra (PAI).
// informe o $APPNAME da outra. Senão, deixe em branco...
$cfg['parent']	= 'clients';
// App Define
$APPNAME		= 'clientsRegistrations';
$APPPATH		= !empty($cfg['parent']) ? $cfg['parent'] : $APPNAME;
$MAIN_TB		= '#__'.$cfg['project'].'_clients';
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

// get user type default in registration
$_SESSION[$APPTAG.'newUsertype'] = 11; // default 'Associado->efetivo'

// get user type default in registration
$_SESSION[$APPTAG.'cardLimit'] = '300,00'; // default

// form action config
	$cfg['isEdit']				= isset(${$APPTAG.'IsEdit'}) ? ${$APPTAG.'IsEdit'} : false;

// Crud's permissions

	$cfg['isPublic']			= 0; // Público -> acesso aberto a todos
	// Registra os parâmetros na session para os arquivos 'ajax'
	if(!$ajaxRequest && isset(${$APPTAG.'IsPublic'})) $_SESSION[$APPTAG.'IsPublic'] = ${$APPTAG.'IsPublic'};
	// Caso os parâmtros sejam enviados, redefine a variável
	if(isset($_SESSION[$APPTAG.'IsPublic']) && $_SESSION[$APPTAG.'IsPublic']) $cfg['isPublic'] = $_SESSION[$APPTAG.'IsPublic'];

// Restrict Access

	// Acesso default, quando não for definido no componente ou módulo
	$viewerDef	= array(11,12,13); // 'default' apenas visualiza o componente. IMPORTANTE: não deve ser vazio. Então => '0'
	$authorDef	= array(); // 'default' cria, mas só edita ou deleta o seu. IMPORTANTE: não deve ser vazio. Então => '0'
	$editorDef	= array(); // 'default' cria e edita, mas só deleta o seu. IMPORTANTE: não deve ser vazio. Então => '0'
	$adminDef	= array(6,7,8); // 'default' Gerente, Administrador, Desenvolvedor
	// Registra os parâmetros na session para os arquivos 'ajax'
	if(!$ajaxRequest) {
		$_SESSION[$APPTAG.'ViewerGroups'] = (isset(${$APPTAG.'ViewerGroups'}) && count(${$APPTAG.'ViewerGroups'})) ? array_unique(array_merge($viewerDef,${$APPTAG.'ViewerGroups'})) : $viewerDef;
		$_SESSION[$APPTAG.'AuthorGroups'] = (isset(${$APPTAG.'AuthorGroups'}) && count(${$APPTAG.'AuthorGroups'})) ? array_unique(array_merge($authorDef,${$APPTAG.'AuthorGroups'})) : $authorDef;
		$_SESSION[$APPTAG.'EditorGroups'] = (isset(${$APPTAG.'EditorGroups'}) && count(${$APPTAG.'EditorGroups'})) ? array_unique(array_merge($editorDef,${$APPTAG.'EditorGroups'})) : $editorDef;
		$_SESSION[$APPTAG.'AdminGroups'] = (isset(${$APPTAG.'AdminGroups'}) && count(${$APPTAG.'AdminGroups'})) ? array_unique(array_merge($adminDef,${$APPTAG.'AdminGroups'})) : $adminDef;
	}
	// ----------------------------------------------------
	$cfg['groupId']['viewer']	= isset($_SESSION[$APPTAG.'ViewerGroups']) ? $_SESSION[$APPTAG.'ViewerGroups'] : $viewerDef;
	$cfg['groupId']['author']	= isset($_SESSION[$APPTAG.'AuthorGroups']) ? $_SESSION[$APPTAG.'AuthorGroups'] : $authorDef;
	$cfg['groupId']['editor']	= isset($_SESSION[$APPTAG.'EditorGroups']) ? $_SESSION[$APPTAG.'EditorGroups'] : $editorDef;
	$cfg['groupId']['admin']	= isset($_SESSION[$APPTAG.'AdminGroups']) ? $_SESSION[$APPTAG.'AdminGroups'] : $adminDef;

// crud's name
	$cfg['APPNAME']				= $APPNAME;
	$cfg['APPTAG']				= $APPTAG;

// crud's main table
	$cfg['mainTable']			= $MAIN_TB;

// Save Function
// fuction called after save action
	$cfg['saveTrigger']			= '';

// form's

	$cfg['showFormToolbar']		= true;

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
	endif;

?>
