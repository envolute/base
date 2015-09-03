<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */

// ENDEREÇO

	// RUA
	$address = ($this->contact->address && $this->params->get('show_street_address')) ? '<span class="contact-street">'.$this->contact->address.'</span><br />' : '';

	// CEP
	$address .= ($this->contact->postcode && $this->params->get('show_postcode')) ? '<span class="contact-postcode right-space">'.$this->contact->postcode.'</span>' : '';
	
	// BAIRRO
	if ($this->contact->suburb && $this->params->get('show_suburb')) :
		$address .= '
		<span class="contact-suburb">
			'.($this->contact->state && $this->params->get('show_state')) ? $this->contact->suburb.', ' : $this->contact->suburb.'
		</span>
		';
	endif;
	
	// ESTADO
	if ($this->contact->state && $this->params->get('show_state')) :
		$address .= '
		<span class="contact-state">
			'.($this->contact->country && $this->params->get('show_country')) ? $this->contact->state.', ' : $this->contact->state.'
		</span>
		';
	endif;
	
	// PAÍS
	$address .= ($this->contact->country && $this->params->get('show_country')) ? '<span class="contact-country">'.$this->contact->country.'</span>' : '';

// CONTATO

	// TELEFONE
	$label = '<span class="glyphicon glyphicon-phone-alt" ></span> '.$this->contact->telephone;
	$contact = ($this->contact->telephone && $this->params->get('show_telephone')) ? '<li class="contact-phone">'.$label.'</li>' : '';
	
	// CELULAR
	$label = '<span class="glyphicon glyphicon-earphone" ></span> '.$this->contact->mobile;
	$contact .= ($this->contact->mobile && $this->params->get('show_mobile')) ? '<li class="contact-mobile">'.$label.'</li>' : '';
	
	// FAX
	$label = '<span class="glyphicon glyphicon-print" ></span> '.$this->contact->fax;
	$contact .= ($this->contact->fax && $this->params->get('show_fax')) ? '<li class="contact-fax">'.$label.'</li>' : '';
	
	// EMAIL
	$label = '<span class="glyphicon glyphicon-envelope" ></span> '.$this->contact->email_to;
	$contact .= ($this->contact->email_to && $this->params->get('show_email')) ? '<li class="contact-emailto">'.$label.'</li>' : '';
	
	// URL
	$label = '<span class="glyphicon glyphicon-share" ></span> <a title="Website" href="'.$this->contact->webpage.'" target="_blank">Website</a>';
	$contact .= ($this->contact->webpage && $this->params->get('show_webpage')) ? '<li class="contact-webpage">'.$label.'</li>' : '';
	
// TAGS
	
	$tags = '';
	
	if ($this->params->get('show_tags', 1) && !empty($this->item->tags)) :
		$this->item->tagLayout = new JLayoutFile('joomla.content.tags');
		$tags = $this->item->tagLayout->render($this->item->tags->itemTags);
	endif;

if($address || $contact || $tags) :

	echo '<div class="contact-address clearfix">';
	
		// CATEGORIA
		echo $categ;
		
		// RUA
		// CEP, BAIRRO, ESTADO, PAÍS
		if ($address) :
			echo '<address>'.$address.'</address>';
		endif;
		
		// TELEFONE, CELULAR, FAX, EMAIL, WEBPAGE
		if ($contact):
			
			echo '<ul class="hlist hlist-no-space clearfix">'.$contact.'</ul>';
		endif;
	
		// TAGS
		echo $tags;
	
	echo '</div>';

endif;