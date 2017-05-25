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
jimport('joomla.application.component.helper');

class SimpleChatSupportController extends JControllerLegacy
{
	/**
	* Custom Constructor
	*/

	function __construct()
	{
		parent::__construct();
		$view = $this->getView( 'chat', 'html' );
		
		JRequest::setVar('view', 'chat');
	}
	
	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}
	
	function sendmail()
	{
		jimport( 'joomla.mail.helper' );
		$params = JComponentHelper::getParams('com_simplechatsupport');
		$contact = $params->get( 'contact' );
		if($contact == '') {
			$config =& JFactory::getConfig();
			$contact = $config->get('mailfrom');
		}
		
		$email = JRequest::getString('email');
		$name = JRequest::getString('name');
		$subject = JRequest::getString('subject');
		$text = JRequest::getString('text');
		$widget = JRequest::getString('widget');
		
		$mail 	= JMail::getInstance();
		$success = $mail->sendMail($email, $name, $contact, $subject, $text);
		
		if ($success) {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_EMAIL_SUCCESSFULLY' );
		} else {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_EMAIL_FAILED' );
		}
		
		$return = ($widget) ? '&ml=1&widget=1' : '';
		
		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_simplechatsupport'.$return;
		$this->setRedirect($link, $msg, 'success');

	}
}
?>