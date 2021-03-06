<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

  // load Joomla's framework
  require_once('../load.joomla.php');
	$app = JFactory::getApplication('site');

  defined('_JEXEC') or die;

	// database connect
	$db = JFactory::getDbo();
  // classes customizadas para usuários Joomla
  JLoader::register('baseUserHelper', JPATH_BASE.'/templates/base/source/helpers/user.php');

	//joomla get request data
	$input = $app->input;
	// fields 'Form' requests
  $username = $input->get('username', '', 'str');

	echo baseUserHelper::checkUsername($username, true) ? 'true' : 'false';

else :

  # Otherwise, bad request
  header('status: 400 Bad Request', true, 400);

endif;

?>
