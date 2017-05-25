<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Create a shortcut for params.
$params = &$this->item->params;
$images = json_decode($this->item->images);
$canEdit = $this->item->params->get('access-edit');
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
$categ = $this->item->params->get('info_block_position', 0);
JHtml::_('behavior.tooltip');
JHtml::_('behavior.framework');

// LINK PARA O CONTEÚDO

	if ($params->get('access-view')) :
		$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
	else :
		$menu = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		$itemId = $active->id;
		$link1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
		$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
		$link = new JURI($link1);
		$link->setVar('return', base64_encode($returnURL));
	endif;

	$setContentMode = $setModalParams = '';
	if ($params->get('content_mode') == 1) :
		$setContentMode = 'set-modal';
		$setModalParams = ' data-modal-iframe="true" data-modal-width="'.$params->get('modal_width', '95%').'" data-modal-height="'.$params->get('modal_height', '95%').'" ';
		$setModalParams .= ($params->get('modal_header', 1)) ? 'data-modal-title="'.$this->escape($this->item->title).'" ' : '';
	elseif ($params->get('content_mode') == 2) :
		$setContentMode = 'view-content-custom';
	endif;

// INTRODUÇÃO

	$intro = '';

	if ($this->showDescription != 0) :

		if($this->showDescription == 1) :
			$intro = $this->item->introtext;
		elseif($this->showDescription == 2) :
			$intro = !empty($this->item->fulltext) ? $this->item->introtext : '';
		endif;

		if(!empty($intro)) :
			// Limite de caracteres -> se for informado, o html do texto é retirado. vide: strip_tags()
			if($this->introLength) :
				$texto  = strip_tags($intro);
				$intro  = baseHelper::textLimit($texto, $this->introLength);
			endif;
			//remove {tags} de plugins
			$intro  = trim(preg_replace('/\s*\{[^}]*\}/', '', $intro));
			$intro  = '<div class="item-intro">'.$intro.'</div>';
		endif;

	endif;

// EXTRAFIELDS

	// load API
	$extrafields = JPATH_BASE.'/templates/base/source/extrafields/extrafields_category.php';
	if(file_exists($extrafields)) :
		include_once $extrafields;
		$intro  .= $efields;
	endif;

// IMAGEM

	$image = '';

	if($this->showImage == 1) :

		// Verifica se existe uma imagem para a introdução
		$img = (isset($images->image_intro) && !empty($images->image_intro)) ? $images->image_intro : NULL;
		if($this->showImage != 2) :
			// Senão houver imagem de introdução, verifica se tem uma para o conteúdo
			$img = (is_null($img) && isset($images->image_fulltext) && !empty($images->image_fulltext)) ? $images->image_fulltext : $img;
			// Se não houverem imagens de conteúdo e for informada uma imagem default...
			$img = (is_null($img) && isset($this->imgDef) && !empty($this->imgDef)) ? $this->imgDef : $img;
		endif;

		// Determina a posição da imagem
		$imgfloat = (empty($images->float_intro)) ? $params->get('float_intro') : $images->float_intro;
		$imgfloat = ($this->forceImageFloat == "") ? $imgfloat : $this->forceImageFloat;

		if(!is_null($img)) :

			// Para imagens remotas ou sem largura e altura definidas, será mostrada a imagem original...
			$imgFile = baseHelper::thumbnail($img,$this->imgW,$this->imgH);

			// container principal -> controla o posicionamento e define a moldura da imagem
			$image = '<div class="item-image obj-to-'.htmlspecialchars($imgfloat).'" style="width:'.$this->imgW.'px;">';

				// ícone para preview da imagem
				if($this->imgZoom) :
					$image .= '<a class="img-zoom set-modal" rel="image-preview" href="'.JURI::root(true).'/'.htmlspecialchars($img).'" data-modal-title="'.$this->escape($this->item->title).'">Zoom</a>';
				endif;

				// imagem
				// propriedades da imagem
				$props  = ' src="'.$imgFile.'"';
				$props .= ' alt="'.htmlspecialchars($images->image_intro_alt).'"';
				$imgTag = '<img class="img-responsive '.$this->imageStyle.'" '.$props.' />';
				$layer  = '<span class="item-image-layer"></span>';
				$image .= '<a href="'.$link.'" class="'.$setContentMode.'"'.$setModalParams.'>'.$imgTag.$layer.'</a>';

			// fecha container principal
			$image .= '</div>';

		endif;

	endif;

