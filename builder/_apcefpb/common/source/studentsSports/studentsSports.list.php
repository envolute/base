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
			'. $db->quoteName('T2.name') .' name,
			'. $db->quoteName('T3.name') .' sport,
			'. $db->quoteName('T2.gender') .',
			'. $db->quoteName('T2.has_disease') .',
			'. $db->quoteName('T2.disease_desc') .',
			'. $db->quoteName('T2.has_allergy') .',
			'. $db->quoteName('T2.allergy_desc') .',
			'. $db->quoteName('T1.registry_date') .',
			'. $db->quoteName('T1.note') .',
			'. $db->quoteName('T1.coupon_free') .',
			'. $db->quoteName('T1.price') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__apcefpb_students') .' T2
			ON T2.id = T1.student_id
			JOIN '. $db->quoteName('#__apcefpb_sports') .' T3
			ON T3.id = T1.sport_id
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
					<th>'.$$SETOrder(JText::_('FIELD_LABEL_STUDENT'), 'T2.name', $APPTAG).'</th>
					<th>'.$$SETOrder(JText::_('FIELD_LABEL_SPORT'), 'T3.name', $APPTAG).'</th>
					<th>'.$$SETOrder(JText::_('FIELD_LABEL_REGISTRY_DATE'), 'T1.registry_date', $APPTAG).'</th>
					<th class="text-center" width="80">'.$$SETOrder(JText::_('FIELD_LABEL_COUPON_FREE').'?', 'T1.coupon_free', $APPTAG).'</th>
          <th class="text-center" width="80">'.JText::_('FIELD_LABEL_PRICE').'</th>
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
					<a href="#" class="base-icon-dollar btn btn-xs btn-'.($item->coupon_free ? 'info' : 'success').' hasTooltip" title="'.JText::_(($item->coupon_free ? 'MSG_COUPON_FREE' : 'MSG_INSERT_PAYMENT')).'" onclick="studentsPayments_setParent('.$item->id.')" data-toggle="modal" data-target="#modal-studentsPayments" data-backdrop="static" data-keyboard="false"></a>
					<a href="#" class="base-icon-print btn btn-xs btn-warning hasTooltip" title="Imprimir Carteira" onclick="'.$APPTAG.'_printCard('.$item->id.', 0)"></a>
					<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</td>
			';
		endif;

		$free = $item->coupon_free == 1 ? '<span class="base-icon-ok text-success"></span> ' : '';
		$gender = '<span '.($item->gender == 1 ? 'class="base-icon-male-symbol cursor-help text-primary hasTooltip" title="'.JText::_('FIELD_LABEL_GENDER_MALE').'"' : 'class="base-icon-female-symbol cursor-help text-danger hasTooltip" title="'.JText::_('FIELD_LABEL_GENDER_FEMALE').'"').'"></span> ';
		$note = !empty($item->note) ? ' <span class="base-icon-info-circled text-live cursor-help hasPopover" data-placement="top" title="<strong>'.JText::_('FIELD_LABEL_NOTE').'</strong>" data-content="<small class=\'font-featured\'>'.$item->note.'</small>"></span> ' : '';
		$info = '';
		if($item->has_disease == 1) :
			$info .= !empty($item->disease_desc) ? '<span class="base-icon-attention text-danger cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DISEASE').'"></span> <small class="font-featured text-danger hidden-xs">'.baseHelper::nameFormat($item->disease_desc).'</small> ' : '';
		endif;
		if($item->has_allergy == 1) :
			$info .= !empty($item->allergy_desc) ? '<span class="base-icon-attention text-live cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_ALLERGY').'"></span> <small class="font-featured text-live hidden-xs">'.baseHelper::nameFormat($item->allergy_desc).'</small>' : '';
		endif;
		$info = !empty($info) ? '<br />'.$info : '';
		$rowState = $item->state == 0 ? 'danger' : '';
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
