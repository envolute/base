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
	$slider_fullScreen = $params->get('slide_fullScreen', 0); // slider FullScreen
	$slider_minHeight = $params->get('slide_minHeight', '0'); // altura do slider 'FullScreen'
	$slider_fullHeight = $params->get('slide_fullHeight', '100vh'); // altura do slider 'FullScreen'
	$slider_RemoveContainer = $params->get('slide_removeContainer', 0); // remover container do slider

	// Opções do slide
	$mode = $params->get('slide_mode', 'horizontal');
	$captions = $params->get('slide_caption', 0) ? 'true' : 'false';
	$auto = $params->get('slide_auto', 1) ? 'true' : 'false';
	$pause = $params->get('slide_pause', 10000);
	$autocontrols = $params->get('slide_controls', 0) ? 'true' : 'false';
	$controls = $params->get('slide_nav', 1) ? 'true' : 'false';
	$pager = $params->get('slide_pager', 0) ? 'true' : 'false';
	$autoHover = $params->get('slide_autoHover', 1) ? 'true' : 'false';
	$loop = $params->get('slide_loop', 1) ? 'true' : 'false';
	$adaptiveHeight = $params->get('slide_adaptiveHeight', 0) ? 'true' : 'false';
	// carrousel
	$minSlides = $params->get('slide_minSlides', 1);
	$maxSlides = $params->get('slide_maxSlides', 1);
	$slideWidth = $params->get('slide_slideWidth', 0);
	$slideMargin = $params->get('slide_slideMargin', 15);
	$moveSlides = $params->get('slide_moveSlides', 0);

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
	$doc->addStyleDeclaration('#mod-base-slider-item-'.$module->id.' > li:not(:first-child) { position: absolute; top: 0; visibility: hidden; }');

	// CHAMADA DO SLIDER
	// w => pega a largura atual do container
	// sWidth => Define a largura do item "slideWidth"
	$script = '
		jQuery(window).on("load", function(){

			var w = jQuery("#mod-base-slider-'.$module->id.'").width();
			var sWidth = (w / '.$maxSlides.') - '.$slideMargin.';

			jQuery("#mod-base-slider-item-'.$module->id.'").bxSlider({
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
				slideWidth: sWidth,
				slideMargin: '.$slideMargin.',
				moveSlides: '.$moveSlides.',
				adaptiveHeight: '.$adaptiveHeight.',
				onSliderLoad:function(currentIndex){
					// mostra as imagens após o carregamento do plugin
					jQuery("#mod-base-slider-item-'.$module->id.' img").attr("title","");
					jQuery("#mod-base-slider-item-'.$module->id.' > li:not(:first-child)").css("visibility", "visible");
				}
			});

		});
	';
	$doc->addScriptDeclaration($script);

	echo '
		<div id="mod-base-slider-'.$module->id.'">
			<ul id="mod-base-slider-item-'.$module->id.'" class="bxslider">
	';
			// INCLUDE
			if(strpos($file, 'http') === false) :
				require(JPATH_BASE.'/'.$file);
			else :
				echo '<p class="alert alert-danger">'.Jtext::_('MOD_BASEAPP_INCLUDE_ALERT').'</p>';
			endif;
	echo '
			</ul>
		</div>
	';

endif; // end 'file'
?>
