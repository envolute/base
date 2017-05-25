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
class SimpleChatSupportViewMessage extends JViewLegacy
{
	/**
	 * display method of Hello view
	 * @return void
	 **/
	function display($tpl = null)
	{
		$item		= $this->get('Data', 'messages');
		$isNew		= ($item->id < 1);

		$text = $isNew ? JText::_( 'COM_SIMPLECHATSUPPORT_NEW' ) : JText::_( 'COM_SIMPLECHATSUPPORT_EDIT' );
		JToolBarHelper::title(JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY' ).': <small>[ ' . $text.' ]</small>');
		JToolbarHelper::apply('apply');
		JToolBarHelper::save();
		JToolbarHelper::save2new('save2new');
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		
		$this->item = $item;
		parent::display($tpl);
	}
}