// HEADER -> TÍTULO

	$head = '';

	if ($this->showTitle) :

		// tamnho máx para o título
		if(!empty($this->headerLength)) :
			$texto = $this->escape($this->item->title);
			$title = baseHelper::textLimit($texto, $this->headerLength);
		else :
			$title = $this->escape($this->item->title);
		endif;

		// tamanho da fonte
		$hh = 'font-size:'.$this->fontSize.';';

		// altura mínima do header
		$hh .= (!empty($this->headerHeight)) ? 'min-height:'.str_replace('px','',$this->headerHeight).'px' : '';

		// no formato 'galeria de imagens' a introdução fica no título
		if($this->descriptionPos) $title .= $intro;

		// verifica se o título tem link..
		$head = ($params->get('link_titles')) ? '<a href="'.$link.'" class="'.$setContentMode.'"'.$setModalParams.'>'.$title.'</a>' : $title;

		// aviso se o item não estiver publicado
		if($this->item->state == 0) $head .= '<span class="label label-warning pull-right">'.JText::_('JUNPUBLISHED').'</span>';

		$head = '<h3 class="item-title" style="'.$hh.'">'.$head.'</h3>';

	endif;

// INFORMAÇÕES DE PUBLICAÇÃO
// Categoria pai, Categoria, Autor, Data de Publicação e Hits;

	$info = '';

	// Verifica se a categoria do item é igual a categoria da página. Se for, não mostra...
	$redundant = ($this->item->category_title == $this->category->title) ? true : false;

	// HITS -> ACESSOS
	if ($this->showHits) :
		$info .= '
		<span class="hits label label-info small pull-right hasTooltip" title="'.JText::sprintf('COM_CONTENT_ARTICLE_HITS', '').'">
			<span class="base-icon-eye"></span>
			'.$this->item->hits.'
		</span>
		';
	endif;

	if (
	($this->showCategory && !$redundant) ||
	(!empty($this->item->author ) && $this->showAuthor) ||
	($this->showDate)
	) :

		$info .= '<div class="item-publish-info info-pos-'.$this->infoPosition.' small">';

			$info .= '<ul class="hlist hlist-no-space">';

			// INFORMAÇÕES DA CATEGORIA
			if ($this->showCategory && !$redundant) :

				$info .= '<li>';

					// Verifica se a categoria pai do item é igual a categoria da página. Se for, não mostra...
					$redundant = ($this->item->parent_title == $this->category->title) ? true : false;

					// Categoria Pai
					if ($params->get('show_parent_category') && !empty($this->item->parent_slug) && !$redundant) :
						$cat = $this->escape($this->item->parent_title);
						$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$cat.'</a>';
						$cat = ($params->get('link_parent_category') && !empty($this->item->parent_slug)) ? $url : $cat;
						$info .= '<strong class="parent-category-name">'.$cat.' &raquo; </strong>';
					endif;

					// Categoria
					if ($this->item->category_title) :
						$cat = $this->escape($this->item->category_title);
						$url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)) . '">' . $cat. '</a>';
						$cat = ($params->get('link_category') && $this->item->catslug) ? $url : $cat;
						$info .= '<strong class="category-name">'.$cat.'</strong>';
					endif;

				$info .= '</li>';
			endif;

			// AUTOR
			if (!empty($this->item->author ) && $this->showAuthor) :
				$author = ($this->item->created_by_alias) ? $this->item->created_by_alias : $this->item->author;
				$info .= '
				<li class="createdby">
					'.JText::sprintf('COM_CONTENT_WRITTEN_BY', $author).'
				</li>
				';
			endif;

			// DATA DE PUBLICAÇÃO
			if ($this->showDate) :
				$info .= '
				<li class="date-published">
					'.JText::sprintf(JHtml::_('date', $this->item->publish_up, $this->dateFormat)).'
				</li>
				';
			endif;

			$info .= '</ul>';

		$info .= '</div>';

		$infoBeforeTitle = ($this->infoPosition == 1) ? $info : '';
		$infoAfterTitle = ($this->infoPosition == 2) ? $info : '';
		$infoAfterIntro = ($this->infoPosition == 3) ? $info : '';

	endif;

