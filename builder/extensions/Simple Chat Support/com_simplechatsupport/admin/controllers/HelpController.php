<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */
 
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class SimpleChatSupportControllerHelp extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();
		
		$view = $this->getView( 'help', 'html' );
		$view->setModel( $this->getModel( 'Help' ) );
		JRequest::setVar('view', 'help');
	}
	
	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}
}
?>