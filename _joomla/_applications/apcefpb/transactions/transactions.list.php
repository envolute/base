<?php
defined('_JEXEC') or die;

// LOAD FILTER
require($PATH_APP_FILE.'.filter.php');

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			T1.*,
			'. $db->quoteName('T2.name') .' provider,
			'. $db->quoteName('T3.name') .' client,
			'. $db->quoteName('T3.user_id') .',
			'. $db->quoteName('T4.name') .' dependent,
			'. $db->quoteName('T5.due_date') .' invoiceDate,
			IF(`T5`.`custom_desc` <> "", `T5`.`custom_desc`, `T5`.`description`) invoice_desc
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__base_providers') .' T2
			ON T2.id = T1.provider_id
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T3
			ON T3.id = T1.client_id
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_dependents') .' T4
			ON T4.id = T1.dependent_id
			LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_invoices') .' T5
			ON T5.id = T1.invoice_id
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
		<th width="30" class="d-print-none"><input type="checkbox" id="'.$APPTAG.'_checkAll" /></th>
		<th width="50" class="d-none d-lg-table-cell d-print-none">'.baseAppHelper::linkOrder('#', 'T1.id', $APPTAG).'</th>
	';
	$adminView['head']['actions'] = '
		<th class="text-center d-none d-lg-table-cell d-print-none" width="60">'.baseAppHelper::linkOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
		<th class="text-center d-print-none" width="70">'.JText::_('TEXT_ACTIONS').'</th>
	';
endif;

// VIEW
$recurr		= ($isFixed == 1) ? true : false;
$showInv 	= (!$recurr) ? '<th class="d-none d-md-table-cell">'.JText::_('FIELD_LABEL_INVOICE').'</th>' : '';
$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<table class="table table-striped table-hover table-sm">
			<thead>
				<tr>
					'.$adminView['head']['info'].'
					<th>'.JText::_('FIELD_LABEL_CLIENT').'</th>
					<th class="d-none d-md-table-cell">'.JText::_('FIELD_LABEL_PROVIDER').'</th>
					<th class="d-none d-md-table-cell">'.JText::_('FIELD_LABEL_DESCRIPTION').'</th>
					<th>'.JText::_('FIELD_LABEL_DATE').'</th>
					<th>'.JText::_('FIELD_LABEL_PRICE').'</th>
					'.$showInv.'
					<th width="120" class="d-none d-lg-table-cell">'.JText::_('TEXT_CREATED_DATE').'</th>
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

	$tIDS = array();
	$total  = 0;
	foreach($res as $item) {

		// MOSTRA APENAS A PRÓXIMA PARCELA EM ABERTO
		if(!in_array($item->transaction_id, $tIDS) || $fInst == 1) :

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
			if($hasAdmin) :
				$adminView['list']['info'] = '
					<td class="check-row d-print-none">
						<input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" />
						<input type="hidden" name="'.$APPTAG.'_parent_id[]" value="'.$item->parent_id.'" />
						<input type="hidden" name="'.$APPTAG.'_provider_id[]" value="'.$item->provider_id.'" />
						<input type="hidden" name="'.$APPTAG.'_client_id[]" value="'.$item->client_id.'" />
						<input type="hidden" name="'.$APPTAG.'_dependent_id[]" value="'.$item->dependent_id.'" />
						<input type="hidden" name="'.$APPTAG.'_description[]" value="'.$item->description.'" />
						<input type="hidden" name="'.$APPTAG.'_fixed[]" value="'.$item->fixed.'" />
						<input type="hidden" name="'.$APPTAG.'_date[]" value="'.$item->date.'" />
						<input type="hidden" name="'.$APPTAG.'_price[]" value="'.$item->price.'" />
					</td>
					<td class="d-none d-lg-table-cell d-print-none">'.$item->id.'</td>
				';
				$adminView['list']['actions'] = '
					<td class="text-center d-none d-lg-table-cell d-print-none">
						<a href="#" class="hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
							<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span>
						</a>
					</td>
					<td class="text-center d-print-none">
						<a href="#" class="btn btn-xs btn-warning hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span></a>
						<a href="#" class="btn btn-xs btn-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash"></span></a>
					</td>
				';
			endif;

			$urlToInvoice = JURI::root().'apps/clients/invoices/details?invID='.$item->invoice_id.($item->user_id != $user->id ? '&uID='.$item->user_id : '');
			$urlToPhoneInvoice = JURI::root().'apps/clients/phonesinvoices/details?invID='.$item->phoneInvoice_id.'&pID='.$item->phone_id.($item->user_id != $user->id ? '&uID='.$item->user_id : '');
			$desc = !empty($item->phoneInvoice_id) ? '<a href="'.$urlToPhoneInvoice.'" class="new-window" target="_blank">'.$item->description.'</a>' : $item->description;
			$info = !empty($desc) ? $desc : '';
			$info .= !empty($item->doc_number) ? '<div class="text-xs text-muted">Item: '.$item->doc_number.'</div>' : '';
			$dependent = !empty($item->dependent) ? '<div class="small text-muted">&raquo; <span class="cursor-help hasTooltip" title="'.JText::_('TEXT_TRANSACTION_BY').'">'.baseHelper::nameFormat($item->dependent).'</span></div>' : '';
			$invoice = !empty($item->invoice_id) ? '<a href="'.$urlToInvoice.'" class="new-window" target="_blank">'.$item->invoice_desc.'</a><div class="small text-live">'.baseHelper::dateFormat($item->invoiceDate).'</div>' : '';
			if(!$recurr) :
				$invoice = '<td class="d-none d-lg-table-cell">'.$invoice.'</td>';
			endif;
			$isCard = $item->isCard == 1 ? '<span class="badge badge-warning text-uppercase cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_IS_CARD').'">'.JText::_('FIELD_LABEL_CARD').'</span>' : '';
			$note = !empty($item->note) ? '<div class="small text-muted font-featured"><span class="base-icon-info-circled text-live cursor-help hasTooltip" title="Observação"></span> '.$item->note.'</div>' : '';
			$total = $total + $item->price;
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
					<td>'.baseHelper::nameFormat($item->client).$dependent.'</td>
	  				<td class="d-none d-lg-table-cell">'.baseHelper::nameFormat($item->provider).'</td>
	  				<td class="d-none d-lg-table-cell">'.$info.'</td>
	  				<td>'.baseHelper::dateFormat($item->date_installment).'</td>
	  				<td>
						'.baseHelper::priceFormat($item->price, false, 'R$ ').'
						<div class="small text-muted">
							('.$item->installment.'/'.$item->total.') '.$isCard.'
						</div>
					</td>
					'.$invoice.'
					<td class="d-none d-lg-table-cell">
						'.baseHelper::dateFormat($item->created_date, 'd/m/Y').'
						<a href="#" class="base-icon-info-circled setPopover" title="'.JText::_('TEXT_REGISTRATION_INFO').'" data-content="'.$regInfo.'" data-placement="top"></a>
					</td>
					'.$adminView['list']['actions'].'
				</tr>
			';

			// Se for uma parcela,
			if(!empty($item->transaction_id)) $tIDS[] = $item->transaction_id;

		endif;

	}

	// TOTAL
	$html .= '
		<tr class="warning">
			<td colspan="6"><strong>TOTAL</strong></td>
			<td colspan="7"><strong>'.baseHelper::priceFormat($total, false, 'R$ ').'</strong></td>
		</tr>
	';

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="13">
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

return $htmlFilter.$html;

?>