// PLUGINS DE CONTEÚDO

	$pluginsTitle = '';
	$pluginsBefore = '';
	$pluginsAfter = '';

	if ($this->showPlugins == 1) :
		$pluginsTitle = '
		<div class="item-plugins-title">
			'.$this->item->event->afterDisplayTitle.'
		</div>
		';

		$pluginsBefore = '
		<div class="item-plugins-before">
			'.$this->item->event->beforeDisplayContent.'
		</div>
		';

		$pluginsAfter = '
		<div class="item-plugins-after">
			'.$this->item->event->afterDisplayContent.'
		</div>
		';
	endif;

// BOTÃO 'LEIA MAIS'

	$btn = '';

	// 'force_readmore' mostra o 'readmore' sempre, independente do tamanho da introdução...
	if ($params->get('force_readmore') || ($params->get('show_readmore') && $this->item->readmore)) :

		$btn = '<a class="readmore '.$params->get('readmore_style').' '.$setContentMode.'"'.$setModalParams.' href="'.$link.'">';

			if (!$params->get('access-view')) :
				$btn .= JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
			elseif ($readmore == $this->item->alternative_readmore) :
				$btn .= $params->get('show_readmore_text') ? $params->get('show_readmore_text') : $readmore;
				if ($params->get('show_readmore_title', 0) != 0) :
					$btn .= JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
				endif;
			elseif ($params->get('show_readmore_title', 0) == 0) :
				$btn .= JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
			else :
				$btn .= $params->get('show_readmore_text') ? $params->get('show_readmore_text') : JText::_('COM_CONTENT_READ_MORE');
				$btn .= JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
			endif;

		$btn .= '</a>';

	endif;

// TAGS

	$tags = '';

	if ($this->showTags && !empty($this->item->tags->itemTags)) :
		$this->item->tagLayout = new JLayoutFile('joomla.content.tags');
		$tags = '<div class="clear"></div>'.$this->item->tagLayout->render($this->item->tags->itemTags);
	endif;


// ITEM

	// BOTÃO PARA EDITAR CONTEÚDO
	if ($canEdit) echo '<div class="edit-icon bottom-expand"><span class="btn btn-success btn-xs">'.JHtml::_('icon.edit', $this->item, $params).'</span></div>';

	$h = $infoBeforeTitle.$head.$infoAfterTitle.$pluginsTitle;

	// QUANDO O TÍTULO FOR ANTES DA IMAGEM
	if ($this->titlePosition == 0) echo $h;

	// IMAGEM
	echo $image;

	// QUANDO O TÍTULO FOR DEPOIS DA IMAGEM
	if ($this->titlePosition == 1) echo $h;

	// CONTEÚDO -> INTRODUÇÃO
	if(!$this->descriptionPos) echo $intro;

	// INFORMAÇÕES DO CONTEÚDO -> DEPOIS INTRODUÇÃO
	echo $infoAfterIntro;

	// PLUGINS ANTES O CONTEÚDO -> entre a introdução e o botão 'leia mais'
	echo $pluginsBefore;

	// BOTÃO 'LEIA MAIS'
	echo $btn;

	// PLUGINS APÓS O CONTEÚDO
	echo $pluginsAfter;

	// TAGS
	echo $tags;

?>
