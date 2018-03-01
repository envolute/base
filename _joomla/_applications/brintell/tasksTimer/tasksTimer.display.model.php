<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	// load Joomla's framework
	// _DIR_ => apps/THIS_APP
	require(__DIR__.'/../../libraries/envolute/_init.joomla.php');
	$app = JFactory::getApplication('site');
	defined('_JEXEC') or die;

	$ajaxRequest = true;
	require('config.php');

	// IMPORTANTE: Carrega o arquivo 'helper' do template
	JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');

	//joomla get request data
	$input      = $app->input;

	// Default Params
	$data       = array();

	// Carrega o arquivo de tradução
	// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
	// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
	// Para possibilitar o carregamento da language 'default' de forma dinâmica,
	// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
	if(isset($_SESSION[$APPTAG.'langDef'])) :
		$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
		$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	endif;

	// get current user's data
	$user = JFactory::getUser();
	$groups = $user->groups;

	// database connect
	$db = JFactory::getDbo();

	// GET DATA
	$query	= '
		SELECT
			T1.*,
			T2.subject
		FROM '.$db->quoteName($cfg['mainTable']) .' T1
			JOIN '.$db->quoteName('#__'.$cfg['project'].'_tasks') .' T2
			ON T2.id = T1.task_id
		WHERE
			'. $db->quoteName('T1.user_id') .' = '. $user->id .' AND
			'. $db->quoteName('T1.start_hour') .' != "00:00:00" AND
			'. $db->quoteName('T1.end_hour') .' = "00:00:00" AND
			'. $db->quoteName('T1.time') .' = "00:00:00"
	';
	try {
		$db->setQuery($query);
		$timer = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if($timer->task_id) :

		$data[] = array(
			'status'			=> 1,
			'id'				=> $timer->id,
			'task_id'			=> $timer->task_id,
			'subject'			=> $timer->subject,
			'start_date'		=> $timer->date,
			'start_hour'		=> $timer->start_hour
		);

	else :

		$data[] = array(
			'status'			=> 0
		);

	endif;

	$json = json_encode($data);

	# JSON if no callback
	if(!isset($_GET['callback'])) exit($json);

	# JSONP if valid callback
	if(is_valid_callback($_GET['callback'])) exit("{$_GET['callback']}($json)");

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
