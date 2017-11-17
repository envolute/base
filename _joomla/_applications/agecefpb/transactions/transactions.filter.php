<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 1, 'int');
	$where .= $db->quoteName('T1.state').' = '.$active;
	// USERGROUP -> select
	$fGroup	= $app->input->get('fGroup', 0, 'int');
	if($fGroup != 0) $where .= ' AND '.$db->quoteName('T3.usergroup').' = '.$fGroup;
	// FIXED -> select
	$isFixed	= $app->input->get('isFixed', 0, 'int');
	// IMPORTED -> cópia de uma fixa (importada)
	$fSType	= $app->input->get('fSType', 1, 'int');
	if($isFixed == 1) :
		$where .= ' AND '.$db->quoteName('T1.fixed').' = 1';
	else :
		$op = ($fSType == 1) ? ' <> ' : ' = ';
		$where .= ' AND '.$db->quoteName('T1.fixed').$op.$fSType;
	endif;
	// CLIENT -> select
	$fClient	= $app->input->get('cID', 0, 'int');
	if($fClient != 0) $where .= ' AND '.$db->quoteName('T3.id').' = '.$fClient;
	// PROVIDER -> select
	$fProvider	= $app->input->get('pID', 0, 'int');
	if($fProvider != 0) $where .= ' AND '.$db->quoteName('T1.provider_id').' = '.$fProvider;

	$listActions = '';
	// Apenas para movimentações Avulsas/Importadas
	if($isFixed == 1) :

		// Reseta os campos para movimentações avulsas
		$fIgrp = $fCard = 2;
		$fInv = 0;
		$fInst = 1;
		// Desabilita os campos das movimentações avulsas
		$js = '
			jQuery(function() {
				jQuery("#fSType, #fInv, #fInst, #fCard").prop("disabled", true);
				jQuery("#fSType, #fInv, #fInst, #fCard").selectUpdate();
			});
		';
		$listActions = '
			<div class="col-sm-4">
				<div class="form-group">
					<button type="button" class="btn btn-sm btn-primary mt-2 '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_ADD_SELECTED_DESC').'" disabled data-toggle="modal" data-target="#modal-'.$APPTAG.'-addSelectedFixed" data-backdrop="static" data-keyboard="false">
						<span class="base-icon-download btn-icon"></span> '.JText::_('TEXT_ADD_SELECTED').'
					</button>
				</div>
			</div>
		';

	else :

		// IS CARD -> select
		$fCard	= $app->input->get('fCard', 2, 'int');
		if($fCard != 2) $where .= ' AND '.$db->quoteName('T1.isCard').' = '.$fCard;
		// INVOICE -> select
		$fInv	= $app->input->get('fInv', 0, 'int');
		// [filter] ALL INSTALLMENTS -> opção para visualizar todas as parcelas
		$fInst	= $app->input->get('fInst', 1, 'int');
		// $fInst: 1 => disponíveis para faturamento
		// $fInst: 3 => parcelas não disponíveis para faturamento
		$where .= ' AND '.$db->quoteName('T1.charged').' = '.($fInst == 3 ? 0 : 1);
		if($fInst != 1) :
			// installment = total => à vista
			// installment < total => parcela
			// $fInst: 0 => apenas à vista
			// $fInst: 2/3 => apenas parcelas
			$oper = ($fInst == 0) ? ' = ' : ' < ';
			$where .= ' AND '.$db->quoteName('T1.installment').$oper.$db->quoteName('T1.total');
		endif;
		// Reabilita os campos das movimentações avulsas
		$js = '
			jQuery(function() {
				jQuery("#fSType, #fInv, #fInst, #fCard").prop("disabled", false);
				jQuery("#fSType, #fInv, #fInst, #fCard").selectUpdate();
			});
		';

		// Apenas para movimentações Avulsas/Importadas
		$instActions = '';
		$txtSelect = 'TEXT_CHARGE_SELECTED';
		if($fInst < 3) :
			// Importa próximas parcelas
			$instActions = '
				<div class="form-group d-inline-block">
					<button type="button" class="btn btn-sm btn-primary hasTooltip" title="'.JText::_('TEXT_CHARGE_INSTALLMENTS_DESC').'" data-toggle="modal" data-target="#modal-'.$APPTAG.'-chargeInstallments" data-backdrop="static" data-keyboard="false">
						<span class="base-icon-download btn-icon"></span> '.JText::_('TEXT_CHARGE_INSTALLMENTS').'
					</button>
				</div>
			';
			$txtSelect = 'TEXT_UNCHARGE_SELECTED';
		endif;
		// Importa parcelas selecionadas
		$instActions .= '
			<div class="form-group d-inline-block">
				<button type="button" class="btn btn-sm btn-primary '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_CHARGE_SELECTED_DESC').'" disabled data-toggle="modal" data-target="#modal-'.$APPTAG.'-chargeSelected" data-backdrop="static" data-keyboard="false">
					<span class="base-icon-download btn-icon"></span> '.JText::_($txtSelect).'
				</button>
			</div>
		';

		if($fInv != 1) : // 1 = sem filtro para faturas, todas as movimentações

			if($fInst == 3) :
				// Reseta os campos para movimentações avulsas
				$fSType = 1;
				$fInv = 0;
				// Desabilita os campos das movimentações avulsas
				$js = '
					jQuery(function() {
						jQuery("#fSType, #fInv").prop("disabled", true);
						jQuery("#fSType, #fInv").selectUpdate();
					});
				';
				// força a paginação para evitar o carregamento de todas as parcelas em aberto
				if($_SESSION[$APPTAG.'plim'] == 1) $_SESSION[$APPTAG.'plim'] = 50;
				$listActions = '
					<div class="col-sm pt-2">
						'.$instActions.'
					</div>
				';
			else :
				// sem paginação => mostra todos os resultados
				$_SESSION[$APPTAG.'plim'] = 1;
				if($fInv == 0) :
					$listActions = '
						<div class="col-md-4">
							<div class="form-group">
								<label class="label-sm text-live b-bottom mb-3">'.JText::_('FIELD_LABEL_INVOICE').'</label>
								<button type="button" class="btn btn-sm btn-success '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_ADD_TO_INVOICE_DESC').'" disabled data-toggle="modal" data-target="#modal-'.$APPTAG.'-invoice" data-backdrop="static" data-keyboard="false">
									<span class="base-icon-forward btn-icon"></span> '.JText::_('TEXT_ADD_TO_INVOICE').'
								</button>
							</div>
						</div>
						<div class="col-sm b-left">
							<label class="label-sm text-live b-bottom mb-3">'.JText::_('TEXT_TO_IMPORT').'</label>
							<div class="form-group d-inline-block">
								<button type="button" class="btn btn-sm btn-primary hasTooltip" title="'.JText::_('TEXT_ADD_FIXED_DESC').'" data-toggle="modal" data-target="#modal-'.$APPTAG.'-addFixed" data-backdrop="static" data-keyboard="false">
									<span class="base-icon-download btn-icon"></span> '.JText::_('TEXT_ADD_FIXED').'
								</button>
							</div>
							'.$instActions.'
							<div class="form-group d-inline-block float-lg-right">
								<button type="button" class="btn btn-sm btn-info hasTooltip" title="'.JText::_('TEXT_ADD_PHONE_INVOICE_DESC').'" data-toggle="modal" data-target="#modal-'.$APPTAG.'-phoneInvoice" data-backdrop="static" data-keyboard="false">
									<span class="base-icon-phone btn-icon"></span> '.JText::_('TEXT_ADD_PHONE_INVOICE').'
								</button>
							</div>
						</div>
					';
				else :
					// get sequential number
					$query = 'SELECT MAX(sequencial) + 1 FROM '. $db->quoteName($cfg['mainTable'].'_invoices_debits');
					$db->setQuery($query);
					$lastSeq = $db->loadResult();
					if($lastSeq == 'null') $lastSeq =  0;

					// current invoice's sequential number
					$query = 'SELECT MAX(sequencial) FROM '. $db->quoteName($cfg['mainTable'].'_invoices_debits') .' WHERE invoice_id = '.$fInv;
					$db->setQuery($query);
					$currSeq = $db->loadResult();
					$btnCurrSeq = '';
					if(!empty($currSeq)) :
						$btnCurrSeq = '
							<button type="button" class="btn btn-sm btn-warning hasTooltip" title="'.JText::_('TEXT_GET_SEQUENCIAL_DESC').'" onclick="'.$APPTAG.'_setSequencial('.$currSeq.')">
								<span class="base-icon-ccw"></span>
							</button>
						';
					endif;

					$listActions = '
						<div class="col-sm-4 col-md-2 pt-2">
							<div class="form-group">
								<button type="button" class="btn btn-sm btn-block btn-danger '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_REMOVE_TO_INVOICE_DESC').'" disabled onclick="'.$APPTAG.'_removeInvoice()">
									<span class="base-icon-reply btn-icon"></span> '.JText::_('TEXT_REMOVE_TO_INVOICE').'
								</button>
							</div>
						</div>
						<div class="col-sm-4 col-md-2 pt-2">
							<div class="form-group">
								<span class="input-group">
									<input type="text" name="'.$APPTAG.'_seq" id="'.$APPTAG.'-seq" value="'.$lastSeq.'" class="form-control form-control-sm field-number" />
									<span class="input-group-btn">
										'.$btnCurrSeq.'
										<button type="button" class="btn btn-sm btn-success '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_GENERATE_FILE_DESC').'" onclick="'.$APPTAG.'_getInvoiceFile('.$fInv.')">
											<span class="base-icon-download"></span>
										</button>
									</span>
								</span>
							</div>
						</div>
					';
				endif;

			endif;

			$where .= ' AND '.$db->quoteName('T1.invoice_id').' = '.$fInv;

		else :

			// força a paginação para evitar o carregamento de todas as movimentações sem filto
			if($_SESSION[$APPTAG.'plim'] == 1) $_SESSION[$APPTAG.'plim'] = 50;

		endif;

	endif;

	// Habilita/desabilita campos das movimentações avulsas
	$doc->addScriptDeclaration($js);

	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);
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
		'T1.id'					=> 'ID',
		'T1.transaction_id'		=> '',
		'T1.description'		=> 'FIELD_LABEL_DESCRIPTION',
		'T1.doc_number'			=> 'FIELD_LABEL_DOC_NUMBER',
		'T4.name'				=> 'FIELD_LABEL_DEPENDENT',
		'T3.cpf'				=> '',
		'T1.note'				=> ''
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

	$orderDef = 'T1.transaction_id, T1.parent_id'; // não utilizar vírgula no inicio ou fim
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

