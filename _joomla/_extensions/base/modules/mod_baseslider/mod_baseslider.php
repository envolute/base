<?php
/**
* Author:	Ivo Junior
* Email:	dev@envolute.com
* Website:	http://www.envolute.com
* Component: Base Slider
* Version:	1.0.0
* Date:		07/10/2017
* copyright	Copyright (C) 2012 http://www.envolute.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined('_JEXEC') or die;


$file = $params->get('phpFile');
if(!empty($file) && file_exists($file)) :

	// PARAMS
	$slide_noContainer	= $params->get('slide_noContainer', 1); // remover container do bxslider
	$slide_id			= $params->get('slide_id'); // propriedade ID do container do bxslider
	$slide_class		= $params->get('slide_class'); // classe CSS do container do bxslider

	// App
	$items_total		= $params->get('items_total', '', 'int'); // Total de itens carregados
	$items_ids			= $params->get('items_ids'); // Ids dos itens que devem ser visualizados
	$categ_ids			= $params->get('categ_ids'); // Ids das categorias dos itens que devem ser visualizados
	$items_order		= $params->get('items_order'); // ordem dos itens
	$image_width		= $params->get('image_width', '', 'int'); // Largura da imagem
	$image_height		= $params->get('image_height', '', 'int'); // Altura da imagem
	$show_title			= $params->get('show_title', 0); // mostrar o título/nome do item
	$show_categ			= $params->get('show_categ', 0); // mostrar a categoria/grupo do item
	$show_desc			= $params->get('show_desc', 0); // mostrar a descrição do item
	$show_user			= $params->get('show_user', 0); // mostrar o usuário/autor do item
	$show_value			= $params->get('show_value', 0); // mostrar o valor do item

	// Opções do slide
	$mode				= $params->get('slide_mode', 'horizontal');
	$captions			= $params->get('slide_caption', 0) ? 'true' : 'false';
	$auto				= $params->get('slide_auto', 1) ? 'true' : 'false';
	$pause				= $params->get('slide_pause', 10000);
	$autocontrols		= $params->get('slide_controls', 0) ? 'true' : 'false';
	$controls			= $params->get('slide_nav', 1) ? 'true' : 'false';
	$pager				= $params->get('slide_pager', 0) ? 'true' : 'false';
	$autoHover			= $params->get('slide_autoHover', 1) ? 'true' : 'false';
	$loop				= $params->get('slide_loop', 1) ? 'true' : 'false';
	$adaptiveHeight		= $params->get('slide_adaptiveHeight', 0) ? 'true' : 'false';
	// Carrossel
	$minSlides			= $params->get('slide_minSlides', 1);
	$maxSlides			= $params->get('slide_maxSlides', 1);
	$slideMargin		= $params->get('slide_slideMargin', 0);
	$moveSlides			= $params->get('slide_moveSlides', 0);
	$slideWidth			= $params->get('slide_slideWidth', 0);

	$doc = JFactory::getDocument();

	// CUSTOM CODE
	if($params->get('css')) $doc->addStyleDeclaration($params->get('css'));
	if($params->get('script')) $doc->addScriptDeclaration($params->get('script'));
	$code = $params->get('code');
	if(!empty($code)) :
		$code=ltrim($code,'<?php');
		$code=rtrim($code,'?>');
		echo eval($code);
	endif;

	// Importa biblioteca bxslider
	$doc->addStyleSheet(JURI::root().'templates/base/libs/content/bxslider/jquery.bxslider.min.css');
	$doc->addScript(JURI::root().'templates/base/libs/content/bxslider/jquery.bxslider.min.js');

	// esconde imagens no carregamento
	$doc->addStyleDeclaration('#mod-base-bxslider-'.$module->id.' > li:not(:first-child) { position: absolute; top: 0; visibility: hidden; }');

	// propriedade ID do container
	$propID = !empty($slide_id) ? str_replace('#', '', $slide_id) : 'mod-base-slider-'.$module->id;
	// $slider => Nome da função de carregamento do slider
	// Utiliza o ID para gerar um nome de função 'fixo'. Isso possibilita que uma aplicação
	// possa utilizar a função sem depender do ID do módulo, que é variável em cada projeto...
	$slider = str_replace('-', '', $propID);
	// Carousel
	// Opções que implementam o formato de carousel
	$carousel = '';
	if($minSlides > 1 || $maxSlides > 1) :
		$carousel = '
			minSlides: '.$minSlides.',
			maxSlides: '.$maxSlides.',
			slideMargin: '.$slideMargin.',
			moveSlides: '.$moveSlides.',
		';
	endif;
	// CHAMADA DO SLIDER
	// w => pega a largura atual do container
	// sWidth => Define a largura do item "slideWidth"
	$script = '
		jQuery(window).on("load", function(){

			var w = jQuery("#'.$propID.'").width();
			var sWidth = (w / '.$maxSlides.') - '.$slideMargin.';

			window.'.$slider.' = function() {
				baseBxSlider'.$module->id.' = jQuery("#mod-base-bxslider-'.$module->id.'").bxSlider({
					mode:"'.$mode.'",
					autoHover: '.$autoHover.',
					captions: '.$captions.',
					auto: '.$auto.',
					pause: '.$pause.',
					autoControls: '.$autocontrols.',
					controls: '.$controls.',
					pager: '.$pager.',
					infiniteLoop: '.$loop.',
					slideWidth: '.($slideWidth == 0 ? 'sWidth' : $slideWidth).',
					adaptiveHeight: '.$adaptiveHeight.',
					'.$carousel.'
					onSliderLoad:function(currentIndex) {
						// mostra as imagens após o carregamento do plugin
						jQuery("#mod-base-bxslider-'.$module->id.' img").attr("title","");
						jQuery("#mod-base-bxslider-'.$module->id.' > li:not(:first-child)").css("visibility", "visible");
					}
				});
			};

			'.$slider.'();

		});
	';
	$doc->addScriptDeclaration($script);

	$class = $slide_noContainer ? 'no-container' : '';
	$class .= !empty($class) ? ' ' : '';
	$class .= !empty($slide_class) ? $slide_class : '';
	$class = !empty($class) ? ' class="'.$class.'"' : '';

	echo '
		<div id="'.$propID.'"'.$class.'>
			<ul id="mod-base-bxslider-'.$module->id.'" class="bxslider">
	';
			// INCLUDE
			if(strpos($file, 'http') === false) :
				require(JPATH_BASE.'/'.$file);
			else :
				echo '<p class="alert alert-danger">'.Jtext::_('MOD_BASESLIDER_INCLUDE_ALERT').'</p>';
			endif;
	echo '
			</ul>
		</div>
	';

endif; // end 'file'
?>
