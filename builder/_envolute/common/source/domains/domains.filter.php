<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// PROVIDER -> select
	$fProvider	= $app->input->get('fProvider', 0, 'int');
	if(isset($fProvider) && $fProvider != 0) $where .= ' AND '.$db->quoteName('T1.provider').' = '.$fProvider;
	// MAIN -> checkbox
	$fmain	= $app->input->get('fmain', 0, 'int');
	if(isset($fmain) && $fmain == 1) $where .= ' AND '.$db->quoteName('T1.main').' = 1';
	// HOSTS
	$hostID	= $app->input->get('hID', 0, 'int');
	if(isset($hostID) && $hostID != 0) $where .= ' AND '.$db->quoteName('T1.host_id').' = '.$hostID;
	// CLIENT
	$clientID	= $app->input->get('cID', 0, 'int');
	if(isset($clientID) && $clientID != 0) $where .= ' AND '.$db->quoteName('T4.client_id').' = '.$clientID;
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.domain').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'
	$orderDef = 'T1.main DESC, T1.domain'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T1.domain';
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
	$query = 'SELECT * FROM '. $db->quoteName('#__envolute_clients') .' ORDER BY name';
	$db->setQuery($query);
	$clients = $db->loadObjectList();
	foreach ($clients as $obj) {
		$flt_client .= '<option value="'.$obj->id.'"'.($obj->id == $clientID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// hosts -> select
	$flt_host = '';
	$query = '
		SELECT
			'. $db->quoteName('T1.id') .',
			CONCAT('. $db->quoteName('T3.name') .', " - ", '. $db->quoteName('T2.name') .', " (", '. $db->quoteName('T2.space') .', "Gb)") name
		FROM
			'. $db->quoteName('#__envolute_hosts') .' T1
			JOIN '. $db->quoteName('#__envolute_hosts_plans') .' T2
			ON T2.id = T1.plan_id
			JOIN '. $db->quoteName('#__envolute_projects') .' T3
			ON T3.id = T1.project_id
		WHERE T1.state = 1 ORDER BY T3.name
	';
	$db->setQuery($query);
	$hosts = $db->loadObjectList();
	foreach ($hosts as $obj) {
		$flt_host .= '<option value="'.$obj->id.'"'.($obj->id == $hostID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
  // providers -> select
  $flt_provider = '';
  $query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_providers') .' ORDER BY name';
  $db->setQuery($query);
  $providers = $db->loadObjectList();
  foreach ($providers as $obj) {
    $flt_provider .= '<option value="'.$obj->id.'"'.($obj->id == $fProvider ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
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
						<select name="hID" id="hID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_HOST').' -</option>
							'.$flt_host.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="fProvider" id="fProvider" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_PROVIDER').' -</option>
							'.$flt_provider.'
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
				<div class="col-sm-2">
					<div class="form-group">
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<div class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-sm btn-warning btn-active-success set-filter">
								<span class="base-icon-cancel btn-icon"></span>
								<input type="checkbox" name="fmain" value="1"'.($fmain == 1 ? ' checked' : '').' /> '.JText::_('TEXT_MAIN_PLURAL').'
							</label>
						</div>
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
