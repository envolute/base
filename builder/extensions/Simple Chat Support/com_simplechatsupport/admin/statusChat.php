<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */
 
define( '_JEXEC', 1 );
define( '_VALID_MOS', 1 );
define('JPATH_BASE', '../../../');
define( 'DS', DIRECTORY_SEPARATOR );
require_once ( JPATH_BASE .'includes'.DS.'defines.php' );
require_once ( JPATH_BASE .'includes'.DS.'framework.php' );
require_once ( JPATH_BASE .'libraries'.DS.'joomla'.DS.'factory.php' );

$db = JFactory::getDbo();

//Check to see if a message was sent.
if(isset($_GET['status']) && $_GET['status'] != '') {
	
	if($_GET['status'] == '0') {
		$query = "UPDATE #__simplechatsupport_status SET status = 0";
		$db->setQuery($query);
		if($db->execute()) $sts = "Off line";
	} else if($_GET['status'] == '1') {
		$query = "UPDATE #__simplechatsupport_status SET status = 1";
		$db->setQuery($query);
		if($db->execute()) $sts = "On line";
	} else {
		$query = "SELECT * FROM #__simplechatsupport_status WHERE status = 1";
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$sts = ($num_rows == 0) ? "Off line" : "On line";
	}
}
echo $sts;

?>