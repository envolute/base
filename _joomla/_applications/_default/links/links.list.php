<?php
defined('_JEXEC') or die;

// LOAD FILTER
require($PATH_APP_FILE.'.filter.php');

// LIST

	// pagination var's
	$limitDef = !isset($_SESSION[$APPTAG.'plim']) ? $cfg['pagLimit'] : $_SESSION[$APPTAG.'plim'];
	$_SESSION[$APPTAG.'plim']	= $app->input->post->get('list-lim-'.$APPTAG, $limitDef, 'int');
	$lim	= $app->input->get('limit', ($_SESSION[$APPTAG.'plim'] !== 1 ? $_SESSION[$APPTAG.'plim'] : 10000000), 'int');
	$lim0	= $app->input->get('limitstart', 0, 'int');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T2.name') .' category,
			'. $db->quoteName('T1.type') .' type,
			'. $db->quoteName('T1.url') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.paid') .',
			'. $db->quoteName('T1.period') .',
			'. $db->quoteName('T1.due_date') .',
			'. $db->quoteName('T1.currency') .',
			'. $db->quoteName('T1.price') .',
			'. $db->quoteName('T1.start_date') .',
			'. $db->quoteName('T1.user') .',
			'. $db->quoteName('T1.password') .',
			'. $db->quoteName('T1.note') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_categories') .' T2
			ON T2.id = T1.category_id
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
		<th width="50" class="hidden-print">'.baseAppHelper::linkOrder('#', 'T1.id', $APPTAG).'</th>
	';
	$adminView['head']['actions'] = '
		<th class="text-center hidden-print" width="60">'.baseAppHelper::linkOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
		<th class="text-center hidden-print" width="100">'.JText::_('TEXT_ACTIONS').'</th>
	';
endif;

// VIEW
$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<table class="table table-striped table-hover table-sm">
			<thead>
				<tr>
					'.$adminView['head']['info'].'
					<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_TYPE'), 'T1.type', $APPTAG).'</th>
					<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_URL'), 'T1.url', $APPTAG).'</th>
					<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_CATEGORY'), 'T2.name', $APPTAG).'</th>
					<th width="120" class="text-center">'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_PAID'), 'T1.paid', $APPTAG).'</th>
					<th width="80" class="text-center">'.JText::_('FIELD_LABEL_ACCESS').'</th>
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
					<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</td>
			';
		endif;

		switch ($item->period) {
			case '1':
				$period = JText::_('FIELD_LABEL_MONTH');
				break;
			case '2':
				$period = JText::_('FIELD_LABEL_QUARTERLY');
				break;
			case '3':
				$period = JText::_('FIELD_LABEL_SEMESTER');
				break;
			case '4':
				$period = JText::_('FIELD_LABEL_YEARLY');
				break;
			default:
				$period = '-';
		}
		$url = '<br /><a href="'.$item->url.'" class="small font-featured text-muted" target="_blank">'.$item->url.'</a><small class="base-icon-link-ext text-live left-space-xs"></small>';
		switch ($item->currency) {
			case 'USD':
				$currency = '&euro; ';
				break;
			case 'EUR':
				$currency = 'U$ ';
				break;
			default:
				$currency = 'R$ ';
		}
		$paid = '';
		if($item->paid == 1) :
			$paid = (!empty($item->price) && $item->price != '0.00') ? '<strong>'.JText::_('FIELD_LABEL_PRICE').'</strong>: '.baseHelper::priceFormat($item->price, false, $currency, false) : '';
			$paid .= !empty($item->due_date) ? '<br /><strong>'.JText::_('FIELD_LABEL_DUE_DATE').'</strong>: '.$item->due_date : '';
			$paid .= $item->start_date != '0000-00-00' ? '<br /><strong>'.JText::_('FIELD_LABEL_START_DATE').'</strong>: '.baseHelper::dateFormat($item->start_date, 'd/m/Y') : '';
			$paid = '<span class="base-icon-ok text-success cursor-help hasPopover" title="<strong>'.$period.'</strong>" data-content="<small>'.$paid.'</small>"></span>';
		endif;
		$access = '<span class="base-icon-lock-open text-success cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_NO_PASSWORD').'"></span>';
		if(!empty($item->user) || !empty($item->password)) :
			$access = !empty($item->user) ? '<strong>'.JText::_('TEXT_USER').'</strong>: '.$item->user : '';
			$access .= !empty($item->password) ? '<br /><strong>'.JText::_('FIELD_LABEL_PASSWORD').'</strong>: '.$item->password : '';
			$access = '<span class="base-icon-info-circled text-live cursor-pointer" data-toggle="popover" data-content="<small>'.$access.'</small>"></span>';
		endif;
		$note = !empty($item->note) ? '<span class="base-icon-info-circled text-live cursor-help hasPopover" data-content="<small>'.$item->note.'</small>"></span> ' : '';
		$rowState = $item->state == 0 ? 'table-danger' : '';
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				'.$adminView['list']['info'].'
				<td>'.($item->type == 0 ? JText::_('FIELD_LABEL_TYPE_LINK') : JText::_('FIELD_LABEL_TYPE_SERVICE')).'</td>
				<td>'.$note.$item->description.$url.'</td>
				<td>'.baseHelper::nameFormat($item->category).'</td>
				<td class="text-center">'.$paid.'</td>
				<td class="text-center">'.$access.'</td>
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
