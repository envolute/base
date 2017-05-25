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

class SimpleChatSupportModelHelp extends JModelLegacy
{
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
	
	function getSavedRooms($filter = null, $period = null)
	{
		$dt = (!empty($period)) ? explode('-', $period) : Array('MONTH(NOW())', 'YEAR(NOW())');
		$f = ' AND MONTH(start_time) = '.$dt[0].' AND YEAR(start_time) = '.$dt[1];
		$f .= (!empty($filter)) ? ' AND chat_name LIKE "%'.$filter.'%"' : '';
		
		// Lets load the data if it doesn't already exist
		if (empty( $this->savedRooms ))
		{
			$query = 'SELECT * FROM #__simplechatsupport_chat WHERE status = 1'.$f.' ORDER BY chat_id DESC';
			$this->savedRooms = $this->_getList( $query );
		}
		return $this->savedRooms;
	}
	
	function getSavedMonths()
	{
		// Lets load the data if it doesn't already exist
		if (empty( $this->savedMonths ))
		{
			$query = 'SELECT DISTINCT(DATE_FORMAT(start_time, "%m-%Y")) date FROM #__simplechatsupport_chat WHERE status = 1 ORDER BY start_time DESC';
			$this->savedMonths = $this->_getList( $query );
		}
		return $this->savedMonths;
	}	
}
?>