<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// USERGROUP -> select
	$fGroup	= $app->input->get('fGroup', 0, 'int');
	if($fGroup != 0) $where .= ' AND '.$db->quoteName('T3.usergroup').' = '.$fGroup;
	// GROUP -> select
	$fIgrp	= $app->input->get('fIgrp', 2, 'int');
	if($fIgrp != 2) $where .= ' AND '.$db->quoteName('T1.invoice_group').' = '.$fIgrp;
	// FIXED -> select
	$isFixed	= $app->input->get('isFixed', 9, 'int');
	$where .= ($isFixed == 9) ? ' AND '.$db->quoteName('T1.fixed').' != 1' : ' AND '.$db->quoteName('T1.fixed').' = '.$isFixed;
	// IS CARD -> select
	$fCard	= $app->input->get('fCard', 2, 'int');
	if($fCard != 2) $where .= ' AND '.$db->quoteName('T1.isCard').' = '.$fCard;
	// CLIENT -> select
	$fClient	= $app->input->get('cID', 0, 'int');
	if($fClient != 0) $where .= ' AND '.$db->quoteName('T3.id').' = '.$fClient;
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
				<div class="col-sm-10">
					<div class="form-group">
						<button type="button" class="btn btn-sm btn-success '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_ADD_TO_INVOICE_DESC').'" disabled data-toggle="modal" data-target="#modal-'.$APPTAG.'-invoice" data-backdrop="static" data-keyboard="false">
							<span class="base-icon-forward"></span> '.JText::_('TEXT_ADD_TO_INVOICE').'
						</button>
						<button type="button" class="btn btn-sm btn-warning '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_ADD_FIXED_DESC').'" data-toggle="modal" data-target="#modal-'.$APPTAG.'-addFixed" data-backdrop="static" data-keyboard="false">
							<span class="base-icon-download"></span> '.JText::_('TEXT_ADD_FIXED').'
						</button>
					</div>
				</div>
			';

		else :

			// get sequential number
			$query = 'SELECT MAX(sequencial) + 1 FROM '. $db->quoteName('#__apcefpb_invoices_debits');
			$db->setQuery($query);
			$lastSeq = $db->loadResult();
			if($lastSeq == 'null') $lastSeq =  0;

			// current invoice's sequential number
			$query = 'SELECT MAX(sequencial) FROM '. $db->quoteName('#__apcefpb_invoices_debits') .' WHERE invoice_id = '.$fInv;
			$db->setQuery($query);
			$currSeq = $db->loadResult();
			$btnCurrSeq = '';
			if(!empty($currSeq)) :
				$btnCurrSeq = '
					<button type="button" class="btn btn-sm btn-warning '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_GET_SEQUENCIAL_DESC').'" onclick="'.$APPTAG.'_setSequencial('.$currSeq.')">
						<span class="base-icon-ccw"></span>
					</button>
				';
			endif;

			$invoiceActions = '
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<button type="button" class="btn btn-sm btn-danger '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_REMOVE_TO_INVOICE_DESC').'" disabled onclick="'.$APPTAG.'_removeInvoice()">
							<span class="base-icon-reply"></span> '.JText::_('TEXT_REMOVE_TO_INVOICE').'
						</button>
					</div>
				</div>
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<span class="input-group">
							<input type="text" name="'.$APPTAG.'_seq" id="'.$APPTAG.'-seq" value="'.$lastSeq.'" class="form-control input-sm field-number" />
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
		$where .= ' AND '.$db->quoteName('T1.invoice_id').' = '.$fInv;
	else :
		// força a paginação para evitar o carregamento de todos as movimentações sem filto
		$_SESSION[$APPTAG.'plim'] = 50;
	endif;
	// [filter] ALL INSTALLMENTS -> opção para visualizar todas as parcelas
	$fInst	= $app->input->get('fInst', 0, 'int');
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

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T3.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T3.code').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T4.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.doc_number').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.note').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'
	$orderDef = 'T1.parent_id ASC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T3.name'; //'T1.transaction_id';
			$_SESSION[$APPTAG.'oT'] = 'ASC'; //'DESC';
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
	$$SETOrder = function($title, $col, $APPTAG) {
		$tp = 'ASC';
		$icon = '';
		if($col == $_SESSION[$APPTAG.'oF']) :
			$tp = ($_SESSION[$APPTAG.'oT'] == 'DESC' || empty($_SESSION[$APPTAG.'oT'])) ? 'ASC' : 'DESC';
			$icon = ' <span class="'.($tp == 'ASC' ? 'base-icon-down-dir' : 'base-icon-up-dir').'"></span>';
		endif;
		return '<a href="#" onclick="'.$APPTAG.'_setListOrder(\''.$col.'\', \''.$tp.'\')">'.$title.$icon.'</a>';
	};

