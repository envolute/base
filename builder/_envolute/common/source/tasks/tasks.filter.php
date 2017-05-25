<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 1, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// VISIBLE -> select
	$visible	= $app->input->get('visible', 2, 'int');
	if($visible != 2) $where .= ' AND '.$db->quoteName('T1.visible').' = '.$visible;
	// TYPE -> select
	$fType	= $app->input->get('fType', 0, 'int');
	$where .= ($fType == 2) ? '' : ' AND '.$db->quoteName('T1.type').' = '.$fType;
	if($fType == 1) :
		// reset client, project & service filter
		$clientID = $projectID = 0;
	else :
		// CLIENT
		$clientID	= $app->input->get('client', 0, 'int');
		if($clientID != 0) $where .= ' AND '.$db->quoteName('T4.id').' = '.$clientID;
		// PROJECT
		$projectID	= $app->input->get('proj', 0, 'int');
		if($projectID != 0) $where .= ' AND '.$db->quoteName('T3.id').' = '.$projectID;
	endif;
	// SERVICE
	$serviceID	= $app->input->get('sID', 0, 'int');
	if($serviceID != 0) $where .= ' AND '.$db->quoteName('T2.id').' = '.$serviceID;
	// PRICE -> select
	$fPrice	= $app->input->get('fPrice', 2, 'int');
	$where .= ($fPrice == 2) ? '' : ' AND (IF('.$db->quoteName('T1.price').' = '.$db->quote('0.00').', 0, 1) = '.$fPrice.' OR IF('.$db->quoteName('T2.price').' = '.$db->quote('0.00').', 0, 1) = '.$fPrice.')';
	// BILLABLE -> select
	$fBill	= $app->input->get('fBill', 2, 'int');
	$where .= ($fBill == 2) ? '' : ' AND '.$db->quoteName('T1.billable').' = '.$fBill;
	// PERIOD
	$fPeriod	= $app->input->get('fPeriod', 2, 'int');
	$where .= ($fPeriod == 2) ? '' : ' AND '.$db->quoteName('T1.period').' = '.$fPeriod;
	// RECURRENT TYPE
	$fRecurr	= $app->input->get('fRecurr', 9, 'int');
	$where .= ($fRecurr == 9) ? '' : ' AND '.$db->quoteName('T1.recurrent_type').' = '.$fRecurr;
	// STATUS -> select
	$fStatus	= $app->input->get('fStatus', 9, 'int');
	$where .= ($fStatus == 9) ? ' AND '.$db->quoteName('T1.status').' < 3' : ' AND '.$db->quoteName('T1.status').' = '.$fStatus;
	// DATE -> prazo
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.deadline').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);
	// DATE -> início
	$dateStartMin	= $app->input->get('dateStartMin', '', 'string');
	$dateStartMax	= $app->input->get('dateStartMax', '', 'string');
	$dtmin = !empty($dateStartMin) ? $dateStartMin : '0000-00-00';
	$dtmax = !empty($dateStartMax) ? $dateStartMax : '9999-12-31';
	if(!empty($dateStartMin) || !empty($dateStartMax)) $where .= ' AND '.$db->quoteName('T1.start_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);
	// DATE -> fechamento
	$dateEndMin	= $app->input->get('dateEndMin', '', 'string');
	$dateEndMax	= $app->input->get('dateEndMax', '', 'string');
	$dtmin = !empty($dateEndMin) ? $dateEndMin : '0000-00-00';
	$dtmax = !empty($dateEndMax) ? $dateEndMax : '9999-12-31';
	$dtmin = $dtmin.' 00:00:00';
	$dtmax = $dtmax.' 23:59:59';
	if(!empty($dateEndMin) || !empty($dateEndMax)) $where .= ' AND '.$db->quoteName('T1.end_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);
	// DAY OF WEEK -> select
	$fDayWeek	= $app->input->get('fDayWeek', '', 'string');
	$where .= (!empty($fDayWeek)) ? ' AND FIND_IN_SET ('.$db->quote($fDayWeek).', '.$db->quoteName('T1.weekly').')' : '';
	// DAY OF MONTHLY -> select
	$fDayMonth	= $app->input->get('fDayMonth', '', 'string');
	$where .= (!empty($fDayMonth)) ? ' AND FIND_IN_SET ('.$db->quote($fDayMonth).', '.$db->quoteName('T1.monthly').')' : '';
	// DAY OF YEARLY
	$fDayYear	= $app->input->get('fDayYear', '', 'string');
	$where .= (!empty($fDayYear)) ? ' AND FIND_IN_SET ('.$db->quote($fDayYear).', '.$db->quoteName('T1.yearly').')' : '';

	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.title').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T3.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T4.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.status_desc').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = ($fType == 1 ? 'T1.service_id, ordering' : ''); // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = '';
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
	$$SETOrder = function($title, $col, $APPTAG) {
		$tp = 'ASC';
		$icon = '';
		if($col == $_SESSION[$APPTAG.'oF']) :
			$tp = ($_SESSION[$APPTAG.'oT'] == 'DESC' || empty($_SESSION[$APPTAG.'oT'])) ? 'ASC' : 'DESC';
			$icon = ' <span class="'.($tp == 'ASC' ? 'base-icon-down-dir' : 'base-icon-up-dir').'"></span>';
		endif;
		return '<a class="SETOrder" href="#" onclick="'.$APPTAG.'_setListOrder(\''.$col.'\', \''.$tp.'\')">'.$title.$icon.'</a>';
	};

// FILTER'S DINAMIC FIELDS

	// clients -> select
	$flt_client = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__envolute_clients') .' ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();
	foreach ($clients as $obj) {
		$flt_client .= '<option value="'.$obj->id.'"'.($obj->id == $clientID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// projects -> select
	$flt_project = $listProject = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__envolute_projects') .' ORDER BY name';
	$db->setQuery($query);
	$projects = $db->loadObjectList();
	foreach ($projects as $obj) {
		$flt_project .= '<option value="'.$obj->id.'"'.($obj->id == $projectID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
		$listProject .= '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// service -> select
	$flt_service = $listService = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__envolute_services') .' ORDER BY name';
	$db->setQuery($query);
	$service = $db->loadObjectList();
	foreach ($service as $obj) {
		$flt_service .= '<option value="'.$obj->id.'"'.($obj->id == $serviceID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
		$listService .= '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
	}

// VIEW
$htmlFilter = '
	<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
		<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<select name="client" id="client" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_CLIENT').' -</option>
							'.$flt_client.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="proj" id="proj" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_PROJECT').' -</option>
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
						<span class="input-group">
              <span class="input-group-addon strong">'.JText::_('FIELD_LABEL_DEADLINE').'</span>
							<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<span class="input-group">
              <span class="input-group-addon strong">'.JText::_('FIELD_LABEL_START_DATE').'</span>
							<input type="text" name="dateStartMin" value="'.$dateStartMin.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateStartMax" value="'.$dateStartMax.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<span class="input-group">
              <span class="input-group-addon strong">'.JText::_('FIELD_LABEL_END_DATE').'</span>
							<input type="text" name="dateEndMin" value="'.$dateEndMin.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateEndMax" value="'.$dateEndMax.'" class="form-control input-sm field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm-2">
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-sm btn-default btn-active-success'.($fType == 0 ? ' active' : '').'">
							<input type="radio" name="fType" id="fType-0" class="set-filter" value="0"'.($fType == 0 ? ' checked' : '').' />
							'.JText::_('FIELD_LABEL_TASK').'
						</label>
						<label class="btn btn-sm btn-default btn-active-success'.($fType == 1 ? ' active' : '').'">
							<input type="radio" name="fType" id="fType-1" class="set-filter" value="1"'.($fType == 1 ? ' checked' : '').' />
							'.JText::_('FIELD_LABEL_TEMPLATE').'
						</label>
					</span>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fBill" id="fBill" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('FIELD_LABEL_INVOICE').' -</option>
							<option value="0"'.($fBill == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_BILLABLE').'</option>
							<option value="1"'.($fBill == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_NOT_BILLABLE').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fPrice" id="fPrice" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_CHARGE').' -</option>
							<option value="0"'.($fPrice == 0 ? ' selected' : '').'>'.JText::_('TEXT_IN_TIME').'</option>
							<option value="1"'.($fPrice == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_PRICE_FIXED').'</option>
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
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fRecurr" id="fRecurr" class="form-control input-sm set-filter">
							<option value="9">- '.JText::_('FIELD_LABEL_RECURRENT_TYPE').' -</option>
							<option value="1"'.($fRecurr == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_DAYLY').'</option>
							<option value="2"'.($fRecurr == 2 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_WEEKLY').'</option>
							<option value="3"'.($fRecurr == 3 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_MONTHLY').'</option>
							<option value="4"'.($fRecurr == 4 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_YEARLY').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fDayWeek" id="fDayWeek" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_WEEKLY').' -</option>
							<option value="1"'.($fDayWeek == '1' ? ' selected' : '').'>'.JText::_('FIELD_LABEL_WEEKLY_DAY_1').'</option>
							<option value="2"'.($fDayWeek == '2' ? ' selected' : '').'>'.JText::_('FIELD_LABEL_WEEKLY_DAY_2').'</option>
							<option value="3"'.($fDayWeek == '3' ? ' selected' : '').'>'.JText::_('FIELD_LABEL_WEEKLY_DAY_3').'</option>
							<option value="4"'.($fDayWeek == '4' ? ' selected' : '').'>'.JText::_('FIELD_LABEL_WEEKLY_DAY_4').'</option>
							<option value="5"'.($fDayWeek == '5' ? ' selected' : '').'>'.JText::_('FIELD_LABEL_WEEKLY_DAY_5').'</option>
							<option value="6"'.($fDayWeek == '6' ? ' selected' : '').'>'.JText::_('FIELD_LABEL_WEEKLY_DAY_6').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fDayMonth" id="fDayMonth" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_MONTHLY').' -</option>
							<option value="01"'.($fDayMonth == '01' ? ' selected' : '').'>01</option>
							<option value="02"'.($fDayMonth == '02' ? ' selected' : '').'>02</option>
							<option value="03"'.($fDayMonth == '03' ? ' selected' : '').'>03</option>
							<option value="04"'.($fDayMonth == '04' ? ' selected' : '').'>04</option>
							<option value="05"'.($fDayMonth == '05' ? ' selected' : '').'>05</option>
							<option value="06"'.($fDayMonth == '06' ? ' selected' : '').'>06</option>
							<option value="07"'.($fDayMonth == '07' ? ' selected' : '').'>07</option>
							<option value="08"'.($fDayMonth == '08' ? ' selected' : '').'>08</option>
							<option value="09"'.($fDayMonth == '09' ? ' selected' : '').'>09</option>
							<option value="10"'.($fDayMonth == '10' ? ' selected' : '').'>10</option>
							<option value="11"'.($fDayMonth == '11' ? ' selected' : '').'>11</option>
							<option value="12"'.($fDayMonth == '12' ? ' selected' : '').'>12</option>
							<option value="13"'.($fDayMonth == '13' ? ' selected' : '').'>13</option>
							<option value="14"'.($fDayMonth == '14' ? ' selected' : '').'>14</option>
							<option value="15"'.($fDayMonth == '15' ? ' selected' : '').'>15</option>
							<option value="16"'.($fDayMonth == '16' ? ' selected' : '').'>16</option>
							<option value="17"'.($fDayMonth == '17' ? ' selected' : '').'>17</option>
							<option value="18"'.($fDayMonth == '18' ? ' selected' : '').'>18</option>
							<option value="19"'.($fDayMonth == '19' ? ' selected' : '').'>19</option>
							<option value="20"'.($fDayMonth == '20' ? ' selected' : '').'>20</option>
							<option value="21"'.($fDayMonth == '21' ? ' selected' : '').'>21</option>
							<option value="22"'.($fDayMonth == '22' ? ' selected' : '').'>22</option>
							<option value="23"'.($fDayMonth == '23' ? ' selected' : '').'>23</option>
							<option value="24"'.($fDayMonth == '24' ? ' selected' : '').'>24</option>
							<option value="25"'.($fDayMonth == '25' ? ' selected' : '').'>25</option>
							<option value="26"'.($fDayMonth == '26' ? ' selected' : '').'>26</option>
							<option value="27"'.($fDayMonth == '27' ? ' selected' : '').'>27</option>
							<option value="28"'.($fDayMonth == '28' ? ' selected' : '').'>28</option>
							<option value="29"'.($fDayMonth == '29' ? ' selected' : '').'>29</option>
							<option value="30"'.($fDayMonth == '30' ? ' selected' : '').'>30</option>
							<option value="31"'.($fDayMonth == '31' ? ' selected' : '').'>31</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fStatus" id="fStatus" class="form-control input-sm set-filter">
							<option value="9">- '.JText::_('FIELD_LABEL_STATUS').' -</option>
							<option value="0"'.($fStatus == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_STATUS_WAITING').'</option>
							<option value="1"'.($fStatus == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_STATUS_ACTIVE').'</option>
							<option value="2"'.($fStatus == 2 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_STATUS_PAUSED').'</option>
							<option value="3"'.($fStatus == 3 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_STATUS_COMPLETED').'</option>
							<option value="4"'.($fStatus == 4 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_STATUS_CANCELED').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="visible" id="visible" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('FIELD_LABEL_VISIBLE').' -</option>
							<option value="1"'.($visible == 1 ? ' selected' : '').'>'.JText::_('TEXT_YES').'</option>
							<option value="0"'.($visible == 0 ? ' selected' : '').'>'.JText::_('TEXT_NO').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
					</div>
				</div>
				<div class="col-sm-2">
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
		</form>
		<hr class="hr-xs hr-label" />
		<span class="label label-warning">'.JText::_('TEXT_TEMPLATES_GENERATER').'</span>
		<p class="text-live font-featured top-space"><span class="base-icon-info-circled text-info"></span> '.JText::_('TEXT_TEMPLATES_GENERATE_DESC').'</p>
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group">
					<select name="serviceID" id="'.$APPTAG.'-serviceID" class="form-control input-sm">
						<option value="0">- '.JText::_('FIELD_LABEL_SERVICE').' -</option>
						'.$listService.'
					</select>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<select name="projectID" id="'.$APPTAG.'-projectID" class="form-control input-sm">
						<option value="0">- '.JText::_('FIELD_LABEL_PROJECT').' -</option>
						'.$listProject.'
					</select>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group text-right">
					<button type="button" class="btn btn-sm btn-primary" onclick="'.$APPTAG.'_setTemplatesService()">
						<span class="base-icon-cogs btn-icon"></span> '.JText::_('TEXT_TEMPLATES_GENERATE').'
					</button>
				</div>
			</div>
		</div>
	</fieldset>
';

?>
