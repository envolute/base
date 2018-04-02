<?php
defined('_JEXEC') or die;

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			T1.*,
			'. $db->quoteName('T2.subject') .' task,
			'. $db->quoteName('T2.state') .' isOpened,
			'. $db->quoteName('T3.name') .',
			'. $db->quoteName('T3.nickname') .',
			'. $db->quoteName('T3.price_hour') .',
			'. $db->quoteName('T4.id') .' project_id,
			'. $db->quoteName('T4.name') .' project,
			'. $db->quoteName('T5.name') .' client
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_tasks') .' T2
			ON T2.id = T1.task_id
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_staff') .' T3
			ON T3.user_id = T1.user_id
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_projects') .' T4
			ON T4.id = T2.project_id
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T5
			ON T5.id = T4.client_id
		WHERE
			'.$where.$orderList
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

if($num_rows) { // verifica se existe

	// pagination
	$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
	jimport('joomla.html.pagination');
	$found_rows = $db->loadResult();
	$pageNav = new JPagination($found_rows , $lim0, $lim );

	$times = array();
	$sumHours = 0;
	function sumTime($times) {
	    $minutes = 0;
	    foreach ($times as $time) {
	        list($hour, $minute) = explode(':', $time);
	        $minutes += $hour * 60;
	        $minutes += $minute;
	    }
	    $hours = floor($minutes / 60);
	    $minutes -= $hours * 60;
	    // returns the time already formatted
	    return sprintf('%02d:%02d', $hours, $minutes);
	}

	$listCount = 0;
	foreach($res as $item) {

		// define permissões de execução
		$canEdit	= ($cfg['canEdit'] || $item->created_by == $user->id);
		$canDelete	= ($cfg['canDelete'] || $item->created_by == $user->id);
		$listCount++;

		if($listCount == 1) {

			// ADMIN VIEW
			$adminView = array();
			$adminView['head']['info'] = $adminView['head']['actions'] = '';
			if($canEdit) :
				$adminView['head']['info'] = '
					<th width="30" class="d-print-none"><input type="checkbox" class="input-checkAll" onchange="'.$APPTAG.'_setBtnStatus()" /></th>
					<th width="50" class="d-none d-lg-table-cell d-print-none">'.baseAppHelper::linkOrder('#', 'T1.id', $APPTAG).'</th>
				';
				$adminView['head']['actions'] = '
					<th class="text-center d-print-none" width="70">'.JText::_('TEXT_ACTIONS').'</th>
				';
			endif;

			// VIEW
			$html = '
				<form id="form-list-'.$APPTAG.'" method="post" class="pt-3">
					<table class="table table-striped table-hover table-sm">
						<thead>
							<tr>
								'.$adminView['head']['info'].'
								<th>'.JText::_('FIELD_LABEL_USER').'</th>
								<th width="90">'.JText::_('FIELD_LABEL_TIME').'</th>
								<th width="120">'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_DATE'), 'T1.date', $APPTAG).'</th>
								<th>'.JText::_('FIELD_LABEL_TASK').'</th>
								<th width="120" class="d-none d-lg-table-cell">'.JText::_('TEXT_CREATED_DATE').'</th>
								'.$adminView['head']['actions'].'
							</tr>
						</thead>
						<tbody>
			';
		}

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$listFiles = '';
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					$listFiles .= '
						<a href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
						</a>
					';
				endif;
			}
		endif;

		$adminView['list']['info'] = $adminView['list']['actions'] = '';
		$btnDelete = $canDelete ? '<a href="#" class="btn btn-xs btn-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash"></span></a>' : '';
		if($canEdit) :
			$adminView['list']['info'] = '
				<td class="check-row d-print-none"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="checkAll-child" value="'.$item->id.'" onchange="'.$APPTAG.'_setBtnStatus()" /></td>
				<td class="d-none d-lg-table-cell d-print-none">'.$item->id.'</td>
			';
			$btnEdit = ($item->isOpened || $hasAdmin) ? '<a href="#" class="btn btn-xs btn-warning hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span></a>' : '';
			$adminView['list']['actions'] = '
				<td class="text-center d-print-none">
					'.$btnEdit.' '.$btnDelete.'
				</td>
			';
		endif;

		$info = !empty($item->project) ? '<span class="text-live cursor-help hasTooltip" title="'.JText::_('TEXT_PROJECT').'">'.baseHelper::nameFormat($item->project).'</span>' : '';
		$info .= !empty($item->client) ? ' [ <span class="cursor-help hasTooltip" title="'.JText::_('TEXT_CLIENT').'">'.baseHelper::nameFormat($item->client).'</span> ]' : '';
		if(!empty($info)) $info = '<div class="small text-muted">'.$info.'</div>';
		$name = !empty($item->nickname) ? $item->nickname : $item->name;

		$setTip = $tipMsg = '';
		if($item->start_hour != '00:00:00' && $item->end_hour == '00:00:00' && $item->time == '00:00:00') {
			$timeInfo = '
				<div class="text-danger lh-1"><span class="text-sm base-icon-arrows-cw"></span> '.JText::_('TEXT_RUNNING').'</div>
				<div class="small text-live ml-1"><span class="base-icon-level-down cursor-help hasTooltip" title="'.JText::_('TEXT_STARTED_HOUR').'"> '.substr($item->start_hour, 0, 5).' '.JText::_('TEXT_HOURS_ABBR').'</span></div>
			';
		} else {
			$time		= substr($item->total_time, 0, 5);	// tempo total do registro
			$times[]	= $time;							// Guarda o tempo para o somatório no fim da listagem
			$sumHours	+= $item->hours;					// Tempo em formato numérico
			if($item->start_hour != '00:00:00') {
				$tipMsg = JText::_('TEXT_PERIOD').'<br />'.substr($item->start_hour, 0, 5).' - '.substr($item->end_hour, 0, 5);
			} else {
				$tipMsg = JText::_('FIELD_LABEL_TOTAL_TIME');
			}
			$timeInfo = '
				<div class="text-live lh-1 cursor-help hasTooltip" title="'.$tipMsg.'"><span class="text-sm base-icon-clock"></span> '.$time.'</div>
				<div class="small text-primary ml-1"><span class="base-icon-level-down cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_TIME_COUNT').'"> '.$item->hours.'</span></div>
			';
		}

		$rowState = $item->state == 0 ? 'table-danger' : '';
		$regInfo	= JText::_('TEXT_CREATED_DATE').': '.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'<br />';
		$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->created_by)->name);
		if($item->alter_date != '0000-00-00 00:00:00') :
			$regInfo	.= '<hr class=&quot;my-2&quot; />';
			$regInfo	.= JText::_('TEXT_ALTER_DATE').': '.baseHelper::dateFormat($item->alter_date, 'd/m/Y H:i').'<br />';
			$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->alter_by)->name);
		endif;

		// Resultados
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				'.$adminView['list']['info'].'
				<td>'.baseHelper::nameFormat($name).'</td>
				<td>'.$timeInfo.'</td>
				<td>'.baseHelper::dateFormat($item->date, 'd/m/Y').'</td>
				<td>#'.$item->task_id.' - '.baseHelper::nameFormat($item->task).$info.'</td>
				<td class="d-none d-lg-table-cell">
					'.baseHelper::dateFormat($item->created_date, 'd/m/Y').'
					<a href="#" class="base-icon-info-circled hasPopover" title="'.JText::_('TEXT_REGISTRATION_INFO').'" data-content="'.$regInfo.'" data-placement="top"></a>
				</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

	if(count($times)) {
		$workValue = '';
		if($fAssign != 0) {
			if($item->price_hour > 0) {
				$workValue = '
					<div class="float-left text-muted">
						<div>R$ '.baseHelper::priceFormat($item->price_hour).'</div>
						<div class="small">'.JText::_('TEXT_HOUR_WORKED_VALUE').'</div>
					</div>
				';
				if($sumHours > 0) {
					$workValue .= '
						<div class="float-left px-4 text-muted base-icon-right-big"></div>
						<div class="float-left">
							<div class="text-primary">R$ '.baseHelper::priceFormat(($item->price_hour * $sumHours)).'</div>
							<div class="small text-muted">'.JText::_('TEXT_TOTAL_WORKED_VALUE').'</div>
						</div>
					';
				}
			}
		}
		$period = '';
		if(!empty($dateMin) && !empty($dateMax)) {
			$period = '
				<div class="text-sm font-weight-normal">
					<span class="badge badge-primary"> '.baseHelper::dateFormat($dateMin).'</span><br />
					<span class="badge badge-danger"> '.baseHelper::dateFormat($dateMax).'</span>
				</div>
			';
		}
		$html .= '
			<tr class="table-warning b-top-2 b-gray-500 text-lg">
				<th colspan="3" class="text-uppercase py-3 font-weight-normal">'.JText::_('TEXT_TOTAL').'</th>
				<th class="py-3 font-weight-normal">
					<div class="text-live"><span class="base-icon-clock"></span> '.sumTime($times).'</div>
					<div class="small text-primary ml-1"><span class="base-icon-level-down cursor-help hasTooltip" title="'.JText::_('TEXT_TOTAL_TIME_COUNT').'"> '.str_replace(',', '.', $sumHours).'</span></div>
				</th>
				<th class="py-3 font-weight-normal">'.$period.'</th>
				<th colspan="3" class="py-3 text-right font-weight-normal">'.$workValue.$btnGetFile.'</th>
			</tr>
		';
	}

} else { // num_rows = 0

	$html .= '
		<tr>
			<td colspan="8">
				<div class="alert alert-warning alert-icon m-0">'.JText::_('MSG_LISTNOREG').'</div>
			</td>
		</tr>
	';

}

$html .= '
			</tbody>
		</table>
	</form>
';

if($num_rows) :

	// PAGINATION
	require(JPATH_CORE.DS.'apps/layout/list/pagination.php');

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/formOrder.php');

	// PAGE LIMIT
	require(JPATH_CORE.DS.'apps/layout/list/pageLimit.php');

endif;

return $html;

?>
