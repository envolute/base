<?php
// define o caminho para a 'RAÍZ' do site
$JRoot = isset($_root) ? $_root : JURI::root();

// App Define
$APPNAME  = 'clients';

// carrega o arquivo de tradução
$lang = JFactory::getLanguage();
$lang->load('base_'.$APPNAME, JPATH_BASE, $lang->getTag(), true);
// GLOBAL VARS
if(!$ajaxRequest && (!isset($_SESSION[$APPTAG.'langDef']) || (isset($_SESSION[$APPTAG.'langDef']) && $_SESSION[$APPTAG.'langDef'] != $lang->getTag()))) :
  if(isset($_SESSION[$APPTAG.'langDef'])) unset($_SESSION[$APPTAG.'langDef']);
  $_SESSION[$APPTAG.'langDef'] = $lang->getTag(); // define a language
endif;

// App Configuration's Vars
$cfg = array();

// Crud's permissions
  $cfg['isPublic']          = false; // Público -> acesso aberto a todos
  // Restrict Access
    // $cfg['groupId'][]       = '2'; // Registrado
    // $cfg['groupId'][]       = '6'; // Gerente
    // acesso liberado sempre
    $cfg['groupId'][]       = '7'; // Administrador
    $cfg['groupId'][]       = '8'; // Desenvolvedor

// crud's name
  $cfg['$APPTAG']            = $APPTAG;

// crud's main table
  $cfg['mainTable']         = '#__zenite_clients';

// due dates
  $cfg['dueDates']          = array(1, 10, 18, 23);

// view

  // show app (Add Button & List)
  $cfg['showApp']           = true;
  if(isset(${$APPTAG.'ShowApp'})) $cfg['showApp'] = ${$APPTAG.'ShowApp'} ? true : false;
  // show/hidden list
  $cfg['showList']          = true;
  if(isset(${$APPTAG.'Showlist'})) $cfg['showList'] = ${$APPTAG.'Showlist'} ? true : false;
  // list modal
  $cfg['listModal']         = false;
  if(isset(${$APPTAG.'ListModal'})) $cfg['listModal'] = ${$APPTAG.'ListModal'} ? true : false;
  // list type
  $cfg['listFull']          = true;
  if(isset(${$APPTAG.'ListFull'})) $cfg['listFull'] = ${$APPTAG.'ListFull'} ? true : false;
  // toolbar position
  $cfg['staticToolbar']     = false;
  if(isset(${$APPTAG.'StaticToolbar'})) $cfg['staticToolbar'] = ${$APPTAG.'StaticToolbar'} ? true : false;
  // show/hidden 'add' button
  $cfg['showAddBtn']        = true;
  if(isset(${$APPTAG.'ShowAddBtn'})) $cfg['showAddBtn'] = ${$APPTAG.'ShowAddBtn'} ? true : false;
  // hidden button 'add' text
  $cfg['addText']           = true;
  if(isset(${$APPTAG.'AddText'})) $cfg['addText'] = ${$APPTAG.'AddText'} ? true : false;
  // show filter opened
  $cfg['showFilter']        = true;
  // list's elements default
  $cfg['pagLimit']          = 20;

// descriptions
  $cfg['showListDesc']      = false;
  $cfg['showFormDesc']      = false;

// date & price convertions
  $cfg['dateConvert']       = true;
  $cfg['priceDecimal']      = false;
  $cfg['load_UI']           = false;
  $cfg['htmlEditor']        = false;
  $cfg['htmlEditorFull']    = false;

