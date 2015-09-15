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
JHtml::_('behavior.framework');
JHtml::_('behavior.tooltip');

// IMPORTANTE: Carrega o arquivo 'helper' do template
include_once JPATH_BASE.'/templates/base/core/libs/php/helper.php';

// VIEW PARAMS
$this->leading = true;
$this->showTitle = $params->get('lead_show_title');
$this->fontSize = $params->get('lead_title_size');
$this->headerLength = $params->get('title_length');
$this->showImage = $params->get('lead_show_image');
$this->imgW = ($params->get('lead_image_width')) ? str_replace('%','',str_replace('px','',$params->get('lead_image_width'))) : NULL;
$this->imgH = ($params->get('lead_image_height')) ? str_replace('%','',str_replace('px','',$params->get('lead_image_height'))) : NULL;
$this->imageStyle = $params->get('lead_image_style');
$this->imgDef = $params->get('lead_image_default');
$this->showDescription = $params->get('lead_show_item_description');
$this->introLength = $params->get('lead_intro_length');
	
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
	
// HEADER

	// tamnho máx para o título
	if(!empty($this->headerLength)) :
		$texto	= $this->escape($this->item->title);
		$title  = baseHelper::textLimit($texto, $this->headerLength);;
	else :
		$title  = $this->escape($this->item->title);
	endif;		
		
	$head = '<h3 class="item-title" style="font-size:'.$this->fontSize.';">'.$title.'</h3>';
	
// CONTEÚDO / INTRODUÇÃO
	
	$intro = '';
	
	if($this->showDescription == 1) :
		
		$intro = $this->item->introtext;
		// Limite de caracteres -> se for informado, o html do texto é retirado. vide: strip_tags()
		if($this->introLength) :
			$texto  = strip_tags($intro);
			$intro  = baseHelper::textLimit($texto, $this->introLength);
		endif;
		$intro  = '<p>'.$intro.'</p>';
		
	endif;
	
// IMAGEM
	
	$image = '';
		
	// Verifica se existe uma imagem para a introdução
	$img = (isset($images->image_intro) && !empty($images->image_intro)) ? $images->image_intro : NULL;
	if($this->showImage != 2) :
		// Senão houver imagem de introdução, verifica se tem uma para o conteúdo
		$img = (is_null($img) && isset($images->image_fulltext) && !empty($images->image_fulltext)) ? $images->image_fulltext : $img;
		// Se não houverem imagens de conteúdo e for informada uma imagem default...
		$img = (is_null($img) && isset($this->imgDef) && !empty($this->imgDef)) ? $this->imgDef : $img;
	endif;
	
	if(is_null($img)) $img = JURI::root()."images/template/no-image.jpg";
		
	// Para imagens remotas ou sem largura e altura definidas, será mostrada a imagem original... 
	$imgFile = baseHelper::thumbnail($img,$this->imgW,$this->imgH);
			
	// container interno -> controla o tamanho, ou a área visível da imagem
	$cssW = ($this->imgW) ? 'width:'.$this->imgW.'px;' : '';
	$cssH = ($this->imgH) ? 'height:'.$this->imgH.'px;' : '';
	$image .= '<div class="img-responsive" style="width:100%;'.$cssH.'overflow:hidden;">';
	
		// imagem
		// propriedades da imagem
		$props  = ' src="'.$imgFile.'"';
		$props .= ' title="'.$head.$intro.'"';
		$imgTag = '<img class="img-responsive" '.$props.' />';
		$image .= '<a href="'.$link.'">'.$imgTag.'</a>';
	
	// fecha container interno			
	$image .= '</div>';
	
	// slider
	echo '<a href="'.$link.'">'.$image.'</a>';
	
?>