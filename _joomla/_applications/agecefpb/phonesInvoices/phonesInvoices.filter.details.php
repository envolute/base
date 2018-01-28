<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = 'tel <> ""';

// filter params

	// INVOICES -> select
	$invID	= $app->input->get('invID', 0, 'int');
	if($invID != 0) $where .= ' AND '.$db->quoteName('T1.invoice_id').' = '.$invID;

	// TELEFONES -> select
	$fPhone	= $app->input->get('fPhone', '', 'string');
	if(!empty($fPhone)) {
		$where .= ' AND LOWER('.$db->quoteName('tel').') LIKE LOWER("%'.$fPhone.'%")';
		// sem paginação => mostra todos os resultados
		if($invID != 0) :
			$_SESSION[$APPTAG.'plim'] = 1;
			$showTotal = 1;
		endif;
	} else {
		// força a paginação para evitar o carregamento de todos as movimentações sem filto
		$_SESSION[$APPTAG.'plim'] = 50;
		$showTotal = 0;
	}

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.data'	=> '',
		'T1.secao'	=> 'FIELD_LABEL_SECTION',
		'T1.sub_secao'	=> 'FIELD_LABEL_SUB_SECTION',
		'T1.descricao'	=> '',
		'T1.origem_destino'	=> '',
		'T1.nome_local_origem'	=> '',
		'T1.nome_local_destino'	=> ''
	);
	$i = 0;
	foreach($searchFields as $key => $value) {
		$_OR = ($i > 0) ? ' OR ' : '';
		$sQuery .= $_OR.'LOWER('.$db->quoteName($key).') LIKE LOWER("%'.$search.'%")';
		if(!empty($value)) $sLabel[] .= JText::_($value);
		$i++;
	}
	if(!empty($search)) $where .= ' AND ('.$sQuery.')';

// FILTER'S DINAMIC FIELDS

	// FATURAS -> select
	$flt_invoice = '';
	$query = '
		SELECT
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T2.name') .',
			'. $db->quoteName('T1.due_date') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__base_providers') .' T2
			ON '. $db->quoteName('T2.id') .' = '. $db->quoteName('T1.provider_id') .'
		WHERE T1.state = 1 ORDER BY T1.due_date DESC
	';
	$db->setQuery($query);
	$invoices = $db->loadObjectList();
	foreach ($invoices as $obj) {
		$flt_invoice .= '<option value="'.$obj->id.'"'.($obj->id == $invID ? ' selected = "selected"' : '').'>'.$obj->name.' - '.baseHelper::dateFormat($obj->due_date).'</option>';
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
			<a href="'.JURI::current().'?invID='.$invID.'" class="btn btn-sm btn-danger base-icon-cancel-circled">
				'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'
			</a>
		';
		$textResults = '<span class="base-icon-down-big text-muted d-none d-sm-inline"> '.JText::_('TEXT_SEARCH_RESULTS').'</span>';
	endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-6 col-lg-4 col-xl-3">
					<div class="form-group">
						<div class="input-group input-group-sm">
							<span class="input-group-btn">
								<a href="'.JURI::root().'apps/telefonia/phonesinvoices" class="btn btn-sm btn-default base-icon-left-big"> '.JText::_('TEXT_RETURN_TO_INVOICES').'</a>
							</span>
							<select name="invID" id="invID" class="form-control form-control-sm set-filter" onchange="">
								<option value="0">- '.JText::_('TEXT_SELECT').' -</option>
								'.$flt_invoice.'
							</select>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-lg-2">
					<div class="form-group">
						<input type="text" name="fPhone" value="'.$fPhone.'" class="form-control form-control-sm" placeholder="'.JText::_('FIELD_LABEL_PHONE').'" />
					</div>
				</div>
				<div class="col-sm-6 col-lg-6 col-xl-4">
					<div class="form-group">
						<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm field-search" placeholder="'.implode(', ', $sLabel).'" />
					</div>
				</div>
				<div class="col-sm col-lg-6 col-xl-3 text-xl-right">
					<div class="form-group">
						<button type="submit" class="btn btn-sm btn-primary base-icon-search">
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