// crud's upload config
  $cfg['hasUpload']         = true;
  $cfg['hasUpload'] = isset(${$APPTAG.'hasUpload'}) ? ${$APPTAG.'hasUpload'} : $cfg['hasUpload'];
  // habilita a adição dinamica de novos campos do tipo 'file'
  $cfg['dinamicFiles']      = false;
    // valor inicial do index do arquivo... considerando '0' o campo estático
    // caso exitam outros campos estáticos, o index será igual ao número de itens estáticos
    // Ex: 4 itens estáticos => $cfg['indexFileInit'] = 4;
    $cfg['indexFileInit']     = 1;

  if($cfg['hasUpload']) :
    $cfg['fileField']       = 'file'; // upload's field name
    $cfg['fileTable']       = $cfg['mainTable'].'_files'; // upload's database table
    // upload params
    $cfg['maxFileSize']     = 1024000; // 1MB
    $cfg['uploadDir']       = JPATH_BASE.'images/uploads/'.$APPNAME.'/'; // IMPORTANTE: colocar a barra '/' no fim
    // file types enabled
    $cfg['fileTypes']       = array();
      $cfg['fileTypes'][]   = 'image/png';
      $cfg['fileTypes'][]   = 'image/gif';
      $cfg['fileTypes'][]   = 'image/jpeg';
      $cfg['fileTypes'][]   = 'image/pjpeg';
      // $cfg['fileTypes'][]   = 'text/plain';
      // $cfg['fileTypes'][]   = 'text/html';
      // $cfg['fileTypes'][]   = 'application/x-zip-compressed';
      // $cfg['fileTypes'][]   = 'application/pdf';
      // $cfg['fileTypes'][]   = 'application/msword';
      // $cfg['fileTypes'][]   = 'application/vnd.ms-excel';
      // $cfg['fileTypes'][]   = 'video/mp4';
  endif;

// crud's relation actions

  if(!$ajaxRequest) :
    // RELATION TAG
    // evita o conflito entre sessões de diferentes carregamento do mesmo APP
    $RTAG = isset(${$APPTAG.'RelTag'}) ? $APPTAG.${$APPTAG.'RelTag'} : $APPTAG;
    // gera parâmetros da nova sessao
    // relation table Name
    $_SESSION[$RTAG.'RelTable'] = isset(${$APPTAG.'RelTable'}) ? ${$APPTAG.'RelTable'} : '';
    // relation table ID name
    $_SESSION[$RTAG.'RelNameId'] = isset(${$APPTAG.'RelNameId'}) ? ${$APPTAG.'RelNameId'} : '';
    // relation table ID default value [optional]
    $_SESSION[$RTAG.'RelId'] = isset(${$APPTAG.'RelId'}) ? ${$APPTAG.'RelId'} : '';
    // ID name of mainTable in relation table
    $_SESSION[$RTAG.'AppNameId'] = isset(${$APPTAG.'AppNameId'}) ? ${$APPTAG.'AppNameId'} : '';
    // list filter
    $_SESSION[$RTAG.'RelListId'] = isset(${$APPTAG.'RelListId'}) ? ${$APPTAG.'RelListId'} : '';
    $_SESSION[$RTAG.'RelListNameId'] = isset(${$APPTAG.'RelListNameId'}) ? ${$APPTAG.'RelListNameId'} : '';
    // show only childs
    $_SESSION[$RTAG.'OnlyChildList'] = isset(${$APPTAG.'OnlyChildList'}) ? ${$APPTAG.'OnlyChildList'} : 0;
    // UPDATE SELECT FIELD
    // select element that will be updated
    $_SESSION[$RTAG.'FieldUpdated'] = (isset(${$APPTAG.'FieldUpdated'}) && !empty(${$APPTAG.'FieldUpdated'})) ? ${$APPTAG.'FieldUpdated'} : '';
    // table field for element updated
    $_SESSION[$RTAG.'TableField'] = (isset(${$APPTAG.'TableField'}) && !empty(${$APPTAG.'TableField'})) ? ${$APPTAG.'TableField'} : '';
    // hide 'parentFieldId'
    $_SESSION[$RTAG.'HideParentField'] = isset(${$APPTAG.'HideParentField'}) ? ${$APPTAG.'HideParentField'} : 0;
  endif;

?>
