<?php
// load Joomla's framework
require(__DIR__.'/../../libraries/envolute/_init.joomla.php');
defined('_JEXEC') or die;
$app = JFactory::getApplication('site');

if(isset($_REQUEST['fn']) && !empty($_REQUEST['fn'])) :
	$fileName = htmlspecialchars($_REQUEST['fn'], ENT_QUOTES);
	$filePath = JPATH_SITE.'/images/debitos/'.$fileName;
	if(file_exists($filePath)) :
		header("Content-type: text/plain");
		header("Content-disposition: attachment; filename=".$fileName);
		flush();
		ob_clean();
		readfile($filePath);
	else :
		echo 'Não foi possível localizar o arquivo "'.$filePath.'"!';
	endif;
endif;
exit();
?>
