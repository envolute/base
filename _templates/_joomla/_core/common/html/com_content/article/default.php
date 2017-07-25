<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params  = $this->item->params;
$images  = json_decode($this->item->images);
$urls    = json_decode($this->item->urls);
$canEdit = $params->get('access-edit');
$user    = JFactory::getUser();
$info    = $params->get('info_block_position', 0);

// Check if associations are implemented. If they are, define the parameter.
$assocParam = (JLanguageAssociations::isEnabled() && $params->get('show_associations'));
JHtml::_('behavior.caption');

$doc	 = JFactory::getDocument();

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/libraries/envolute/helpers/base.php');

// CATEGORIA

	$categ = '';

	if (($params->get('show_parent_category') && !empty($this->item->parent_slug)) || $params->get('show_category')) :

		// IMPORTANT: Desabilita a categoria raíz -> 'publicações'...
		$parentDisable = ($this->item->parent_alias == 'posts') ? true : false;

		// Categoria pai
		if (!$parentDisable && $params->get('show_parent_category') && !empty($this->item->parent_slug)) :
			$title = $this->escape($this->item->parent_title);
			$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$title.'</a>';
			$title = ($params->get('link_parent_category') && !empty($this->item->parent_slug)) ? $url : $title;
			$categ .= '<span class="parent-category-name">'.$title.' &raquo; </span>';
		endif;

		// Categoria
		if ($params->get('show_category')) :
			$title = $this->escape($this->item->category_title);
			$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';
			$title = ($params->get('link_category') && $this->item->catslug) ? $url : $title;
			$categ .= '<span class="category-name">'.$title.'</span>';
		endif;

	endif;

// AÇÕES -> FONTSIZE, PLUGINS DE CONTEÚDO(beforeDisplayContent), {PRINT, E-MAIL, EDIT}

	$actions = '';

	if (!$this->print) :
		if ($params->get('show_icons') || $canEdit) :

			// PRINT & SEND
			// Links para imprimie e enviar
			if($params->get('show_print_icon') || $params->get('show_email_icon')) :
				$actions .= '<span class="float-left mr-1">';
					if ($params->get('show_print_icon'))
					$actions .= '<span class="btn btn-default btn-xs mr-1">'.JHtml::_('icon.print_popup', $this->item, $params).'</span>';

					if ($params->get('show_email_icon'))
					$actions .= '<span class="btn btn-default btn-xs">'.JHtml::_('icon.email', $this->item, $params).'</span>';
				$actions .= '</span>';
			endif;

			// BTN EDIT
			// Link para editar o conteúdo
			if($canEdit)
			$actions .= '<div class="btn btn-success btn-xs ml-1 float-right">'.JHtml::_('icon.edit', $this->item, $params).'</div>';

			// FONT SIZER
			// Carrega a funcionalidade para alteração do tamanho da fonte como um recurso de acessibilidade
			$actions .= '<span id="fontsize" class="btn-group float-right"></span>';
			$doc->addScriptDeclaration('jQuery(function() { jQuery("#fontsize").fontSize(".content-text"); });');

		endif;
	else :
		$actions .= '<div class="btn btn-default btn-xs float-right hidden-print">'.JHtml::_('icon.print_screen', $this->item, $params).'</div>';
	endif;

// PAGE HEADING

	$heading = '';

	// Page Heading
	if ($this->params->get('show_page_heading')) :
		$heading = $this->escape($this->params->get('page_heading'));
	// Ou Categoria [Categoria Pai > Categoria]
	elseif($categ) :
		$heading = $categ;
		$categ = '';
	endif;

	if(!empty($pagehead) || !empty($actions)) :
		$pagehead = '<h4 id="item-page-heading" class="page-header clearfix">';
			if(!empty($heading)) :
				$pagehead .= $heading;
				$pagehead .= '<span class="float-right">'.$actions.'</span>';
			elseif(!empty($actions)):
				$pagehead .= $actions;
			endif;
		$pagehead .= '</h4>';
	endif;

