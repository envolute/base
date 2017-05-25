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
			'. $db->quoteName('T1.task_id') .',
			'. $db->quoteName('T2.title') .' task,
			'. $db->quoteName('T3.name') .' user,
			'. $db->quoteName('T4.name') .' service,
			'. $db->quoteName('T5.id') .' project_id,
			'. $db->quoteName('T5.name') .' project,
			'. $db->quoteName('T6.name') .' client,
			'. $db->quoteName('T7.due_date') .',
			'. $db->quoteName('T1.date') .',
			'. $db->quoteName('T1.start_hour') .',
			'. $db->quoteName('T1.end_hour') .',
			'. $db->quoteName('T1.time') .',
			'. $db->quoteName('T1.total_time') .',
			'. $db->quoteName('T1.hours') .',
			'. $db->quoteName('T1.price_hour') .',
			'. $db->quoteName('T1.price') .',
			'. $db->quoteName('T2.price') .' taskPrice,
			'. $db->quoteName('T1.billable') .',
			'. $db->quoteName('T1.billed') .',
			'. $db->quoteName('T1.billed_date') .',
			'. $db->quoteName('T1.note') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__envolute_tasks') .' T2
			ON T2.id = T1.task_id
			LEFT JOIN '. $db->quoteName('#__users') .' T3
			ON T3.id = T1.user_id
			JOIN '. $db->quoteName('#__envolute_services') .' T4
			ON T4.id = T2.service_id
			LEFT JOIN '. $db->quoteName('#__envolute_projects') .' T5
			ON T5.id = T2.project_id
			LEFT JOIN '. $db->quoteName('#__envolute_clients') .' T6
			ON T6.id = T5.client_id
			LEFT JOIN '. $db->quoteName('#__envolute_invoices') .' T7
			ON T7.id = T1.invoice_id
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
					<th>'.JText::_('FIELD_LABEL_TASK').'</th>
					<th width="120"><span class="base-icon-calendar"></span> '.$$SETOrder(JText::_('FIELD_LABEL_DATE'), 'T1.date', $APPTAG).'</th>
					<th width="70">'.$$SETOrder(JText::_('FIELD_LABEL_TIME'), 'T1.total_time', $APPTAG).'</th>
					<th width="70">R$/hs</th>
					<th width="70">R$ '.JText::_('TEXT_TOTAL').'</th>
					<th class="text-center" width="85">'.$$SETOrder(JText::_('FIELD_LABEL_BILLED'), 'T1.billed', $APPTAG).'</th>
					'.$adminView['head']['actions'].'
				</tr>
			</thead>
			<tbody>
';

$total = 0.00;
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
				<td class="check-row">
					<input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" />
					<input type="hidden" name="'.$APPTAG.'_task_project_id[]" class="'.$APPTAG.'-task-'.$item->id.'-project-id" value="'.$item->project_id.'" />
				</td>
				<td class="hidden-print">'.$item->id.'</td>
			';
			$adminView['list']['actions'] = '
				<td class="text-center hidden-print">
					<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
					</a>
				</td>
				<td class="text-center hidden-print">
					<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</td>
			';
		endif;

		$task = '<span class="cursor-help hasPopover" data-placement="top" data-content="<strong>'.JText::_('FIELD_LABEL_SERVICE').'</strong><div class=\'small\'>'.baseHelper::nameFormat($item->service).'</div><strong>'.JText::_('FIELD_LABEL_PROJECT').'</strong><div class=\'small\'>'.baseHelper::nameFormat($item->project).'</div>"><strong>#'.$item->task_id.'</strong> - <span class="font-condensed">'.baseHelper::nameFormat($item->task).'</span></span>';
		$billed = ($item->billed == 1) ? '<span class="base-icon-ok text-success cursor-help hasTooltip" title="'.baseHelper::dateFormat($item->billed_date, 'd/m/Y H:i:s').'"></span>' : '<span class="base-icon-cancel text-danger"></span>';
		// PRICE
		// fixo, definido na tarefa
		$price = baseHelper::priceFormat($item->price);
		$priceHour = baseHelper::priceFormat($item->price_hour);
		if($item->billable == 0) :
			$price = $priceHour = '<span class="text-muted font-featured cursor-help hasTooltip" style="text-decoration:line-through;" title="'.JText::_('MSG_TASK_PRICE_FIXED').' R$'.baseHelper::priceFormat($item->taskPrice).'">'.$price.'</span>';
		else :
			$total = $total + $item->price;
		endif;
		$taskUser = !empty($item->user) ? ' <span class="base-icon-user text-live cursor-help hasTooltip" title="'.JText::_('TEXT_REGISTERED_BY').'<br />'.baseHelper::nameFormat($item->user).'"></span>' : '';
		$rowState = $item->state == 0 ? 'danger' : '';
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				'.$adminView['list']['info'].'
				<td>'.$task.'</td>
				<td>'.baseHelper::dateFormat($item->date, 'd/m/Y').$taskUser.'</td>
				<td><span class="cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_TIME_COUNT').'<br />'.$item->hours.' hr">'.substr($item->total_time, 0, 5).'</span></td>
				<td>'.$priceHour.'</td>
				<td class="warning strong">'.$price.'</td>
				<td class="text-center">'.$billed.'</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="12">
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
