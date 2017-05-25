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
/**
 * Ipnsub View
 *
 * @package    Joomla.Tutorials
 * @subpackage Components
 */
class SimpleChatSupportViewHelp extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
		JToolBarHelper::title(JText::_( 'COM_SIMPLECHATSUPPORT' ));
		JToolBarHelper::back('','administrator');
		JToolBarHelper::preferences('com_simplechatsupport', '500');
		
		$this->template_messages = $this->get('TemplateMessages', 'help');
		$this->filter = JRequest::getVar('filter');
		$this->period = JRequest::getVar('period');
		//$this->saved_rooms = $this->get('SavedRooms', 'help');
		$this->saved_months = $this->get('savedMonths', 'help');
		
		$model = $this->getModel();
		$this->saved_rooms = $model->getSavedRooms($this->filter, $this->period);
		
		$params = JComponentHelper::getParams('com_simplechatsupport');
		
		$this->operator = $params->get('operator');
		$this->sound = $params->get('sound');
		parent::display($tpl);
	}
}