<?php
// define o caminho para a 'RAÍZ' do site
$JRoot = isset($_ROOT) ? $_ROOT : JURI::root();

// App Define
$APPNAME  = 'pageItens';
$APPTAG   = isset(${$APPNAME.'AppTag'}) ? ${$APPNAME.'AppTag'} : $APPNAME;
$newInstance = ($APPTAG != $APPNAME) ? true : false;

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
  $cfg['isPublic']          = true; // Público -> acesso aberto a todos
  // Restrict Access
  // $cfg['groupId']['viewer'][]  = apenas visualiza o componente
  // $cfg['groupId']['admin'][]   = administra o componente
  // ----------------------------------------------------
    $cfg['groupId']['viewer'][] = 0; // '0' pois não pode ser vazio
    // acesso liberado sempre
    $cfg['groupId']['admin'][]  = 6; // Gerente
    $cfg['groupId']['admin'][]  = 7; // Administrador
    $cfg['groupId']['admin'][]  = 8; // Desenvolvedor
  // ----------------------------------------------------

// crud's name
  $cfg['APPNAME']           = $APPNAME;
  $cfg['APPTAG']            = $APPTAG;

// crud's main table
  $cfg['mainTable']         = '#__base_pageItens';

// content layout default
  $cfg['contentLayout']     = "<h2 class=\"mt-0\">{PAGEITEM TITLE}</h2>\r<p class=\"mb-3\">\r<a href=\"{PAGEITEM URLLIST}\">{PAGEITEM TAGNAME}</a>, {PAGEITEM DATE} \r{PAGEITEM MONTH} {PAGEITEM YEAR}\r</p>\r<div class=\"pageItens-content\">\r{PAGEITEM CONTENT}\r</div>";

// view

  // show app (Add Button & List)
  $cfg['showApp']           = true;
  if(isset(${$APPTAG.'ShowApp'})) $cfg['showApp'] = ${$APPTAG.'ShowApp'} ? true : false;
  // item content
  $cfg['itemContent']       = false;
  if(isset(${$APPTAG.'ItemContent'})) $cfg['itemContent'] = ${$APPTAG.'ItemContent'} ? true : false;
  // show/hidden list
  $cfg['showList']          = true;
  if(isset(${$APPTAG.'ShowList'})) $cfg['showList'] = ${$APPTAG.'ShowList'} ? true : false;
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
  $cfg['openFilter']        = true;
  // list's elements default
  $cfg['pagLimit']          = isset(${$APPTAG.'PageLimit'}) ? ${$APPTAG.'PageLimit'} : 20;

// descriptions
  $cfg['showListDesc']      = false;
  $cfg['showFormDesc']      = false;

// date & price convertions
  $cfg['dateConvert']       = true;
  $cfg['priceDecimal']      = false;
  $cfg['load_UI']           = false;
  $cfg['htmlEditor']        = true;
  $cfg['htmlEditorFull']    = true;

