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

//Check to see if a message was sent.
if(isset($_POST['message']) && $_POST['message'] != '') {
	$xml = '<?xml version="1.0" encoding="utf-8"?><root>';
	if(empty($_REQUEST['chat'])) {
		$sql = "INSERT INTO " . $db_prefix . "simplechatsupport_chat (chat_id, chat_name, start_time) VALUES ('', '".db_input($_POST['name'])."', NOW())";
		mysql_query ($sql, $con);
		$chatid = mysql_insert_id();
	} else {
		$sql = "UPDATE " . $db_prefix . "simplechatsupport_chat SET chat_name='" . db_input($_POST['name']) . "' WHERE chat_id =" . db_input($_REQUEST['chat']);
		$chatid = $_REQUEST['chat'];
	}

	$sql = "INSERT INTO " . $db_prefix . "simplechatsupport_message (chat_id, user_id, user_name, message, post_time) VALUES "
			. "(" . db_input($chatid) . ", 1, '" . db_input($_POST['name']) . "', '" . db_input($_POST['message']) . "', NOW())";
	mysql_query ($sql, $con);
	
	$last = (isset($_REQUEST['last']) && $_REQUEST['last'] != '') ? $_REQUEST['last'] : 0;
	$sql = "SELECT message_id, user_name, message, date_format(post_time, '%h:%i:%s') as post_time" . 
		" FROM " . $db_prefix . "simplechatsupport_message WHERE chat_id = " . $chatid . " AND message_id > " . $last;

	$message_query = mysql_query($sql, $con);

	$xml .= '<room id="' . $chatid . '">' . sizeof($message_query) .'</room>';
	
	while ($message_array = mysql_fetch_array($message_query, MYSQL_ASSOC)) { 	
		$xml .= '<message id="' . $message_array["message_id"] . '">';
		$xml .= '<user>' . htmlspecialchars($message_array["user_name"]) . ' -> ' . $last . '</user>';
		$xml .= '<text>' . htmlspecialchars($message_array["message"]) . '</text>';
		$xml .= '<time>' . $message_array["post_time"] . '</time>';
		$xml .= '</message>';
	}
	
	$xml .= '</root>';
	echo $xml;
	return;
}

//Check to see if a reset request was sent.
if(isset($_POST['action']) && $_POST['action'] == 'reset') {
	$sql = "DELETE FROM " . $db_prefix . "simplechatsupport_message WHERE chat_id = " . db_input($_REQUEST['chat']);
	return;
}

//Create the XML response.
$xml = '<?xml version="1.0" encoding="utf-8"?><root>';
//Check to ensure the user is in a chat room.
if( !empty($_REQUEST['chat']) && $_REQUEST['chat'] != '' && $_REQUEST['chat'] != 'undefined' ) {
	
	$last = (isset($_REQUEST['last']) && $_REQUEST['last'] != '' && $_REQUEST['last'] != 'undefined') ? $_REQUEST['last'] : 0;
	$sql = "SELECT message_id, user_name, message, date_format(post_time, '%h:%i:%s') as post_time" . 
		" FROM " . $db_prefix . "simplechatsupport_message WHERE chat_id = " . db_input($_REQUEST['chat']) . " AND message_id > " . $last . " ORDER BY message_id ASC";

	$message_query = mysql_query ($sql, $con);

	$xml .= '<room id="' . db_input($_REQUEST['chat']) . '">' . sizeof($message_query);
	$xml .= '</room>';
	
	// Last all rooms
	if(isset($_REQUEST['lastAll']) && $_REQUEST['lastAll'] != '' && $_REQUEST['lastAll'] !== 0 && $_REQUEST['lastAll'] != 'undefined') :
		
		$listen = "SELECT DISTINCT(chat_id)" . " FROM " . $db_prefix . "simplechatsupport_message WHERE message_id > " . $_REQUEST['lastAll'];
		$room_query = mysql_query ($listen, $con);		
		while ($room_array = mysql_fetch_array($room_query, MYSQL_ASSOC)) { 	
			$xml .= '<messages>';
			$xml .= '<room>' . $room_array["chat_id"] . '</room>';
			$xml .= '</messages>';
		}
	endif;

	while ($message_array = mysql_fetch_array($message_query, MYSQL_ASSOC)) { 	
		$xml .= '<message id="' . $message_array["message_id"] . '">';
		$xml .= '<user>' . htmlspecialchars($message_array["user_name"]) . '</user>';
		$xml .= '<text>' . htmlspecialchars($message_array["message"]) . '</text>';
		$xml .= '<time>' . $message_array["post_time"] . '</time>';
		$xml .= '</message>';
	}
}
$xml .= '</root>';
echo $xml;
?>