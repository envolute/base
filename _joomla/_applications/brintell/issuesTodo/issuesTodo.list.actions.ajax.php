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
			'. $db->quoteName('T2.id') .' staff_id,
			'. $db->quoteName('T2.name') .',
			'. $db->quoteName('T2.gender') .'
	';
	if(!empty($rNID) && (!empty($rID) && $rID !== 0)) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients_staff') .' T2
				ON T2.user_id = T1.created_by
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T3
				ON '.$db->quoteName('T3.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T3.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= '
				FROM '. $db->quoteName($cfg['mainTable']) .' T1
					LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients_staff') .' T2
					ON T2.user_id = T1.created_by
				WHERE '. $db->quoteName($rNID) .' = '. $rID
			;
		endif;
	else :
		$query .= '
			FROM '. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients_staff') .' T2
			ON T2.user_id = T1.created_by
		';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		endif;
	endif;
	$query	.= ' ORDER BY '. $db->quoteName('T1.orderer') .' ASC, '. $db->quoteName('T1.created_date') .' ASC';
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
						if(strpos($files[$item->id][$i]->mimetype, 'image') !== false) {
							$listFiles .= '
								<a class="set-modal modal_link cboxElement d-inline-block mr-3" href="'.$_ROOT.'images/apps/'.$APPPATH.'/'.$files[$item->id][$i]->filename.'">
									<span class="base-icon-file-image hasTooltip" title="'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"> '.$files[$item->id][$i]->filename.'</span>
								</a>
							';
						} else {
							$listFiles .= '
								<a class="d-inline-block mr-3" href="'.$_ROOT.'apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
									<span class="base-icon-attach hasTooltip" title="'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"> '.$files[$item->id][$i]->filename.'</span>
								</a>
							';
						}
					endif;
				}
			endif;

			$attachs = !empty($listFiles) ? '<div class="font-condensed text-sm pt-1">'.$listFiles.'</div>' : '';

			$desc = '';
			if(!empty($item->description)) {
				$desc = htmlspecialchars($item->description);
				$desc = preg_replace('~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~','<a href="\\0" target="_blank">\\0</a>', $desc);
				$desc = '<div class="break-word text-pre-wrap font-condensed text-sm mt-1 py-2 b-top b-top-dashed">'.$desc.'</div>';
			}

			$btnState = '';
			$txtState = ($item->state == 1) ? ' class="text-success"' : ' class="text-danger" style="text-decoration: line-through;"';
			if($canEdit) :
				$btnState = '
					<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-check-empty text-success' : 'base-icon-check text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
					</a>
				';
			endif;
			$btnEdit = $canEdit ? '<a href="#" class="mr-1" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil text-live hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a> ' : '';
			$btnDelete = $canDelete ? '<a href="#" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash text-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>' : '';

			$collapse = '';
			$btnCollapse = '<span class="text-sm float-right">'.$btnEdit.$btnDelete.'</span>';
			if(!empty($attachs) || !empty($desc)) :
				$collapse = '
					<div id="'.$APPTAG.'-todo-item-'.$item->id.'" class="collapse">
						'.$desc.$attachs.'
						<div class="pt-2 text-sm">'.$btnEdit.$btnDelete.'</div>
					</div>
				';
				$btnCollapse = '
					<a href="#'.$APPTAG.'-todo-item-'.$item->id.'" class="text-lg px-1 lh-1-2 float-right toggle-state toggle-icon base-icon-right-dir hasTooltip" title="'.JText::_('TEXT_CLICK_TO_VIEW_INFO').'" data-icon-default="base-icon-right-dir" data-icon-active="base-icon-down-dir text-live" data-toggle="collapse" aria-expanded="false" aria-controls="todoItem"></a>
				';
			endif;

			$html .= '
				<div class="bg-white p-2 mb-1 rounded b-top-2 b-danger set-shadow">
					<div class="d-flex">
						<div style="flex:0 0 20px;">'.$btnState.'</div>
						<div style="flex-grow:1;" class="font-condensed lh-1-3">
							'.$btnCollapse.'
							 <span'.$txtState.'>'.$item->title.'</span>
						</div>
					</div>
					'.$collapse.'
				</div>
			';
		}
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
