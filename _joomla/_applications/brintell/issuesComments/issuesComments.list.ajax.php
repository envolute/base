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

	// LOAD FILTER
	$fQuery = $PATH_APP_FILE.'.filter.query.php';
	if($aFLT && file_exists($fQuery)) require($fQuery);

	// GET DATA
	$noReg	= true;
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.type') .',
			'. $db->quoteName('T2.staff_id') .',
			'. $db->quoteName('T2.clientsStaff_id') .',
			'. $db->quoteName('T2.app') .',
			'. $db->quoteName('T2.app_table') .',
			'. $db->quoteName('T2.user_id') .',
			'. $db->quoteName('T2.name') .',
			'. $db->quoteName('T2.nickname') .',
			'. $db->quoteName('T2.gender') .',
			'. $db->quoteName('T3.session_id') .' online
	';
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				LEFT JOIN '. $db->quoteName('vw_'.$cfg['project'].'_teams') .' T2
				ON T2.user_id = T1.created_by
				LEFT JOIN '. $db->quoteName('#__session') .' T3
				ON '.$db->quoteName('T3.userid') .' = T1.created_by AND T3.client_id = 0
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T4
				ON '.$db->quoteName('T4.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T4.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= '
				FROM '. $db->quoteName($cfg['mainTable']) .' T1
					LEFT JOIN '. $db->quoteName('vw_'.$cfg['project'].'_teams') .' T2
					ON T2.user_id = T1.created_by
					LEFT JOIN '. $db->quoteName('#__session') .' T3
					ON '.$db->quoteName('T3.userid') .' = T1.created_by AND T3.client_id = 0
				WHERE '. $db->quoteName($rNID) .' = '. $rID
			;
		endif;
	else :
		$query .= '
			FROM '. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('vw_'.$cfg['project'].'_teams') .' T2
			ON T2.user_id = T1.created_by
			LEFT JOIN '. $db->quoteName('#__session') .' T3
			ON '.$db->quoteName('T3.userid') .' = T1.created_by AND T3.client_id = 0
		';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		endif;
	endif;
	$query	.= ' ORDER BY '. $db->quoteName('T1.created_date') .' ASC';
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
		$html .= '<ul class="set-list bordered">';
		foreach($res as $item) {

			// define permissões de execução
			$canEdit	= ($cfg['canEdit'] || $item->created_by == $user->id);
			$canDelete	= ($cfg['canDelete'] || $item->created_by == $user->id);

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
				$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
				$listFiles = '';
				for($i = 0; $i < count($files[$item->id]); $i++) {
					if(!empty($files[$item->id][$i]->filename)) :
						$listFiles .= '
							<a class="d-inline-block mr-3" href="'.$_ROOT.'apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
								<span class="base-icon-attach hasTooltip" title="'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"> '.$files[$item->id][$i]->filename.'</span>
							</a>
						';
					endif;
				}

				// Imagem do usuário
				$member_id = $item->staff_id ? $item->staff_id : $item->clientsStaff_id;
				$img = uploader::getFile('#__brintell_'.$item->app_table.'_files', '', $member_id, 0, JPATH_BASE.DS.'images/apps/'.$item->app.'/');
				if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$item->app.'/'.$img['filename'], 41, 41);
				else $imgPath = $_ROOT.'images/apps/icons/user_'.$item->gender.'.png';
				$img = '<img src="'.$imgPath.'" class="img-fluid rounded mb-2" style="width:41px; height:41px;" />';
			endif;

			$lStatus = '';
			$iStatus = '';
			if($item->online) :
				$lStatus = JText::_('TEXT_USER_STATUS_1');
				$iStatus = ' <small class="base-icon-circle text-success cursor-help hasTooltip" title="'.$lStatus.'" style="bottom:-5px;"></small>';
			endif;
			$name = baseHelper::nameFormat((!empty($item->nickname) ? $item->nickname : $item->name));
			if($item->type == 2) $name = ' <span class="badge badge-warning">'.JText::_('TEXT_CLIENT').'</span>';

			$attachs = !empty($listFiles) ? '<div class="font-condensed text-sm pt-1">'.$listFiles.'</div>' : '';

			// $btnState = $canEdit ? '<a href="#" class="btn btn-xs btn-link" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'"><span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span></a> ' : '';
			$btnEdit = $canEdit ? '<a href="#" class="btn btn-xs btn-default" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil text-live hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a> ' : '';
			$btnDelete = $canDelete ? '<a href="#" class="btn btn-xs btn-default" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash text-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>' : '';

			$html .= '
				<li class="d-flex">
					<div class="mr-3" style="flex:0 0 42px;">
						<a href="'.$urlProfile.'">'.$img.'</a>
						<div class="btn-group btn-group-justified">'.$btnEdit.$btnDelete.'</div>
					</div>
					<div style="flex-grow:1;" class="font-condensed text-sm mb-2 lh-1-3">
						<div class="page-header text-muted b-bottom-dashed pb-1 clearfix">'.$name.$iStatus.' <span class="float-right">'.baseHelper::dateFormat($item->created_date, 'd.m.y H:i').'</span></div>
						<div>'.$item->comment.'</div>
						'.$attachs.'
					</div>
				</li>
			';
		}
		$html .= '</ul>';
	else :
		if($noReg) $html = '<div class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_LISTNOREG').'</div>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