// FILTER'S DINAMIC FIELDS

	// clients -> select
	$flt_client = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_clients') .' ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();
	foreach ($clients as $obj) {
		$flt_client .= '<option value="'.$obj->id.'"'.($obj->id == $fClient ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

	// providers -> select
	$flt_provider = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_providers') .' ORDER BY name';
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
		IF('. $db->quoteName('T1.group_id') .' = 1, "Contribuintes", "Associados Caixa") grp,
		'. $db->quoteName('T1.month') .',
		'. $db->quoteName('T1.year') .'
	FROM
		'. $db->quoteName('#__apcefpb_invoices') .' T1
	WHERE T1.state = 1 ORDER BY T1.year DESC, T1.month DESC, T1.group_id ASC';
  $db->setQuery($query);
  $invoices = $db->loadObjectList();
  foreach ($invoices as $obj) {
    $flt_invoice .= '<option value="'.$obj->id.'"'.($obj->id == $fInv ? ' selected = "selected"' : '').'>'.baseHelper::getMonthName($obj->month).' de '.$obj->year.' - '.baseHelper::nameFormat($obj->grp).'</option>';
  }

	// usergroups -> select
  $flt_group = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__usergroups') .' WHERE '. $db->quoteName('parent_id') .' = 10 ORDER BY id';
	$db->setQuery($query);
	$userGrps = $db->loadObjectList();
  foreach ($userGrps as $obj) {
    $flt_group .= '<option value="'.$obj->id.'"'.($obj->id == $fGroup ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->title).'</option>';
  }

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<select name="cID" id="cID" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_CLIENT').' -</option>
							'.$flt_client.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="pID" id="pID" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_PROVIDER').' -</option>
							'.$flt_provider.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fGroup" id="fGroup" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_USERGROUP').' -</option>
							'.$flt_group.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="isFixed" id="isFixed" class="form-control input-sm set-filter">
							<option value="9">- '.JText::_('TEXT_TYPE').' -</option>
							<option value="0"'.($isFixed == 0 ? ' selected' : '').'>'.JText::_('TEXT_NORMAL').'</option>
							<option value="2"'.($isFixed == 2 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_FIXED').'</option>
							<option value="1"'.($isFixed == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_FIXED_PARENT').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<span class="input-group">
              <span class="input-group-addon strong">'.JText::_('FIELD_LABEL_DATE').'</span>
							<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
            <span class="input-group">
              <span class="input-group-addon strong">R$</span>
							<input type="text" name="priceMin" value="'.$priceMin.'" class="form-control input-sm field-price" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="priceMax" value="'.$priceMax.'" class="form-control input-sm field-price" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fCard" id="fCard" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('FIELD_LABEL_IS_CARD').' -</option>
							<option value="1"'.($fCard == 1 ? ' selected' : '').'>'.JText::_('TEXT_YES').'</option>
							<option value="0"'.($fCard == 0 ? ' selected' : '').'>'.JText::_('TEXT_NO').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="active" id="active" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="fInv" id="fInv" class="form-control input-sm set-filter" onchange="">
						<option value="0">- '.JText::_('TEXT_NOT_INVOICED').' -</option>
						<option value="1"'.($fInv == 1 ? ' selected' : '').'>- '.JText::_('TEXT_ALL_INVOICED').' -</option>
							'.$flt_invoice.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fIgrp" id="fIgrp" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_INVOICE_GROUP').' -</option>
							<option value="0"'.($fIgrp == 0 ? ' selected' : '').'>Sócio Efetivo</option>
							<option value="1"'.($fIgrp == 1 ? ' selected' : '').'>Contribuinte</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<div class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-sm btn-warning btn-active-success set-filter hasTooltip" title="'.JText::_('TEXT_ALL_INSTALLMENTS_DESC').'">
								<span class="base-icon-cancel btn-icon"></span>
								<input type="checkbox" name="fInst" value="1"'.($fInst == 1 ? ' checked' : '').' /> '.JText::_('TEXT_ALL_INSTALLMENTS').'
							</label>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
					</div>
				</div>
			</div>
			<hr class="hr-sm no-margin-top" />
			<div class="row">
				'.$invoiceActions.'
				<div class="col-sm-2 pull-right">
					<div class="form-group text-right">
						<span class="btn-group">
							<button type="submit" class="btn btn-sm btn-primary">
                <span class="base-icon-search btn-icon"></span> '.JText::_('TEXT_SEARCH').'
              </button>
							<a href="'.JURI::current().'" class="base-icon-cancel-circled btn btn-sm btn-danger hasTooltip" title="'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'"></a>
						</span>
					</div>
				</div>
			</div>
		</fieldset>
	</form>
';

?>
