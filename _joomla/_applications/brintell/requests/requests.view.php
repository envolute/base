<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
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

// Get request data
$vID = $app->input->get('vID', 0, 'int'); // VIEW 'ID'

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

// Admin Actions
// require_once('_contacts.select.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' project
		FROM
			'.$db->quoteName($cfg['mainTable']).' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_projects') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.project_id AND T2.state = 1
		WHERE '.$db->quoteName('T1.id') .' = '. $vID
	;
	try {
		$db->setQuery($query);
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if(!empty($item->subject)) : // verifica se existe

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$listFiles = '';
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					$listFiles .= '
						<a class="d-inline-block mr-3" href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"> '.$files[$item->id][$i]->filename.'</span>
						</a>
					';
				endif;
			}
		endif;
		$attachs = '';
		if(!empty($listFiles)) :
			$attachs = '
				<hr class="hr-tag" /><span class="badge badge-primary"> '.JText::_('TEXT_ATTACHMENTS').'</span>
				<div class="font-condensed text-sm mb-4">'.$listFiles.'</div>
			';
		endif;

		$itemStatus = ($item->status == 0) ? 'warning' : JText::_('TEXT_COLOR_STATUS_'.$item->status);
		$iconStatus = JText::_('TEXT_ICON_STATUS_'.$item->status);
		$statusAction = $hasAdmin ? ' onclick="'.$APPTAG.'_setStatusModal(this)"' : '';
		$status = '<a href="#" id="'.$APPTAG.'-item-'.$item->id.'-status" class="badge badge-'.$itemStatus.' base-icon-'.$iconStatus.'" data-id="'.$item->id.'" data-status="'.$item->status.'"'.$statusAction.'> '.JText::_('TEXT_STATUS_'.$item->status).'</a>';

		$type = ' <span class="badge badge-primary cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_TYPE').'">'.JText::_('TEXT_TYPE_'.$item->type).'</span>';

		switch ($item->priority) {
			case 1:
				$priority = ' <span class="badge badge-warning base-icon-attention"> '.JText::_('TEXT_PRIORITY_DESC_1').'</span>';
				break;
			case 2:
				$priority = ' <span class="badge badge-danger base-icon-attention"> '.JText::_('TEXT_PRIORITY_DESC_2').'</span>';
				break;
			default :
				$priority = ' <span class="badge badge-primary">'.JText::_('TEXT_PRIORITY_DESC_0').'</span>';
		}

		$desc = !empty($item->description) ? '<div class="font-condensed mb-4">'.$item->description.'</div>' : '';
		$urlViewProject = JURI::root().'apps/projects/view?pID='.$item->project_id;

		// CREATED BY
		$createdBy = '';
		if(!empty($item->created_by)) :
			$query	= '
				SELECT
					T1.*,
					'. $db->quoteName('T2.name') .' role,
					'. $db->quoteName('T3.session_id') .' online
				FROM '. $db->quoteName('#__'.$cfg['project'].'_teams') .' T1
					LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_teams_roles') .' T2
					ON '.$db->quoteName('T2.id') .' = T1.role_id
					LEFT JOIN '. $db->quoteName('#__session') .' T3
					ON '.$db->quoteName('T3.userid') .' = T1.user_id AND T3.client_id = 0
				WHERE T1.user_id = '.$item->created_by
			;
			$db->setQuery($query);
			$obj = $db->loadObject();
			if(!empty($obj->name)) : // verifica se existe
				if($obj->online) :
					$lStatus = JText::_('TEXT_USER_STATUS_1');
					$iStatus = '<small class="base-icon-circle text-success pos-absolute pos-right-0 pos-bottom-0"></small>';
				else :
					$lStatus = JText::_('TEXT_USER_STATUS_0');
					$iStatus = '';
				endif;
				$name = baseHelper::nameFormat((!empty($obj->nickname) ? $obj->nickname : $obj->name));
				$role = baseHelper::nameFormat($obj->occupation);
				if(!empty($role)) $role = '<br />'.$role;
				$info = baseHelper::nameFormat($name).$role.'<br />'.$lStatus;

				// Imagem Principal -> Primeira imagem (index = 0)
				$img = uploader::getFile('#__brintell_teams_files', '', $obj->id, 0, JPATH_BASE.DS.'images/apps/teams/');
				if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/teams/'.$img['filename'], 24, 24);
				else $imgPath = JURI::root().'images/apps/icons/user_'.$obj->gender.'.png';
				$img = '<img src="'.$imgPath.'" class="img-fluid rounded mb-2" style="width:24px; height:24px;" />';

				$createdBy .= '
					<a href="apps/teams/profile?vID='.$obj->user_id.'" class="d-inline-block pos-relative hasTooltip" title="'.$info.'">
						'.$img.$iStatus.'
					</a>
				';
			endif;
		endif;

		$tags = '';
		if(!empty($item->tags)) :
			$t = explode(',', $item->tags);
			for($i = 0; $i < count($t); $i++) {
				$tags .= ' <span class="badge badge-secondary"><small class="base-icon-tag text-primary align-middle"></small> '.$t[$i].'</span>';
			}
			$tags = '<span class="d-inline-block pl-3 ml-3 b-left">'.$tags.'</span>';
		endif;

		$btnActions = '';
		if($hasAdmin || ($item->created_by == $user->id)) :
			$btnActions = '
				<div class="float-right">
					<a href="#" class="btn btn-lg btn-link py-0 px-2" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-toggle-on text-success' : 'base-icon-toggle-on text-muted').' hasTooltip" title="'.JText::_(($item->state == 1 ? 'MSG_ARCHIVE_ITEM' : 'MSG_ACTIVATE_ITEM')).'"></span>
					</a>
					<a href="#" class="btn btn-lg btn-link py-0 px-2 hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil text-live"></span></a>
					<a href="#" class="btn btn-lg btn-link py-0 px-2 hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash text-danger"></span></a>
				</div>
			';
		endif;

		echo '
			<div id="'.$APPTAG.'-request-pageitem">
				<div id="'.$APPTAG.'-request-pageitem-header" class="mb-3 b-bottom-2 b-primary">
					<div class="pb-1 mb-2 b-bottom">'.$status.$type.$priority.'</div>
					<h2 class="font-condensed text-primary">
						'.$item->subject.'
					</h2>
					<div class="font-condensed text-sm text-muted mb-2">
						<a href="'.$urlViewProject.'" target="_blank">'.baseHelper::nameFormat($item->project).'</a> - '.JText::_('TEXT_SINCE').' '.baseHelper::dateFormat($item->created_date).
						' <span class="text-live">'.$deadline.'</span>
					</div>
					'.$btnActions.$createdBy.$tags.'
				</div>
				<div class="row">
					<div class="col-md-8 b-right">
						'.$desc.$attachs
		;
						// COMMENTS
						$requestsCommentsListFull		= false;
						$requestsCommentsRelTag			= 'requests';
						$requestsCommentsRelListNameId	= 'request_id';
						$requestsCommentsRelListId		= $item->id;
						$requestsCommentsOnlyChildList	= true;
						$requestsCommentsShowAddBtn		= false;
						echo '
							<h4 class="font-condensed text-live mb-3">
								'.JText::_('TEXT_COMMENTS').'
								<a href="#" class="btn btn-xs btn-success base-icon-plus float-right" onclick="requestsComments_setParent('.$item->id.')" data-toggle="modal" data-target="#modal-requestsComments" data-backdrop="static" data-keyboard="false"></a>
								<a href="#" class="btn btn-xs btn-info base-icon-arrows-cw mx-1 float-right" onclick="requestsComments_listReload(false, false, false, requestsCommentsoCHL, requestsCommentsrNID, requestsCommentsrID)"></a>
							</h4>
						';
						require(JPATH_APPS.DS.'requestsComments/requestsComments.php');
						echo '<hr class="my-1" /><a href="#" class="btn btn-xs btn-success base-icon-plus" onclick="requestsComments_setParent('.$item->id.')" data-toggle="modal" data-target="#modal-requestsComments" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_ADD').'</a>';
		echo '
					</div>
					<div class="col-md-4">
		';
						// TO DO LIST
						$requestsTodoListFull		= false;
						$requestsTodoRelTag			= 'requests';
						$requestsTodoRelListNameId	= 'request_id';
						$requestsTodoRelListId		= $item->id;
						$requestsTodoOnlyChildList	= true;
						$requestsTodoShowAddBtn		= false;
						echo '
							<h4 class="font-condensed text-danger mb-3">
								'.JText::_('TEXT_TODO_LIST').'
								<a href="#" class="btn btn-xs btn-success base-icon-plus float-right" onclick="requestsTodo_setParent('.$item->id.')" data-toggle="modal" data-target="#modal-requestsTodo" data-backdrop="static" data-keyboard="false"></a>
							</h4>
						';
						require(JPATH_APPS.DS.'requestsTodo/requestsTodo.php');
						echo '<hr class="my-1" /><a href="#" class="btn btn-xs btn-success base-icon-plus" onclick="requestsTodo_setParent('.$item->id.')" data-toggle="modal" data-target="#modal-requestsTodo" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_ADD').'</a>';
		echo '
					</div>
				</div>
			</div>
		';

	else :
		echo '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_ITEM_NOT_AVAILABLE').'</p>';
	endif;

else :

	echo '<h4 class="alert alert-warning">'.JText::_('MSG_NO_ITEM_SELECTED').'</h4>';

endif;
?>
