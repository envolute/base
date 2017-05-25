<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	// load Joomla's framework
	require_once('../load.joomla.php');
	$app = JFactory::getApplication('site');

	defined('_JEXEC') or die;
	$ajaxRequest = true;
	require('config.php');
	// IMPORTANTE: Carrega o arquivo 'helper' do template
	JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

	// Carrega o arquivo de tradução
	// OBS: para arquivos externos com o carregamento do framework 'load.joomla.php' (geralmente em 'ajax')
	// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
	// Para possibilitar o carregamento da language 'default' de forma dinâmica,
	// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
	if(isset($_SESSION[$APPTAG.'langDef']))
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);

	//joomla get request data
	$input      = $app->input;

	// params requests
	$APPTAG			= $input->get('aTag', $APPTAG, 'str');
	$RTAG				= $input->get('rTag', $APPTAG, 'str');
	$oCHL				= $input->get('oCHL', 0, 'bool');
	$oCHL				= $_SESSION[$RTAG.'OnlyChildList'] ? $_SESSION[$RTAG.'OnlyChildList'] : $oCHL;
	$rNID       = $input->get('rNID', '', 'str');
	$rNID				= !empty($_SESSION[$RTAG.'RelListNameId']) ? $_SESSION[$RTAG.'RelListNameId'] : $rNID;
	$rID      	= $input->get('rID', 0, 'int');
	$rID				= !empty($_SESSION[$RTAG.'RelListId']) ? $_SESSION[$RTAG.'RelListId'] : $rID;

	// get current user's data
	$user = JFactory::getUser();
	$groups = $user->groups;

	// verifica o acesso
	$hasGroup = array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
	$hasAdmin = array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos

	// database connect
	$db = JFactory::getDbo();

	// GET DATA
	$noReg = true;
	$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T1.type') .',
		'. $db->quoteName('T1.service_id') .',
		'. $db->quoteName('T1.project_id') .',
		'. $db->quoteName('T1.priority') .',
		'. $db->quoteName('T2.price') .' servicePrice,
		'. $db->quoteName('T2.name') .' service,
		'. $db->quoteName('T3.name') .' project,
		'. $db->quoteName('T4.name') .' client,
		'. $db->quoteName('T1.title') .',
		'. $db->quoteName('T1.description') .',
		'. $db->quoteName('T1.price') .',
		'. $db->quoteName('T1.estimate') .',
		'. $db->quoteName('T1.billable') .',
		'. $db->quoteName('T1.period') .',
		'. $db->quoteName('T1.start_date') .',
		'. $db->quoteName('T1.deadline') .',
		'. $db->quoteName('T1.end_date') .',
		'. $db->quoteName('T1.recurrent_type') .',
		'. $db->quoteName('T1.weekly') .',
		'. $db->quoteName('T1.monthly') .',
		'. $db->quoteName('T1.yearly') .',
		'. $db->quoteName('T1.percent') .',
		'. $db->quoteName('T1.hour') .',
		'. $db->quoteName('T1.visible') .',
		'. $db->quoteName('T1.status') .',
		'. $db->quoteName('T1.status_desc') .',
		'. $db->quoteName('T1.state')
	;
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				LEFT JOIN '. $db->quoteName('#__envolute_services') .' T2
				ON T2.id = T1.service_id
				LEFT JOIN '. $db->quoteName('#__envolute_projects') .' T3
				ON T3.id = T1.project_id
				LEFT JOIN '. $db->quoteName('#__envolute_clients') .' T4
				ON T4.id = T3.client_id
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T6
				ON '.$db->quoteName('T6.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T6.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID .'
				AND '. $db->quoteName('T1.type') .' = 0 AND '. $db->quoteName('T1.status') .' < 3 AND '. $db->quoteName('T1.state') .' = 1
			';
		else :
			$query .= '
			FROM
				'. $db->quoteName($cfg['mainTable']) .' T1
				LEFT JOIN '. $db->quoteName('#__envolute_services') .' T2
				ON T2.id = T1.service_id
				LEFT JOIN '. $db->quoteName('#__envolute_projects') .' T3
				ON T3.id = T1.project_id
				LEFT JOIN '. $db->quoteName('#__envolute_clients') .' T4
				ON T4.id = T3.client_id
			WHERE '. $db->quoteName($rNID) .' = '. $rID .' AND '. $db->quoteName('T1.type') .' = 0 AND '. $db->quoteName('T1.status') .' < 3 AND '. $db->quoteName('T1.state') .' = 1';
		endif;
	else :
		$query .= '
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__envolute_services') .' T2
			ON T2.id = T1.service_id
			LEFT JOIN '. $db->quoteName('#__envolute_projects') .' T3
			ON T3.id = T1.project_id
			LEFT JOIN '. $db->quoteName('#__envolute_clients') .' T4
			ON T4.id = T3.client_id
		';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		else :
			// mostra apenas os pendentes, esconde os finalizados e cancelados
			$query .= ' WHERE '. $db->quoteName('T1.type') .' = 0 AND '. $db->quoteName('T1.status') .' < 3 AND '. $db->quoteName('T1.state') .' = 1';
		endif;
	endif;
	$query .= ' ORDER BY '. $db->quoteName('T1.priority') .' DESC, '. $db->quoteName('T1.deadline') .' DESC, '. $db->quoteName('T1.status');
	try {

		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

	$html = '<span class="ajax-loader hide"></span>';

	if($num_rows) : // verifica se existe
		$html .= '<ul class="list list-striped list-hover">';
		foreach($res as $item) {

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
				$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
				$listFiles = '';
				for($i = 0; $i < count($files[$item->id]); $i++) {
					if(!empty($files[$item->id][$i]->filename)) :
						$listFiles .= '
							<a href="'.$_root.'get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
								<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
							</a>
						';
					endif;
				}
			endif;

			// get registered acitivity
			$query = '
			SELECT
				SEC_TO_TIME(SUM(TIME_TO_SEC('. $db->quoteName('total_time') .'))) totalTime,
				SUM('. $db->quoteName('hours') .') totalHours
			FROM '. $db->quoteName($cfg['mainTable'].'_timer') .' WHERE '. $db->quoteName('task_id') .' = '. $item->id;
			$db->setQuery($query);
			$time = $db->loadObject();
			$totalTime = '<span class="cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_TIME_COUNT').'"> '.(!empty($time->totalTime) ? substr($time->totalTime, 0, 5) : '00:00').'</span>';
			$totalHours = $time->totalHours;

			// get comments
			$query = '
			SELECT COUNT(*) FROM '. $db->quoteName('#__envolute_rel_tasks_comments') .' WHERE '. $db->quoteName('task_id') .' = '. $item->id;
			$db->setQuery($query);
			$comments = $db->loadResult();
			$comments = $comments ? ' ('.$comments.')' : '';

			switch ($item->status) {
				case '0':
					$stext = JText::_('FIELD_LABEL_STATUS_WAITING');
					$sClass = 'base-icon-clock text-live';
					break;
				case '1':
					$stext = JText::_('FIELD_LABEL_STATUS_ACTIVE');
					$sClass = 'base-icon-off text-success';
					break;
				case '2':
					$stext = JText::_('FIELD_LABEL_STATUS_PAUSED');
					$sClass = 'base-icon-pause text-live';
					break;
				case '3':
					$stext = JText::_('FIELD_LABEL_STATUS_COMPLETED');
					$sClass = 'base-icon-ok text-success';
					break;
				case '4':
					$stext = JText::_('FIELD_LABEL_STATUS_CANCELED');
					$sClass = 'base-icon-cancel text-danger';
					break;
				default:
					$stext = '';
					$sClass = '';
			}
			$stDesc = '';
			if(!empty($item->status_desc)) :
				$sClass .= ' hasPopover';
			else :
				$sClass .= ' hasTooltip';
			endif;
			$status = '<a href="#" id="'.$APPTAG.'-item-'.$item->id.'-status" class="'.$sClass.'" title="'.$stext.'<br />'.JText::_('TEXT_CLICK_TO_ALTER').'" data-id="'.$item->id.'" data-status="'.$item->status.'" onclick="'.$APPTAG.'_setStatusModal(this)"></a>';
			$desc = !empty($item->description) ? '<hr class=\'hr-xs\'><div class=\'text-xs text-muted\'>'.$item->description.'</div>' : '';
			$billable = ($item->billable == 1) ? '<span class="base-icon-dollar text-success cursor-help right-space hasTooltip" title="'.JText::_('FIELD_LABEL_BILLABLE').'"></span>' : '';
			$visible = ($item->visible == 1) ? '<span class="base-icon-eye text-success cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_VISIBLE_DESC').'"></span>' : '<span class="base-icon-eye-off text-danger cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_VISIBLE_NOT_DESC').'"></span>';
			// tarefa ou template
			if($item->type == 1) :
				$task = '<span class="base-icon-star text-live cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_TEMPLATE').'"></span> <span class="cursor-help hasPopover" data-placement="top" data-content="<div class=\'small\'>'.baseHelper::nameFormat($item->serviceType).$desc.'</div>"><span class="font-condensed">#'.$item->id.'</span> - '.baseHelper::nameFormat($item->title).'</span>';
			else :
				$task = ($item->priority == 1) ? '<span class="base-icon-attention text-live cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_PRIORITY').'"></span> ' : '';
				$task .= '<span class="cursor-help hasPopover" data-placement="top" data-content="<strong>'.JText::_('FIELD_LABEL_SERVICE').'</strong><div class=\'small\'>'.baseHelper::nameFormat($item->service).'</div><strong>'.JText::_('FIELD_LABEL_PROJECT').'</strong><div class=\'small\'>'.baseHelper::nameFormat($item->project).$desc.'</div>"><span class="font-condensed">#'.$item->id.'</span> - '.baseHelper::nameFormat($item->title).'</span> ';
			endif;
			// preço fixo
			$taskPrice = '';
			$taskPrice = ($item->price != '0.00') ? '<span class="cursor-help hasTooltip" title="'.JText::_('MSG_TASK_PRICE_FIXED').'">'.baseHelper::priceFormat($item->price, false, '<small>R$</small>', true, '-').'</span>' : '';
			// Recurrent
			$days = $periodTip = '';
			if($item->recurrent_type == 2) : // semanal
				$week = explode(',', $item->weekly);
				for($i = 0; $i < count($week); $i++) {
					if(!empty($days)) $days .= ', ';
					$days .= JText::_('FIELD_LABEL_WEEKLY_DAY_'.$week[$i].'_ABBR');
				}
				$periodTip = ': '.JText::_('FIELD_LABEL_WEEKLY');
			elseif($item->recurrent_type == 3) : // mensal
				$days .= str_replace(',', ', ', $item->monthly);
				$periodTip = ': '.JText::_('FIELD_LABEL_MONTHLY');
			elseif($item->recurrent_type == 4) : // anual
				$days .= str_replace(',', ', ', $item->yearly);
				$periodTip = ': '.JText::_('FIELD_LABEL_YEARLY');
			endif;
			// Period
			$date = '';
			if($item->period == 0) :
				$date = $item->deadline != '0000-00-00' ? '<span class="cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DEADLINE').'">'.baseHelper::dateFormat($item->deadline, 'd/m/Y', true, '-').'</span>' : '';
			else :
				$recurr = '<span class="base-icon-arrows-cw text-success cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_RECURRENT').$periodTip.'"></span> ';
				if($item->recurrent_type == 1) :
					$date = $recurr.JText::_('FIELD_LABEL_DAYLY');
				else :
					$date = (!empty($item->weekly) ? $recurr.$days : '');
					$date = (!empty($item->monthly) ? $recurr.$days : $date);
					$date = (!empty($item->yearly) ? $recurr.$days : $date);
				endif;
			endif;
			$date = !empty($date) ? '<span class="right-space">'.$date.'</span>' : '';
			if($item->status > 2) :
				$eDt = explode(' ', $item->end_date);
				$end_date = baseHelper::dateFormat($eDt[0], 'd/m/Y').' <span class="base-icon-clock hasTooltip" title="'.$eDt[1].'"></span>';
			else :
				$end_date = '-';
			endif;
			$btnState = $hasAdmin ? '<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'"><span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span></a> ' : '';
			$btnEdit = $hasAdmin ? '<a href="#" class="base-icon-pencil text-live hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"></a> ' : '';
			$btnDelete = $hasAdmin ? '<a href="#" class="base-icon-trash text-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"></a>' : '';
			$rowState = $item->state == 0 ? 'danger' : '';
			$html .= '
				<li class="'.$rowState.'">
					<span class="btn-group pull-right">
						<a href="#" class="base-icon-clock text-muted hasTooltip" title="'.JText::_('MSG_INSERT_TASKTIMER').'" onclick="tasksTimer_setParent('.$item->id.', false)" data-toggle="modal" data-target="#modal-tasksTimer" data-backdrop="static" data-keyboard="false"></a>
						<a href="#" class="base-icon-users text-muted hasTooltip" onclick="tasksContacts_listReload(false, false, false, true, \'task_id\', '.$item->id.')" data-toggle="modal" data-target="#modal-list-tasksContacts" title="'.JText::_('MSG_VIEW_CONTACTS').'"></a>
						'.$btnState.$btnEdit.$btnDelete.'
					</span>
					<a href="#">'.$status.' '.$task.'</a>
					<div class="small opacity_80 font-featured clearfix">
						'.$billable.$date.'<span class="right-space">'.$totalTime.' ('.$item->percent.'%)</span>
						<a href="#" class="base-icon-attach text-xs font-featured" onclick="files_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-files"> '.JText::_('MSG_VIEW_ATTACHMENTS').'</a>
						<a href="#" class="base-icon-comment-empty text-xs font-featured" onclick="comments_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-comments"> '.JText::_('MSG_VIEW_COMMENTS').$comments.'</a>
					</div>
				</li>
			';
		}
		$html .= '</ul>';
	else :
		if($noReg) $html = '<p class="base-icon-info-circled alert alert-info no-margin"> '.JText::_('MSG_LISTNOREG').'</p>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
