<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */
 
// Dont allow direct linking
defined('_JEXEC') or die('Restricted access');

class com_simplechatsupportInstallerScript
{
	function install($parent) 
	{
	}

	function uninstall($parent) 
	{
	}
	
	function update($parent) 
	{
	}

	function preflight($type, $parent) 
	{
	}

	function postflight($type, $parent) 
	{
		$this->com_install();
		$this->showResult();
	}
	
	function com_install()
	{
		$database = JFactory::getDBO();
		
		//Load Sample Data
		$database->setQuery("INSERT INTO #__simplechatsupport_template VALUES(1, 0, 'Hello, how can I help you?', 'Hello, how can I help you?', NOW())");
		$database->query();
	}
	
	function showResult()
	{
		echo '<p class="alert alert-success"><strong><code>INSTALL:</code> Success!</p>';
	}}
?>