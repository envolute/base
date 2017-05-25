<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// PLAN -> select
	$fPlan	= $app->input->get('fPlan', 0, 'int');
	if(isset($fPlan) && $fPlan != 0) $where .= ' AND '.$db->quoteName('T1.plan_id').' = '.$fPlan;
	// BILLABLE -> select
	$fBill	= $app->input->get('fBill', 2, 'int');
	if($fBill != 2) $where .= ' AND '.$db->quoteName('T1.billable').' = '.$fBill;
	// PROJECT
	$projectID	= $app->input->get('pID', 0, 'int');
	if(isset($projectID) && $projectID != 0) $where .= ' AND '.$db->quoteName('T1.project_id').' = '.$projectID;
	// price
	$priceMin	= $app->input->get('priceMin', '', 'string');
	$priceMax	= $app->input->get('priceMax', '', 'string');
	$prmin = !empty($priceMin) ? $priceMin : '0.00';
	$prmax = (!empty($priceMax) && $priceMax != '0.00') ? $priceMax : '9999999999.99';
	if(!empty($priceMin) || !empty($priceMax)) $where .= ' AND ('.$db->quoteName('T1.price').' BETWEEN '.$prmin.' AND '.$prmax;
	if(!empty($priceMin) || !empty($priceMax)) $where .= ' OR '.$db->quoteName('T3.price').' BETWEEN '.$prmin.' AND '.$prmax.')';

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.username').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.pass_def').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = ''; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T2.name';
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
		return '<a href="#" onclick="'.$APPTAG.'_setListOrder(\''.$col.'\', \''.$tp.'\')">'.$title.$icon.'</a>';
	};

// FILTER'S DINAMIC FIELDS

	// projects -> select
	$flt_project = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__envolute_projects') .' ORDER BY name';
	$db->setQuery($query);
	$projects = $db->loadObjectList();
	foreach ($projects as $obj) {
		$flt_project .= '<option value="'.$obj->id.'"'.($obj->id == $projectID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
  // plan -> select
  $flt_plan = '';
  $query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_plans') .' ORDER BY space';
  $db->setQuery($query);
  $plans = $db->loadObjectList();
  foreach ($plans as $obj) {
    $flt_plan .= '<option value="'.$obj->id.'"'.($obj->id == $fPlan ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).' ('.$obj->space.'Gb)</option>';
  }

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-6 col-md-4">
					<div class="form-group">
						<select name="pID" id="pID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_PROJECT').' -</option>
							'.$flt_project.'
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-4">
					<div class="form-group">
						<select name="fPlan" id="fPlan" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_PLAN').' -</option>
							'.$flt_plan.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="active" id="active" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-4">
					<div class="form-group">
            <span class="input-group">
              <span class="input-group-addon strong">R$</span>
							<input type="text" name="priceMin" value="'.$priceMin.'" class="form-control input-sm field-price" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="priceMax" value="'.$priceMax.'" class="form-control input-sm field-price" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
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
				<div class="col-sm-2 col-md-offset-2">
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
