<?php
/**
* Author:	Ivo Junior
* Email:	dev@envolute.com
* Website:	http://www.envolute.com
* Component: Base Free Content
* Version:	1.0.0
* Date:		13/12/2016
* copyright	Copyright (C) 2012 http://www.envolute.com. All Rights Reserved.
* @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
**/

defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class BaseContentController extends JControllerLegacy {

	public function display($cachable = false, $urlparams = false) {

		$vName = $this->input->getCmd('view', 'default');
		$this->input->set('view', $vName);

		return parent::display($cachable, $urlparams);

	}

}
