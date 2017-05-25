<?php
/*@module mod_simplecode */
defined('_JEXEC') or die;
$doc = JFactory::getDocument();

// custom code
// Config View
// TAGS - evita conflito entre instâncias da mesma APP
$pageItensAppTag = $params->get('appTag', 'modPageContent');
${$pageItensAppTag.'RelTag'} = $params->get('relTag', 'mod-pageContent');
// CONTENT
${$pageItensAppTag.'ItemContent'} = true;
${$pageItensAppTag.'ContentLayout'} = $params->get('contentLayout'); // layout do conteúdo
${$pageItensAppTag.'ContentDateFormat'} = $params->get('contentDateFormat'); // Formato de data
${$pageItensAppTag.'Content_imageWidth'} = $params->get('content_imageWidth'); // largura da imagem do conteúdo
${$pageItensAppTag.'Content_imageHeight'} = $params->get('content_imageHeight'); // altura da imagem do conteúdo
${$pageItensAppTag.'Content_imageClass'} = $params->get('content_imageClass'); // classe da imagem do conteúdo
// GALLERY
${$pageItensAppTag.'Content_galleryGrid'} = $params->get('content_galleryGrid', 'col-sm-3'); // Grid da galeria
${$pageItensAppTag.'Content_galImageWidth'} = $params->get('content_galImageWidth'); // largura das imagens da galeria
${$pageItensAppTag.'Content_galImageHeight'} = $params->get('content_galImageHeight'); // altura das imagens da galeria
${$pageItensAppTag.'Content_galImageClass'} = $params->get('content_galImageClass'); // classe das imagens da galeria
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

// mod file included
$file = JPATH_BASE.'/templates/base/source/pageItens/pageItens.php';
if(file_exists($file)) :
	require($file);
else :
	echo '<p class="alert alert-danger">File not find: '.$file.'</p>';
endif;
