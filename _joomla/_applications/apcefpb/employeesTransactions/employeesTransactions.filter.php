<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', ($cfg['canEdit'] ? 2 : 1), 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// CLIENT -> select
	$fEmp	= $app->input->get('cID', 0, 'int');
	if($fEmp != 0) $where .= ' AND '.$db->quoteName('T3.id').' = '.$fEmp;
	// PROVIDER -> select
	$fProvider	= $app->input->get('pID', 0, 'int');
	if($fProvider != 0) $where .= ' AND '.$db->quoteName('T1.provider_id').' = '.$fProvider;
	// INVOICE -> select
	$fInv	= $app->input->get('fInv', 0, 'int');
	if($fInv != 1) : // 1 = sem filtro para faturas, todas as movimentações

		// sem paginação => mostra todos os resultados
		$_SESSION[$APPTAG.'plim'] = 1;
		if($fInv == 0) :
			$invoiceActions = '
				<button type="button" class="btn btn-sm btn-success '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_ADD_TO_INVOICE_DESC').'" disabled data-toggle="modal" data-target="#modal-'.$APPTAG.'-invoice" data-backdrop="static" data-keyboard="false">
					<span class="base-icon-forward"></span> '.JText::_('TEXT_ADD_TO_INVOICE').'
				</button>
			';
		else :
			$invoiceActions = '
				<button type="button" class="btn btn-sm btn-danger '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_REMOVE_TO_INVOICE_DESC').'" disabled onclick="'.$APPTAG.'_removeInvoice()">
					<span class="base-icon-reply"></span> '.JText::_('TEXT_REMOVE_TO_INVOICE').'
				</button>
			';
		endif;
		$where .= ' AND '.$db->quoteName('T1.invoice_id').' = '.$fInv;
	else :
		// força a paginação para evitar o carregamento de todos as movimentações sem filto
		$_SESSION[$APPTAG.'plim'] = 50;
	endif;
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T5.date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);
	// price
	$priceMin	= $app->input->get('priceMin', '', 'string');
	$priceMax	= $app->input->get('priceMax', '', 'string');
	$prmin = !empty($priceMin) ? $priceMin : '0.00';
	$prmax = (!empty($priceMax) && $priceMax != '0.00') ? $priceMax : '9999999999.99';
	if(!empty($priceMin) || !empty($priceMax)) $where .= ' AND '.$db->quoteName('T1.price').' BETWEEN '.$prmin.' AND '.$prmax;

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.description'	=> 'FIELD_LABEL_NOTE',
		'T2.name'			=> 'FIELD_LABEL_PROVIDER',
		'T3.name'			=> 'FIELD_LABEL_EMPLOYEE',
		'T1.doc_number'		=> 'FIELD_LABEL_DOC_NUMBER',
		'T1.note'			=> 'FIELD_LABEL_NOTE',
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
	unset($_SESSION[$APPTAG.'oF']);
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
		$_SESSION[$APPTAG.'oF'] = 'T1.id';
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

	// employees -> select
	$flt_employees = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_employees') .' ORDER BY name';
	$db->setQuery($query);
	$employees = $db->loadObjectList();
	foreach ($employees as $obj) {
		$flt_employees .= '<option value="'.$obj->id.'"'.($obj->id == $fEmp ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

	// providers -> select
	$flt_provider = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__base_providers') .' ORDER BY name';
	$db->setQuery($query);
	$providers = $db->loadObjectList();
	foreach ($providers as $obj) {
		$flt_provider .= '<option value="'.$obj->id.'"'.($obj->id == $fProvider ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

	// invoices -> select
	$flt_invoice = '';
	$query = '
		SELECT T1.*
		FROM '. $db->quoteName('#__'.$cfg['project'].'_employees_invoices') .' T1
		WHERE T1.state = 1 ORDER BY T1.due_date DESC
	';
	$db->setQuery($query);
	$invoices = $db->loadObjectList();
	foreach ($invoices as $obj) {
		$flt_invoice .= '<option value="'.$obj->id.'"'.($obj->id == $fInv ? ' selected = "selected"' : '').'>'.baseHelper::dateFormat($obj->due_date).'</option>';
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

	$fieldsetActions = '';
	if($fInv != 1) :
		$fieldsetActions = '
			<fieldset class="fieldset-embed fieldset-sm p-2 mb-4">'.$invoiceActions.'</fieldset>
		';
	endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-6 col-lg-4 col-xl-3">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMPLOYEE').'</label>
						<select name="fEmp" id="fEmp" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_employees.'
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-lg-4 col-xl-3">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PROVIDER').'</label>
						<select name="fProvider" id="fProvider" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_provider.'
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-lg-4 col-xl-3">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_INVOICE').'</label>
						<select name="fInv" id="fInv" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_invoice.'
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-lg-4 col-xl-3">
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
				<div class="col-sm-6 col-lg-4 col-xl-3">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PRICE').'</label>
						<span class="input-group input-group-sm">
							<span class="input-group-addon strong">R$</span>
							<input type="text" name="priceMin" value="'.$priceMin.'" class="form-control form-control-sm field-price" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="priceMax" value="'.$priceMax.'" class="form-control form-control-sm field-price" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm-6 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ITEM_STATE').'</label>
						<select name="active" id="active" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-12 col-md">
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
		'.$fieldsetActions.'
	</form>
';

?>
