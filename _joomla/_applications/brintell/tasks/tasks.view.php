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
$MAINAPP	= $APPNAME;
$MAINTAG	= $APPTAG;
$cfgViewer	= $cfg['groupId']['viewer'];
$cfgAdmin	= $cfg['groupId']['admin'];

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
		$view = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if(!empty($view->subject)) : // verifica se existe

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			$files[$view->id] = uploader::getFiles($cfg['fileTable'], $view->id);
			$listFiles = '';
			for($i = 0; $i < count($files[$view->id]); $i++) {
				if(!empty($files[$view->id][$i]->filename)) :
					$listFiles .= '
						<a class="d-inline-block mr-3" href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$view->id][$i]->filename).'&mt='.base64_encode($files[$view->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.((int)($files[$view->id][$i]->filesize / 1024)).'kb"> '.$files[$view->id][$i]->filename.'</span>
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

		$itemStatus = ($view->status == 2) ? 'warning' : JText::_('TEXT_COLOR_STATUS_'.$view->status);
		$iconStatus = JText::_('TEXT_ICON_STATUS_'.$view->status);

		$type = ' <span class="badge badge-primary cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_TYPE').'">'.JText::_('TEXT_TYPE_'.$view->type).'</span>';

		switch ($view->priority) {
			case 1:
				$priority = ' <span class="badge badge-warning">'.JText::_('TEXT_PRIORITY_DESC_1').'</span>';
				break;
			case 2:
				$priority = ' <span class="badge badge-danger">'.JText::_('TEXT_PRIORITY_DESC_2').'</span>';
				break;
			default :
				$priority = ' <span class="badge badge-primary">'.JText::_('TEXT_PRIORITY_DESC_0').'</span>';
		}
		if($view->visibility == 0) :
			$visible = ' <span class="badge badge-primary base-icon-lock-open-alt cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_VISIBILITY').'"> '.JText::_('TEXT_PROJECT').'</span>';
		else :
			$visible = ' <span class="badge badge-danger base-icon-lock cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_VISIBILITY').'"> '.JText::_('TEXT_PRIVATE').'</span>';
		endif;

		$requests = '';
		if(!empty($view->requests)) :
			$r = explode(',', $view->requests);
			for($i = 0; $i < count($r); $i++) {
				if($i > 0) $requests .= ', ';
				if($hasAdmin) $requests .= '<a href="'.JURI::root().'apps/requests/view?vID='.$r[$i].'">#'.$r[$i].'</a>';
				else $requests .= '<span class="text-live">#'.$r[$i].'</span>';
			}
			$requests = '<div class="float-right text-muted">Req ID: '.$requests.'</div>';
		endif;

		$deadline = '';
		if($view->deadline != '0000-00-00 00:00:00') {
			$dt = explode(' ', $view->deadline);
			$dlDate = baseHelper::dateFormat($dt[0], 'd/m/y');
			$dlTime = ($dt[1] != '00:00:00') ? ' '.substr($dt[1], 0, 5).$view->timePeriod : '';
			$deadline = JText::_('TEXT_UNTIL').' '.$dlDate.$dlTime;
		}
		$estimate = ($view->estimate > 0) ? $view->estimate.JText::_('TEXT_ESTIMATED_UNIT').' ' : '';
		$estimate .= $deadline;
		$estimate = !empty($estimate) ? ' - '.JText::_('TEXT_ESTIMATED').' '.$estimate : '';
		$desc = !empty($view->description) ? '<div class="font-condensed mb-4">'.$view->description.'</div>' : '';
		$urlViewProject = JURI::root().'apps/projects/view?pID='.$view->project_id;

		// CLIENT STAFF
		// MOSTRA A LISTA DE USUÁRIOS DA TAREFA
		$assigned = '';
		if(!empty($view->assign_to)) :
			$query	= '
				SELECT
					T1.*,
					'. $db->quoteName('T2.name') .' role,
					'. $db->quoteName('T3.session_id') .' online
				FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' T1
					LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_staff_roles') .' T2
					ON '.$db->quoteName('T2.id') .' = T1.role_id
					LEFT JOIN '. $db->quoteName('#__session') .' T3
					ON '.$db->quoteName('T3.userid') .' = T1.user_id AND T3.client_id = 0
				WHERE T1.id IN ('.$view->assign_to.')
				ORDER BY '. $db->quoteName('T1.name') .' ASC
			';
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
				foreach($res as $obj) {

					if($obj->online) :
						$lStatus = JText::_('TEXT_USER_STATUS_1');
						$iStatus = '<small class="base-icon-circle text-success pos-absolute pos-right-0 pos-bottom-0"></small>';
					else :
						$lStatus = JText::_('TEXT_USER_STATUS_0');
						$iStatus = '';
					endif;
					$name = baseHelper::nameFormat((!empty($obj->nickname) ? $obj->nickname : $obj->name));
					$role = baseHelper::nameFormat((!empty($obj->role) ? $obj->role : $obj->occupation));
					if(!empty($role)) $role = '<br />'.$role;
					$info = baseHelper::nameFormat($name).$role.'<br />'.$lStatus;

					// Imagem Principal -> Primeira imagem (index = 0)
					$img = uploader::getFile('#__brintell_staff_files', '', $obj->id, 0, JPATH_BASE.DS.'images/apps/staff/');
					if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/staff/'.$img['filename'], 24, 24);
					else $imgPath = JURI::root().'images/apps/icons/user_'.$obj->gender.'.png';
					$img = '<img src="'.$imgPath.'" class="img-fluid rounded mb-2" style="width:24px; height:24px;" />';

					$assigned .= '
						<a href="apps/staff/profile?vID='.$obj->user_id.'" class="d-inline-block pos-relative hasTooltip" title="'.$info.'">
							'.$img.$iStatus.'
						</a>
					';
				}
			endif;
		endif;

		$tags = '';
		if(!empty($view->tags)) :
			$t = explode(',', $view->tags);
			for($i = 0; $i < count($t); $i++) {
				$tags .= ' <span class="badge badge-secondary"><small class="base-icon-tag text-primary align-middle"></small> '.$t[$i].'</span>';
			}
			$tags = '<span class="d-inline-block pl-3 ml-3 b-left">'.$tags.'</span>';
		endif;

		$btnActions = '';
		if($hasAdmin || ($view->created_by == $user->id)) :
			$btnActions = '
				<div class="float-right">
					<a href="#" class="btn btn-lg btn-link py-0 px-2" onclick="'.$MAINTAG.'_setState('.$view->id.', null, false, \'base-icon-toggle-on\', \'base-icon-toggle-on\', \'text-success\', \'text-muted\')" id="'.$MAINTAG.'-state-'.$view->id.'">
						<span class="'.($view->state == 1 ? 'base-icon-toggle-on text-success' : 'base-icon-toggle-on text-muted').' hasTooltip" title="'.JText::_(($view->state == 1 ? 'MSG_ARCHIVE_ITEM' : 'MSG_ACTIVATE_ITEM')).'"></span>
					</a>
					<a href="#" class="btn btn-lg btn-link py-0 px-2 hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$MAINTAG.'_loadEditFields('.$view->id.', false, false)"><span class="base-icon-pencil text-live"></span></a>
					<a href="#" class="btn btn-lg btn-link py-0 px-2 hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$MAINTAG.'_del('.$view->id.', false)"><span class="base-icon-trash text-danger"></span></a>
				</div>
			';
		endif;

		// Hide loader
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration('jQuery(window).on("load", function(){ jQuery("#'.$MAINTAG.'-form-loader").hide() });');

		echo '
			<div id="'.$MAINTAG.'-form-loader" class="text-center">
				<img src="'.JURI::root().'templates/base/images/core/loader-active.gif">
			</div>
			<div id="'.$MAINTAG.'-task-pageitem" class="py-3">
				<div id="'.$MAINTAG.'-task-pageitem-header" class="mb-3 b-bottom-2 b-primary">
					<div class="pb-1 mb-2 b-bottom">'.$type.$priority.$visible.$requests.'</div>
					<h2 class="font-condensed text-primary">
						<span class="badge bg-gray-200"><a href="#" id="'.$MAINTAG.'-item-'.$view->id.'-status" class="base-icon-'.$iconStatus.' text-'.$itemStatus.' hasTooltip" title="'.JText::_('TEXT_STATUS_'.$view->status).'" data-id="'.$view->id.'" data-status="'.$view->status.'" onclick="'.$MAINTAG.'_setStatusModal(this)"></a></span>
						'.$view->subject.'
					</h2>
					<div class="clearfix">
						<div class="font-condensed text-sm text-muted mb-2">
							<a href="'.$urlViewProject.'" target="_blank">'.baseHelper::nameFormat($view->project).'</a> - '.JText::_('TEXT_SINCE').' '.baseHelper::dateFormat($view->created_date).
							' <span class="text-live">'.$estimate.'</span>
						</div>
						'.$btnActions.$assigned.$tags.'
					</div>
				</div>
				<div class="row">
					<div class="col-md-8 b-right">
						'.$desc.$attachs
		;
						// COMMENTS
						$tasksCommentsIsPublic		= 2;
						$tasksCommentsListFull		= false;
						$tasksCommentsRelTag		= 'tasks';
						$tasksCommentsRelListNameId	= 'task_id';
						$tasksCommentsRelListId		= $view->id;
						$tasksCommentsOnlyChildList	= true;
						$tasksCommentsShowAddBtn	= false;
						echo '
							<h4 class="font-condensed text-live mb-3">
								'.JText::_('TEXT_COMMENTS').'
								<a href="#" class="btn btn-xs btn-success base-icon-plus float-right" onclick="'.$MAINAPP.'Comments_setParent('.$view->id.')" data-toggle="modal" data-target="#modal-'.$MAINAPP.'Comments" data-backdrop="static" data-keyboard="false"></a>
								<a href="#" class="btn btn-xs btn-info base-icon-arrows-cw mx-1 float-right" onclick="'.$MAINAPP.'Comments_listReload(false, false, false, '.$MAINAPP.'CommentsoCHL, '.$MAINAPP.'CommentsrNID, '.$MAINAPP.'CommentsrID)"></a>
							</h4>
						';
						require(JPATH_APPS.DS.''.$MAINAPP.'Comments/'.$MAINAPP.'Comments.php');
						echo '<hr class="my-1" /><a href="#" class="btn btn-xs btn-success base-icon-plus" onclick="'.$MAINAPP.'Comments_setParent('.$view->id.')" data-toggle="modal" data-target="#modal-'.$MAINAPP.'Comments" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_ADD').'</a>';
		echo '
					</div>
					<div class="col-md-4">
		';
						// APP ACTIONS
						// Carrega o app diretamente ná página,
						// pois como está sendo chamada no template 'component', não carrega os módulos
						// TASKS => FORM
						$tasksAppTag					= $MAINTAG;
						// get the same group access of main APP
						${$tasksAppTag.'ViewerGroups'}	= $cfgViewer;
						${$tasksAppTag.'AuthorGroups'}	= $cfgAuthor;
						${$tasksAppTag.'EditorGroups'}	= $cfgEditor;
						${$tasksAppTag.'AdminGroups'}	= $cfgAdmin;
						${$tasksAppTag.'ShowApp'}		= false;
						${$tasksAppTag.'ShowList'}		= false;
						${$tasksAppTag.'ListFull'}		= true;
						require(JPATH_APPS.DS.$MAINAPP.'/'.$MAINAPP.'.php');
						// TAGS => FORM
						// get the same group access of main APP
						$tasksTagsViewerGroups		= $cfgViewer;
						$tasksTagsAuthorGroups		= $cfgAuthor;
						$tasksTagsEditorGroups		= $cfgEditor;
						$tasksTagsAdminGroups		= $cfgAdmin;
						$tasksTagsShowApp			= false;
						$tasksTagsShowList			= false;
						$tasksTagsListFull			= false;
						$tasksTagsRelTag			= 'tasks';
						$tasksTagsFieldUpdated		= '#tasks-tags';
						$tasksTagsTableField		= 'name';
						require(JPATH_APPS.DS.$MAINAPP.'Tags/'.$MAINAPP.'Tags.php');
						// TO DO LIST => (instância do FORM)
						$tasksTodoIsPublic			= 3; // 'Editor' + 'Admin'
						$tasksTodoShowApp			= false;
						$tasksTodoShowList			= true;
						$tasksTodoListModal			= true;
						$tasksTodoListFull			= false;
						$tasksTodoRelListNameId		= 'task_id';
						require(JPATH_APPS.DS.$MAINAPP.'Todo/'.$MAINAPP.'Todo.php');

						// TO DO LIST => (instância da VIEW)
						$tasksTodoAppTag					= 'todoView';
						${$tasksTodoAppTag.'IsPublic'}		= 3; // 'Editor' + 'Admin'
						${$tasksTodoAppTag.'ListFull'}		= false;
						${$tasksTodoAppTag.'ListAjax'}		= "list.actions.ajax.php";
						${$tasksTodoAppTag.'RelTag'}		= 'tasks';
						${$tasksTodoAppTag.'RelListNameId'}	= 'task_id';
						${$tasksTodoAppTag.'RelListId'}		= $view->id;
						${$tasksTodoAppTag.'OnlyChildList'}	= true;
						${$tasksTodoAppTag.'ShowAddBtn'}	= false;
						echo '
							<h4 class="font-condensed text-danger mb-3">
								'.JText::_('TEXT_TODO_LIST').'
								<a href="#" class="btn btn-xs btn-success base-icon-plus float-right" onclick="'.$tasksTodoAppTag.'_setParent('.$view->id.')" data-toggle="modal" data-target="#modal-'.$tasksTodoAppTag.'" data-backdrop="static" data-keyboard="false"></a>
								<a href="#" class="btn btn-xs btn-info base-icon-arrows-cw mx-1 float-right" onclick="'.$tasksTodoAppTag.'_listReload(false, false, false, '.$tasksTodoAppTag.'oCHL, '.$tasksTodoAppTag.'rNID, '.$tasksTodoAppTag.'rID)"></a>
							</h4>
						';
						require(JPATH_APPS.DS.'tasksTodo/tasksTodo.php');
						echo '<hr class="my-1" /><a href="#" class="btn btn-xs btn-success base-icon-plus" onclick="'.$tasksTodoAppTag.'_setParent('.$view->id.')" data-toggle="modal" data-target="#modal-'.$tasksTodoAppTag.'" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_ADD').'</a>';
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
