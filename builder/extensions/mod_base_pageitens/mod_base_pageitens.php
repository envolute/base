<?php
/*@module mod_simplecode */
defined('_JEXEC') or die;
$doc = JFactory::getDocument();

// custom code
// Config View
// TAGS - evita conflito entre instâncias da mesma APP
$pageItensAppTag = $params->get('appTag', 'pageItens');
${$pageItensAppTag.'RelTag'} = $params->get('relTag', 'mod-pageItens');
// LIST
${$pageItensAppTag.'ListFull'} = $params->get('listFull', 0); // 0=>listagem ajax; 1=>listagem completa
${$pageItensAppTag.'ListAjaxLimit'} = $params->get('listAjaxLimit', 5); // nº de itens visualizados
${$pageItensAppTag.'ListOrder'} = $params->get('listOrder', 'ordering'); // ordem
${$pageItensAppTag.'ListGrid'} = $params->get('listGrid', 'col-sm-12'); // grid da Listagem
${$pageItensAppTag.'ListItemClass'} = $params->get('listItemClass'); // class opcional da Listagem
${$pageItensAppTag.'ListItemLayout'} = $params->get('listItemLayout'); // class opcional da Listagem
${$pageItensAppTag.'ListItemDateFormat'} = $params->get('listItemDateFormat'); // Formato de data
// DOWLOADS
${$pageItensAppTag.'DownloadButtonLabel'} = $params->get('listDownloadButtonLabel', 'FIELD_LABEL_DOWNLOAD'); // label do botão
${$pageItensAppTag.'DownloadButtonClass'} = $params->get('listDownloadButtonClass'); // class opcional do botão
// IMAGEM
${$pageItensAppTag.'ImageWidth'} = $params->get('listImageWidth', 0); // largura da imagem
${$pageItensAppTag.'ImageHeight'} = $params->get('listImageHeight', 0); // altura da imagem
// OBS: 0=>imagem original; 'outro valor'=>imagem redimensionada
${$pageItensAppTag.'ImageClass'} = $params->get('listImageClass'); // altura da imagem
// FILTER OPTIONS
${$pageItensAppTag.'ListAllTags'} = $params->get('listAllTags', 0); // 1=>lista todas as tags na listagem
// NAVIGATION
${$pageItensAppTag.'UrlListView'} = $params->get('navUrlListView'); // Url para a listagem da TAG
${$pageItensAppTag.'ShowListViewButton'} = $params->get('navShowListViewButton', 0); // Mostrar botão para visualizar listagem
${$pageItensAppTag.'ListViewButtonLabel'} = $params->get('navListViewButtonLabel'); // 'label' do botão
${$pageItensAppTag.'ListViewButtonClass'} = $params->get('navListViewButtonClass'); // 'classe' css do botão

// CONTENT
${$pageItensAppTag.'UrlContent'} = $params->get('urlContent'); // Url para a página de conteúdo customizada
${$pageItensAppTag.'ContentModal'} = $params->get('contentModal', 0); // abrir conteúdo em modal
${$pageItensAppTag.'ContentModalSize'} = $params->get('contentModalSize', 'modal-lg'); // largura da modal
${$pageItensAppTag.'HideModalHeader'} = $params->get('hideModalHeader', 0); // esconde 'header' da modal
// FORM
${$pageItensAppTag.'HideDescriptionField'} = $params->get('formHideDescriptionField', 1); // esconde o campo 'descrição'
${$pageItensAppTag.'DescriptionHtml'} = $params->get('formDescriptionHtml', 1); // esconde o campo 'descrição'
${$pageItensAppTag.'DescriptionHtmlFull'} = $params->get('formDescriptionHtmlFull', 1); // esconde o campo 'descrição'
${$pageItensAppTag.'HideAllDateFields'} = $params->get('formHideAllDateFields', 1); // esconde todos os campos de data
${$pageItensAppTag.'ImageInfo'} = $params->get('formImageInfo'); // informações sobre a dimensão da imagem. ex: 80x80 px
${$pageItensAppTag.'HideDateField'} = $params->get('formHideDateField', 0); // esconde o campo 'data'
${$pageItensAppTag.'HideMonthField'} = $params->get('formHideMonthField', 0); // esconde o campo 'mês'
${$pageItensAppTag.'HideYearField'} = $params->get('formHideYearField', 0); // esconde o campo 'ano'
${$pageItensAppTag.'HideOrderField'} = $params->get('formHideOrderField', 0); // esconde o campo 'ordem'
${$pageItensAppTag.'ContentTypeDef'} = $params->get('formContentTypeDef', 0); // 0=>link; 1=>arquivo; 2=>conteudo; 3=>gallery
${$pageItensAppTag.'OnlyContentDef'} = $params->get('formOnlyContentDef', 0); // mostra apenas o conteúdo padrão
${$pageItensAppTag.'FormShowLinkContent'} = $params->get('formShowLinkContent', 1); // habilita o tipo de conteúdo 'link'
${$pageItensAppTag.'FormShowFileContent'} = $params->get('formShowFileContent', 1); // habilita o tipo de conteúdo 'link'
${$pageItensAppTag.'FormShowTextContent'} = $params->get('formShowTextContent', 1); // habilita o tipo de conteúdo 'link'
${$pageItensAppTag.'FormShowGalleryContent'} = $params->get('formShowGalleryContent', 1); // habilita o tipo de conteúdo 'link'
${$pageItensAppTag.'TargetDef'} = $params->get('formTargetDef', 1); // 0=>_self; 1=>_blank
${$pageItensAppTag.'ShowTabOptions'} = $params->get('showTabOptions', 0); // mostra tab 'opções'

