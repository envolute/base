<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', ($cfg['canEdit'] ? 2 : 1), 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// GROUP -> select
	$fGroup	= $app->input->get('fGroup', 0, 'int');
	if($fGroup != 0) $where .= ' AND '.$db->quoteName('T1.group_id').' = '.$fGroup;
	// GENDER -> select
	$gender	= $app->input->get('fGender', 3, 'int');
	$where .= ($gender == 3) ? ' AND '. $db->quoteName('T1.gender').' != '.$gender : ' AND '. $db->quoteName('T1.gender').' = '.$gender;
	// MARITAL STATUS -> select
	$fMStatus	= $app->input->get('fMStatus', '', 'string');
	if(!empty($fMStatus)) $where .= ' AND '.$db->quoteName('T1.marital_status').' = '.$db->quote($fMStatus);
	// BLOOD TYPE -> select
	$fBt	= $app->input->get('fBt', '', 'string');
	if(!empty($fBt)) $where .= ' AND '.$db->quoteName('T1.blood_type').' = '.$db->quote($fBt);
	// DRIVER's CARD -> select
	$fDriver	= $app->input->get('fDriver', 'X', 'string');
	if($fDriver != 'X') $where .= ' AND '.$db->quoteName('T1.driver').' = '.$db->quote($fDriver);
	// BIRTHDAY
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.birthday').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.name'				=> 'FIELD_LABEL_NAME',
		'T1.email'				=> 'E-mail',
		'T1.mother_name'		=> 'FIELD_LABEL_PLACE_BIRTH',
		'T1.cpf'				=> 'FIELD_LABEL_FATHER_NAME',
		'T1.rg'					=> '',
		'T1.pis'				=> '',
		'T1.ctps'				=> '',
		'T1.occupation'			=> '',
		'T1.address'			=> 'FIELD_LABEL_ADDRESS',
		'T1.address_district'	=> '',
		'T1.address_city'		=> '',
		'T1.zip_code'			=> '',
		'T1.phone'				=> 'FIELD_LABEL_PHONE'
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

	$orderDef = ''; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
		$_SESSION[$APPTAG.'oF'] = 'T1.name';
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

	// types -> select
	$flt_group = '';
	$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' ORDER BY id';
	$db->setQuery($query);
	$grps = $db->loadObjectList();
	foreach ($grps as $obj) {
		$flt_group .= '<option value="'.$obj->id.'"'.($obj->id == $fGroup ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
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
				<div class="col-sm-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_BLOOD').'</label>
						<select name="fBt" id="fBt" class="form-control form-control-sm set-filter">
							<option value="">- '.JText::_('TEXT_ALL').' -</option>
							<option value="O+"'.($fBt == 'O+' ? ' selected' : '').'>O+</option>
							<option value="A+"'.($fBt == 'A+' ? ' selected' : '').'>A+</option>
							<option value="B+"'.($fBt == 'B+' ? ' selected' : '').'>B+</option>
							<option value="O-"'.($fBt == 'O-' ? ' selected' : '').'>O-</option>
							<option value="A-"'.($fBt == 'A-' ? ' selected' : '').'>A-</option>
							<option value="B-"'.($fBt == 'B-' ? ' selected' : '').'>B-</option>
							<option value="AB+"'.($fBt == 'AB+' ? ' selected' : '').'>AB+</option>
							<option value="AB-"'.($fBt == 'AB-' ? ' selected' : '').'>AB-</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_DRIVER_CARD').'</label>
						<select name="fDriver" id="fDriver" class="form-control form-control-sm set-filter">
							<option value="X">- '.JText::_('TEXT_ALL').' -</option>
							<option value=""'.($fDriver == '' ? ' selected' : '').'>'.JText::_('TEXT_HAS_NOT').'</option>
							<option value="A"'.($fDriver == 'A' ? ' selected' : '').'>A</option>
							<option value="B"'.($fDriver == 'B' ? ' selected' : '').'>B</option>
							<option value="C"'.($fDriver == 'C' ? ' selected' : '').'>C</option>
							<option value="D"'.($fDriver == 'D' ? ' selected' : '').'>D</option>
							<option value="E"'.($fDriver == 'E' ? ' selected' : '').'>E</option>
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-3 col-lg-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_MARITAL_STATUS').'</label>
						<select name="fMStatus" id="fMStatus" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($fMStatus == 1 ? ' selected' : '').'>'.JText::_('TEXT_MARITAL_STATUS_1').'</option>
							<option value="2"'.($fMStatus == 2 ? ' selected' : '').'>'.JText::_('TEXT_MARITAL_STATUS_2').'</option>
							<option value="3"'.($fMStatus == 3 ? ' selected' : '').'>'.JText::_('TEXT_MARITAL_STATUS_3').'</option>
							<option value="4"'.($fMStatus == 4 ? ' selected' : '').'>'.JText::_('TEXT_MARITAL_STATUS_4').'</option>
							<option value="5"'.($fMStatus == 5 ? ' selected' : '').'>'.JText::_('TEXT_MARITAL_STATUS_5').'</option>
						</select>
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
				<div class="col-sm-6 col-md-3 col-lg-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_GENDER').'</label>
						<select name="fGender" id="fGender" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($fGender == 1 ? ' selected' : '').'>'.JText::_('TEXT_GENDER_1').'</option>
							<option value="2"'.($fGender == 2 ? ' selected' : '').'>'.JText::_('TEXT_GENDER_2').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-lg-4">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_BIRTHDAY').'</label>
						<span class="input-group input-group-sm">
							<span class="input-group-addon strong">'.JText::_('TEXT_FROM').'</span>
							<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="label-xs text-muted text-truncate">'.implode(', ', $sLabel).'</label>
						<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm" />
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
