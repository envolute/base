<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */
 
define( '_JEXEC', 1 ); 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//Send some headers to keep the user's browser from caching the response.
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");


//define( '_VALID_MOS', 1 );
require_once ("../../../configuration.php");

$config = new jconfig();                     

if(empty($db_prefix)){
$db_prefix = $config->dbprefix;
}

$host = $config->host;
$user = $config->user;
$password = $config->password;
$database = $config->db;

$option['driver'] = 'mysql';
$option['host'] = $host;
$option['user'] = $user;
$option['password'] = $password;
$option['database'] = $database;
$option['prefix'] = $db_prefix;
	
//Setup Database Connection
$con = mysql_connect("$host","$user","$password") or die("Unable to connect to database");
mysql_select_db("$database", $con) or die("Unable to select database");

function db_input($string, $link = '') {
	return addslashes($string);
}

//Check to see if a reset request was sent.
if(isset($_POST['action']) && $_POST['action'] == 'reset') {
	$sql = "DELETE FROM " . $db_prefix . "simplechatsupport_message WHERE chat_id = " . db_input($_GET['chat']);
	mysql_query($sql, $con);
	$sql = "DELETE FROM " . $db_prefix . "simplechatsupport_chat WHERE chat_id = " . db_input($_GET['chat']);
	mysql_query($sql, $con);
}

//Check to see if a reset request was sent.
if(isset($_POST['action']) && $_POST['action'] == 'resetAll') {
	$sql = "SELECT chat_id FROM " . $db_prefix . "simplechatsupport_chat WHERE status = 0";
	$reset_query = mysql_query($sql, $con);
			
	while ($reset_array = mysql_fetch_array($reset_query, MYSQL_ASSOC)) { 
		$sql = "DELETE FROM " . $db_prefix . "simplechatsupport_message WHERE chat_id = " . $reset_array["chat_id"];
		mysql_query($sql, $con);
	}
	$sql = "DELETE FROM " . $db_prefix . "simplechatsupport_chat WHERE status = 0";
	mysql_query($sql, $con);
}

if(isset($_POST['action']) && $_POST['action'] == 'save') {  
	$sql = "UPDATE " . $db_prefix . "simplechatsupport_chat SET status  =1 WHERE chat_id =" . db_input($_GET['chat']);		
	return mysql_query($sql, $con);	
}

//Create the XML response.
$xml = '<?xml version="1.0" ?><root>';

$last = (isset($_GET['last']) && $_GET['last'] != '' && $_GET['last'] != 'undefined') ? $_GET['last'] : 0;
$sql = "SELECT chat_id, chat_name, date_format(start_time, '%h:%i:%s') as start_time, status" . 
	" FROM " . $db_prefix . "simplechatsupport_chat WHERE chat_name != '' AND status = 0 AND chat_id > " . $last . " ORDER BY chat_id ASC";
	
$message_query = mysql_query($sql, $con);
//if($save == 1) trigger_error("Erro:" . mysql_error(). ". SQL -> " . $sql);
		
while ($message_array = mysql_fetch_array($message_query, MYSQL_ASSOC)) { 
	$xml .= '<chat id="' . $message_array["chat_id"] . '">';
	$xml .= '<name>' . htmlspecialchars($message_array["chat_name"]) . '</name>';
	$xml .= '<time>' . $message_array["start_time"] . '</time>';
	$xml .= '</chat>';
}

$xml .= '</root>';
echo $xml;
?>