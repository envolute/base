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
	$query = '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' project
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT OUTER JOIN '. $db->quoteName('#__'.$cfg['project'].'_projects') .' T2
			ON T2.id = T1.project_id AND T2.state = 1
		WHERE
			'.$where.$orderList;
	;
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

			$urlViewData = $_ROOT.'apps/'.$APPPATH.'/view?pID='.$item->id;
			$urlViewProject = $_ROOT.'apps/projects/view?vID='.$item->project_id;
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
					<div id="'.$APPTAG.'-item-status-'.$item->status.'" class="tasks-col col-md pb-3">
						<h6 class="text-center bg-'.$itemStatus.' rounded py-2 set-shadow-right">
							<span class="base-icon-'.$iconStatus.'"></span> '.JText::_('TEXT_STATUS_'.$item->status).'
						</h6>
				';
				$status = $item->status;
			endif;

			$deadline = $item->deadline != '0000-00-00' ? '<small class="badge badge-secondary text-danger pos-absolute pos-top-0 pos-right-0 m-1 cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DEADLINE').'">'.baseHelper::dateFormat($item->deadline).'</small>' : '';

			$btnActions = '';
			if($hasAdmin || ($item->created_by == $user->id)) :
				$btnActions = '
					<a href="#" class="btn btn-xs btn-link" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-toggle-on text-success' : 'base-icon-toggle-on text-muted').' hasTooltip" title="'.JText::_(($item->state == 1 ? 'MSG_ARCHIVE_ITEM' : 'MSG_ACTIVATE_ITEM')).'"></span>
					</a>
					<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil text-live"></span></a>
					<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash text-danger"></span></a>
				';
			endif;

			$regInfo	= 'Task ID: <span class=&quot;text-live&quot;>#'.$item->id.'</span>';
			if(!empty($item->request)):
				$r = str_replace(',', ', #', $item->request);
				$regInfo = 'Issue ID: <span class=&quot;text-live&quot;>#'.$r.'</span>';
			endif;
			$regInfo	.= '<hr class=&quot;my-1&quot; />';
			$regInfo	.= JText::_('TEXT_CREATED_DATE').': '.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'<br />';
			$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->created_by)->name);
			if($item->alter_date != '0000-00-00 00:00:00') :
				$regInfo	.= '<hr class=&quot;my-1&quot; />';
				$regInfo	.= JText::_('TEXT_ALTER_DATE').': '.baseHelper::dateFormat($item->alter_date, 'd/m/Y H:i').'<br />';
				$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->alter_by)->name);
			endif;

			// Assigned
			$assigned = '';
			if(!empty($item->assign_to)) :
				$query = 'SELECT name, nickname FROM '. $db->quoteName('#__'.$cfg['project'].'_teams') .' WHERE '. $db->quoteName('id') .' IN ('.$item->assign_to.') ORDER BY name';
				$db->setQuery($query);
				$team = $db->loadObjectList();
				$uName = '';
				$i = 0;
				foreach ($team as $obj) {
					$uName .= '<div class=&quot;small&quot;>'.baseHelper::nameFormat(!empty($obj->nickname) ? $obj->nickname : $obj->name).'</div>';
					$i++;
				}
				$assigned = '<span class="btn btn-xs btn-link base-icon-user cursor-help hasTooltip" title="'.$uName.'"></span>';
			endif;

			// Resultados
			$html .= '
				<div id="'.$APPTAG.'-item-'.$item->id.'" class="pos-relative rounded b-top-2 b-'.$itemStatus.' bg-white mb-3 set-shadow">
					<div class="d-flex d-justify-content lh-1-2">
						<a href="#" id="'.$APPTAG.'-item-'.$item->id.'-status" class="py-3 px-2 bg-gray-200 base-icon-'.$iconStatus.' text-'.$itemStatus.' hasTooltip" title="'.JText::_('TEXT_STATUS_'.$item->status).'" data-id="'.$item->id.'" data-status="'.$item->status.'" onclick="'.$APPTAG.'_setStatusModal(this)"></a>
						<a href="'.$urlViewData.'" class="py-3 px-2">
							'.baseHelper::nameFormat($item->subject).$deadline.'
						</a>
					</div>
					<span class="d-flex justify-content-between align-items-center text-muted pl-2 b-top">
						<a href="'.$urlViewProject.'" class="small lh-1 hasTooltip" title="'.JText::_('FIELD_LABEL_PROJECT').'">
							'.baseHelper::nameFormat($item->project).'
						</a>
						<span class="btn-group">
							'.$assigned.$btnActions.'
							<a href="#" class="btn btn-xs btn-link base-icon-info-circled hasPopover" title="'.JText::_('TEXT_REGISTRATION_INFO').'" data-content="'.$regInfo.'" data-placement="top" data-trigger="click focus"></a>
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
