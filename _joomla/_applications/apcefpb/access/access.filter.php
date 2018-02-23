<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', ($cfg['canEdit'] ? 2 : 1), 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// CLIENTS -> select
	$fClient	= $app->input->get('fClient', 0, 'int');
	if($fClient != 0) $where .= ' AND '.$db->quoteName('T1.client_id').' = '.$fClient;
	// USERS -> select
	$fUser	= $app->input->get('fUser', 0, 'int');
	if($fUser != 0) $where .= ' AND ('.$db->quoteName('T1.created_by').' = '.$fUser.' OR '.$db->quoteName('T1.alter_by').' = '.$fUser.')';
	// DATE
	// força a data sempre, pois a listagem deve ser referente a um período
	$dateMin	= $app->input->get('dateMin', date('Y-m-d'), 'string');
	$dateMax	= $app->input->get('dateMax', date('Y-m-d'), 'string');
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.accessDate').' BETWEEN '.$db->quote($dateMin.' 00:00:00').' AND '.$db->quote($dateMax.' 23:59:59');

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.dependent'			=> 'TEXT_DEPENDENT',
		'T1.guestName'			=> 'TEXT_GUEST'
	);
	$i = 0;
	foreach($searchFields as $key => $value) {
		$_OR = ($i > 0) ? ' OR ' : '';
		$sQuery .= $_OR.'LOWER('.$db->quoteName($key).') LIKE LOWER("%'.$search.'%")';
		if(!empty($value)) $sLabel[] .= JText::_($value);
		$i++;
	}
	if(!empty($search)) $where .= ' AND ('.$sQuery.')';

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T2.name'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
		$_SESSION[$APPTAG.'oF'] = 'T1.accessDate';
		$_SESSION[$APPTAG.'oT'] = 'DESC';
	endif;
	if(!empty($ordf)) :
		$_SESSION[$APPTAG.'oF'] = $ordf;
		$_SESSION[$APPTAG.'oT'] = $ordt;
	endif;
	$orderList = !empty($_SESSION[$APPTAG.'oF']) ? $db->quoteName($_SESSION[$APPTAG.'oF']).' '.$_SESSION[$APPTAG.'oT'] : '';
	// fixa a ordenação em caso de itens com o mesmo valor (ex: mesma data)
	$orderList .= (!empty($orderList) && !empty($orderDef) ? ', ' : '').$orderDef;
	$orderList .= (!empty($orderList) ? ', ' : '').$db->quoteName('T1.id').' DESC';
	// set order by
	$orderList = !empty($orderList) ? ' ORDER BY '.$orderList : '';

	$SETOrder = $APPTAG.'setOrder';

// ACTION

	$btnAction = $cfg['listFull'] ? 'type="submit"' : 'type="button" onclick="'.$APPTAG.'_listReload(false);"';

// FILTER'S DINAMIC FIELDS

	// Clients -> select
	$flt_client = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();
	foreach ($clients as $obj) {
		$flt_client .= '<option value="'.$obj->id.'"'.($obj->id == $fClient ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

	// usuários -> portaria / ambulatório
	$flt_users = '';
	$query = '
		SELECT T1.*
		FROM '. $db->quoteName('#__users') .' T1
			JOIN '. $db->quoteName('#__user_usergroup_map') .' T2
			ON T2.user_id = T1.id
		WHERE T2.group_id IN ('.$group_portaria.', '.$group_ambulatorio.')
		ORDER BY name
	';
	$db->setQuery($query);
	$users = $db->loadObjectList();
	foreach ($users as $obj) {
		$flt_users .= '<option value="'.$obj->id.'"'.($obj->id == $fUser ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

// VISIBILITY
// Elementos visíveis apenas quando uma consulta é realizada

	$hasFilter = $app->input->get($APPTAG.'_filter', 0, 'int');
	// Estado inicial dos elementos
	$btnClearFilter		= ''; // botão de resetar
	$textResults		= ''; // Texto informativo
	// Filtro ativo
	if($hasFilter || $cfg['ajaxFilter']) :
		$btnClearFilter = '
			<a href="'.JURI::current().'" class="btn btn-sm btn-danger base-icon-cancel-circled btn-icon">
				'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'
			</a>
		';
		$textResults = '<span class="base-icon-down-big text-muted d-none d-sm-inline"> '.JText::_('TEXT_SEARCH_RESULTS').'</span>';
	endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-8 b-right b-right-dashed">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_DATE').'</label>
								<span class="input-group input-group-sm">
									<span class="input-group-addon strong">'.JText::_('TEXT_FROM').'</span>
									<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control field-date" data-width="100%" data-convert="true" />
									<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
									<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control field-date" data-width="100%" data-convert="true" />
								</span>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_CLIENT').'</label>
								<select name="fClient" id="fClient" class="form-control form-control-sm set-filter">
									<option value="0">- '.JText::_('TEXT_SELECT').' -</option>
									'.$flt_client.'
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="label-xs text-muted text-truncate">'.implode(', ', $sLabel).'</label>
								<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm" />
							</div>
						</div>
						<div class="col-sm-6 col-md-3 col-lg-2">
							<div class="form-group">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ITEM_STATE').'</label>
								<select name="active" id="active" class="form-control form-control-sm set-filter">
									<option value="2">- '.JText::_('TEXT_ALL').' -</option>
									<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
									<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('TEXT_CLERK').'</label>
						<select name="fUser" id="fUser" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_SELECT').' -</option>
							'.$flt_users.'
						</select>
					</div>
				</div>
			</div>
			<div id="base-app-filter-buttons" class="row pt-3 b-top align-items-center">
				<div class="col-sm">
					<div class="form-group">
						'.$textResults.'
					</div>
				</div>
				<div class="col-sm text-right">
					<div class="form-group">
						<button '.$btnAction.' id="'.$APPTAG.'-submit-filter" class="btn btn-sm btn-primary base-icon-search btn-icon">
							'.JText::_('TEXT_SEARCH').'
						</button>
						'.$btnClearFilter.'
					</div>
				</div>
			</div>
		</fieldset>
	</form>
';

?>
