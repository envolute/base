<?php
defined('_JEXEC') or die;

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			T1.*,
			'. $db->quoteName('T2.name') .' name,
			'. $db->quoteName('T3.name') .' sport,
			'. $db->quoteName('T2.gender') .',
			'. $db->quoteName('T2.has_disease') .',
			'. $db->quoteName('T2.disease_desc') .',
			'. $db->quoteName('T2.has_allergy') .',
			'. $db->quoteName('T2.allergy_desc') .',
			'. $db->quoteName('T1.registry_date') .'
		FROM '. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_students') .' T2
			ON T2.id = T1.student_id
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_sports') .' T3
			ON T3.id = T1.sport_id
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
					<th class="text-center d-none d-lg-table-cell d-print-none" width="60">'.baseAppHelper::linkOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
					<th class="text-center d-print-none" width="120">'.JText::_('TEXT_ACTIONS').'</th>
				';
			endif;

			// VIEW
			$html .= '
				<form id="form-list-'.$APPTAG.'" method="post" class="pt-3">
					<table class="table table-striped table-hover table-sm">
						<thead>
							<tr>
								'.$adminView['head']['info'].'
								<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_STUDENT'), 'T2.name', $APPTAG).'</th>
								<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_SPORT'), 'T3.name', $APPTAG).'</th>
								<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_REGISTRY_DATE'), 'T1.registry_date', $APPTAG).'</th>
								<th class="text-center" width="80">'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_COUPON_FREE').'?', 'T1.coupon_free', $APPTAG).'</th>
			          			<th class="text-center" width="80">'.JText::_('FIELD_LABEL_PRICE').'</th>
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
			$adminView['list']['actions'] = '
				<td class="text-center d-none d-lg-table-cell d-print-none">
					<a href="#" class="hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span>
					</a>
				</td>
				<td class="text-center d-print-none">
					<button type="button" class="btn btn-xs btn-outline-primary base-icon-print hasTooltip" title="'.JText::_('TEXT_CARD').'" onclick="'.$APPTAG.'_printCard('.$item->id.')"></button>
					<a href="#" class="btn btn-xs btn-success hasTooltip" title="'.JText::_('TEXT_PAYMENT').'" onclick="studentsPayments_listReload(false, false, false, true, \'student_id\', '.$item->id.')" data-toggle="modal" data-target="#modal-list-studentsPayments"><span class="base-icon-dollar"></span></a>
					<a href="#" class="btn btn-xs btn-warning hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span></a>
					'.$btnDelete.'
				</td>
			';
		endif;

		$free = $item->coupon_free == 1 ? '<span class="base-icon-ok text-success"></span> ' : '';
		$gender = '<span '.($item->gender == 1 ? 'class="base-icon-male-symbol cursor-help text-primary hasTooltip" title="'.JText::_('TEXT_MALE').'"' : 'class="base-icon-female-symbol cursor-help text-danger hasTooltip" title="'.JText::_('TEXT_FEMALE').'"').'"></span> ';
		$note = !empty($item->note) ? ' <span class="base-icon-info-circled text-live cursor-help hasPopover" data-placement="top" title="<strong>'.JText::_('FIELD_LABEL_NOTE').'</strong>" data-content="<small class=\'font-featured\'>'.$item->note.'</small>"></span> ' : '';
		$info = '';
		if($item->has_disease == 1) :
			$info .= !empty($item->disease_desc) ? '<span class="base-icon-attention text-danger cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DISEASE').'"></span> <small class="font-featured text-danger hidden-xs">'.baseHelper::nameFormat($item->disease_desc).'</small> ' : '';
		endif;
		if($item->has_allergy == 1) :
			$info .= !empty($item->allergy_desc) ? '<span class="base-icon-attention text-live cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_ALLERGY').'"></span> <small class="font-featured text-live hidden-xs">'.baseHelper::nameFormat($item->allergy_desc).'</small>' : '';
		endif;
		$info = !empty($info) ? '<br />'.$info : '';
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
				<td>'.$gender.$note.baseHelper::nameFormat($item->name).$info.'</td>
				<td>'.baseHelper::nameFormat($item->sport).'</td>
				<td>'.baseHelper::dateFormat($item->registry_date).'</td>
				<td class="text-center">'.$free.'</td>
				<td class="text-center">'.baseHelper::priceFormat($item->price).'</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="9">
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
