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
	$input		= $app->input;

	// params requests
	$APPTAG		= $input->get('aTag', $APPTAG, 'str');
	$RTAG		= $input->get('rTag', $APPTAG, 'str');
	$aFLT		= $input->get('aFTL', 0, 'bool'); // ajax filter
	$oCHL		= $input->get('oCHL', 0, 'bool');
	$oCHL		= $_SESSION[$RTAG.'OnlyChildList'] ? $_SESSION[$RTAG.'OnlyChildList'] : $oCHL;
	$rNID		= $input->get('rNID', '', 'str');
	$rNID		= !empty($_SESSION[$RTAG.'RelListNameId']) ? $_SESSION[$RTAG.'RelListNameId'] : $rNID;
	$rID		= $input->get('rID', 0, 'int');
	$rID		= !empty($_SESSION[$RTAG.'RelListId']) ? $_SESSION[$RTAG.'RelListId'] : $rID;

	// Define as variáveis se houver parâmetros dinâmicos
	if(isset($_SESSION[$APPTAG.'IsPublic'])) $cfg['isPublic'] = $_SESSION[$APPTAG.'IsPublic'];
	if(isset($_SESSION[$APPTAG.'ViewerGroups'])) $cfg['groupId']['viewer'] = $_SESSION[$APPTAG.'ViewerGroups'];
	if(isset($_SESSION[$APPTAG.'AuthorGroups'])) $cfg['groupId']['author'] = $_SESSION[$APPTAG.'AuthorGroups'];
	if(isset($_SESSION[$APPTAG.'EditorGroups'])) $cfg['groupId']['editor'] = $_SESSION[$APPTAG.'EditorGroups'];
	if(isset($_SESSION[$APPTAG.'AdminGroups'])) $cfg['groupId']['admin'] = $_SESSION[$APPTAG.'AdminGroups'];

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
	$user		= JFactory::getUser();
	$groups		= $user->groups;

	// verifica o acesso
	require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxAccess.php');

	// database connect
	$db		= JFactory::getDbo();

	// verifica se é um cliente
	$hasClient	= array_intersect($groups, $cfg['groupId']['client']); // se está na lista de administradores permitidos
	// Check if is a client
	$client_id = 0;
	if($hasClient) {
		// CLIENTS STAFF
		$query = 'SELECT client_id FROM '. $db->quoteName('#__'.$cfg['project'].'_clients_staff') .' WHERE '. $db->quoteName('user_id') .' = '.$user->id.' AND '. $db->quoteName('access') .' = 1 AND '. $db->quoteName('state') .' = 1 ORDER BY name';
		$db->setQuery($query);
		$client_id = $db->loadResult();
	}
	// filtro de projetos e usuários do cliente
	$cProj = $client_id ? 'client_id = '.$client_id.' AND ' : '';

	// LOAD FILTER
	$fQuery = $PATH_APP_FILE.'.filter.query.php';
	if($aFLT && file_exists($fQuery)) require($fQuery);

	// GET DATA
	$noReg	= true;
	$query	= '
		SELECT T1.*
	';
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName('vw_'.$cfg['project'].'_'.$APPNAME) .' T1
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T2
				ON '.$db->quoteName('T2.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.$where.' AND '. $db->quoteName('T2.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID.$orderList
			;
		else :
			$query .= ' FROM '. $db->quoteName('vw_'.$cfg['project'].'_'.$APPNAME) .' T1
				WHERE '.$where.' AND '. $db->quoteName($rNID) .' = '. $rID.$orderList
			;
		endif;
	else :
		$query .= '
			FROM '. $db->quoteName('vw_'.$cfg['project'].'_'.$APPNAME) .' T1
			WHERE '.$where.$orderList
		;
		if($oCHL) $noReg = false;
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

	$html = '';
	if($num_rows) : // verifica se existe

		// Filter Information
		if($fExec) echo '<hr class="hr-tag b-danger" /><span class="badge badge-danger base-icon-arrows-cw"> '.JText::_('TEXT_WORKING').'</span>';

		$html .= '<div class="row py-2 mb-4">';
		$status		= 9;
		$counter	= 0;
		foreach($res as $item) {

			// define permissões de execução
			$canEdit	= ($cfg['canEdit'] || $item->created_by == $user->id);
			$canDelete	= ($cfg['canDelete'] || $item->created_by == $user->id);

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
				// Imagem Principal -> Primeira imagem (index = 0)
				$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
				if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 48, 48);
				else $imgPath = $_ROOT.'images/apps/icons/folder_48.png';
				$img = '<img src="'.$imgPath.'" class="img-fluid mr-2" style="width:48px; height:48px;" />';
			endif;

			$urlViewData = $_ROOT.'apps/'.$APPPATH.'/view?vID='.$item->id;
			$urlViewProject = $_ROOT.'apps/projects/view?pID='.$item->project_id;
			// $rowState = $item->state == 0 ? 'danger bg-light text-muted' : 'primary bg-white';

			$colorStatus	= JText::_('TEXT_COLOR_STATUS_'.$item->status);
			$iconStatus		= JText::_('TEXT_ICON_STATUS_'.$item->status);

			// define as colunas por status
			if($status !== $item->status && !($status == 2 && $item->status == 3)) :
				if($counter > 0) $html .= '</div>';
				if($item->status == 2 || $item->status == 3) { // os 2 na mesma coluna
					$html .= '
						<div id="'.$APPTAG.'-item-status-'.$item->status.'" class="'.$APPTAG.'-col col-sm-6 col-lg-3 pb-3">
							<h6 class="text-center bg-'.$colorStatus.' rounded py-2 mb-2 set-shadow-right">
								<span class="base-icon-'.JText::_('TEXT_ICON_STATUS_2').'"> '.JText::_('TEXT_STATUS_2').'</span>
								<span class="mx-3 b-left b-white"></span>
								<span class="base-icon-'.JText::_('TEXT_ICON_STATUS_3').'"> '.JText::_('TEXT_STATUS_3').'</span>
							</h6>
					';
				} else {
					$html .= '
						<div id="'.$APPTAG.'-item-status-'.$item->status.'" class="'.$APPTAG.'-col col-sm-6 col-lg-3 pb-3">
							<h6 class="text-center bg-'.$colorStatus.' rounded py-2 mb-2 set-shadow-right">
								<span class="base-icon-'.$iconStatus.'"></span> '.JText::_('TEXT_STATUS_'.$item->status).'
							</h6>
					';
				}
				$status = $item->status;
			endif;

			$deadline = '';
			if($item->deadline != '0000-00-00 00:00:00') {
				$dt = explode(' ', $item->deadline);
				$dlDate = baseHelper::dateFormat($dt[0], 'd/m/y');
				$dlTime = ($dt[1] != '00:00:00') ? ' '.substr($dt[1], 0, 5).$item->timePeriod : '';
				$deadline = '<br />'.JText::_('FIELD_LABEL_DEADLINE').'<br />'.$dlDate.$dlTime;
			}

			// CHECK ACTIVITY
			$timeActive = '';
			if($item->working) {
				$query = '
					SELECT GROUP_CONCAT(IF(nickname = "", name, nickname)) name
					FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .'
					WHERE '. $db->quoteName('user_id') .' IN ('. $item->working .')
					ORDER BY name
				';
				$db->setQuery($query);
				$working = $db->loadResult();
				$timeActive = !empty($working) ? '<small class="base-icon-arrows-cw text-danger cursor-help hasTooltip" title="'.JText::sprintf('TEXT_RUNNING', str_replace(',', '<br />', baseHelper::nameFormat($working))).'"></small> ' : '';
			}

			$bug = $item->type ? '<small class="base-icon-bug text-danger cursor-help hasTooltip" title="Bug"></small> ' : '';

			$priority = $timeActive.$bug;
			if($item->priority == 0 && !empty($deadline)) $priority .= ' <small class="base-icon-attention text-primary cursor-help hasTooltip" title="'.JText::_('TEXT_PRIORITY_DESC_0').$deadline.'"></small>';
			else if($item->priority == 1) $priority .= ' <small class="base-icon-attention text-live cursor-help hasTooltip" title="'.JText::_('TEXT_PRIORITY_DESC_1').$deadline.'"></small>';
			else if($item->priority == 2) $priority .= ' <small class="base-icon-attention text-danger cursor-help hasTooltip" title="'.JText::_('TEXT_PRIORITY_DESC_2').$deadline.'"></small>';

			$tags = '';
			if(!empty($item->tags)) :
				$t = explode(',', $item->tags);
				for($i = 0; $i < count($t); $i++) {
					$tags .= ' <span class="badge badge-secondary">'.$t[$i].'</span>';
				}
			endif;

			switch($item->visibility) {
				case 0:
					$visibility = '<span class="base-icon-lock text-danger cursor-help hasTooltip" title="'.JText::_('TEXT_VISIBILITY_0_DESC').'"></span> ';
					break;
				case 2:
					$visibility = '<span class="base-icon-star text-yellow cursor-help hasTooltip" title="'.JText::_('TEXT_VISIBILITY_2_DESC').'"></span> ';
					break;
				default:
					$visibility = '';

			}

			$regInfo	= '';
			$regInfo	.= JText::_('TEXT_CREATED_DATE').': <span class="text-live">'.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'</span><br />';
			$regInfo	.= JText::_('TEXT_BY').': <span class="text-live">'.baseHelper::nameFormat(JFactory::getUser($item->created_by)->name).'</span>';
			if($item->alter_date != '0000-00-00 00:00:00') :
				$regInfo	.= '<hr class="my-1" />';
				$regInfo	.= JText::_('TEXT_ALTER_DATE').': <span class="text-live">'.baseHelper::dateFormat($item->alter_date, 'd/m/Y H:i').'</span><br />';
				$regInfo	.= JText::_('TEXT_BY').': <span class="text-live">'.baseHelper::nameFormat(JFactory::getUser($item->alter_by)->name).'</span>';
			endif;
			$regInfo = '<div class="small text-muted">'.$regInfo.'</div>';

			$btnActions = '<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_COPY_LINK_TO_SHARE').'" onclick="copyToClipboard(\''.$_ROOT.'apps/'.$APPPATH.'/view?vID='.$item->id.'\', \''.JText::_('MSG_COPY_LINK_TO_SHARE').'\')"><span class="base-icon-link"></span></a>';
			$appActions = '';
			if($cfg['canEdit'] || ($item->created_by == $user->id)) :
				if($item->state) {
					$appActions = '
						<a href="#modal-tasksTimer" class="dropdown-item px-3 py-2 b-bottom text-sm text-primary" onclick="tasksTimer_setParent('.$item->id.')" data-toggle="modal" data-backdrop="static" data-keyboard="false"><span class="base-icon-clock"></span> '.JText::_('TEXT_INSERT_TIME').'</a>
						<a href="#" class="dropdown-item px-3 py-2 b-bottom text-sm text-live" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span> '.JText::_('TEXT_EDIT').'</a>
						<a href="#" class="dropdown-item px-3 py-2 b-bottom text-sm text-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash"></span> '.JText::_('TEXT_DELETE').'</a>
					';
				}
				$btnActions .= '
					<div class="dropdown">
						<button class="btn btn-xs btn-link base-icon-cog" type="button" id="'.$APPTAG.'BtnActions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
						<div class="dropdown-menu dropdown-menu-right text-sm p-0 set-shadow" aria-labelledby="'.$APPTAG.'BtnActions">
							'.$appActions.'
							<span class="dropdown-item px-3 py-2 b-bottom text-sm">
								<span class="float-right">Task ID: <span class="text-live">#'.$item->id.'</span></span>
								<span class="text-info base-icon-info-circled"></span>
							</span>
							<span class="dropdown-item p-2">
								'.$regInfo.'
							</span>
						</div>
					</div>
					<a href="#" class="px-2 ml-1 b-left" onclick="'.$APPTAG.'_confirmState('.$item->id.', '.$item->state.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-toggle-on text-success' : 'base-icon-toggle-on text-danger').' hasTooltip" title="'.JText::_(($item->state == 1 ? 'MSG_CLOSED_ITEM' : 'MSG_ACTIVATE_ITEM')).'"></span>
					</a>
				';
			endif;

			// Assigned
			$assigned = '';
			if(!empty($item->assign_to)) :
				$query = 'SELECT name, nickname FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE '. $db->quoteName('user_id') .' IN ('.$item->assign_to.') ORDER BY name';
				$db->setQuery($query);
				$staff = $db->loadObjectList();
				$uName = '';
				$i = 0;
				foreach ($staff as $obj) {
					$uName .= '<div class=&quot;small&quot;>'.baseHelper::nameFormat(!empty($obj->nickname) ? $obj->nickname : $obj->name).'</div>';
					$i++;
				}
				$assigned = '<span class="btn btn-xs btn-link base-icon-user cursor-help hasTooltip" title="'.$uName.'"></span>';
			endif;

			if(!$cfg['canEdit'] && !($item->created_by == $user->id)) {
				$toggleStatus = '<span id="'.$APPTAG.'-item-'.$item->id.'-status" class="base-icon-'.$iconStatus.' text-'.$colorStatus.' hasTooltip" title="'.JText::_('TEXT_STATUS_'.$item->status).'" data-id="'.$item->id.'" data-status="'.$item->status.'"></span>';
			} else {
				$toggleStatus = '<a href="#" id="'.$APPTAG.'-item-'.$item->id.'-status" class="base-icon-'.$iconStatus.' text-'.$colorStatus.' hasTooltip" title="'.JText::_('TEXT_STATUS_'.$item->status).'" data-id="'.$item->id.'" data-status="'.$item->status.'" onclick="'.$APPTAG.'_setStatusModal(this)"></a>';
			}

			$project_state = '';
			$project_desc = 'FIELD_LABEL_PROJECT';
			if($item->project_state == 0) {
				$project_state = ' text-danger';
				$project_desc = 'TEXT_INACTIVE_PROJECT';
			}

			// Resultados
			$html .= '
				<div id="'.$APPTAG.'-item-'.$item->id.'" class="pos-relative rounded b-top-2 b-'.$colorStatus.' bg-white mb-3 set-shadow">
					<span class="d-flex justify-content-between align-items-center text-muted px-1 b-bottom">
						<small><span class="base-icon-tag text-gray-400 cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_TAGS').'"></span> '.(str_replace(',', ', ', $item->tags)).'</small>
						<span>&#160;'.$priority.'</span>
					</span>
					<div class="d-flex d-justify-content align-items-center lh-1-2">
						<div class="align-self-stretch py-3 px-2 bg-gray-200">
							'.$toggleStatus.'
						</div>
						<a href="#'.$APPTAG.'-item-view" class="set-base-modal text-sm text-'.$colorStatus.' py-1 px-2" onclick="'.$APPTAG.'_setItemView('.$item->id.')">
							'.baseHelper::nameFormat($item->subject).'
						</a>
					</div>
					<span class="d-flex justify-content-between align-items-center text-muted pl-1 b-top">
						<span class="small lh-1">
							'.$visibility.'
							<a href="'.$urlViewProject.'" class="ml-1'.$project_state.' hasTooltip" title="'.JText::_($project_desc).'">
								'.baseHelper::nameFormat($item->project_name).'
							</a>
						</span>
						<span class="btn-group">
							'.$assigned.$btnActions.'
						</span>
					</span>
				</div>
			';

			$counter++;
		}
		$html .= '</div>';
	else :
		if($noReg) $html = '<div class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_LISTNOREG').'</div>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
