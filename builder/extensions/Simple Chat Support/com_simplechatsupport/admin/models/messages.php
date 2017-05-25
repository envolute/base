<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */
 
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class SimpleChatSupportModelMessages extends JModelLegacy
{
	private $messageID;
	private $templateMessages;
	
	/**
	 * Constructor that retrieves the ID from the request
	 *
	 * @access	public
	 * @return	void
	 */
	function __construct()
	{
		parent::__construct();
		$this->messageID = JRequest::getVar('cid', array(), '', 'array');
	}
	
	function getTemplateMessages()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->templateMessages ))
		{
			$query = 'SELECT * FROM #__simplechatsupport_template';
			$this->templateMessages = $this->_getList( $query );
		}
		return $this->templateMessages;
	}
	
	function getData()
	{
		if (!empty( $this->messageID[0] ))
		{
			$query = 'SELECT * FROM #__simplechatsupport_template '.
					' WHERE id = '.$this->messageID[0];
			$this->_db->setQuery( $query );
			$result = $this->_db->loadObject();
		}
				
		if(empty($result))
		{
			$result = new stdClass();
			$result->id = 0;
			$result->cat_id = 0;
			$result->title = "";
			$result->message = "";
			$result->created_on = "";
		}
		
		return $result;
	}
	
	function getNewItem()
	{
		$query = 'SELECT MAX(id) FROM #__simplechatsupport_template';
		$this->_db->setQuery( $query );
		$result = $this->_db->loadResult();
				
		if(empty($result))
		{
			$result->id = 0;
		}
		
		return $result;
	}
	
	function save()
	{
		$mid = JRequest::getInt('id');
		$cid = JRequest::getInt('cid');
		$created_on = JRequest::getString('created_on');
		$title = JRequest::getString('title');
		$message = JRequest::getString('message');
		
		if(!empty($mid))
		{
			//update
			$query = 'UPDATE #__simplechatsupport_template SET 
						`cat_id` = '.$this->_db->quote( $cid ).',
						`title` = '.$this->_db->quote( $title ).',
						`message` = '.$this->_db->quote( $message ).',
						`created_on` = '.$this->_db->quote( $created_on ).' WHERE id='.$this->_db->quote( $mid );
		}
		else
		{
			//insert
			$query = "INSERT INTO #__simplechatsupport_template VALUES('',". $this->_db->quote( $cid ) .",". $this->_db->quote( $title ) .",". $this->_db->quote( $message ) .", ". $this->_db->quote( $created_on ) .")";
		}
		$this->_db->setQuery( $query );
		try {
			$this->_db->query(); // Use $db->execute() for Joomla 3.0.
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
	
	function delete()
	{
		$query = 'DELETE FROM #__simplechatsupport_template WHERE id IN ('.implode(",", $this->messageID).')';
		$this->_db->setQuery( $query );
		try {
			$this->_db->query(); // Use $db->execute() for Joomla 3.0.
			return true;
		} catch (Exception $e) {
			return false;
		}
	}
}
?>