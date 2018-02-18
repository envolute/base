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
			'. $db->quoteName('T2.name') .' project
	';
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_projects') .' T2
				ON '.$db->quoteName('T2.id') .' = T1.project_id AND T2.state = 1
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T3
				ON '.$db->quoteName('T3.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.$where.' AND '. $db->quoteName('T3.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID.$orderList
			;
		else :
			$query .= ' FROM '. $db->quoteName($cfg['mainTable']) .' T1
				LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_projects') .' T2
				ON '.$db->quoteName('T2.id') .' = T1.project_id AND T2.state = 1
				WHERE '.$where.' AND '. $db->quoteName($rNID) .' = '. $rID.$orderList
			;
		endif;
	else :
		$query .= ' FROM '. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_projects') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.project_id AND T2.state = 1
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
		if(!$active) echo '<hr class="hr-tag b-danger" /><span class="badge badge-danger base-icon-box"> '.JText::_('TEXT_ARCHIVE').'</span>';
		$html .= '<div class="row py-2 mb-4">';
		$status		= 9;
		$counter	= 0;
		foreach($res as $item) {

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
			$itemStatus = '';
			switch($item->status) {
				case '1': // Todo
					$itemStatus = 'danger';
					$iconStatus = 'clock';
					break;
				case '2': // Doing
					$itemStatus = 'live';
					$iconStatus = 'off';
					break;
				case '3': // completed
					$itemStatus = 'success';
					$iconStatus = 'ok';
					break;
				default:
					$itemStatus = 'info';
					$iconStatus = 'lightbulb';
			}

			// define as colunas por status
			if($status !== $item->status) :
				if($counter > 0) $html .= '</div>';
				$html .= '
					<div id="'.$APPTAG.'-item-status-'.$item->status.'" class="tasks-col col-md-3 pb-3">
						<h6 class="text-center bg-'.$itemStatus.' rounded py-2 set-shadow-right">
							<span class="base-icon-'.$iconStatus.'"></span> '.JText::_('TEXT_STATUS_'.$item->status).'
						</h6>
				';
				$status = $item->status;
			endif;

			$priority = '';
			if($item->priority == 1) $priority = ' <small class="base-icon-attention text-live cursor-help hasTooltip" title="'.JText::_('TEXT_PRIORITY_DESC_1').'"></small> ';
			else if($item->priority == 2) $priority = ' <small class="base-icon-attention text-danger cursor-help hasTooltip" title="'.JText::_('TEXT_PRIORITY_DESC_2').'"></small> ';

			$deadline = $item->deadline != '0000-00-00 00:00:00' ? '<small class="text-muted cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DEADLINE').'">'.baseHelper::dateFormat($item->deadline, 'd/m/y H:i').$item->timePeriod.'</small>' : '';

			$btnActions = '';
			if($hasAdmin || ($item->created_by == $user->id)) :
				$btnActions = '
					<a href="#" class="btn btn-xs btn-link" onclick="'.$APPTAG.'_setState('.$item->id.', null, false, \'base-icon-toggle-on\', \'base-icon-toggle-on\', \'text-success\', \'text-muted\')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-toggle-on text-success' : 'base-icon-toggle-on text-muted').' hasTooltip" title="'.JText::_(($item->state == 1 ? 'MSG_ARCHIVE_ITEM' : 'MSG_ACTIVATE_ITEM')).'"></span>
					</a>
					<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil text-live"></span></a>
					<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash text-danger"></span></a>
				';
			endif;

			$regInfo	= 'Task ID: <span class=&quot;text-live&quot;>#'.$item->id.'</span>';
			if(!empty($item->requests)) :
				$r = str_replace(',', ', #', $item->requests);
				$regInfo .= '<div>Req. ID: <span class=&quot;text-live&quot;>#'.$r.'</span></div>';
			endif;
			$regInfo	.= '<hr class=&quot;my-1&quot; />';
			$regInfo	.= JText::_('TEXT_CREATED_DATE').': '.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'<br />';
			$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->created_by)->name);
			if($item->alter_date != '0000-00-00 00:00:00') :
				$regInfo	.= '<hr class=&quot;my-1&quot; />';
				$regInfo	.= JText::_('TEXT_ALTER_DATE').': '.baseHelper::dateFormat($item->alter_date, 'd/m/Y H:i').'<br />';
				$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->alter_by)->name);
			endif;
			$regInfo = '<div class=&quot;small&quot;>'.$regInfo.'</div>';

			// Assigned
			$assigned = '';
			if(!empty($item->assign_to)) :
				$query = 'SELECT name, nickname FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE '. $db->quoteName('id') .' IN ('.$item->assign_to.') ORDER BY name';
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

			// Resultados
			$html .= '
				<div id="'.$APPTAG.'-item-'.$item->id.'" class="pos-relative rounded b-top-2 b-'.$itemStatus.' bg-white mb-3 set-shadow">
					<div class="d-flex d-justify-content lh-1-2">
						<div class="py-3 px-2 bg-gray-200">
							<a href="#" id="'.$APPTAG.'-item-'.$item->id.'-status" class="base-icon-'.$iconStatus.' text-'.$itemStatus.' hasTooltip" title="'.JText::_('TEXT_STATUS_'.$item->status).'" data-id="'.$item->id.'" data-status="'.$item->status.'" onclick="'.$APPTAG.'_setStatusModal(this)"></a>
						</div>
						<a href="#'.$APPTAG.'-item-view" class="set-base-modal py-3 px-2" onclick="'.$APPTAG.'_setItemView('.$item->id.')">
							'.baseHelper::nameFormat($item->subject).'
							<div class="pos-absolute pos-top-0 pos-right-0 mx-1">
								'.$priority.$deadline.'
							</div>
						</a>
					</div>
					<span class="d-flex justify-content-between align-items-center text-muted pl-2 b-top">
						<a href="'.$urlViewProject.'" class="small lh-1 hasTooltip" title="'.JText::_('FIELD_LABEL_PROJECT').'">
							'.baseHelper::nameFormat($item->project).'
						</a>
						<span class="btn-group">
							'.$assigned.$btnActions.'
							<a href="#" class="btn btn-xs btn-link text-info base-icon-info-circled hasPopover" title="'.JText::_('TEXT_REGISTRATION_INFO').'" data-content="'.$regInfo.'" data-placement="top" data-trigger="click focus"></a>
						</span>
					</span>
				</div>
			';

			$counter++;
		}
		$html .= '</div>';
	else :
		if($noReg) $html = '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_LISTNOREG').'</p>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
