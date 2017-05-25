<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$db->quoteName('T1.state');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// TYPE -> select
	$fType	= $app->input->get('fType', 0, 'int');
	if($fType != 0) $where .= ' AND '.$db->quoteName('T1.type_id').' = '.$fType;
	// USER
	$userID	= $app->input->get('uID', 0, 'int');
	if($userID != 0) $where .= ' AND '.$db->quoteName('T3.id').' = '.$userID;
	// ACCESS -> select
	$fAccess	= $app->input->get('fAccess', 2, 'int');
	if($fAccess != 2) $where .= ' AND '.$db->quoteName('T1.access').' = '.$fAccess;

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = ''; // não utilizar vírgula no inicio ou fim
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

// FILTER'S DINAMIC FIELDS

	// types -> select
  $flt_type = '';
  $query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_types') .' ORDER BY name';
  $db->setQuery($query);
  $types = $db->loadObjectList();
  foreach ($types as $obj) {
    $flt_type .= '<option value="'.$obj->id.'"'.($obj->id == $fType ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
  }
	// users -> select
	$flt_user = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__users') .' WHERE block = 0 ORDER BY name';
	$db->setQuery($query);
	$users = $db->loadObjectList();
	foreach ($users as $obj) {
		$flt_user .= '<option value="'.$obj->id.'"'.($obj->id == $userID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<select name="fType" id="fType" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_TYPE').' -</option>
							'.$flt_type.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="uID" id="uID" class="form-control form-control-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_USER').' -</option>
							'.$flt_user.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fAccess" id="fAccess" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('FIELD_LABEL_ACCESS').' -</option>
							<option value="0"'.($fAccess == 0 ? ' selected' : '').'>'.JText::_('TEXT_PRIVATE').'</option>
							<option value="1"'.($fAccess == 1 ? ' selected' : '').'>'.JText::_('TEXT_PUBLIC').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="active" id="active" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm field-search w-full" />
					</div>
				</div>
				<div class="col-sm text-right">
					<div class="form-group">
						<span class="btn-group">
							<button type="submit" class="btn btn-sm btn-primary">
                <span class="base-icon-search btn-icon"></span> '.JText::_('TEXT_SEARCH').'
              </button>
							<a href="'.JURI::current().'" class="base-icon-cancel-circled btn btn-sm btn-danger hasTooltip" data-animation="false" title="'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'"></a>
						</span>
					</div>
				</div>
			</div>
		</fieldset>
	</form>
';

?>
