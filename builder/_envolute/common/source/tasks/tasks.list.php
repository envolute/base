<?php
defined('_JEXEC') or die;

// LOAD FILTER
require($APPNAME.'.filter.php');

// LIST

	// pagination var's
	$limitDef = !isset($_SESSION[$APPTAG.'plim']) ? $cfg['pagLimit'] : $_SESSION[$APPTAG.'plim'];
	$_SESSION[$APPTAG.'plim']	= $app->input->post->get('list-lim-'.$APPTAG, $limitDef, 'int');
	$lim	= $app->input->get('limit', ($_SESSION[$APPTAG.'plim'] !== 1 ? $_SESSION[$APPTAG.'plim'] : 10000000), 'int');
	$lim0	= $app->input->get('limitstart', 0, 'int');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T1.type') .',
			'. $db->quoteName('T1.service_id') .',
			'. $db->quoteName('T1.project_id') .',
			'. $db->quoteName('T1.priority') .',
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
			'. $db->quoteName('T1.visible') .',
			'. $db->quoteName('T1.hour') .',
			'. $db->quoteName('T1.visible') .',
			'. $db->quoteName('T1.status') .',
			'. $db->quoteName('T1.status_desc') .',
			'. $db->quoteName('T1.ordering') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__envolute_services') .' T2
			ON T2.id = T1.service_id
			LEFT JOIN '. $db->quoteName('#__envolute_projects') .' T3
			ON T3.id = T1.project_id
			LEFT JOIN '. $db->quoteName('#__envolute_clients') .' T4
			ON T4.id = T3.client_id
		WHERE
			'.$where.$orderList;
	;
	try {

		$db->setQuery($query, $lim0, $lim);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

// ADMIN VIEW
$adminView = array();
$adminView['head']['info'] = $adminView['head']['actions'] = '';
if($hasAdmin) :
	$adminView['head']['info'] = '
		<th width="30" class="hidden-print"><input type="checkbox" id="'.$APPTAG.'_checkAll" /></th>
		<th width="50" class="hidden-print">'.$$SETOrder('#', 'T1.id', $APPTAG).'</th>
	';
	$adminView['head']['actions'] = '
		<th class="text-center hidden-print" width="60">'.$$SETOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
		<th class="text-center hidden-print" width="80">'.JText::_('TEXT_ACTIONS').'</th>
	';
endif;

// VIEW
$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					'.$adminView['head']['info'].'
					<th class="text-center" width="60">'.$$SETOrder(JText::_('FIELD_LABEL_STATUS'), 'T1.status', $APPTAG).'</th>
					<th>'.$$SETOrder('<span class="base-icon-attention"></span>', 'T1.priority', $APPTAG).' '.JText::_('FIELD_LABEL_TITLE').'</th>
					<th width="110">'.JText::_('TEXT_EXECUTED').' '.$$SETOrder('<span class="base-icon-arrows-cw"></span>', 'T1.period', $APPTAG).'</th>
					<th width="110"><span class="base-icon-calendar"></span> '.JText::_('FIELD_LABEL_TASK_PERIOD').'</th>
					<th class="text-center" width="120">'.$$SETOrder(JText::_('FIELD_LABEL_BILLABLE'), 'T1.billable', $APPTAG).'</th>
					'.$adminView['head']['actions'].'
				</tr>
			</thead>
			<tbody>
';

if($num_rows) : // verifica se existe

	// pagination
	$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
	jimport('joomla.html.pagination');
	$found_rows = $db->loadResult();
	$pageNav = new JPagination($found_rows , $lim0, $lim );

	foreach($res as $item) {

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$listFiles = '';
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					$listFiles .= '
						<a href="'.JURI::root(true).'/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
						</a>
					';
				endif;
			}
		endif;

		$adminView['list']['info'] = $adminView['list']['actions'] = '';
		if($hasAdmin) :
			$adminView['list']['info'] = '
				<td class="check-row hidden-print"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" /></td>
				<td class="hidden-print">'.$item->id.'</td>
			';
			$adminView['list']['actions'] = '
				<td class="text-center hidden-print">
					<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
					</a>
				</td>
				<td class="text-center hidden-print">
					<a href="#" class="btn btn-xs btn-success" onclick="tasksTimer_setParent('.$item->id.', true)" data-toggle="modal" data-target="#modal-tasksTimer" data-backdrop="static" data-keyboard="false"><span class="base-icon-clock hasTooltip" title="'.JText::_('MSG_INSERT_TASKTIMER').'"></span></a>
					<a href="#" class="btn btn-xs btn-default" onclick="tasksContacts_listReload(false, false, false, true, \'task_id\', '.$item->id.')" data-toggle="modal" data-target="#modal-list-tasksContacts"><span class="base-icon-users hasTooltip" title="'.JText::_('MSG_VIEW_CONTACTS').'"></span></a>
					<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</td>
			';
		endif;

		// get registered acitivity
		$totalTime = '00:00';
		$totalHours = 0;
		if($item->period == 0) :
			$query = '
			SELECT
				SEC_TO_TIME(SUM(TIME_TO_SEC('. $db->quoteName('total_time') .'))) totalTime,
				SUM('. $db->quoteName('hours') .') totalHours
			FROM '. $db->quoteName($cfg['mainTable'].'_timer') .' WHERE '. $db->quoteName('task_id') .' = '. $item->id;
			$db->setQuery($query);
			$time = $db->loadObject();
			$totalTime = !empty($time->totalTime) ? substr($time->totalTime, 0, 5) : '00:00';
			$totalHours = $time->totalHours;
		endif;

		// get todo list
		$query = '
		SELECT COUNT(*) FROM '. $db->quoteName('#__envolute_todoList') .' WHERE '. $db->quoteName('task_id') .' = '. $item->id;
		$db->setQuery($query);
		$toDoList = $db->loadResult();
		$toDoList = $toDoList ? ' ('.$toDoList.')' : '';

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
		$dtContent = !empty($item->status_desc) ? $item->status_desc : '';
		$status = '<a href="#" id="'.$APPTAG.'-item-'.$item->id.'-status" class="display-inline-block '.$sClass.' hasPopover" title="<strong>'.$stext.'</strong>" data-id="'.$item->id.'" data-status="'.$item->status.'" data-content="'.$dtContent.'" data-on="'.(($item->start_date == '0000-00-00') ? 0 : 1).'" onclick="'.$APPTAG.'_setStatusModal(this)"></a>';
		$desc = !empty($item->description) ? '<hr class=\'hr-xs\'><div class=\'text-xs text-muted\'>'.$item->description.'</div>' : '';
		$billable = ($item->billable == 1) ? '<span class="base-icon-dollar text-success cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_BILLABLE').'"></span>' : '<span class="base-icon-minus text-muted cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_NOT_BILLABLE').'"></span>';
		$visible = ($item->visible == 1) ? '<span class="base-icon-eye text-success cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_VISIBLE_DESC').'"></span>' : '<span class="base-icon-eye-off text-danger cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_VISIBLE_NOT_DESC').'"></span>';
		// tarefa ou template
		if($item->type == 1) :
			$order = '<span class="label label-warning pull-right hasTooltip" title="'.JText::_('MSG_ORDER').':<br />'.$item->service.'">'.$item->ordering.'</span>';
			$task = '<span class="base-icon-star text-live cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_TEMPLATE').'"></span> <span class="font-condensed cursor-help hasPopover" data-placement="top" data-content="<div class=\'small\'>'.baseHelper::nameFormat($item->service).htmlentities($desc).'</div>">'.baseHelper::nameFormat($item->title).'</span>'.$order;
		else :
			$order = '';
			$priority = ($item->priority == 1) ? '<span class="base-icon-attention text-live cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_PRIORITY').'"></span> ' : '';
			$task = $priority.'<span class="font-condensed cursor-help hasPopover" data-placement="top" data-content="<strong>'.JText::_('FIELD_LABEL_SERVICE').'</strong><div class=\'small\'>'.baseHelper::nameFormat($item->service).'</div><strong>'.JText::_('FIELD_LABEL_PROJECT').'</strong><div class=\'small\'>'.baseHelper::nameFormat($item->project).$desc.'</div>">'.baseHelper::nameFormat($item->title).'</span> ';
		endif;
		// preço fixo
		$taskPrice = ($item->price != '0.00') ? '<small class="text-muted font-featured cursor-help hasPopover" title="'.JText::_('FIELD_LABEL_PRICE_FIXED').'" data-content="'.JText::_('FIELD_LABEL_PRICE_FIXED_DESC').'">'.baseHelper::priceFormat($item->price, false, '<small>R$</small>').'</small>' : '';
		// Recurrent
		$days = $periodTip = '';
		if($item->recurrent_type == 2) : // semanal
			$week = explode(',', $item->weekly);
			for($i = 0; $i < count($week); $i++) {
				if(!empty($days)) $days .= ', ';
				$days .= JText::_('FIELD_LABEL_WEEKLY_DAY_'.$week[$i].'_ABBR');
			}
			$periodTip = JText::_('FIELD_LABEL_WEEKLY');
		elseif($item->recurrent_type == 3) : // mensal
			$days .= str_replace(',', ', ', $item->monthly);
			$periodTip = JText::_('FIELD_LABEL_MONTHLY');
		elseif($item->recurrent_type == 4) : // anual
			$days .= str_replace(',', ', ', $item->yearly);
			$periodTip = JText::_('FIELD_LABEL_YEARLY');
		endif;
		// Period
		$date = '';
		if($item->period == 0) :
			$date = '<span class="cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_START_DATE').'">'.baseHelper::dateFormat($item->start_date, 'd/m/Y', true, '-').'</span>';
		else :
			if($item->recurrent_type == 1) :
				$date = '<small class="text-muted font-featured">'.JText::_('FIELD_LABEL_DAYLY').'</small>';
			else :
				$date = (!empty($item->weekly) ? '<small class="text-muted font-featured">'.$days.'</small>' : '');
				$date = (!empty($item->monthly) ? '<small class="text-muted font-featured">'.$days.'</small>' : $date);
				$date = (!empty($item->yearly) ? '<small class="text-muted font-featured">'.$days.'</small>' : $date);
			endif;
			$date = '<span class="base-icon-arrows-cw text-success cursor-help hasTooltip" title="'.$periodTip.'"> '.$date.'</span> ';
		endif;
		$date .= '<br /><span class="base-icon-right-big small font-featured text-live cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DEADLINE').'"> '.($item->deadline != '0000-00-00' ? baseHelper::dateFormat($item->deadline, 'd/m/Y', true, '-') : '<strong>&infin;</strong>').'</span>';
		if($item->status > 2) :
			$eDt = explode(' ', $item->end_date);
			$end_date = '<span class="cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_END_DATE').'">'.baseHelper::dateFormat($eDt[0], 'd/m/Y').'</span> <span class="base-icon-clock hasTooltip" title="'.$eDt[1].'"></span>';
			$date .= '<br /><span class="base-icon-right-big small font-featured text-live"> '.$end_date.'</span>';
			$date .= '<div class="text-xs font-featured">TODO: dias Atraso</div>';
		endif;
		$rowState = $item->state == 0 ? 'danger' : ($rowState = $item->priority == 1 ? 'warning' : '');
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				'.$adminView['list']['info'].'
				<td class="text-center">'.$status.'</td>
				<td>
					'.$task.'
					<div class="small text-muted font-featured">
						<a href="#" class="base-icon-menu right-space-xs" onclick="todoList_listReload(false, false, false, true, \'task_id\', '.$item->id.')" data-toggle="modal" data-target="#modal-list-todoList"> '.JText::_('MSG_VIEW_TODOLIST').$toDoList.'</a>
						<a href="#" class="base-icon-comment-empty" onclick="comments_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-comments"> '.JText::_('MSG_VIEW_COMMENTS').$comments.'</a>
					</div>
				</td>
				<td>
					'.($item->period == 0 ? $totalTime.' <small class="text-muted font-featured">('.$item->percent.'%)</small>' : ' <small class="base-icon-arrows-cw text-success font-featured"> '.JText::_('FIELD_LABEL_RECURRENT').'</small>').'
					'.(!empty($item->estimate) ? '<br /><small class="text-muted font-featured cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_ESTIMATE').'">Estim. '.$item->estimate.'</small>' : '').'
				</td>
				<td>'.$date.'</td>
				<td class="text-center">'.$billable.'<br />'.$taskPrice.'</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="13">
				<div class="alert alert-warning alert-icon no-margin">'.JText::_('MSG_LISTNOREG').'</div>
			</td>
		</tr>
	';

