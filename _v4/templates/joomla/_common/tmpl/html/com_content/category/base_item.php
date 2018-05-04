<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Create a shortcut for params.
$params = $this->item->params;
$images = json_decode($this->item->images);
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$canEdit = $this->item->params->get('access-edit');
// $info    = $params->get('info_block_position', 0);

// LINK
if ($params->get('access-view')) :
	$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));
else :
	$menu = JFactory::getApplication()->getMenu();
	$active = $menu->getActive();
	$itemId = $active->id;
	$link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false));
	$link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
endif;

// TITLE
$title = $this->escape($this->item->title);

// DESCRIPTION
$description = '';
if(!empty($this->item->introtext)) :
	$text = $this->item->introtext;
	// Limite de caracteres -> se for informado, o html do texto é retirado. vide: strip_tags()
	if($this->introLength) $text = baseHelper::textLimit(strip_tags($text), $this->introLength);
	//remove {tags} de plugins
	$description = trim(preg_replace('/\s*\{[^}]*\}/', '', $text));
endif;

// IMAGEM
$image = '';
$img = (isset($images->image_intro) && !empty($images->image_intro)) ? $images->image_intro : '';
// Senão houver imagem de introdução, verifica a imagem do conteúdo
if(empty($img) && isset($images->image_fulltext) && !empty($images->image_fulltext)) $img = $images->image_fulltext;
// Senão, a imagem default...
if(empty($img) && isset($this->imgDef) && !empty($this->imgDef)) $img = $this->imgDef;
// ---------------------------------
if(!empty($img)) :
	// Imagem
	$imagePath = baseHelper::thumbnail($img,$this->imgW,$this->imgH);
	//Obs: Para imagens remotas ou sem largura e altura definidas, será mostrada a imagem original...
	$image = '<img src="'.$imagePath.'" class="img-fluid '.$this->imgClass.'" '.$this->imgProps.'/>';
	// Se for uma imagem remota, adiciona um container '.image-container' à imagem
	// Isso serve para controlar o tamanho da imagem
	if(stripos($img,'://') !== false && !empty($this->imgW) && !empty($this->imgH)) :
		$image = '
			<span class="image-container '.$this->imgClass.'" '.$this->imgProps.' style="width:'.$this->imgW.'px;height:'.$this->imgH.'px">
				'.$image.'
			</span>
		';
	endif;
endif;

// CATEGORY
$cat = $this->escape($this->item->category_title);
$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$cat.'</a>';
$category = ($params->get('link_category') && $this->item->catslug) ? $url : $cat;

// AUTHOR
$author = $this->item->author;
$author = ($this->item->created_by_alias ? $this->item->created_by_alias : $author);
if (!empty($this->item->contactid ) && $params->get('link_author') == true) :
	$author = JHtml::_('link', JRoute::_('index.php?option=com_contact&view=contact&id='.$this->item->contactid), $author);
endif;

// PUBLISH DATE
$publish_date = JText::sprintf(JHtml::_('date', $this->item->publish_up, $this->dateFormat));

// HITS
$hits = JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits);

// TAGS
$tags = '';

// PLUGINS
$afterDisplayTitle = ($params->get('showPlugins', 1) == 1) ? '<div class="item-plugins-title">'.$this->item->event->afterDisplayTitle.'</div>' : '';
$beforeDisplayContent = ($params->get('showPlugins', 1) == 1) ? '<div class="item-plugins-before">'.$this->item->event->beforeDisplayContent.'</div>': '';
$afterDisplayContent = ($params->get('showPlugins', 1) == 1) ? '<div class="item-plugins-after">'.$this->item->event->afterDisplayContent.'</div>' : '';

// REPLACE TAGS
$get = Array();
$get[] = '{ITEM LINK}';
$get[] = '{ITEM TITLE}';
$get[] = '{ITEM DESCRIPTION}';
$get[] = '{ITEM IMAGE}';
$get[] = '{ITEM IMAGEPATH}';
$get[] = '{ITEM CATEGORY}';
$get[] = '{ITEM AUTHOR}';
$get[] = '{ITEM PUBLISH_DATE}';
$get[] = '{ITEM HITS}';
$get[] = '{ITEM TAGS}';
$get[] = '{ITEM AFTERDISPLAYTITLE}';
$get[] = '{ITEM BEFOREDISPLAYCONTENT}';
$get[] = '{ITEM AFTERDISPLAYCONTENT}';
$set = Array();
$set[] = $link;
$set[] = $title;
$set[] = $description;
$set[] = $image;
$set[] = $imagePath;
$set[] = $category;
$set[] = $author;
$set[] = $publish_date;
$set[] = $hits;
$set[] = $tags;
$set[] = $afterDisplayTitle;
$set[] = $beforeDisplayContent;
$set[] = $afterDisplayContent;

// LAYOUT
$layout = str_replace($get, $set, $this->layout);

// RETURN
echo $layout;
?>
