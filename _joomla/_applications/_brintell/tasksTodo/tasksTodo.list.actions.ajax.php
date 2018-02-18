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
	$hasGroup	= array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
	$hasAdmin	= array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos

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
			'. $db->quoteName('T2.nickname') .',
			'. $db->quoteName('T2.gender') .'
	';
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_staff') .' T2
				ON T2.user_id = T1.created_by
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T3
				ON '.$db->quoteName('T3.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T3.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= '
				FROM '. $db->quoteName($cfg['mainTable']) .' T1
					LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_staff') .' T2
					ON T2.user_id = T1.created_by
				WHERE '. $db->quoteName($rNID) .' = '. $rID
			;
		endif;
	else :
		$query .= '
			FROM '. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_staff') .' T2
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
			endif;

			$attachs = !empty($listFiles) ? '<div class="font-condensed text-sm pt-1">'.$listFiles.'</div>' : '';
			$desc = !empty($item->description) ? '<div class="font-condensed text-sm"><hr class="my-2" />'.$item->description.'</div>' : '';

			$btnState = '';
			$txtState = ($item->state == 1) ? ' class="text-success"' : ' class="text-danger" style="text-decoration: line-through;"';
			if($hasAdmin) :
				$btnState = '
					<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-check-empty text-success' : 'base-icon-check text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
					</a>
				';
			endif;
			$btnEdit = $hasAdmin ? '<a href="#" class="mr-1" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil text-live hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a> ' : '';
			$btnDelete = $hasAdmin ? '<a href="#" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash text-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>' : '';

			$collapse = '';
			$btnCollapse = '<span class="text-sm float-right">'.$btnEdit.$btnDelete.'</span>';
			if(!empty($attachs) || !empty($desc)) :
				$collapse = '
					<div id="'.$APPTAG.'-taskTodo-item-'.$item->id.'" class="collapse">
						'.$desc.$attachs.'
						<div class="pt-2 text-sm">'.$btnEdit.$btnDelete.'</div>
					</div>
				';
				$btnCollapse = '
					<button class="btn btn-xs btn-link float-right toggle-state" data-toggle="collapse" data-target="#'.$APPTAG.'-taskTodo-item-'.$item->id.'" aria-expanded="" aria-controls="taskTodoItem">
						<span class="base-icon-sort"></span>
					</button>
				';
			endif;

			$html .= '
				<div class="bg-white p-2 mb-1 rounded b-top-2 b-danger set-shadow-right">
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
