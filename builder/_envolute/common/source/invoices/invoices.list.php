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
			'. $db->quoteName('T1.project_id') .',
			'. $db->quoteName('T2.name') .' project,
			'. $db->quoteName('T3.name') .' client,
			'. $db->quoteName('T1.due_date') .',
			'. $db->quoteName('T1.month') .',
			'. $db->quoteName('T1.year') .',
			'. $db->quoteName('T1.price') .',
			'. $db->quoteName('T1.create_boleto') .',
			'. $db->quoteName('T1.hosting') .',
			'. $db->quoteName('T1.discount') .',
			'. $db->quoteName('T1.discount_note') .',
			'. $db->quoteName('T1.tax') .',
			'. $db->quoteName('T1.tax_note') .',
			'. $db->quoteName('T1.assessment') .',
			'. $db->quoteName('T1.assessment_note') .',
			'. $db->quoteName('T1.sent') .',
			'. $db->quoteName('T1.sent_date') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.paid') .',
			'. $db->quoteName('T1.paid_date') .',
			'. $db->quoteName('T1.note') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__envolute_projects').' T2
			ON T2.id = T1.project_id
			LEFT JOIN '. $db->quoteName('#__envolute_clients').' T3
			ON T3.id = T2.client_id
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
		<th class="text-center hidden-print" width="120">'.JText::_('TEXT_ACTIONS').'</th>
	';
endif;

