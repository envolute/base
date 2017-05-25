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

defined( '_JEXEC' ) or die( 'Restricted access' );

$app = JFactory::getApplication();
$admin = $app->isAdmin();
if($admin==1) {
	echo 'Base Free Content';
} else {
	jimport('joomla.application.component.controller');
	// Create the controller
	$controller = JControllerLegacy::getInstance('FreeContent');
	// Perform the Request task
	$controller->execute(JRequest::getCmd('task'));
	// Redirect if set by the controller
	$controller->redirect();
}
 ?>
