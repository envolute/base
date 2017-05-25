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

class SimpleChatSupportControllerMessages extends JControllerLegacy
{
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add', 'edit' );
		
		$view = $this->getView( 'messages', 'html' );
		$view->setModel( $this->getModel( 'Messages' ) );
		JRequest::setVar('view', 'messages');
	}
	
	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}
	
	function edit()
	{
		$view = $this->getView( 'message', 'html' );
		$view->setModel( $this->getModel( 'Messages' ) );
		JRequest::setVar('view', 'message');
		JRequest::setVar( 'layout', 'form'  );
		parent::display();
	}
	
	function save()
	{
		$model = $this->getModel('Messages');
		if ($model->save()) {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY_SAVED' );
		} else {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY_ERROR_SAVE' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_simplechatsupport&controller=messages';
		$this->setRedirect($link, $msg);
	}
	
	function apply()
	{
		$model = $this->getModel('Messages');
		if ($model->save()) {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY_SAVED' );
		} else {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY_ERROR_SAVE' );
		}
		
		$lastID = $model->getNewItem();

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_simplechatsupport&controller=messages&task=edit&cid='.$lastID;
		$this->setRedirect($link, $msg);
	}
	
	function save2new()
	{
		$model = $this->getModel('Messages');
		if ($model->save()) {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY_SAVED' );
		} else {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY_ERROR_SAVE' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_simplechatsupport&controller=messages&task=edit';
		$this->setRedirect($link, $msg);
	}
	
	function remove()
	{
		$model = $this->getModel('Messages');
		if ($model->delete()) {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY_DELETED' );
		} else {
			$msg = JText::_( 'COM_SIMPLECHATSUPPORT_QUICK_REPLY_ERROR_DELETE' );
		}
		
		// Check the table in so it can be edited.... we are done with it anyway
		$link = 'index.php?option=com_simplechatsupport&controller=messages';
		$this->setRedirect($link, $msg);
	}
}
?>