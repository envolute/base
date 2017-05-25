<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// LOCATION -> select
	$fLoc	= $app->input->get('fLoc', 2, 'int');
	if($fLoc != 2) $where .= ' AND '.$db->quoteName('T1.work_location').' = '.$fLoc;
	// PRICE -> select
	$fPrice	= $app->input->get('fPrice', 2, 'int');
	if($fPrice != 2) $where .= ' AND IF('.$db->quoteName('T4.price').' != '.$db->quote('0.00').', 1, IF('.$db->quoteName('T2.price').' != '.$db->quote('0.00').', 1, 0)) = '.$fPrice;
	// BILLABLE -> select
	$fBill	= $app->input->get('fBill', 2, 'int');
	if($fBill != 2) $where .= ' AND '.$db->quoteName('T1.billable').' = '.$fBill;
	// BILLED -> select
	$fBilled	= $app->input->get('fBilled', 0, 'int');
	if($fBilled != 2) $where .= ' AND '.$db->quoteName('T1.billed').' = '.$fBilled;
	// CLIENT
	$clientID	= $app->input->get('cID', 0, 'int');
	if($clientID != 0) $where .= ' AND '.$db->quoteName('T6.id').' = '.$clientID;
	// PROJECT
	$projectID	= $app->input->get('pID', 0, 'int');
	if($projectID != 0) $where .= ' AND '.$db->quoteName('T5.id').' = '.$projectID;
	// SERVICE
	$serviceID	= $app->input->get('sID', 0, 'int');
	if($serviceID != 0) $where .= ' AND '.$db->quoteName('T4.id').' = '.$serviceID;
	// INVOICE -> select
	$fInv	= $app->input->get('fInv', 0, 'int');
	$btnAddInv = $btnRemoveInv = '';
	if($fBilled != 1 && $fInv == 0) :
		$btnAddInv = '
			<button type="button" class="btn btn-sm btn-success '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_ADD_TO_INVOICE_DESC').'" disabled data-toggle="modal" data-target="#modal-'.$APPTAG.'-invoice" data-backdrop="static" data-keyboard="false">
				<span class="base-icon-forward"></span> '.JText::_('TEXT_ADD_TO_INVOICE').'
			</button>
		';
	else :
		$where .= ' AND '.$db->quoteName('T1.invoice_id').' = '.$fInv;
	endif;
	if($fBilled != 0 || $fInv != 0) :
		$btnRemoveInv = '
			<button type="button" class="btn btn-sm btn-danger '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_REMOVE_TO_INVOICE_DESC').'" disabled onclick="'.$APPTAG.'_invoice(false)">
				<span class="base-icon-cancel"></span> '.JText::_('TEXT_REMOVE_TO_INVOICE').'
			</button>
		';
	endif;
	$invoiceActions = '
		<div class="col-sm-10">
			<div class="form-group">
				'.$btnAddInv.'
				'.$btnRemoveInv.'
			</div>
		</div>
	';
	// USER
	$userID	= $app->input->get('uID', 0, 'int');
	if($userID != 0) $where .= ' AND '.$db->quoteName('T3.id').' = '.$userID;
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);
	// INVOICE DATE
	$dateInvMin	= $app->input->get('dateInvMin', '', 'string');
	$dateInvMax	= $app->input->get('dateInvMax', '', 'string');
	$dtmin = !empty($dateInvMin) ? $dateInvMin : '0000-00-00';
	$dtmax = !empty($dateInvMax) ? $dateInvMax : '9999-12-31';
	if(!empty($dateInvMin) || !empty($dateInvMax)) $where .= ' AND '.$db->quoteName('T1.billed_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T1.date DESC, task_id DESC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = '';
			$_SESSION[$APPTAG.'oT'] = '';
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
	$query = 'SELECT * FROM '. $db->quoteName('#__envolute_clients') .' WHERE state = 1 ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();
	foreach ($clients as $obj) {
		$flt_client .= '<option value="'.$obj->id.'"'.($obj->id == $clientID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// projects -> select
	$flt_project = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__envolute_projects') .' ORDER BY name';
	$db->setQuery($query);
	$projects = $db->loadObjectList();
	foreach ($projects as $obj) {
		$flt_project .= '<option value="'.$obj->id.'"'.($obj->id == $projectID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// services -> select
	$flt_service = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__envolute_services') .' ORDER BY name';
	$db->setQuery($query);
	$services = $db->loadObjectList();
	foreach ($services as $obj) {
		$flt_service .= '<option value="'.$obj->id.'"'.($obj->id == $serviceID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// users -> select
	$flt_user = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__users') .' WHERE block = 0 ORDER BY name';
	$db->setQuery($query);
	$users = $db->loadObjectList();
	foreach ($users as $obj) {
		$flt_user .= '<option value="'.$obj->id.'"'.($obj->id == $userID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// invoices -> select
  $flt_invoice = '';
  $query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T2.name').' project,
		'. $db->quoteName('T3.name').' client,
		'. $db->quoteName('T1.due_date') .',
		'. $db->quoteName('T1.month') .',
		'. $db->quoteName('T1.year') .'
	FROM
		'. $db->quoteName('#__envolute_invoices') .' T1
		JOIN '. $db->quoteName('#__envolute_projects').' T2
		ON T2.id = T1.project_id
		JOIN '. $db->quoteName('#__envolute_clients').' T3
		ON T3.id = T2.client_id
	WHERE T1.state = 1 ORDER BY T1.due_date DESC, T3.name ASC, T2.name ASC';
  $db->setQuery($query);
  $invoices = $db->loadObjectList();
  foreach ($invoices as $obj) {
    $flt_invoice .= '<option value="'.$obj->id.'"'.($obj->id == $fInv ? ' selected = "selected"' : '').'>'.baseHelper::dateFormat($obj->due_date, 'd.m').' - '.baseHelper::nameFormat($obj->project).' ['.baseHelper::nameFormat($obj->client).']</option>';
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
							<option value="">- '.JText::_('FIELD_LABEL_CLIENT').' -</option>
							'.$flt_client.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="pID" id="pID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_PROJECT').' -</option>
							'.$flt_project.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="sID" id="sID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_SERVICE').' -</option>
							'.$flt_service.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="uID" id="uID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_USER').' -</option>
							'.$flt_user.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fBill" id="fBill" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('FIELD_LABEL_TYPE').' -</option>
							<option value="1"'.($fBill == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_BILLABLE').'</option>
							<option value="0"'.($fBill == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_NOT_BILLABLE').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fBilled" id="fBilled" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_BILLING').' -</option>
							<option value="1"'.($fBilled == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_BILLED').'s</option>
							<option value="0"'.($fBilled == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_NOT_BILLED').'s</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="fInv" id="fInv" class="form-control input-sm set-filter" onchange="">
							<option value="0">- '.JText::_('TEXT_ALL_INVOICED').' -</option>
							'.$flt_invoice.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fPrice" id="fPrice" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_CHARGE').' -</option>
							<option value="0"'.($fPrice == 0 ? ' selected' : '').'>'.JText::_('TEXT_IN_TIME').'</option>
							<option value="1"'.($fPrice == 1 ? ' selected' : '').'>'.JText::_('TEXT_FIXED_ABBR').'</option>
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
              <span class="input-group-addon strong">'.JText::_('FIELD_LABEL_INVOICE').'</span>
							<input type="text" name="dateInvMin" value="'.$dateInvMin.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateInvMax" value="'.$dateInvMax.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
						</span>
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
