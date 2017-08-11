<?php
// load Joomla's framework
require_once('../load.joomla.php');
$app = JFactory::getApplication('site');

defined('_JEXEC') or die;
if(isset($_REQUEST['fn']) && !empty($_REQUEST['fn'])) :
	$fileName = htmlspecialchars($_REQUEST['fn'], ENT_QUOTES);
	$filePath = JPATH_SITE.'/images/debitos/'.$fileName;
	if(file_exists($filePath)) :
		header("Content-type: text/plain");
		header("Content-disposition: attachment; filename=".$fileName);
		flush();
		ob_clean();
		readfile($filePath);
	endif;
endif;
exit();
?>
