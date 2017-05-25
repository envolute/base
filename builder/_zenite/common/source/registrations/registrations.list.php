<?php
defined('_JEXEC') or die;

// LOAD FILTER
require($APPNAME.'.filter.php');

if(!isset($pID) || empty($pID) || $pID == 0) :

	return $htmlFilter.'<h4 class="alert alert-info no-margin"><span class="base-icon-up-big"></span> '.JText::_('TEXT_SELECT_PROJECT').'</h4>';

else :

	// LIST

	// pagination var's
	$limitDef = !isset($_SESSION[$APPTAG.'plim']) ? $cfg['pagLimit'] : $_SESSION[$APPTAG.'plim'];
	$_SESSION[$APPTAG.'plim']	= $app->input->post->get('list-lim-'.$APPTAG, $limitDef, 'int');
	$lim	= $app->input->get('limit', ($_SESSION[$APPTAG.'plim'] !== 1 ? $_SESSION[$APPTAG.'plim'] : 10000000), 'int');
	$lim0	= $app->input->get('limitstart', 0, 'int');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T6.name') .',
			'. $db->quoteName('T7.cpf') .',
			'. $db->quoteName('T7.birthday') .',
			'. $db->quoteName('T7.gender') .',
			'. $db->quoteName('T2.name') .' project,
			'. $db->quoteName('T2.date') .' projectDate,
			'. $db->quoteName('T4.name') .' category,
			'. $db->quoteName('T5.name') .' disability,
			'. $db->quoteName('T3.distance') .',
			'. $db->quoteName('T3.distance_unit') .',
			'. $db->quoteName('T1.price') .',
			'. $db->quoteName('T1.sizeShirt') .',
			'. $db->quoteName('T1.discount') .',
			'. $db->quoteName('T1.due_date') .',
			IF('.$db->quoteName('T1.due_date').' < CURDATE(), 1, 0) vencido,
			WEEKDAY(CURDATE()) weekDay,
			'. $db->quoteName('T1.url_boleto') .',
			'. $db->quoteName('T1.status') .',
			'. $db->quoteName('T1.created_date') .' date,
			'. $db->quoteName('T1.alter_date') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__zenite_projects') .' T2
			ON T2.id = T1.project_id
			JOIN '. $db->quoteName('#__zenite_projects_types') .' T3
			ON T3.id = T1.projectType_id
			JOIN '. $db->quoteName('#__zenite_projects_categories') .' T4
			ON T4.id = T3.category_id
			LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T5
			ON T5.id = T3.disability_id
			JOIN '. $db->quoteName('#__users') .' T6
			ON T6.id = T1.user_id
			JOIN '. $db->quoteName('#__zenite_user_info') .' T7
			ON T7.user_id = T6.id
		WHERE
			'.$where.$orderList;
	;
	$qwe = $query;
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
			<th class="text-center hidden-print" width="60">'.$$SETOrder('Pagamento', 'T1.status', $APPTAG).'</th>
			<th class="text-center hidden-print" width="60">'.$$SETOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
			<th class="text-center hidden-print" width="70">'.JText::_('TEXT_ACTIONS').'</th>
		';
	endif;

	// VIEW
	$html = '
		<form id="form-list-'.$APPTAG.'" method="post">
			<table class="table table-striped table-hover table-condensed">
				<thead>
					<tr>
						'.$adminView['head']['info'].'
						<th>'.$$SETOrder(JText::_('FIELD_LABEL_USER'), 'T6.name', $APPTAG).'</th>
						<th>'.$$SETOrder(JText::_('FIELD_LABEL_PROJECT'), 'T1.project_id', $APPTAG).'</th>
						<th>'.$$SETOrder(JText::_('FIELD_LABEL_PRICE'), 'T1.price', $APPTAG).'</th>
						<th>'.JText::_('FIELD_LABEL_TOTAL').'</th>
						<th>'.$$SETOrder(JText::_('FIELD_LABEL_DATE'), 'T1.created_date', $APPTAG).'</th>
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
		$pageNav = new JPagination($found_rows, $lim0, $lim );

		// verifica o status através de webservice
		require_once(JPATH_BASE."/templates/base/source/_f2b/status/WSBillingStatus.php");

		$totalSum = $totalPaid = (float)0.00;
		$counter = $totalSubs = $totalSubsConf = 0;
		foreach($res as $item) {

			$counter++;
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
						$filePath = JPATH_BASE.'/images/uploads/'.$APPNAME.'/'.$files[$item->id][$i]->filename;
					endif;
				}
			endif;

			// verifica o status caso ainda não tenha sido registrado o pagamento
			$status = '';
			if($item->status < 2 && $item->state == 1 && $item->price > 0.00) :
				JLoader::register('registrations', JPATH_BASE.'/templates/base/source/registrations/registrations.class.php');
				$status = registrations::getStatus($item->id, $item->status, $cfg);
			elseif($item->status == 2) :
				$status = 'Paga';
			endif;

			$adminView['list']['info'] = $adminView['list']['actions'] = '';
			if($hasAdmin) :
				$adminView['list']['info'] = '
					<td class="check-row hidden-print"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" /></td>
				';
				$adminView['list']['actions'] = '
					<td class="text-center hidden-print">
						<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
							<span class="'.(($item->state == 1 && !($item->vencido == 1 && $status != 'Paga')) ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
						</a>
					</td>
					<td class="text-center hidden-print">
						<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
						<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
					</td>
				';
			endif;

			$st = '<span class="base-icon-eye-off text-live cursor-help hasTooltip" title="'.JText::_('MSG_STATUS_NOT_VIEWED').'"></span>';
			if(!empty($status)) :
				if($status == 'Registrada') :
					$st = '<span class="base-icon-eye text-success cursor-help hasTooltip" title="'.JText::_('MSG_STATUS_VIEWED').'"></span>';
				elseif($status == 'Paga') :
					$st = '<span class="base-icon-ok text-success cursor-help hasTooltip" title="'.JText::_('MSG_STATUS_PAID').'"></span>';
				else :
					$st = $status;
				endif;
			else :
				if($item->status == 1) :
					$st = '<span class="base-icon-eye text-success cursor-help hasTooltip" title="'.JText::_('MSG_STATUS_VIEWED').'"></span>';
				elseif($item->status == 2) :
					$st = '<span class="base-icon-ok text-success cursor-help hasTooltip" title="'.JText::_('MSG_STATUS_PAID').'"></span>';
				endif;
			endif;
			$textPrice = ($item->status < 2 && $status != 'Paga') ? 'text-muted' : 'text-success';

			$distance_unit = ($item->distance_unit == 0 ? ' m' : ' Km');
			if($item->max_age == 0) :
				$faixa = JText::_('TEXT_AS_FROM').' '.$item->min_age.' '.JText::_('TEXT_YEARS');
			else :
				$faixa = $item->min_age.' '.JText::_('TEXT_TO').' '.$item->max_age.' '.JText::_('TEXT_YEARS');
			endif;
			$disability = !empty($item->disability) ? ' ('.baseHelper::nameFormat($item->disability).')' : '';
			$discount = ($item->discount != '0.00') ? '<br /><small class="text-muted font-featured cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DISCOUNT').'">R$ - '.baseHelper::priceFormat($item->discount).'</small>' : '';
			$total = $item->price - $item->discount;
			$sizeShirt = !empty($item->sizeShirt) ? ' - Camisa ('.$item->sizeShirt.')' : '';

			// verifica se 'ainda não foi pago' e 'já passou a data de vencimento'
			$closed = 0;
			$error = '';
			if(($item->status < 2 && $status != 'Paga') && $item->vencido == 1 && $item->weekDay < 5 && $total > 0.00) :
				// atualiza o valor
				$query = 'UPDATE '. $db->quoteName($cfg['mainTable']) .' SET state = 0 WHERE id = '.$item->id;
				try {
			    $db->setQuery($query);
					$db->execute();
					$closed = 1;
				}
				catch (RuntimeException $e) {
			    $error = $e->getMessage();
				}
			endif;

			$rowState = ($item->state == 0 || $closed == 1) ? 'danger' : '';
			$html .= '
				<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
					'.$adminView['list']['info'].'
					<td>'.$counter.'</td>
					<td><span class="cursor-help hasTooltip" title="CPF: '.$item->cpf.'">'.baseHelper::nameFormat($item->name).'</span><br /><small class="text-muted font-featured"><span class="cursor-help hasTooltip" title="Data de Nascimento">'.baseHelper::dateFormat($item->birthday).'</span> - <span class="'.($item->gender == 'M' ? 'text-info' : 'text-live').' cursor-help hasTooltip" title="Sexo">'.($item->gender == 'M' ? 'Masculino' : 'Feminino').'</span></small></td>
					<td>
						'.baseHelper::nameFormat($item->project).'<br />
						<small class="text-muted font-featured">'.baseHelper::nameFormat($item->category).' '.$item->distance.$distance_unit.$disability.$sizeShirt.'</small>
					</td>
					<td>R$ '.baseHelper::priceFormat($item->price).$discount.'</td>
					<td class="'.$textPrice.'">R$ '.baseHelper::priceFormat($total).'</td>
					<td>'.baseHelper::dateFormat($item->date).'<br /><small class="font-featured text-live cursor-help hasTooltip" title="Vencimento"><span class="base-icon-attention"></span> '.baseHelper::dateFormat($item->due_date).'</small></td>
					<td class="text-center hidden-print">'.$st.'</td>
					'.$adminView['list']['actions'].'
				</tr>
			';
			if($item->state == 1 && $closed == 0) :
				$totalSum += $total;
				$totalSubs++;
			endif;
			if($item->status == 2 || $status == 'Paga') :
				$totalPaid += $total;
				$totalSubsConf++;
			endif;
		}

		$html .= '
			</tbody>
			<tfoot>
				<tr class="warning">
					<td colspan="4"></td>
					<td class="strong">Arrecadação:</td>
					<td colspan="5"><strong>R$ '.baseHelper::priceFormat($totalPaid).'</strong> <span class="text-muted text-sm cursor-help hasTooltip" title="Arrecadação Total Ativa">('.baseHelper::priceFormat($totalSum).')</span></td>
				</tr>
			</tfoot>
		';

	else : // num_rows = 0

		$html .= '
			<tr>
				<td colspan="10">
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

endif;

?>
