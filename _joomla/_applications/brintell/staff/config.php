<?php
defined('_JEXEC') or die;
// SYSTEM VARIABLES
require(JPATH_BASE.'/libraries/envolute/_system.vars.php');

// App Configuration's Vars
$cfg = array();
$cfg['project'] = 'brintell';
// PARENT APP:
// Caso essa aplicação utilize dados de outra (PAI).
// Informe o $APPNAME da outra. Senão, deixe em branco...
$cfg['parent']	= '';
// App Define
$APPNAME		= 'staff';
$APPPATH		= !empty($cfg['parent']) ? $cfg['parent'] : $APPNAME;
$MAIN_TB		= '#__'.$cfg['project'].'_staff';
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

// Brintell Access Levels
// 11 => Manager
// 12 => Analyst
// 13 => Developer
$cfg[$APPTAG.'AccessLevel']['brintell']	= '11, 12, 13';
// 14 => External
$cfg[$APPTAG.'AccessLevel']['external']	= '14';

// Crud's permissions

	$cfg['isPublic']			= 0; // Público -> acesso aberto a todos
	// Registra os parâmetros na session para os arquivos 'ajax'
	if(!$ajaxRequest && isset(${$APPTAG.'IsPublic'})) $_SESSION[$APPTAG.'IsPublic'] = ${$APPTAG.'IsPublic'};
	// Caso os parâmtros sejam enviados, redefine a variável
	if(isset($_SESSION[$APPTAG.'IsPublic']) && $_SESSION[$APPTAG.'IsPublic']) $cfg['isPublic'] = $_SESSION[$APPTAG.'IsPublic'];

// Restrict Access

	// Acesso default, quando não for definido no componente ou módulo
	$viewerDef	= array(); // 'default' apenas visualiza o componente. IMPORTANTE: não deve ser vazio. Então => '0'
	$authorDef	= array(); // 'default' cria, mas só edita ou deleta o seu. IMPORTANTE: não deve ser vazio. Então => '0'
	$editorDef	= array(); // 'default' cria e edita, mas só deleta o seu. IMPORTANTE: não deve ser vazio. Então => '0'
	$adminDef	= array(8, 11); // 'default' Desenvolvedor, Brintell Manager
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
	// ----------------------------------------------------
	$cfg['groupId']['client'][]	= 15;	// Client
	$cfg['groupId']['client'][]	= 16;	// Client Manager

// crud's name
	$cfg['APPNAME']				= $APPNAME;
	$cfg['APPTAG']				= $APPTAG;

// crud's main table
	$cfg['mainTable']			= $MAIN_TB;

// Save Function
// fuction called after save action
	$cfg['addTrigger']			= ''; // only insert
	$cfg['saveTrigger']			= '';

// form definitions
	// País default -> preenche o campo com o valor padrão
	$cfg['countryDef']			= 'BRASIL';
	if(isset(${$APPTAG.'CountryDef'}) && !empty(${$APPTAG.'CountryDef'})) $cfg['countryDef'] = ${$APPTAG.'CountryDef'};
	// Estado default -> preenche o campo com o valor padrão
	$cfg['stateDef']			= '';
	if(isset(${$APPTAG.'StateDef'}) && !empty(${$APPTAG.'StateDef'})) $cfg['stateDef'] = ${$APPTAG.'StateDef'};
	// Cidade default -> preenche o campo com o valor padrão
	$cfg['cityDef']				= '';
	if(isset(${$APPTAG.'CityDef'}) && !empty(${$APPTAG.'CityDef'})) $cfg['cityDef'] = ${$APPTAG.'CityDef'};

// view

	// show app (Add Button & List)
	$cfg['showApp']				= true;
	if(isset(${$APPTAG.'ShowApp'})) $cfg['showApp'] = ${$APPTAG.'ShowApp'} ? true : false;
	// item view
	$cfg['itemView']			= false;
	if(isset(${$APPTAG.'ItemView'})) $cfg['itemView'] = ${$APPTAG.'ItemView'} ? true : false;

	if($cfg['itemView']) :
		// A view Desabilita a listagem
		$cfg['showList']		= false;
		$cfg['listModal']		= false;
		$cfg['listFull']		= false;
		$cfg['listCustom']		= '';
		$cfg['staticToolbar']	= false;
		$cfg['showAddBtn']		= false;
		$cfg['addText']			= false;
		$cfg['addClass']		= false;
		$cfg['openFilter']		= false;
		$cfg['ajaxFilter']		= false;
	else :
		// show/hidden list
		$cfg['showList']			= true;
		if(isset(${$APPTAG.'ShowList'})) $cfg['showList'] = ${$APPTAG.'ShowList'} ? true : false;
		// list modal
		$cfg['listModal']			= false;
		if(isset(${$APPTAG.'ListModal'})) $cfg['listModal'] = ${$APPTAG.'ListModal'} ? true : false;
		// list type
		$cfg['listFull']			= true;
		if(isset(${$APPTAG.'ListFull'})) $cfg['listFull'] = ${$APPTAG.'ListFull'} ? true : false;
		// custom list
		$cfg['listCustom']			= '';
		if(isset(${$APPTAG.'ListCustom'})) $cfg['listCustom'] = ${$APPTAG.'ListCustom'};
		// show button to list ajax reload
		$cfg['ajaxReload']			= false;
		if(isset(${$APPTAG.'AjaxReload'})) $cfg['ajaxReload'] = ${$APPTAG.'AjaxReload'};
		// toolbar position
		$cfg['staticToolbar']		= false;
		if(isset(${$APPTAG.'StaticToolbar'})) $cfg['staticToolbar'] = ${$APPTAG.'StaticToolbar'} ? true : false;
		// show/hidden 'add' button
		$cfg['showAddBtn']			= true;
		if(isset(${$APPTAG.'ShowAddBtn'})) $cfg['showAddBtn'] = ${$APPTAG.'ShowAddBtn'} ? true : false;
		// hidden button 'add' text
		$cfg['addText']				= true;
		if(isset(${$APPTAG.'AddText'})) $cfg['addText'] = ${$APPTAG.'AddText'} ? true : false;
		// button 'add' css class
		$cfg['addClass']			= '';
		if(isset(${$APPTAG.'AddClass'})) $cfg['addClass'] = ${$APPTAG.'AddClass'};
		// show filter opened
		$cfg['openFilter']			= false;
		if(isset(${$APPTAG.'OpenFilter'})) $cfg['openFilter'] = ${$APPTAG.'OpenFilter'};
		// load filter
		$cfg['ajaxFilter']			= false;
		if(isset(${$APPTAG.'AjaxFilter'})) $cfg['ajaxFilter'] = ${$APPTAG.'AjaxFilter'};
	endif;
	// list's elements default
	$cfg['pagLimit']			= 20;

	// descriptions
	$cfg['showListDesc']		= false;
	$cfg['showFormDesc']		= false;

// form's

	$cfg['showFormHeader']		= true;
	$cfg['showFormToolbar']		= false;
	// Form button action
	// false => save (keep)
	// true => save and close (remove 'save & keep')
	$cfg['formBtnAction']		= false;

	// date & price convertions
	$cfg['dateConvert']			= true;
	$cfg['load_UI']				= $cfg['dateConvert'];
	$cfg['priceDecimal']		= true;
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