// CUSTOM CODE
if($params->get('css')) $doc->addStyleDeclaration($params->get('css'));
if($params->get('script')) $doc->addScriptDeclaration($params->get('script'));
$code = $params->get('code');
if($params->get('code')) :
	$code=ltrim($code,'<?php');
	$code=rtrim($code,'?>');
	echo eval($code);
endif;

// BXSLIDER
// carrega os artigos em destaque em um slider
${$pageItensAppTag.'Enable_slide'} = $params->get('enable_slide'); // habilita o slider
${$pageItensAppTag.'Slider_fullScreen'} = $params->get('slide_fullScreen', 0); // slider FullScreen
${$pageItensAppTag.'Slider_minHeight'} = $params->get('slide_minHeight', '0'); // altura do slider 'FullScreen'
${$pageItensAppTag.'Slider_fullHeight'} = $params->get('slide_fullHeight', '100vh'); // altura do slider 'FullScreen'
${$pageItensAppTag.'Slider_RemoveContainer'} = $params->get('slide_removeContainer', 0); // remover container do slider
if(${$pageItensAppTag.'Enable_slide'}) :

	// Importa biblioteca bxslider
	$doc->addStyleSheet(JURI::root().'templates/base/core/libs/bxslider/jquery.bxslider.min.css');
	$doc->addScript(JURI::root().'templates/base/core/libs/bxslider/jquery.bxslider.min.js');

	// Opções do slide
	$mode = $params->get('slide_mode', 'fade');
	$captions = $params->get('slide_caption', 0) ? 'true' : 'false';
	$auto = $params->get('slide_auto', 1) ? 'true' : 'false';
	$pause = $params->get('slide_pause', 7000);
	$autocontrols = $params->get('slide_controls', 0) ? 'true' : 'false';
	$controls = $params->get('slide_nav', 0) ? 'true' : 'false';
	$pager = $params->get('slide_pager', 1) ? 'true' : 'false';
	$autoHover = $params->get('slide_autoHover', 1) ? 'true' : 'false';
	$loop = $params->get('slide_loop', 1) ? 'true' : 'false';
	$adaptiveHeight = $params->get('slide_adaptiveHeight', 0) ? 'true' : 'false';
	// carrousel
	$minSlides = $params->get('slide_minSlides', 1);
	$maxSlides = $params->get('slide_maxSlides', 1);
	$slideWidth = $params->get('slide_slideWidth', 0);
	$slideMargin = $params->get('slide_slideMargin', 0);
	$moveSlides = $params->get('slide_moveSlides', 0);

	// CHAMADA DO SLIDER
	// esconde imagens no carregamento
	$style = ${$pageItensAppTag.'Slider_fullScreen'} ? '.'.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'BxSlider { height: '.${$pageItensAppTag.'Slider_fullHeight'}.'!important; }' : '';
	$style .= '.'.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'BxSlider { min-height: '.${$pageItensAppTag.'Slider_minHeight'}.'; }';
	$style .= '.'.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'BxSlider > li { position: absolute; top: 0; visibility: hidden; }';
	$doc->addStyleDeclaration($style);
	$script = '
	window.'.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'Slider = function() {
		'.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'BxSlider = jQuery(".'.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'BxSlider").bxSlider({
			mode:"'.$mode.'",
			autoHover: '.$autoHover.',
			captions: '.$captions.',
			auto: '.$auto.',
			pause: '.$pause.',
			autoControls: '.$autocontrols.',
			controls: '.$controls.',
			pager: '.$pager.',
			infiniteLoop: '.$loop.',
			minSlides: '.$minSlides.',
		  maxSlides: '.$maxSlides.',
		  slideWidth: '.$slideWidth.',
		  slideMargin: '.$slideMargin.',
    	moveSlides: '.$moveSlides.',
			adaptiveHeight: '.$adaptiveHeight.',
			onSliderLoad:function(currentIndex){
				// mostra as imagens após o carregamento do plugin
				jQuery(".'.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'BxSlider img").attr("title","");
				jQuery(".'.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'BxSlider > li").css("visibility", "visible");
			}
		});
	};
	jQuery(window).load(function(){
		setTimeout(function() { '.$pageItensAppTag.${$pageItensAppTag.'RelTag'}.'Slider(); }, 1000);
	});
	';
	$doc->addScriptDeclaration($script);
endif;

// mod file included
$file = JPATH_BASE.'/templates/base/source/pageItens/pageItens.php';
if(file_exists($file)) :
	require($file);
else :
	echo '<p class="alert alert-danger">File not find: '.$file.'</p>';
endif;
