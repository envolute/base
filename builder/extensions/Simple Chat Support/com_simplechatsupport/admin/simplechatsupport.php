<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

// Require the base controller	
require_once (JPATH_COMPONENT.DS.'controller.php');

$controllerName = JRequest::getVar('controller');

switch ($controllerName)
{	case "messages":
	require_once( JPATH_COMPONENT.DS.'controllers'.DS.'MessageController.php' );
	$controller = new SimpleChatSupportControllerMessages();
	$controller->execute( JRequest::getCmd('task') );
	$controller->redirect();
	break;
	
	default:
	$controller = new SimpleChatSupportController();
	$controller->execute(JRequest::getCmd('task'));
	$controller->redirect();
	break;}

?>