// crud's upload config
  $cfg['hasUpload']         = true;
  $cfg['hasUpload'] = isset(${$APPTAG.'hasUpload'}) ? ${$APPTAG.'hasUpload'} : $cfg['hasUpload'];
  // habilita a adição dinamica de novos campos do tipo 'file'
  $cfg['dinamicFiles']      = true;
    // valor inicial do index do arquivo... considerando '0' o campo estático
    // caso exitam outros campos estáticos, o index será igual ao número de itens estáticos
    // Ex: 4 itens estáticos => $cfg['indexFileInit'] = 4;
    $cfg['indexFileInit']     = 3;

  if($cfg['hasUpload']) :
    $cfg['fileField']       = 'file'; // upload's field name
    $cfg['fileTable']       = $cfg['mainTable'].'_files'; // upload's database table
    // upload params
    $cfg['maxFileSize']     = 10485760; // 10MB
    $cfg['uploadDir']       = JPATH_BASE.'images/apps/'.$APPPATH.'/'; // IMPORTANTE: colocar a barra '/' no fim
    // file types enabled
    $cfg['fileTypes']       = array();
      $cfg['fileTypes']['image'][] = 'image/png';
      $cfg['fileTypes']['image'][] = 'image/gif';
      $cfg['fileTypes']['image'][] = 'image/jpeg';
      $cfg['fileTypes']['image'][] = 'image/pjpeg';
      $cfg['fileTypes']['file'][]  = 'text/plain';
      $cfg['fileTypes']['file'][]  = 'text/html';
      $cfg['fileTypes']['file'][]  = 'application/x-zip-compressed';
      $cfg['fileTypes']['file'][]  = 'application/x-compressed';
      $cfg['fileTypes']['file'][]  = 'application/zip';
      $cfg['fileTypes']['file'][]  = 'application/x-zip';
      $cfg['fileTypes']['file'][]  = 'application/pdf';
      $cfg['fileTypes']['file'][]  = 'application/msword';
      $cfg['fileTypes']['file'][]  = 'application/excel';
      $cfg['fileTypes']['file'][]  = 'application/vnd.ms-excel';
      $cfg['fileTypes']['file'][]  = 'application/x-excel';
      $cfg['fileTypes']['file'][]  = 'application/x-msexcel';
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

    // PAGE ITENS CONFIG

      // IMAGES
      // image width -> Se não for informadas mas medidas abaixo mostra a imagem original
      $_SESSION[$RTAG.'ImageWidth'] = isset(${$APPTAG.'ImageWidth'}) ? ${$APPTAG.'ImageWidth'} : 0;
      // image height -> Se não for informadas mas medidas abaixo mostra a imagem original
      $_SESSION[$RTAG.'ImageHeight'] = isset(${$APPTAG.'ImageHeight'}) ? ${$APPTAG.'ImageHeight'} : 0;
      // image class
      $_SESSION[$RTAG.'ImageClass'] = isset(${$APPTAG.'ImageClass'}) ? ${$APPTAG.'ImageClass'} : '';

      // FORM OPTIONS
      // hide description field
      $_SESSION[$RTAG.'HideDescriptionField'] = isset(${$APPTAG.'HideDescriptionField'}) ? ${$APPTAG.'HideDescriptionField'} : 0;
      $_SESSION[$RTAG.'DescriptionHtml'] = isset(${$APPTAG.'DescriptionHtml'}) ? ${$APPTAG.'DescriptionHtml'} : 1;
      $_SESSION[$RTAG.'DescriptionHtmlFull'] = isset(${$APPTAG.'DescriptionHtmlFull'}) ? ${$APPTAG.'DescriptionHtmlFull'} : 0;
      // cover type default
      $_SESSION[$RTAG.'CoverTypeDef'] = isset(${$APPTAG.'CoverTypeDef'}) ? ${$APPTAG.'CoverTypeDef'} : 0;
      // image Info
      $imgInfo = ($_SESSION[$RTAG.'ImageWidth'] != 0 && $_SESSION[$RTAG.'ImageHeight'] != 0) ? 'Tamanho da imagem:<br />'.$_SESSION[$RTAG.'ImageWidth'].' x '.$_SESSION[$RTAG.'ImageHeight'].' (px)' : 'TEXT_IMAGE_SIZE_UNDEFINED';
      $_SESSION[$RTAG.'ImageInfo'] = isset(${$APPTAG.'ImageInfo'}) ? ${$APPTAG.'ImageInfo'} : $imgInfo;
      // hide all date fields
      $_SESSION[$RTAG.'HideAllDateFields'] = isset(${$APPTAG.'HideAllDateFields'}) ? ${$APPTAG.'HideAllDateFields'} : 0;
      // hide date field
      $_SESSION[$RTAG.'HideDateField'] = isset(${$APPTAG.'HideDateField'}) ? ${$APPTAG.'HideDateField'} : 0;
      // hide month field
      $_SESSION[$RTAG.'HideMonthField'] = isset(${$APPTAG.'HideMonthField'}) ? ${$APPTAG.'HideMonthField'} : 0;
      // hide year field
      $_SESSION[$RTAG.'HideYearField'] = isset(${$APPTAG.'HideYearField'}) ? ${$APPTAG.'HideYearField'} : 0;
      // hide order field
      $_SESSION[$RTAG.'HideOrderField'] = isset(${$APPTAG.'HideOrderField'}) ? ${$APPTAG.'HideOrderField'} : 0;
      // content type default
      $_SESSION[$RTAG.'ContentTypeDef'] = isset(${$APPTAG.'ContentTypeDef'}) ? ${$APPTAG.'ContentTypeDef'} : 0;
      // only content type default
      $_SESSION[$RTAG.'OnlyContentDef'] = isset(${$APPTAG.'OnlyContentDef'}) ? ${$APPTAG.'OnlyContentDef'} : 0;
      // enable content type 'link'
      $_SESSION[$RTAG.'FormShowLinkContent'] = isset(${$APPTAG.'FormShowLinkContent'}) ? ${$APPTAG.'FormShowLinkContent'} : 1;
      // enable content type 'file'
      $_SESSION[$RTAG.'FormShowFileContent'] = isset(${$APPTAG.'FormShowFileContent'}) ? ${$APPTAG.'FormShowFileContent'} : 1;
      // enable content type 'Text'
      $_SESSION[$RTAG.'FormShowTextContent'] = isset(${$APPTAG.'FormShowTextContent'}) ? ${$APPTAG.'FormShowTextContent'} : 1;
      // enable content type 'Image Gallery'
      $_SESSION[$RTAG.'FormShowGalleryContent'] = isset(${$APPTAG.'FormShowGalleryContent'}) ? ${$APPTAG.'FormShowGalleryContent'} : 1;
      // target default
      $_SESSION[$RTAG.'TargetDef'] = isset(${$APPTAG.'TargetDef'}) ? ${$APPTAG.'TargetDef'} : 1;
      // mostra a tab 'opções'
      $_SESSION[$RTAG.'ShowTabOptions'] = isset(${$APPTAG.'ShowTabOptions'}) ? ${$APPTAG.'ShowTabOptions'} : 1;

      // NAVIGATION OPTIONS
      // filtered list url -> Listagem dos itens
      $_SESSION[$RTAG.'UrlListView'] = isset(${$APPTAG.'UrlListView'}) ? ${$APPTAG.'UrlListView'} : '';
      // admin list url -> caso a listagem filtrada não seja informada, leva para a listagem principal {menubase -> base-tools/pageItens}
      $_SESSION[$RTAG.'UrlListAdmin'] = isset(${$APPTAG.'UrlListView'}) ? ${$APPTAG.'UrlListView'} : 'base-tools/pageItens?fTag='.$APPTAG;
      // list view button -> only ajax list
      $_SESSION[$RTAG.'ShowListViewButton'] = isset(${$APPTAG.'ShowListViewButton'}) ? ${$APPTAG.'ShowListViewButton'} : 1;
      $_SESSION[$RTAG.'ListViewButtonLabel'] = isset(${$APPTAG.'ListViewButtonLabel'}) ? ${$APPTAG.'ListViewButtonLabel'} : 'TEXT_LINK_TO_LIST';
      $_SESSION[$RTAG.'ListViewButtonClass'] = isset(${$APPTAG.'ListViewButtonClass'}) ? ${$APPTAG.'ListViewButtonClass'} : '';

      // SLIDER OPTIONS
      $_SESSION[$RTAG.'Enable_slide'] = isset(${$APPTAG.'Enable_slide'}) ? ${$APPTAG.'Enable_slide'} : 0;
      $_SESSION[$RTAG.'Slider_fullScreen'] = isset(${$APPTAG.'Slider_fullScreen'}) ? ${$APPTAG.'Slider_fullScreen'} : 0;
      $_SESSION[$RTAG.'Slider_RemoveContainer'] = isset(${$APPTAG.'Slider_RemoveContainer'}) ? ${$APPTAG.'Slider_RemoveContainer'} : 0;

      // LIST OPTIONS
      // nº de itens visualizados na listagem 'ajax'
      $_SESSION[$RTAG.'ListAjaxLimit'] = isset(${$APPTAG.'ListAjaxLimit'}) ? ${$APPTAG.'ListAjaxLimit'} : 1;
      // nº de itens visualizados na listagem
      $_SESSION[$RTAG.'ListOrder'] = isset(${$APPTAG.'ListOrder'}) ? ${$APPTAG.'ListOrder'} : 'id DESC';
      // grid padrão para a listagem
      $_SESSION[$RTAG.'ListGrid'] = isset(${$APPTAG.'ListGrid'}) ? ${$APPTAG.'ListGrid'} : 'col-sm-12';
      $_SESSION[$RTAG.'ListItemClass'] = isset(${$APPTAG.'ListItemClass'}) ? ${$APPTAG.'ListItemClass'} : '';
      $_SESSION[$RTAG.'ListItemLayout'] = isset(${$APPTAG.'ListItemLayout'}) ? ${$APPTAG.'ListItemLayout'} : '';
      $_SESSION[$RTAG.'ListItemDateFormat'] = isset(${$APPTAG.'ListItemDateFormat'}) ? ${$APPTAG.'ListItemDateFormat'} : 'd/m/Y';
      // download button
      $_SESSION[$RTAG.'DownloadButtonLabel'] = isset(${$APPTAG.'DownloadButtonLabel'}) ? ${$APPTAG.'DownloadButtonLabel'} : 'FIELD_LABEL_DOWNLOAD';
      $_SESSION[$RTAG.'DownloadButtonClass'] = isset(${$APPTAG.'DownloadButtonClass'}) ? ${$APPTAG.'DownloadButtonClass'} : 'btn btn-xs btn-success';

      // FILTER OPTIONS
      // list all tags -> mostra todos os itens independente das tags
      $_SESSION[$RTAG.'ListAllTags'] = isset(${$APPTAG.'ListAllTags'}) ? ${$APPTAG.'ListAllTags'} : 0;

      // CONTENT OPTIONS
      $_SESSION[$RTAG.'UrlContent'] = isset(${$APPTAG.'UrlContent'}) ? ${$APPTAG.'UrlContent'} : 'page-content';
      $_SESSION[$RTAG.'ContentLayout'] = isset(${$APPTAG.'ContentLayout'}) ? ${$APPTAG.'ContentLayout'} : $cfg['contentLayout'];
      $_SESSION[$RTAG.'ContentDateFormat'] = isset(${$APPTAG.'ContentDateFormat'}) ? ${$APPTAG.'ContentDateFormat'} : 'd/m/Y';
      $_SESSION[$RTAG.'Content_imageWidth'] = isset(${$APPTAG.'Content_imageWidth'}) ? ${$APPTAG.'Content_imageWidth'} : 0;
      $_SESSION[$RTAG.'Content_imageHeight'] = isset(${$APPTAG.'Content_imageHeight'}) ? ${$APPTAG.'Content_imageHeight'} : 0;
      $_SESSION[$RTAG.'Content_imageClass'] = isset(${$APPTAG.'Content_imageClass'}) ? ${$APPTAG.'Content_imageClass'} : '';
      // gallery
      $_SESSION[$RTAG.'Content_galleryGrid'] = isset(${$APPTAG.'Content_galleryGrid'}) ? ${$APPTAG.'Content_galleryGrid'} : 'col-sm-3';
      $_SESSION[$RTAG.'Content_galImageWidth'] = isset(${$APPTAG.'Content_galImageWidth'}) ? ${$APPTAG.'Content_galImageWidth'} : 300;
      $_SESSION[$RTAG.'Content_galImageHeight'] = isset(${$APPTAG.'Content_galImageHeight'}) ? ${$APPTAG.'Content_galImageHeight'} : $_SESSION[$RTAG.'Content_galImageWidth'];
      $_SESSION[$RTAG.'Content_galImageClass'] = isset(${$APPTAG.'Content_galImageClass'}) ? ${$APPTAG.'Content_galImageClass'} : '';

      // CUSTOM OPTIONS
      // list slider
      $_SESSION[$RTAG.'ListSlider'] = isset(${$APPTAG.'ListSlider'}) ? ${$APPTAG.'ListSlider'} : 0;
      $_SESSION[$RTAG.'ContentModal'] = isset(${$APPTAG.'ContentModal'}) ? ${$APPTAG.'ContentModal'} : 0;
      // modal size {modal-sm; ''; modal-lg}
      $_SESSION[$RTAG.'ContentModalSize'] = isset(${$APPTAG.'ContentModalSize'}) ? ${$APPTAG.'ContentModalSize'} : 'modal-lg';
      $_SESSION[$RTAG.'HideModalHeader'] = isset(${$APPTAG.'HideModalHeader'}) ? ${$APPTAG.'HideModalHeader'} : 0;

    // END 'PAGE ITENS CONFIG'

  endif;

?>
