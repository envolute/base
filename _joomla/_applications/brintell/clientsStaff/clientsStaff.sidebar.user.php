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

	// verifica o acesso
	$hasViewer = array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
	$hasAdmin = array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos

	// database connect
	$db = JFactory::getDbo();

	// GET DATA
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.username') .',
			'. $db->quoteName('T3.name') .' role
		FROM '. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__users') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.user_id
			LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_roles') .' T3
			ON '.$db->quoteName('T3.id') .' = T1.role_id AND T3.state = 1
		WHERE T1.user_id = '.$user->id
	;
	try {
		$db->setQuery($query);
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	$html = '';
	if(!empty($item->name)) :

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 128, 128);
			else $imgPath = $_ROOT.'images/apps/icons/user_'.$item->gender.'.png';
			$img = '<figure><img src="'.$imgPath.'" width="128" height="128" class="img-fluid rounded-circle" /></figure>';
		endif;

		$name = baseHelper::nameFormat((!empty($item->nickname) ? $item->nickname : $item->name));
		$job = (!empty($item->role)) ? baseHelper::nameFormat($item->role) : (!empty($item->occupation) ? baseHelper::nameFormat($item->occupation) : '');

		// User status (online/offline)
		$query	= 'SELECT COUNT("session_id") FROM '. $db->quoteName('#__session') .' WHERE client_id = 0 AND userid = '.$user->id;
		$db->setQuery($query);
		$status = ($db->loadResult() > 0 ? 1 : 0);
		$statusIcon = ' <small class="base-icon-circle text-'.($status ? 'success' : 'gray-200').' align-middle cursor-help hasTooltip" title="'.JText::_('TEXT_USER_STATUS_'.$status).'"></small>';

		$html .= '
			<div id="'.$APPTAG.'-details-view">
				<div class="px-2">
					<div class="pos-relative text-center py-2 mb-3 bg-dark-opacity-45 clearfix">
						<a href="'.$_ROOT.'apps/'.$APPTAG.'/edit-profile" class="btn btn-xs btn-warning base-icon-pencil pos-absolute pos-bottom-0 pos-right-0 m-1 hasTooltip" title="'.JText::_('TEXT_EDIT').'"></a>
						'.$img.'
						<h5 class="text-gray-200 mb-0 lh-1-2">'.$name.$statusIcon.'</h5>
						<div class="small text-primary">'.$job.'</div>
						<div class="text-sm mt-2 pt-1 b-top b-top-dashed b-gray-900">@'.$item->username.'</div>
					</div>
				</div>
			</div>
		';

	else :

		$html = '<div class="alert alert-warning text-sm mx-2">'.JText::_('MSG_NOT_STAFF_PROFILE').'</div>';

	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
