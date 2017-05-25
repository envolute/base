<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.caption');

// REDIRECIONAMENTO
// Opção para desabilitar a visualizaçãoda listagens dos itens de uma determinada categoria
$app = JFactory::getApplication();
$doc = JFactory::getDocument();

$url = $this->params->get('redirection');
if(!empty($url)) :
	$root = (strpos($url, 'http') === false) ? JURI::base() : '';
	$app->redirect($root.$this->params->get('redirection'));
	return;
endif;

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

// Customização de estilo
$doc->addStyleSheet(JURI::base().'templates/base/css/style.content.list.css');

// TIPO DO ITEM

	// leading -> Slider ou Default
	$leadType = $this->params->get('lead_layout') ? 'slide' : 'default';

	// item
	if(count($this->intro_items)) :
		$l = $this->params->get('item_layout');
		// item -> Listagem
		$layout = ($l == '1') ? 'in-list' : '';
		// item -> Galeria/media
		if($l == '2' || $l == '3') :
			$layout = 'thumbnail';
			$doc->addStyleSheet(JURI::base().'templates/base/css/style.content.list.gallery.css');
		endif;
		// item -> Galeria/media -> Álbum
		// atribui a classe à página
		if($l == '3') :
			$doc->addScriptDeclaration('jQuery(function(){ jQuery("body").addClass("base-media-gallery") });');
			$doc->addStyleSheet(JURI::base().'templates/base/css/style.content.media-gallery.css');
		endif;
		// item -> espaçamento
		$layout = $layout.' '.$this->params->get('list_type', 'list-md');
	endif;

// BXSLIDER
// carrega os artigos em destaque em um slider

	if($this->params->get('enable_slide') && count($this->lead_items)) :

		// Importa biblioteca bxslider
		$doc->addStyleSheet(JURI::root().'templates/base/core/libs/bxslider/jquery.bxslider.min.css');
		$doc->addScript(JURI::root().'templates/base/core/libs/bxslider/jquery.bxslider.min.js');

		// Opções do slide
		$mode = $this->params->get('slide_mode', 'fade');
		$captions = ($this->params->get('lead_show_title') && $this->params->get('slide_caption')) ? 'true' : 'false';
		$auto = ($this->params->get('slide_auto') && count($this->lead_items) > 1) ? 'true' : 'false';
		$pause = $this->params->get('slide_pause','4000');
		$autocontrols = ($this->params->get('slide_controls')) ? 'true' : 'false';
		$controls = ($this->params->get('slide_nav')) ? 'true' : 'false';
		$pager = ($this->params->get('slide_pager') && count($this->lead_items) > 1) ? 'true' : 'false';

		// CHAMADA DO SLIDER
		// esconde imagens no carregamento
		$doc->addStyleDeclaration('.bxslider > li { position: absolute; top: 0; visibility: hidden; }');
		$script = '
		jQuery(window).load(function(){
			jQuery(".bxslider").bxSlider({
				mode:"'.$mode.'",
				autoHover: true,
				captions: '.$captions.',
				auto: '.$auto.',
				pause: '.$pause.',
				autoControls: '.$autocontrols.',
				controls: '.$controls.',
				pager: '.$pager.',
				infiniteLoop: true,
				onSliderLoad:function(currentIndex){
					// mostra as imagens após o carregamento do plugin
					jQuery(".bxslider img").attr("title","");
					jQuery(".bxslider > li").css("visibility", "visible");
				}
			});
		});
		';
		$doc->addScriptDeclaration($script);
	endif;

// LIMITE DE ALTURA DO ITEM -> Altura mínima e máxima do item

	$itemHeight  = $this->params->get('min_height') ? 'min-height:'.str_replace('px','',$this->params->get('min_height')).'px;' : '';
	$itemHeight .= $this->params->get('max_height') ? 'max-height:'.str_replace('px','',$this->params->get('max_height')).'px;overflow-y:hidden;' : '';

// HEADER
// <h4.page-header> = page_heading ou category-title
// se page-heading ? category-title = <h3>

	$pagHeader = ($this->params->get('show_page_heading') && $this->params->get('page_heading')) ? $this->escape($this->params->get('page_heading')) : '';
	$catHeader = ($this->params->get('show_category_title') && $this->category->title) ? $this->category->title : '';

	$header = ($pagHeader) ? '<h4 class="page-header">'.$pagHeader.'</h4>' : '';
	if($catHeader) :
		 $header .= (!$header) ? '<h4 class="page-header">'.$catHeader.'</h4>' : '<h3 class="subheading-category">'.$catHeader.'</h3>';
	endif;

