<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 1, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// PROJECT
	$projectID	= $app->input->get('pID', 0, 'int');
	if($projectID != 0) $where .= ' AND '.$db->quoteName('T1.project_id').' = '.$projectID;
	// DEPARTMENT
	$depID	= $app->input->get('dID', 0, 'int');
	if($depID != 0) $where .= ' AND '.$db->quoteName('T1.department_id').' = '.$depID;
	// USER
	$userID	= $app->input->get('uID', 0, 'int');
	if($userID != 0) $where .= ' AND '.$db->quoteName('T4.id').' = '.$userID;
	// STATUS -> select
	$fStatus	= $app->input->get('fStatus', 0, 'int');
	$where .= ($fStatus == 2) ? '' : ' AND '.$db->quoteName('T1.status').' = '.$fStatus;
	// TYPE -> select
	$fType	= $app->input->get('fType', 9, 'int');
	$where .= ($fType == 9) ? '' : ' AND '.$db->quoteName('T1.type').' = '.$fType;
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin.' 00:00:00' : '0000-00-00 00:00:00';
	$dtmax = !empty($dateMax) ? $dateMax.' 23:59:59' : '9999-12-31 23:59:59';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.created_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.subject').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T3.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T4.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T1.created_date DESC, T1.priority DESC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T1.alter_date';
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
  // departments -> select
  $flt_department = '';
  $query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_departments') .' ORDER BY name';
  $db->setQuery($query);
  $departments = $db->loadObjectList();
  foreach ($departments as $obj) {
    $flt_department .= '<option value="'.$obj->id.'"'.($obj->id == $depID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
  }
  // users -> select
  $flt_user = '';
  $query = 'SELECT * FROM '. $db->quoteName('#__users') .' ORDER BY name';
  $db->setQuery($query);
  $users = $db->loadObjectList();
  foreach ($users as $obj) {
    $flt_user .= '<option value="'.$obj->id.'"'.($obj->id == $userID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
  }

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
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
						<select name="uID" id="uID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('TEXT_USER').' -</option>
							'.$flt_user.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="dID" id="dID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_DEPARTMENT').' -</option>
							'.$flt_department.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fType" id="fType" class="form-control input-sm set-filter">
							<option value="9">- '.JText::_('FIELD_LABEL_TYPE').' -</option>
							<option value="0"'.($fType == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_REQUEST').'</option>
							<option value="1"'.($fType == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_HELP').'</option>
							<option value="2"'.($fType == 2 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_ISSUE').'</option>
							<option value="3"'.($fType == 3 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_SUGGESTION').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fStatus" id="fStatus" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="0"'.($fStatus == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_OPENED').'s</option>
							<option value="1"'.($fStatus == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_CLOSED').'s</option>
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
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
					</div>
				</div>
				<div class="col-sm-2 col-sm-offset-10">
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
