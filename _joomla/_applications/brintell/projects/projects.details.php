<?php
/* SISTEMA PARA CADASTRO DE PROJETOS
 * AUTOR: IVO JUNIOR
 * EM: 29/01/2018
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
// $cfg['isPublic'] = 1; // Público -> acesso aberto a todos

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
$MAINAPP	= $APPNAME;
$MAINTAG	= $APPTAG;

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// verifica o acesso
$hasClient	= array_intersect($groups, $cfg['groupId']['client']);

//joomla get request data
$input		= $app->input;

// params requests
$pID		= $input->get('pID', 0, 'int');

// Current APP
$parts = explode("?", JURI::current());
$url = $parts[0];


// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

// database connect
$db		= JFactory::getDbo();

$html = $removeThis = '';
if($pID > 0) :

	// GET DATA
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' client
		FROM '. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.client_id AND T2.state = 1
		WHERE T1.id = '.$pID
	;
	try {
		$db->setQuery($query);
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if(!empty($item->name)) :

		// REMOVE THIS FROM LIST
		$removeThis = ' AND T1.id != '.$item->id;

		// GET PROJECTS LIST
		$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' WHERE state = 1 ORDER BY name';
		$db->setQuery($query);
		$projects = $db->loadObjectList();

		$html .= '
			<a href="'.$url.'" id="'.$APPTAG.'-details-select" class="hasTooltip" title="'.JText::_('TEXT_VIEW_ALL_PROJECTS').'">
				<span class="base-icon-left-big ml-1 mr-2"></span> '.JText::_('TEXT_BACK').'
			</a>
		';

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 64, 64);
			else $imgPath = $_ROOT.'images/apps/icons/folder_64.png';
			$img = '<img src="'.$imgPath.'" class="img-fluid rounded mb-1" style="width:64px; height:64px;" />';
		endif;

		$since = (!empty($item->start_date) && $item->start_date != '0000-00-00') ? JText::_('TEXT_SINCE').': '.baseHelper::dateFormat($item->start_date) : '';
		$desc = !empty($item->description) ? '<div>'.$item->description.'</div>' : '';
		$info = (!empty($since) || !empty($desc)) ? '<div class="small text-center py-2 b-top b-top-dashed b-gray-900">'.$since.$desc.'</div>' : '';
		$html .= '
			<div id="'.$APPTAG.'-details-view" class="text-center">
				<span class="'.$APPTAG.'-selector base-icon-left-dir"></span>
				<div class="px-2 pt-3">
					<div class="pb-2 clearfix">
						'.$img.'
						<h4 class="text-gray-200 mb-1 lh-1-1">'.baseHelper::nameFormat($item->name).'</h4>
						<div class="small text-muted">'.baseHelper::nameFormat($item->client).'</div>
					</div>
					'.$info.'
				</div>
			</div>
		';

		// // CLIENT STAFF
		// // MOSTRA A LISTA DE USUÁRIOS DO CLIENTE
		// if($item->client_id == 1) : // Brintell
		// 	$query	= '
		// 		SELECT
		// 			T1.*,
		// 			'. $db->quoteName('T2.name') .' role,
		// 			'. $db->quoteName('T3.session_id') .' online
		// 		FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' T1
		// 			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_staff_roles') .' T2
		// 			ON '.$db->quoteName('T2.id') .' = T1.role_id
		// 			LEFT JOIN '. $db->quoteName('#__session') .' T3
		// 			ON '.$db->quoteName('T3.userid') .' = T1.user_id AND T3.client_id = 0
		// 		WHERE '. $db->quoteName('T1.type') .' = 0 AND T1.state = 1
		// 		ORDER BY '. $db->quoteName('T1.name') .' ASC
		// 	';
		// else :
		// 	$query	= '
		// 		SELECT
		// 			T1.*,
		// 			'. $db->quoteName('T1.role') .',
		// 			'. $db->quoteName('T2.session_id') .' online
		// 		FROM '. $db->quoteName('#__'.$cfg['project'].'_clients_staff') .' T1
		// 			LEFT JOIN '. $db->quoteName('#__session') .' T2
		// 			ON '.$db->quoteName('T2.userid') .' = T1.user_id AND T2.client_id = 0
		// 		WHERE '.$db->quoteName('T1.client_id') .' = '.$item->client_id.' AND T1.state = 1
		// 		ORDER BY '. $db->quoteName('T1.name') .' ASC
		// 	';
		// endif;
		// try {
		// 	$db->setQuery($query);
		// 	$db->execute();
		// 	$num_rows = $db->getNumRows();
		// 	$res = $db->loadObjectList();
		// } catch (RuntimeException $e) {
		// 	echo $e->getMessage();
		// 	return;
		// }
		//
		// if($num_rows) : // verifica se existe
		// 	$html .= '
		// 		<div id="'.$APPTAG.'-client-staff" class="text-center px-2 pb-2">
		// 			<hr class="hr-tag b-top-dashed b-primary" />
		// 			<span class="badge badge-primary">
		// 				'.JText::sprintf('TEXT_STAFF_CLIENT', baseHelper::nameFormat($item->client)).'
		// 			</span>
		// 			<div class="clearfix">
		// 	';
		// 	foreach($res as $obj) {
		//
		// 		if($obj->online) :
		// 			$lStatus = JText::_('TEXT_USER_STATUS_1');
		// 			$iStatus = '<small class="base-icon-circle text-success pos-absolute pos-right-0 pos-bottom-0"></small>';
		// 		else :
		// 			$lStatus = JText::_('TEXT_USER_STATUS_0');
		// 			$iStatus = '';
		// 		endif;
		// 		$name = baseHelper::nameFormat((!empty($obj->nickname) ? $obj->nickname : $obj->name));
		// 		$role = baseHelper::nameFormat((!empty($obj->role) ? $obj->role : $obj->occupation));
		// 		if(!empty($role)) $role = '<br />'.$role;
		// 		$info = baseHelper::nameFormat($name).$role.'<br />'.$lStatus;
		//
		// 		if($cfg['hasUpload']) :
		// 			// Imagem Principal -> Primeira imagem (index = 0)
		// 			$img = uploader::getFile('#__brintell_staff_files', '', $obj->id, 0, JPATH_BASE.DS.'images/apps/staff/');
		// 			if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/staff/'.$img['filename'], 32, 32);
		// 			else $imgPath = $_ROOT.'images/apps/icons/user_'.$obj->gender.'.png';
		// 			$img = '<img src="'.$imgPath.'" class="img-fluid rounded mb-2" style="width:32px; height:32px;" />';
		// 		endif;
		//
		// 		$html .= '
		// 			<a href="apps/staff/profile?vID='.$obj->user_id.'" class="d-inline-block pos-relative hasTooltip" title="'.$info.'">
		// 				'.$img.$iStatus.'
		// 			</a>
		// 		';
		// 	}
		// 	$html .= '
		// 			</div>
		// 		</div>
		// 	';
		// endif;

	else :

		$html = '<div class="alert alert-warning text-sm mx-2">'.JText::_('MSG_NOT_PROJECTS_TO_VIEW').'</div>';

	endif;


endif;

if($hasClient) :

	// GET STAFF MEMBER ID
	$query = 'SELECT client_id FROM '. $db->quoteName('#__'.$cfg['project'].'_clients_staff') .' WHERE '.$db->quoteName('user_id') .' = '.$user->id;
	$db->setQuery($query);
	$client = $db->loadResult();
	// MOSTRA A LISTA DE PROJETOS DO USUÁRIO
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' client
		FROM '. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.client_id AND T2.state = 1
		WHERE '.$db->quoteName('T2.id') .' = '.$client.$removeThis.' AND T1.state = 1
		ORDER BY '. $db->quoteName('T1.name') .' ASC
	';

else :

	// MOSTRA A LISTA COMPLETA DE PROJETOS
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' client
		FROM '. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.client_id AND T2.state = 1
		WHERE T1.state = 1'.$removeThis.'
		ORDER BY '. $db->quoteName('T1.name') .' ASC
	';

endif;
try {
	$db->setQuery($query);
	$db->execute();
	$num_rows = $db->getNumRows();
	$res = $db->loadObjectList();
} catch (RuntimeException $e) {
	echo $e->getMessage();
	return;
}

if($num_rows) : // verifica se existe
	if(!empty($removeThis)) :
		$html .= '
			<div id="'.$APPTAG.'-sidebar-list-view">
				<div class="pl-1">
					<hr class="hr-tag b-top-dashed b-primary" />
					<span class="badge badge-primary">'.JText::_('TEXT_PROJECTS').'</span>
				</div>
		';
	else :
		$html .= '
			<div id="'.$APPTAG.'-details-select">
				<span class="base-icon-folder-open ml-1 mr-2"></span>
				'.JText::_('TEXT_PROJECTS').'
			</div>
			<div id="'.$APPTAG.'-sidebar-list-view">
		';
	endif;
	$html .= '';
	foreach($res as $item) {

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 24, 24);
			else $imgPath = $_ROOT.'images/apps/icons/folder_24.png';
			$img = '<img src="'.$imgPath.'" class="img-fluid rounded" style="width:24px; height:24px;" />';
		endif;

		$html .= '
			<a href="'.$url.'?pID='.$item->id.'" class="d-flex align-items-center">
				<span class="mr-2" style="flex:0 0 24px;">'.$img.'</span>
				<h6 class="m-0 lh-1">
					'.baseHelper::nameFormat($item->name).'
					<div class="small text-muted lh-1">'.baseHelper::nameFormat($item->client).'</div>
				</h6>
			</a>
		';
	}
	$html .= '</div>';
else :
	$html = '<p class="base-icon-info-circled alert alert-warning m-0"> '.JText::_('MSG_LISTNOREG').'</p>';
endif;

echo $html;

?>