// LINKS PARA MAIS ITENS

	$links = '';

	if (!empty($this->link_items)) :
		$links = '
		<div class="items-more">
			<h4 class="page-header">'.JTEXT::_('COM_CONTENT_FEED_READMORE').'</h4>
			'.$this->loadTemplate('links').'
		</div>
		';
	endif;

// PAGINAÇÃO

	$pType = ($this->params->get('pagination_type', 0) == 1) ? ' infinity-pager' : '';

	$pager = '';

	if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) :
		$pager = '<div id="view-pagination-base" class="text-center">';
			if ($this->params->def('show_pagination_results', 1) && $this->pagination->getPagesCounter()) :
				$pager .= '
				<p class="counter">
					<span class="label label-default">'.$this->pagination->getPagesCounter().'</label>
				</p>
				';
			endif;
			$pager .= $this->pagination->getPagesLinks();
		$pager .= '</div>';
	endif;

?>

<div class="view-category-base lead-<?php echo $leadType.$pType;?>">

	<?php

	//HEADER
	echo $header;

	// DESCRIÇÃO DA CATEGORIA
	echo isset($categoryDesc) ? $categoryDesc : '';

	// TAGS
	echo isset($tags) ? $tags : '';

	// MENSAGEM 'SEM ARTIGOS'
	echo isset($no_articles) ? $no_articles : '';

	echo '<div class="items-container">';

	// ARTIGOS PRINCIPAIS -> Leadings

		$leadingcount = 0;
		if (!empty($this->lead_items)) :

			// ABRE OS ITENS -> ul.bxslider || div.items-leading
			echo ($this->params->get('enable_slide')) ? '<ul class="bxslider no-padding">' : '<div class="items-leading clearfix">';

			foreach ($this->lead_items as &$item) :

				// vars
				$itemState = $item->state == 0 ? ' system-unpublished' : '';
				$this->item = &$item;
				$leadClass = 'leading-'.$leadingcount.$itemState;

				// abre o container do item -> li = slider, div = default
				echo ($this->params->get('enable_slide')) ? '<li class="'.$leadClass.' clearfix">' : '<div class="'.$leadClass.' clearfix">';

				// carrega o layout
				echo ($this->params->get('lead_layout') == 1) ? $this->loadTemplate('bxslider') : $this->loadTemplate('leading');

				// fecha o container do item -> li = slider, div = default
				echo ($this->params->get('enable_slide')) ? '</li>' : '</div>';

				$leadingcount++;

			endforeach;

			// FECHA OS ITENS
			echo ($this->params->get('enable_slide')) ? '</ul>' : '</div>';
		endif;

	// INTRODUÇÕES DOS ARQUIVOS -> itens

		$introcount = (count($this->intro_items));
		$counter = 0;

		if (!empty($this->intro_items)) :

			foreach ($this->intro_items as $key => &$item) :

				// vars
				$rowcount = ((int) $counter % (int) $this->columns) + 1;
				// $rowcount = ((int) $key % (int) $this->columns) + 1;
				// inicia uma nova linha se for a primeira coluna
				if ($rowcount == 1) {
					$row = $counter / $this->columns;
					echo '<div class="items-row cols-'.(int) $this->columns.' row-'.$row.' row">';
				}

				$itemState = $item->state == 0 ? ' system-unpublished' : '';
				$this->item = &$item;

				// CLASSE INDICANDO A CATEGORIA
				// retira os acentos e espaços do nome da categoria
				$classCateg = str_replace(' ','',baseHelper::removeAcentos(strtolower($this->item->category_title)));

				// MOSTRA O ITEM
				$grid = round((12 / $this->columns));
				echo '
				<div class="col-sm-'.$grid.'">
					<div class="item '.$layout.' '.$classCateg.' column-'.$rowcount.$itemState.' clearfix" style="'.$itemHeight.'">
						'.$this->loadTemplate('intro').'
					</div><!-- end item -->
				</div><!-- end col -->
				';

				$counter++;

				// fecha a linha se for a última coluna
				if (($rowcount == $this->columns) or ($counter == $introcount)) echo '</div><!-- end row -->';

			endforeach;

		endif;

	// FECHA CONTAINER
	echo '</div>';

	// LINKS PARA MAIS ITENS
	echo $links;

	// PAGINAÇÃO
	echo $pager;

	?>

</div>
