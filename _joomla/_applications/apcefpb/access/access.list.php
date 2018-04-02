<?php
defined('_JEXEC') or die;

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			T1.*,
			'. $db->quoteName('T2.name') .' client
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
			ON T2.id = T1.client_id
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

if($num_rows) : // verifica se existe

	// pagination
	$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
	jimport('joomla.html.pagination');
	$found_rows = $db->loadResult();
	$pageNav = new JPagination($found_rows , $lim0, $lim );

	$clients_total = 0;
	$dependents_total = 0;
	$guests_total = 0;
	$exam_total = 0;
	$tax_total = 0;
	$tax_amount = 0;
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
				';
				$adminView['head']['actions'] = '
					<th class="text-center d-none d-lg-table-cell d-print-none" width="60">'.baseAppHelper::linkOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
					<th class="text-center d-print-none" width="90">'.JText::_('TEXT_ACTIONS').'</th>
				';
			endif;

			// VIEW
			$html = '
				<form id="form-list-'.$APPTAG.'" method="post" class="pt-3">
					<table class="table table-striped table-hover table-sm">
						<thead>
							<tr>
								'.$adminView['head']['info'].'
								<th>'.JText::_('FIELD_LABEL_DATE').'</th>
								<th>'.JText::_('FIELD_LABEL_CLIENT').'</th>
								<th>'.JText::_('TEXT_DEPENDENTS').'</th>
								<th>'.JText::_('TEXT_GUESTS').'</th>
								<th width="70">'.JText::_('TEXT_EXAMS').'</th>
								<th>'.JText::_('TEXT_TAX_S').'</th>
								<th>'.JText::_('TEXT_TOTAL_TAX').'</th>
								'.$adminView['head']['actions'].'
							</tr>
						</thead>
						<tbody>
			';
		}

		$query = '
			SELECT T1.*, T2.name rel
			FROM  '. $db->quoteName('#__'.$cfg['project'].'_dependents') .' T1
				JOIN '. $db->quoteName('#__'.$cfg['project'].'_dependents_groups') .' T2
				ON T2.id = T1.group_id
			WHERE
				'. $db->quoteName('T1.client_id') .' = '.$item->client_id .'
				AND T1.state = 1
		';
		$db->setQuery($query);
		$deps = $db->loadObjectList();
		$dep = array();
		foreach($deps as $d) {
			$dep[$d->id]['name'] = $d->name;
			$dep[$d->id]['rel'] = $d->rel;
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
			';
			$regInfo	= 'ID: <span class=&quot;text-live&quot;>#'.$item->id.'</span>';
			$regInfo	.= '<hr class=&quot;my-1&quot; />';
			$regInfo	.= JText::_('TEXT_CREATED_DATE').': '.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'<br />';
			$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->created_by)->name);
			if($item->alter_date != '0000-00-00 00:00:00') :
				$regInfo	.= '<hr class=&quot;my-1&quot; />';
				$regInfo	.= JText::_('TEXT_ALTER_DATE').': '.baseHelper::dateFormat($item->alter_date, 'd/m/Y H:i').'<br />';
				$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->alter_by)->name);
			endif;
			$adminView['list']['actions'] = '
				<td class="text-center d-none d-lg-table-cell d-print-none">
					<a href="#" class="hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span>
					</a>
				</td>
				<td class="text-center d-print-none">
					<a href="#" class="btn btn-xs btn-warning hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span></a>
					'.$btnDelete.'
					<a href="#" class="btn btn-xs btn-outline-primary base-icon-info-circled hasPopover" title="'.JText::_('TEXT_REGISTRATION_INFO').'" data-content="'.$regInfo.'" data-placement="top" data-trigger="click focus"></a>
				</td>
			';
		endif;

		$totalExams = 0;
		// clients
		if($item->client_id > 0 && !empty($item->client)) :
			if($item->presence == 1) :
				$clients_total++;
				if($item->cExam == 1) $totalExams++;
				$client = baseHelper::nameFormat($item->client);
			else :
				$client = '<span class="text-danger base-icon-cancel cursor-help hasTooltip" title="'.JText::_('MSG_NO_PRESENCE').'"> '.baseHelper::nameFormat($item->client).'</span>';
			endif;
		else :
			$text = !empty($item->client_desc) ? $item->client_desc : JText::_('TEXT_SELECT_ACCESS_FREE');
			$client = '<span class="text-live base-icon-thumbs-up-alt"> '.$text.'</span>'; //$item->client_desc;
		endif;
		// dependents
		$dependents = $dList = '';
		$deps = explode(';', $item->dependent);
		$totalDeps = 0; // soma o total geral de dependentes
		if(!empty($item->dependent) && $item->dependent != ';') :
			$exams = explode(';', $item->exam);
			for($i = 0; $i < count($deps); $i++) {
				if(!empty($deps[$i])) :
					$exam = '';
					if($exams[$i] == 1) :
						$totalExams++;
						$exam = '<span class="badge badge-danger align-text-top cursor-help hasTooltip" title="'.JText::_('TEXT_EXAM').'"><span class="base-icon-plus"></span></span> ';
					endif;
					$totalDeps++;
					$dList .= '<li>'.$exam.baseHelper::nameFormat($dep[$deps[$i]]['name']).'<br /><small class="text-muted">'.baseHelper::nameFormat($dep[$deps[$i]]['rel']).'</small></li>';
				endif;
			}
			$dependents_total += $totalDeps; // soma o total geral de dependentes
			$dCollapse = empty($dList) ? '' : ' <small>[ <a href="#'.$APPTAG.'-guestsView'.$item->id.'" data-toggle="collapse" href="#guestsView" role="button" aria-expanded="false" aria-controls="guestsView">'.JText::_('TEXT_SEE_ALL').'</a> ]</small>';
			$dependents .= '<strong>'.$totalDeps.'</strong> <small>[ <a href="#'.$APPTAG.'-depsView'.$item->id.'" data-toggle="collapse" href="#depsView" role="button" aria-expanded="false" aria-controls="depsView">'.JText::_('TEXT_SEE_ALL').'</a> ]</small>';
			if(!empty($dList)) :
				$dependents .= '
					<div id="'.$APPTAG.'-depsView'.$item->id.'" class="collapse">
						<ul class="text-sm set-list bordered b-top b-top-dashed b-live mt-2">
							'.$dList.'
						</ul>
					</div>
				';
			endif;
		else :
			$dependents .= JText::_('TEXT_NONE');
		endif;
		// guests
		$guests = $gList = '';
		$total_tax = $total_amount = 0;
		$gName = explode(';', $item->guestName);
		$totalGuests = 0;
		if(!empty($item->guestName) && $item->guestName != ';') :
			$gAge = explode(';', $item->guestAge);
			$gNote = explode(';', $item->guestNote);
			$gTax = explode(';', $item->guestTax);
			for($i = 0; $i < count($gName); $i++) {
				if(!empty($gName[$i])) :
					$tax = '';
					if($gTax[$i] == 1) :
						$tax = '<span class="badge badge-success align-text-top cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_TAX').'"><span class="base-icon-dollar"></span></span> ';
						$total_tax++;
						$totalExams++;
						$total_amount += $item->tax_price;
						$tax_total++; // soma o total geral de pagamentos de taxa
					endif;
					$totalGuests++;
				else :
					$tax = '';
				endif;
				$note = !empty($gNote[$i]) ? ' - <span class="text-live base-icon-info-circled"> '.$gNote[$i].'</span>' : '';
				$gList .= '<li>'.$tax.baseHelper::nameFormat($gName[$i]).'<div class="small text-muted">'.$gAge[$i].' '.JText::_('TEXT_YEARS').$note.'</div></li>';
			}
			$guests_total += $totalGuests; // soma o total geral de convidados
			$tax_amount += $total_amount; // soma o valor total acumulado de taxas
			$printTax = ($total_tax > 0) ? '<a href="#" class="btn btn-xs btn-outline-primary hasTooltip" title="'.JText::_('TEXT_PRINT').'" onclick="'.$APPTAG.'_printPayment('.$item->id.', true)"><span class="base-icon-print"></span></a>' : '';
			$gCollapse = empty($gList) ? '' : ' <small>[ <a href="#'.$APPTAG.'-guestsView'.$item->id.'" data-toggle="collapse" href="#guestsView" role="button" aria-expanded="false" aria-controls="guestsView">'.JText::_('TEXT_SEE_ALL').'</a> ]</small> '.$printTax;
			$guests .= '<strong>'.$totalGuests.'</strong>'.$gCollapse;
			if(!empty($gList)) :
				$guests .= '
					<div id="'.$APPTAG.'-guestsView'.$item->id.'" class="collapse">
						<ul class="text-sm set-list bordered b-top b-top-dashed b-live mt-2">
							'.$gList.'
						</ul>
					</div>
				';
			endif;
		else :
			$guests .= JText::_('TEXT_NONE');
		endif;
		$exam_total += $totalExams;
		$access_tax = ($item->tax_price > 0) ? $total_tax.' <span class="badge badge-warning align-text-top">'.baseHelper::priceFormat($item->tax_price).'</span>' : 0;

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
				<td>'.baseHelper::dateFormat($item->accessDate).'</td>
				<td>'.$client.'</td>
				<td>'.$dependents.'</td>
				<td>'.$guests.'</td>
				<td>'.$totalExams.'</td>
				<td>'.$access_tax.'</td>
				<td>'.baseHelper::priceFormat($total_amount).'</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

	$html .= '
		<tr class="table-warning b-top-2 b-live">
			<td class="py-3 text-center" colspan="2"><h2 class="m-0 text-live">'.($clients_total + $dependents_total + $guests_total).'</h1><small>Acessos</small></td>
			<td class="py-3"><h2 class="m-0">'.$clients_total.'</h1><small>'.JText::_('TEXT_CLIENTS').'</small></td>
			<td class="py-3"><h2 class="m-0">'.$dependents_total.'</h1><small>'.JText::_('TEXT_DEPENDENTS').'</small></td>
			<td class="py-3"><h2 class="m-0">'.$guests_total.'</h1><small>'.JText::_('TEXT_GUESTS').'</small></td>
			<td class="py-3"><h2 class="m-0">'.$exam_total.'</h1><small>'.JText::_('TEXT_EXAMS').'</small></td>
			<td class="py-3"><h2 class="m-0">'.$tax_total.'</h1><small>'.JText::_('TEXT_CHARGES').'</small></td>
			<td class="py-3" colspan="3"><h2 class="m-0"><small>R$</small> '.baseHelper::priceFormat($tax_amount).'</h1><small>'.JText::_('TEXT_ACCOUNT_FEES').'</small></td>
		</tr>
	';

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="10">
				<div class="alert alert-warning alert-icon m-0">'.JText::_('MSG_LISTNOREG').'</div>
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

	// PAGINATION
	require(JPATH_CORE.DS.'apps/layout/list/pagination.php');

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/formOrder.php');

	// PAGE LIMIT
	require(JPATH_CORE.DS.'apps/layout/list/pageLimit.php');

endif;

return $html;

?>