// VIEW
$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					'.$adminView['head']['info'].'
					<th width="20">&nbsp;</th>
					<th>'.$$SETOrder(JText::_('FIELD_LABEL_PROJECT'), 'T2.name', $APPTAG).'</th>
					<th width="120">'.$$SETOrder(JText::_('FIELD_LABEL_DUE_DATE'), 'T1.due_date', $APPTAG).'</th>
					<th width="70">'.JText::_('FIELD_LABEL_DISCOUNT').'</th>
					<th width="70">'.JText::_('FIELD_LABEL_TAX').'</th>
					<th width="70">'.JText::_('FIELD_LABEL_ASSESSMENT').'</th>
					<th>'.JText::_('TEXT_SERVICE').'s</th>
					<th class="text-center" width="60">Host</th>
					<th class="text-center" width="60">'.$$SETOrder(JText::_('FIELD_LABEL_BOLETO'), 'T1.create_boleto', $APPTAG).'</th>
					<th class="text-center" width="60">'.$$SETOrder(JText::_('TEXT_SENT'), 'T1.sent', $APPTAG).'</th>
					<th class="text-center" width="60">'.$$SETOrder(JText::_('TEXT_PAID'), 'T1.paid', $APPTAG).'</th>
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

		$btnReminder = '';
		if($item->sent == 1) :
			$btnReminder = '<a href="#" id="'.$APPTAG.'-reminder-'.$item->id.'" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_sendInvoice('.$item->id.', '.$item->sent.', 1)"><span class="base-icon-history hasTooltip" title="'.JText::_('TEXT_SEND_REMINDER').'"></span></a>';
		endif;

		if($item->paid == 1) :
			$statusPaid = '<span class="base-icon-ok text-success cursor-help hasTooltip" title="'.JText::_('TEXT_PAID_DATE').'<br />'.baseHelper::dateFormat($item->sent_date, 'd/m/Y').'"></span>';
			$btnActions = '';
		else :
			$statusPaid = '
			<a href="#" onclick="'.$APPTAG.'_setPay('.$item->id.')" id="'.$APPTAG.'-paid-'.$item->id.'" data-toggle="modal" data-target="#modal-'.$APPTAG.'-pay" data-backdrop="static" data-keyboard="false">
				<span class="base-icon-cancel text-danger hasTooltip" title="'.JText::_('MSG_SET_PAID_ITEM').'"></span>
			</a>
			';
			$btnActions = $btnReminder.'
				<a href="#" id="'.$APPTAG.'-send-'.$item->id.'" class="btn btn-xs btn-'.($item->sent ? 'danger' : 'success').'" onclick="'.$APPTAG.'_sendInvoice('.$item->id.', '.$item->sent.', 0)"><span class="base-icon-paper-plane hasTooltip" title="'.JText::_($item->sent ? 'TEXT_RESEND_INVOICE' : 'TEXT_SEND_INVOICE').'"></span></a>
				<a href="#" id="'.$APPTAG.'-edit-'.$item->id.'" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
			';
		endif;

		// cobrança de hospedagem
    $hosting = '<span class="base-icon-cancel text-danger"></span>';
    $hostPrice = (float)0.00;
    if($item->hosting == 1) :
      $query = '
        SELECT
          SUM(IF('.$db->quoteName('T1.price').' != '.$db->quote('0.00').', '.$db->quoteName('T1.price').', '.$db->quoteName('T2.price').')) priceHost
        FROM
          '. $db->quoteName('#__envolute_hosts') .' T1
          JOIN '. $db->quoteName('#__envolute_hosts_plans') .' T2
          ON T2.id = T1.plan_id
        WHERE
          T1.project_id = '.$item->project_id.' AND T1.state = 1';
      ;
      $db->setQuery($query);
      $hostPrice = $db->loadResult();
      $hosting = '<span class="base-icon-ok text-success cursor-help hasTooltip" title="R$ '.baseHelper::priceFormat($hostPrice).'"></span>';
    endif;

		$discount = '<span'.(!empty($item->discount_note) ? 'class="cursor-help hasTooltip" title="'.$item->discount_note.'"' : '').'>'.baseHelper::priceFormat($item->discount, false, '', true, '<span class="text-muted">R$ 0,00</span>').'</span>';
		$tax = '<span'.(!empty($item->tax_note) ? 'class="cursor-help hasTooltip" title="'.$item->tax_note.'"' : '').'>'.baseHelper::priceFormat($item->tax, false, '', true, '<span class="text-muted">R$ 0,00</span>').'</span>';
		$assessment = '<span'.(!empty($item->assessment_note) ? 'class="cursor-help hasTooltip" title="'.$item->assessment_note.'"' : '').'>'.baseHelper::priceFormat($item->assessment, false, '', true, '<span class="text-muted">R$ 0,00</span>').'</span>';

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
					'.$btnActions.'
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</td>
			';
		endif;

		$note = !empty($item->note) ? ' <span class="base-icon-info-circled text-live cursor-help hasPopover" data-content="'.$item->note.'"></span>' : '';
		$rowState = $item->state == 0 ? 'danger' : '';
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				'.$adminView['list']['info'].'
				<td class="text-center"><a href="invoice?id='.htmlentities(urlencode(base64_encode($item->id))).'" class="base-icon-doc-text hasTooltip" title="'.JText::_('TEXT_VIEW_INVOICE').'" target="_blank"></a></td>
				<td>
					<span class="cursor-help hasPopover" data-content="'.$item->description.'" data-placement="top">'.baseHelper::nameFormat($item->project).'</span>'.$note.'
					<div class="small text-muted font-featured cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_CLIENT').'">'.baseHelper::nameFormat($item->client).'</div>
				</td>
				<td>
					'.baseHelper::dateFormat($item->due_date).'
					<div class="small text-muted font-featured">'.baseHelper::getMonthName($item->month).' de '.$item->year.'</div>
				</td>
				<td>'.$discount.'</td>
				<td>'.$tax.'</td>
				<td>'.$assessment.'</td>
				<td>'.baseHelper::priceFormat(($item->price + $hostPrice), false, 'R$ ', true, '<span class="text-muted">R$ 0,00</span>').'</td>
				<td class="text-center">'.$hosting.'</td>
				<td class="text-center"><span class="'.($item->create_boleto ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span></td>
				<td class="text-center">'.($item->sent ? '<span class="base-icon-ok text-success cursor-help hasTooltip" title="'.JText::_('TEXT_SENT_DATE').'<br />'.baseHelper::dateFormat($item->sent_date, 'd/m/Y H:i:s').'"></span>' : '<span id="'.$APPTAG.'-sent-'.$item->id.'" class="base-icon-cancel text-danger"></span>').'</td>
				<td class="text-center">
					'.$statusPaid.'
				</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="15">
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