endif;

$html .= '
			</tbody>
		</table>
	</form>
';

if($num_rows) :

	// PAGINAÇÃO
		// stats
		$listStart	= $lim0 + 1;
		$listEnd		= $lim0 + $num_rows;
	if($found_rows != $num_rows) :
		$html .= '
			<div class="base-app-pagination pull-left">
				'.$pageNav->getListFooter().'
				<div class="list-stats small text-muted">
					'.JText::sprintf('LIST_STATS', $listStart, $listEnd, $found_rows).'
				</div>
			</div>
		';
	endif;

	$html .= '
		<form id="form-order-'.$APPTAG.'" action="'.$_SERVER['REQUEST_URI'].'" class="pull-right form-inline" method="post">
			<input type="hidden" name="'.$APPTAG.'oF" id="'.$APPTAG.'oF" value="'.$_SESSION[$APPTAG.'oF'].'" />
			<input type="hidden" name="'.$APPTAG.'oT" id="'.$APPTAG.'oT" value="'.$_SESSION[$APPTAG.'oT'].'" />
		</form>
	';

	// ITENS POR PÁGINA
	// seta o parametro 'start = 0' na URL sempre que o limit for refeito
	// isso evita erro quando estiver navegando em páginas subjacentes
	$a = preg_replace("#\?start=.*#", '', $_SERVER['REQUEST_URI']);
	$a = preg_replace("#&start=.*#", '', $a);

	$html .= '
		<form id="form-limit-'.$APPTAG.'" action="'.$a.'" class="pull-right form-inline hidden-print" method="post">
			<label>'.JText::_('LIST_PAGINATION_LIMIT').'</label>
			<select name="list-lim-'.$APPTAG.'" onchange="'.$APPTAG.'_setListLimit()">
				<option value="5" '.($_SESSION[$APPTAG.'plim'] === 5 ? 'selected' : '').'>5</option>
				<option value="20" '.($_SESSION[$APPTAG.'plim'] === 20 ? 'selected' : '').'>20</option>
				<option value="50" '.($_SESSION[$APPTAG.'plim'] === 50 ? 'selected' : '').'>50</option>
				<option value="100" '.($_SESSION[$APPTAG.'plim'] === 100 ? 'selected' : '').'>100</option>
				<option value="1" '.($_SESSION[$APPTAG.'plim'] === 1 ? 'selected' : '').'>Todos</option>
			</select>
		</form>
	';

endif;

return $htmlFilter.$html;

?>
