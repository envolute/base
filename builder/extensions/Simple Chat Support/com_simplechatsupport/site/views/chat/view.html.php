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

jimport( 'joomla.application.component.view' );
jimport('joomla.application.component.helper');
	
class SimpleChatSupportViewChat extends JViewLegacy
{

	function display( $tpl = null)
	{
		$document = JFactory::getDocument();
		$mainframe = JFactory::getApplication();
		$document->setTitle(JText::_( 'COM_SIMPLECHATSUPPORT_CHAT_TITLE' ));
		
		$params = JComponentHelper::getParams('com_simplechatsupport');
		
		$this->message = $params->get('message');
		$this->message_on = $params->get('message_on');
		$this->message_off = $params->get('message_off');

		parent::display($tpl);

	}
}
?>