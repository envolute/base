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
			'. $db->quoteName('T1.main') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.zip_code') .',
			'. $db->quoteName('T1.address') .',
			'. $db->quoteName('T1.address_number') .',
			'. $db->quoteName('T1.address_info') .',
			'. $db->quoteName('T1.address_district') .',
			'. $db->quoteName('T1.address_city') .',
			'. $db->quoteName('T1.address_state') .',
			'. $db->quoteName('T1.address_country') .',
			'. $db->quoteName('T1.latitude') .',
			'. $db->quoteName('T1.longitude') .',
			'. $db->quoteName('T1.url_map') .',
			'. $db->quoteName('T1.state') .',
			'. $db->quoteName('T3.name') .' client,
			'. $db->quoteName('T5.name') .' contact,
			CONCAT('. $db->quoteName('T3.name') .', '. $db->quoteName('T5.name') .') owner
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName('#__zenite_rel_clients_addresses') .' T2
			ON T2.address_id = T1.id
			LEFT JOIN '. $db->quoteName('#__zenite_clients') .' T3
			ON T3.id = T2.client_id
			LEFT JOIN '. $db->quoteName('#__zenite_rel_contacts_addresses') .' T4
			ON T4.address_id = T1.id
			LEFT JOIN '. $db->quoteName('#__zenite_contacts') .' T5
			ON T5.id = T4.contact_id
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

// VIEW
$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					<th width="30" class="hidden-print"><input type="checkbox" id="'.$APPTAG.'_checkAll" /></th>
					<th width="50" class="hidden-print">'.$$SETOrder('#', 'T1.id', $APPTAG).'</th>
					<th>'.JText::_('FIELD_LABEL_PHONE_OWNER').'</th>
					<th>'.JText::_('FIELD_LABEL_ADDRESS').'</th>
					<th>'.JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE').'</th>
					<th>'.$$SETOrder(JText::_('FIELD_LABEL_ADDRESS_DISTRICT'), 'T1.address_district', $APPTAG).'</th>
					<th>'.$$SETOrder(JText::_('FIELD_LABEL_ADDRESS_CITY'), 'T1.address_city', $APPTAG).'</th>
					<th>'.$$SETOrder(JText::_('FIELD_LABEL_ADDRESS_STATE'), 'T1.address_state', $APPTAG).'</th>
					<th>'.$$SETOrder(JText::_('FIELD_LABEL_ADDRESS_COUNTRY'), 'T1.address_country', $APPTAG).'</th>
					<th class="text-center hidden-print" width="60">'.$$SETOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
					<th class="text-center hidden-print" width="70">'.JText::_('TEXT_ACTIONS').'</th>
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

		$onwer = !empty($item->client) ? '<span class="base-icon-empire text-live cursor-help hasTooltip" title="'.JText::_('TEXT_MAIN_CLIENT').'"></span> '.baseHelper::nameFormat($item->client) : '-';
		$onwer = !empty($item->contact) ? '<span class="base-icon-user text-live cursor-help hasTooltip" title="'.JText::_('TEXT_MAIN_CONTACT').'"></span> '.baseHelper::nameFormat($item->contact) : $onwer;
		$main = $item->main == 1 ? '<span class="base-icon-star text-live cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_MAIN').'"></span> ' : '<strong>'.baseHelper::nameFormat($item->description).'</strong> - ';
		$info = !empty($item->address_info) ? ', '.$item->address_info : '';
		$mapa = !empty($item->url_map) ? ' <a href="'.$item->url_map.'" class="base-icon-location set-modal hasTooltip" data-modal-title="'.JText::_('TEXT_LOCATION').'" data-modal-iframe="true" data-modal-width="95%" data-modal-height="95%" title="'.JText::_('TEXT_IN_MAP').'"></a>' : '';
		$rowState = $item->state == 0 ? 'danger' : '';
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				<td class="check-row hidden-print"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" /></td>
				<td class="hidden-print">'.$item->id.'</td>
				<td>'.$onwer.'</td>
				<td>'.$main.baseHelper::nameFormat($item->address).', '.$item->address_number.$info.$mapa.'</td>
				<td>'.$item->zip_code.'</td>
				<td>'.baseHelper::nameFormat($item->address_district).'</td>
				<td>'.baseHelper::nameFormat($item->address_city).'</td>
				<td>'.baseHelper::nameFormat($item->address_state).'</td>
				<td>'.baseHelper::nameFormat($item->address_country).'</td>
				<td class="text-center">
					<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
					</a>
				</td>
				<td class="text-center">
					<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</td>
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="11">
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