// INFORMAÇÕES DE PUBLICAÇÃO -> *CATEGORIA, AUTOR, DATA DE PUBLICAÇÃO, ACESSOS.
// (*) Caso a categoria não seja mostrada no 'Page Heading', é visualizada aqui...

	$info = '';

	if ($params->get('show_publish_date') || $params->get('show_author') || $params->get('show_hits')) :

		$info .= '<ul id="item-publish-info" class="set-list inline bordered list-sm list-trim small">';

			// CATEGORIA -> caso a page-heading seja informada
			if(!empty($categ)) $info .= '<li class="category">'.$categ.'</li>';

			// AUTOR
			if ($params->get('show_author') && !empty($this->item->author)) :
				$author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author;
				$info .= '<li class="createdby">'.JText::sprintf('COM_CONTENT_WRITTEN_BY', $author).'</li>';
				$elem = true;
			endif;

			// DATA DE PUBLICAÇÃO
			if ($params->get('show_publish_date')) :
				$info .= '<li class="published">';
				$info .= JText::sprintf(JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC3')));
				$info .= '</li>';
				$elem = true;
			endif;

			// ACESSOS
			if ($params->get('show_hits')):
				$info .= '<li class="hits">'.JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits).'</li>';
			endif;

			// UNPUBLISHED ALERT
			if ($this->item->state == 0) :
				echo '<li class="unpublished"><span class="badge badge-warning">'.JText::_('JUNPUBLISHED').'</span></li>';
			endif;

			// NOT PUBLISHED ALERT
			if (strtotime($this->item->publish_up) > strtotime(JFactory::getDate())) :
				echo '<li class="notpublished"><span class="badge badge-warning">'.JText::_('JNOTPUBLISHEDYET').'</span></li>';
			endif;

			// EXPIRED ALERT
			if ((strtotime($this->item->publish_down) < strtotime(JFactory::getDate())) && $this->item->publish_down != JFactory::getDbo()->getNullDate()) :
				echo '<li class="expired"><span class="badge badge-warning">'.JText::_('JEXPIRED').'</span></li>';
			endif;

		$info .= '</ul>';

	endif;

// IMAGEM PRINCIPAL

	$image = '';

	if (isset($images->image_fulltext) && !empty($images->image_fulltext)) :

		// Posição da imagem -> esquerda, direita, centralizada
		$imgfloat = empty($images->float_fulltext) ? $params->get('float_fulltext') : $images->float_fulltext;

		$image .= '<div class="item-image to-'.htmlspecialchars($imgfloat).'">';

			// Legenda da imagem
			$caption = ($images->image_fulltext_caption) ? ' caption' : '';

			// Propriedades da imagem
			$props  = 'src="'.htmlspecialchars($images->image_fulltext).'"';
			$props .= ' title="'.htmlspecialchars($images->image_fulltext_caption).'"';
			$props .= ' alt="'.htmlspecialchars($images->image_fulltext_alt).'"';
			$props .= ' class="img-fluid'.$caption.'"';

			$image .= '<img '.$props.' />';

		$image .= '</div>';

		// FACEBOOK TAGS
		$imgSrc	= htmlspecialchars($images->image_fulltext);
		// IMPORTANTE: configura metatag para mostrar imagem quando compartilhar no facebook
		$doc->setMetaData("og:image",JURI::root().$imgSrc); // 1 image for Like/Send
		$doc->setMetaData("image",JURI::root().$imgSrc); // 1 image for Like/Send
		$doc->addCustomTag('<link rel="image_src" href="'.JURI::root().$imgSrc.'" />'); //Multiple for Share

	// Caso não tenha a imagem principal, configura a imagem de introdução como opção para o facebook
	elseif (isset($images->image_intro) && !empty($images->image_intro)) :

		$imgSrc	= htmlspecialchars($images->image_intro);
		// IMPORTANTE: configura metatag para mostrar imagem quando compartilhar no facebook
		$doc->setMetaData("og:image",JURI::root().$imgSrc); // 1 image for Like/Send
		$doc->setMetaData("image",JURI::root().$imgSrc); // 1 image for Like/Send
		$doc->addCustomTag('<link rel="image_src" href="'.JURI::root().$imgSrc.'" />'); //Multiple for Share

	endif;

