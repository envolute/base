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
class SimpleChatSupportViewMessages extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
		JToolBarHelper::title(JText::_('COM_SIMPLECHATSUPPORT_QUICK_REPLY'));
		JToolBarHelper::back('','index.php?option=com_simplechatsupport');
		JToolBarHelper::addNew();
		JToolBarHelper::deleteList();
		JToolBarHelper::editList();
		$this->items = $this->get('TemplateMessages', 'messages');
		
		parent::display($tpl);
	}
}