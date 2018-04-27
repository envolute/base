<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', ($cfg['canEdit'] ? 2 : 1), 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// MAIN -> checkbox
	$fMain	= $app->input->get('fMain', 0, 'int');
	if($fMain == 1) $where .= ' AND '.$db->quoteName('T1.main').' = 1';
	// CONTACT -> select
	$fContact	= $app->input->get('cID', 0, 'int');
	if($fContact > 0) $where .= ' AND '.$db->quoteName('T1.contact_id').' = '.$fContact;
	// PROVIDER
	$providerID	= $app->input->get('pID', 0, 'int');
	if($providerID > 0) $where .= ' AND '.$db->quoteName('T1.provider_id').' = '.$providerID;

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.department'				=> 'FIELD_LABEL_DEPARTMENT'
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
		$_SESSION[$APPTAG.'oF'] = 'T3.name';
		$_SESSION[$APPTAG.'oT'] = 'ASC';
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

	// providers -> select
	$flt_provider = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_providers') .' ORDER BY name';
	$db->setQuery($query);
	$providers = $db->loadObjectList();
	foreach ($providers as $obj) {
		$flt_provider .= '<option value="'.$obj->id.'"'.($obj->id == $providerID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// contacts -> select
	$flt_contact = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_contacts') .' ORDER BY name';
	$db->setQuery($query);
	$contacts = $db->loadObjectList();
	foreach ($contacts as $obj) {
		$flt_contact .= '<option value="'.$obj->id.'"'.($obj->id == $fContact ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
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
				<div class="col-sm-6 col-md-4">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PROVIDER').'</label>
						<select name="pID" id="pID" class="form-control form-control-sm set-filter">
							<option value="">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_provider.'
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-4">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_CONTACT').'</label>
						<select name="cID" id="cID" class="form-control form-control-sm set-filter">
							<option value="">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_contact.'
						</select>
					</div>
				</div>
				<div class="col-sm-3 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_MAIN').'</label>
						<select name="fMain" id="fMain" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($fMain == 1 ? ' selected' : '').'>'.JText::_('TEXT_YES').'</option>
							<option value="0"'.($fMain == 0 ? ' selected' : '').'>'.JText::_('TEXT_NO').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-3 col-md-2">
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
