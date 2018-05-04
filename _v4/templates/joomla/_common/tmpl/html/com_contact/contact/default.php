<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$cparams = JComponentHelper::getParams('com_media');

// IMPORTANTE: Carrega o arquivo 'helper' do template
include_once JPATH_BASE.'/libraries/envolute/helpers/base.php';

// PAGE-HEADING || CATEGORIA, FILTRO (lista de usuários da categoria)

	// CATEGORIA
	$categ = '';

	if ($this->params->get('show_contact_category') == 'show_no_link') :
		$categ .= $this->contact->category_title;
	elseif ($this->params->get('show_contact_category') == 'show_with_link') :
		$contactLink = ContactHelperRoute::getCategoryRoute($this->contact->catid);
		$categ .= '<a href="'.$contactLink.'">'.$this->escape($this->contact->category_title).'</a>';
	endif;

	$head = $filter = '';
	if ($this->params->get('show_page_heading') || $categ || ($this->params->get('show_contact_list') && count($this->contacts) > 1)) :

		//PAGE-HEADING || CATEGORIA
		if ($this->params->get('show_page_heading')) :
			$head .= $this->escape($this->params->get('page_heading'));
		elseif ($categ) :
			$head .= $categ;
		endif;

		// FILTRO
		if ($this->params->get('show_contact_list') && count($this->contacts) > 1) :
			$filter .= '
			<form action="#" method="get" name="selectForm" id="selectForm" class="filter float-right">
				'.JHtml::_('select.genericlist', $this->contacts, 'id', 'class="inputbox" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link).'
			</form>
			';
		endif;

		$head = ($head) ? '<h4 class="page-header clearfix">'.$head.$filter.'</h4>' : $filter;

  endif;

// NOME

	$name = ($this->contact->name && $this->params->get('show_name')) ? $this->contact->name : '';

// POSIÇÃO

	$pos = ($this->contact->con_position && $this->params->get('show_position')) ? '<span class="badge badge-info">'.$this->contact->con_position.'</span>' : '';

// TAG DESPUBLICADO

	$unpub = ($this->item->published == 0) ? '<span class="badge badge-warning">'.JText::_('JUNPUBLISHED').'></span>' : '';

// IMAGEM

        $image = '';
        $s = '65px';
        $sz = ' style="width:'.$s.';height:'.$s.'"';
        if ($this->contact->image && $this->params->get('show_image')) :
                $image = '<img class="img-thumbnail obj-to-left" src="'.baseHelper::thumbnail($this->contact->image,$s,$s).'" '.$sz.' />';
        endif;

// LINK VCARD

	$vcard = '';

	if ($this->params->get('allow_vcard')) :
		$vcard =
		JText::_('COM_CONTACT_DOWNLOAD_INFORMATION_AS').'
		<a href="'.JRoute::_('index.php?option=com_contact&amp;view=contact&amp;id='.$this->contact->id . '&amp;format=vcf').'">
			'.JText::_('COM_CONTACT_VCARD').'
		</a>
		';
        endif;

// OUTRAS INFORMAÇÕES

	$info = ($this->contact->misc && $this->params->get('show_misc')) ? '<div class="contact-miscinfo">'.$this->contact->misc.'</div>' : '';

// FORMULÁRIO

	$form = ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) ? $this->loadTemplate('form') : '';

// LINKS

	$links = ($this->params->get('show_links')) ? $this->loadTemplate('links') : '';

// ARTIGOS

	$posts = '';

	if ($this->params->get('show_articles') && $this->contact->user_id && $this->contact->articles) :
		$posts = '<h4>'. JText::_('JGLOBAL_ARTICLES').'</h4>'.$this->loadTemplate('articles');
	endif;

// PERFIL

        $profile = '';

        if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) :
                $profile = '<h4>'. JText::_('COM_CONTACT_PROFILE').'</h4>'.$this->loadTemplate('profile');
	endif;
?>

<div class="contact">

	<?php

	// PAGE-HEADING || CATEGORIA, FILTRO (lista de usuários da categoria)
	echo $head;

	// IMAGEM
	echo $image;

	// NOME, POSIÇÃO, TAG DESPUBLICADO
	echo ($name || $pos || $unpub) ? '<h4>'.$name.' '.$pos.' '.$unpub.'</h4>' : '';

        // ENDEREÇO
        echo $this->loadTemplate('address');

        // LINK VCARD
        echo $vcard;

	// OUTRAS INFORMAÇÕES
	echo $info;

	// FORMULÁRIO
	echo ($this->params->get('show_email_form') && ($this->contact->email_to || $this->contact->user_id)) ? $this->loadTemplate('form') : '';

        // LINKS
        echo $links;

	//ARTIGOS
	echo $posts;

	// PERFIL
	echo $profile;

	?>

</div>