// FILTER'S DINAMIC FIELDS

	// clients -> select
	$flt_client = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();
	foreach ($clients as $obj) {
		$flt_client .= '<option value="'.$obj->id.'"'.($obj->id == $fClient ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
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
		SELECT
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T1.due_date') .',
			IF(`T1`.`custom_desc` <> "", `T1`.`custom_desc`, `T1`.`description`) invoice_desc
		FROM
			'. $db->quoteName($cfg['mainTable'].'_invoices') .' T1
		WHERE T1.state = 1 ORDER BY T1.due_date DESC, T1.description ASC, T1.custom_desc ASC
	';
	$db->setQuery($query);
	$invoices = $db->loadObjectList();
	$currInvoice = '';
	foreach ($invoices as $obj) {
		$desc = baseHelper::nameFormat($obj->invoice_desc, 20);
		$flt_invoice .= '<option value="'.$obj->id.'"'.($obj->id == $fInv ? ' selected = "selected"' : '').'>'.baseHelper::dateFormat($obj->due_date).' - '.$desc.'</option>';
		if($obj->id == $fInv) $currInvoice = JText::_('FIELD_LABEL_INVOICE').' '.baseHelper::dateFormat($obj->due_date).' <span class="text-md">('.$desc.')</span>';
	}

	// usergroups -> select
	$flt_group = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__usergroups') .' WHERE '. $db->quoteName('parent_id') .' = 10 ORDER BY id';
	$db->setQuery($query);
	$userGrps = $db->loadObjectList();
	foreach ($userGrps as $obj) {
		$flt_group .= '<option value="'.$obj->id.'"'.($obj->id == $fGroup ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->title).'</option>';
	}

// VISIBILITY
// Elementos visíveis apenas quando uma consulta é realizada

	$hasFilter = $app->input->get($APPTAG.'_filter', 0, 'int');
	// Estado inicial dos elementos
	$btnClearFilter		= ''; // botão de resetar
	$textResults		= ''; // Texto informativo
	$txt 				= JText::_('TEXT_NOT_INVOICED');
	// Filtro ativo
	if($hasFilter) :
		$btnClearFilter = '
			<a href="'.JURI::current().'" class="btn btn-sm btn-danger base-icon-cancel-circled btn-icon">
				'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'
			</a>
		';
		if($fInv == 0) {
			if($isFixed == 1) $txt = JText::_('TEXT_FIXED_TRANSACTIONS');
			else if($fInst == 0) $txt .= ' <span class="text-md text-danger">('.JText::_('TEXT_IN_CASH').')</span>';
			else if($fInst == 2) $txt .= ' <span class="text-md text-danger">('.JText::_('TEXT_ONLY_INSTALLMENTS').')</span>';
			else if($fInst == 3) $txt = JText::_('TEXT_CHARGE_INSTALLMENTS');
		} else if($fInv == 1) {
			$txt = JText::_('TEXT_ALL_INVOICED');
		} else {
			$txt = $currInvoice;
		}
	endif;
	$textResults = '<span class="base-icon-down-big text-lg text-live d-none d-sm-inline"> '.$txt.'</span>';

	$fieldsetActions = '';
	if($fInv != 1) :
		$fieldsetActions = '
			<fieldset class="fieldset-embed fieldset-sm pt-2 pb-0 mb-4">
				<div class="row">
					'.$listActions.'
				</div>
			</fieldset>
		';
	endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset id="filter-container-'.$APPTAG.'" class="fieldset-embed fieldset-sm pt-3 pb-0 collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? 'show' : '').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-md-4">
					<div class="row">
						<div class="col-12">
							<div class="form-group">
								<label class="label-sm">'.JText::_('TEXT_TRANSACTION_TYPE').'</label>
								<span class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="isFixed" name="isFixed-0" class="set-filter" value="0"'.($isFixed == 0 ? ' checked' : '').' />
										'.JText::_('TEXT_SEPARATE').'
									</label>
									<label class="btn btn-default btn-active-warning">
										<input type="radio" name="isFixed" name="isFixed-1" class="set-filter" value="1"'.($isFixed == 1 ? ' checked' : '').' />
										'.JText::_('TEXT_RECURRENT').'
									</label>
								</span>
							</div>
						</div>
						<div class="col-lg-6">
							<div id="fSType-group" class="form-group">
								<label class="label-sm iconTip hasTooltip" title="'.JText::_('TEXT_IMPORTED_TRANSACTIONS_DESC').'">'.JText::_('TEXT_IMPORTED_TRANSACTIONS').'</label>
								<select name="fSType" id="fSType" class="form-control form-control-sm set-filter">
									<option value="1">'.JText::_('TEXT_SHOW').'</option>
									<option value="0"'.($fSType == 0 ? ' selected' : '').'>'.JText::_('TEXT_HIDE').'</option>
									<option value="2"'.($fSType == 2 ? ' selected' : '').'>'.JText::_('TEXT_ONLY_IMPORTED').'</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6">
							<div id="fSType-group" class="form-group">
								<label class="label-sm iconTip hasTooltip" title="'.JText::_('TEXT_INSTALLMENTS_TRANSACTIONS_DESC').'">'.JText::_('TEXT_INSTALLMENTS_TRANSACTIONS').'</label>
								<select name="fInst" id="fInst" class="form-control form-control-sm set-filter">
									<option value="1">'.JText::_('TEXT_SHOW').'</option>
									<option value="0"'.($fInst == 0 ? ' selected' : '').'>'.JText::_('TEXT_HIDE').'</option>
									<option value="2"'.($fInst == 2 ? ' selected' : '').'>'.JText::_('TEXT_ONLY_INSTALLMENTS').'</option>
									<option value="3"'.($fInst == 3 ? ' selected' : '').'>'.JText::_('TEXT_ONLY_UNCHARGED').'</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 b-left">
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label class="label-sm">'.JText::_('FIELD_LABEL_USERGROUP').'</label>
								<select name="fGroup" id="fGroup" class="form-control form-control-sm set-filter">
									<option value="0">- '.JText::_('TEXT_ALL').' -</option>
									'.$flt_group.'
								</select>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label class="label-sm">'.JText::_('TEXT_STATE').'</label>
								<span class="btn-group btn-group-sm btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="active" name="active-1" class="set-filter" value="1"'.($active == 1 ? ' checked' : '').' />
										'.JText::_('TEXT_ACTIVES').'
									</label>
									<label class="btn btn-default btn-active-danger">
										<input type="radio" name="active" name="active-0" class="set-filter" value="0"'.($active == 0 ? ' checked' : '').' />
										'.JText::_('TEXT_INACTIVES').'
									</label>
								</span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label class="label-sm text-truncate">'.implode(', ', $sLabel).'</label>
								<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm field-search" />
							</div>
						</div>
						<div class="col-lg-6">
							<label class="label-sm">'.JText::_('FIELD_LABEL_IS_CARD').'</label>
							<div class="form-group">
								<select name="fCard" id="fCard" class="form-control form-control-sm set-filter">
									<option value="2">- '.JText::_('TEXT_ALL').' -</option>
									<option value="1"'.($fCard == 1 ? ' selected' : '').'>'.JText::_('TEXT_YES').'</option>
									<option value="0"'.($fCard == 0 ? ' selected' : '').'>'.JText::_('TEXT_NO').'</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4 b-left">
					<div class="form-group">
						<label class="label-sm">'.JText::_('FIELD_LABEL_TRANSACTION_DATE').'</label>
						<span class="input-group input-group-sm">
              				<span class="input-group-addon strong">'.JText::_('TEXT_FROM').'</span>
							<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
					<div class="form-group">
						<label class="label-sm">'.JText::_('FIELD_LABEL_PRICE').'</label>
						<span class="input-group input-group-sm">
							<span class="input-group-addon strong">R$</span>
							<input type="text" name="priceMin" value="'.$priceMin.'" class="form-control field-price" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="priceMax" value="'.$priceMax.'" class="form-control field-price" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
			</div>
			<div class="row b-top-2 b-primary">
				<div class="col-md-4 pt-3">
					<div class="form-group">
						<label class="text-sm text-success mb-0">'.JText::_('FIELD_LABEL_INVOICE').'</label>
						<select name="fInv" id="fInv" class="form-control form-control-sm set-filter" onchange="">
							<option value="0">- '.JText::_('TEXT_NOT_INVOICED').' -</option>
							<option value="1"'.($fInv == 1 ? ' selected' : '').'>- '.JText::_('TEXT_ALL_INVOICED').' -</option>
							'.$flt_invoice.'
						</select>
					</div>
				</div>
				<div class="col-md-4 pt-3 b-left">
					<div class="form-group">
						<label class="text-sm text-success mb-0">'.JText::_('FIELD_LABEL_CLIENT').'</label>
						<select name="cID" id="cID" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_client.'
						</select>
					</div>
				</div>
				<div class="col-md-4 pt-3 b-left">
					<div class="form-group">
						<label class="text-sm text-success mb-0">'.JText::_('FIELD_LABEL_PROVIDER').'</label>
						<select name="pID" id="pID" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_provider.'
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
						<button type="submit" class="btn btn-sm btn-primary base-icon-search btn-icon">
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