// NAVEGAÇÃO ENTRE ARTIGOS -> PAGINAÇÃO

	$pagenav = (!empty($this->item->pagination) && $this->item->pagination) ? 1 : 0;
	// Depois do conteúdo
	$navAfter = ($pagenav) ? '<div class="pagenav-after">'.$this->item->pagination.'</div>' : '';

// LISTA DE LINKS

	// Depois do conteúdo
	$linksAfter = (isset($urls) && ((!empty($urls->urls_position) && ($urls->urls_position == '1')) || ($params->get('urls_position') == '1'))) ? $this->loadTemplate('links') : '';

// TOC -> tabela de links de quebra de página

	$toc = (isset($this->item->toc)) ? $this->item->toc : '';

// TAGS

	$tags = '';

	if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) :
		$this->item->tagLayout = new JLayoutFile('joomla.content.tags');
		$tags = '<hr class="my-sm" />';
		$tags .= '<div class="tags">';
		if($params->get('tags_label')) $tags .= '<span class="content-tags-label">'.$params->get('tags_label').'</span>';
		$tags .= $this->item->tagLayout->render($this->item->tags->itemTags);
		$tags .= '</div>';
	endif;

?>

<div class="item-page" itemscope itemtype="https://schema.org/Article">
	<meta itemprop="inLanguage" content="<?php echo ($this->item->language === '*') ? JFactory::getConfig()->get('language') : $this->item->language; ?>" />
	<?php

	// PAGE HEADING
	echo $pagehead;

	// TÍTULO
	if ($params->get('show_title'))
	echo '<h2 class="m-0 pb-4" itemprop="headline">'.$this->escape($this->item->title).'</h2>';

	// INFO
	echo $info;

	// INTRODUÇÃO
	if ($params->get('show_intro') && !empty($this->item->event->afterDisplayTitle))
	echo '<div class="text-muted pb-4">'.$this->item->event->afterDisplayTitle.'</div>';

	// PLUGINS ANTES DO CONTEÚDO
	if ($this->item->event->beforeDisplayContent) :
		echo '<div class="before-content">'.$this->item->event->beforeDisplayContent.'</div>';
	endif;

	if ($params->get('access-view')):

		// TOC
		echo $toc;

		echo '<div id="item-content-text" itemprop="articleBody">';

			// IMAGEM PRINCIPAL
			// '$start = 1,2,3...' indica que é uma quebra de página
			echo (isset($_REQUEST['start']) && $_REQUEST['start'] > 1) ? '' : $image;

			// CONTEÚDO
			echo $this->item->text;

		echo '</div>';

		// TAGS
		echo $tags;

		echo '<div class="clearfix"></div>';

		// LINKS NO FIM
		echo isset($linksAfter) ? $linksAfter : '';

		// PAGINAÇÃO NO FIM
		echo isset($pagerAfter) ? $pagerAfter : '';

	// introdução opcional para visitantes 'guests'
	elseif ($params->get('show_noauth') == true && $user->get('guest')) :

		echo $this->item->introtext;

		//Optional link to let them register to see the whole article.
		if ($params->get('show_readmore') && $this->item->fulltext != null) :
			$link1 = JRoute::_('index.php?option=com_users&view=login');
			$link = new JURI($link1);
			echo '<p class="readmore">';
				echo '<a href="'.$link.'">';
					$attribs = json_decode($this->item->attribs);
					if ($attribs->alternative_readmore == null) :
						echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
					elseif ($readmore = $this->item->alternative_readmore) :
						echo $readmore;
						if ($params->get('show_readmore_title', 0) != 0) :
							echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
						endif;
					elseif ($params->get('show_readmore_title', 0) == 0) :
						echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
					else :
						echo JText::_('COM_CONTENT_READ_MORE');
						echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
					endif;
				echo '</a>';
			echo '</p>';
		endif;
	endif;

	// NAVEGAÇÃO ENTRE ARTIGOS
	echo $navAfter;

	// PLUGINS (AfterDisplayContent) -> carrega plugins após o conteúdo completo
	echo '<div class="itempage-plugins-after clearfix">'.$this->item->event->afterDisplayContent.'</div>';
	?>

</div>
