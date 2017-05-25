<?php
/**
* Author:	Ivo Junior
* Email:	dev@envolute.com
* Website:	http://www.envolute.com
* Component: Base Content
* Version:	1.0.0
* Date:		24/02/2017
* copyright	Copyright (C) 2012 http://www.envolute.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class BaseContentViewInclude extends JViewLegacy {

	protected $params;

	public function display($tpl = null) {

		$app	= JFactory::getApplication();
		$params = $app->getParams();
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();

		if($menu) :
			$params->set('page_heading', $params->get('page_heading', $menu->title));
		else :
			$params->set('page_title', '');
		endif;

		$title = $params->get('page_title');
		if ($app->getCfg('sitename_pagetitles', 0)) :
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		endif;
		$this->document->setTitle($title);

		if ($params->get('menu-meta_description')) :
			$this->document->setDescription($params->get('menu-meta_description'));
		endif;

		if ($params->get('menu-meta_keywords')) :
			$this->document->setMetadata('keywords', $params->get('menu-meta_keywords'));
		endif;

		if ($params->get('robots')) :
			$this->document->setMetadata('robots', $params->get('robots'));
		endif;

		$this->assignRef('params', $params);
		$this->phpFile = $params->get('phpFile');
		$this->code = $params->get('code');

		parent::display($tpl);

	}

}
