<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// STATUS -> select
	$status	= $app->input->get('status', 9, 'int');
	if($status < 9) $where .= ' AND '.$db->quoteName('T1.status').' = '.$status;
	// PROJECTS
	$pID	= $app->input->get('pID', 0, 'int');
	$pcID	= $app->input->get('pcID', 0, 'int'); // Prev ID
	$where .= ' AND '.$db->quoteName('T1.project_id').' = '.$pID;
	// PROJECTS TYPES
	$tID	= $app->input->get('tID', 0, 'int');
	if($pcID != 0 && $pID != $pcID) : // resetar
		$tID = 0;
	else :
		if(!empty($tID) && $tID != 0) $where .= ' AND '.$db->quoteName('T1.projectType_id').' = '.$tID;
	endif;
	// SIZE SHIRTS
	$sz	= $app->input->get('sz', '', 'string');
	if(!empty($sz)) $where .= ' AND '.$db->quoteName('T1.sizeShirt').' IN ('.$db->quote($sz).')';
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.created_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T6.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T7.cpf').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = ''; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T1.created_date';
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
	if($hasGroup) : // cliente
		$query = '
		SELECT T1.id, T1.name FROM
			'. $db->quoteName('#__zenite_projects') .' T1
			JOIN '. $db->quoteName('#__zenite_clients') .' T2
			ON T2.id = T1.client_id
			JOIN '. $db->quoteName('#__zenite_rel_clients_contacts') .' T3
			ON T3.client_id = T1.client_id
			JOIN '. $db->quoteName('#__zenite_contacts') .' T4
			ON T4.id = T3.contact_id
			JOIN '. $db->quoteName('#__users') .' T5
			ON T5.id = T4.user_id
		WHERE T5.id = '.$user->id.' AND T1.state = 1
		ORDER BY T1.id DESC
		';
	else :
		$query = 'SELECT * FROM '. $db->quoteName('#__zenite_projects') .' ORDER BY id DESC';
	endif;
	$db->setQuery($query);
	$projects = $db->loadObjectList();
	foreach ($projects as $obj) {
		$flt_project .= '<option value="'.$obj->id.'"'.($obj->id == $pID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

	// projects types -> select
	$flt_types = '';
	$query = '
		SELECT
		 	T2.id, T3.name, T2.distance, IF(T2.distance_unit = 1, "Km", "m") distance_unit, T2.gender, T4.name disability, T2.min_age, T2.max_age
		FROM '. $db->quoteName('#__zenite_projects') .' T1
		 	JOIN '. $db->quoteName('#__zenite_projects_types') .' T2
			ON T2.project_id = T1.id
		 	JOIN '. $db->quoteName('#__zenite_projects_categories') .' T3
			ON T3.id = T2.category_id
		 	LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T4
			ON T4.id = T2.disability_id
		WHERE T1.id = '.$pID
	;
	$db->setQuery($query);
	$types = $db->loadObjectList();
	foreach ($types as $obj) {
		if($obj->max_age == 0) :
			$faixa = ' - '. JText::_('TEXT_AS_FROM').' '.$obj->min_age.' '.JText::_('TEXT_YEARS');
		else :
			$faixa = ' - '. $obj->min_age.' '.JText::_('TEXT_TO').' '.$obj->max_age.' '.JText::_('TEXT_YEARS');
		endif;
		switch ($obj->gender) {
			case 1:
				$gender = ' - '.JText::_('TEXT_MALE');
				break;
			case 2:
				$gender = ' - '.JText::_('TEXT_FEMALE');
				break;
			default:
				$gender = '';
				break;
		}
		$flt_types .= '<option value="'.$obj->id.'"'.($obj->id == $tID ? ' selected = "selected"' : '').'>'.$obj->name.' '.$obj->distance.' '.$obj->distance_unit.$gender.$faixa.'</option>';
	}

	// sizes shirts -> select
	$flt_sizes = '';
	$query = 'SELECT DISTINCT(sizeShirt) size FROM '. $db->quoteName('#__zenite_registrations') .' WHERE project_id = '.$pID.' AND sizeShirt != ""';
	$db->setQuery($query);
	$db->execute();
	$hasSizes = $db->getNumRows();
	$sizes = $db->loadObjectList();
	foreach ($sizes as $obj) {
		$flt_sizes .= '<option value="'.$obj->size.'"'.($obj->size == $sz ? ' selected = "selected"' : '').'>'.$obj->size.'</option>';
	}
	$enableSizes = !$hasSizes ? 'disabled' : '';

	$btnFile = '';
	if($pID != 0) :
		$btnFile = '
		<button type="button" class="btn btn-sm btn-success '.$APPTAG.'-btn-action hasTooltip" title="'.JText::_('TEXT_GENERATE_FILE_DESC').'" onclick="'.$APPTAG.'_getSubsFile('.$pID.')">
			<span class="base-icon-download"></span> CSV
		</button>
		';
	endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<select name="pID" id="pID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('TEXT_SELECT_PROJECT').' -</option>
							'.$flt_project.'
						</select>
						<input type="hidden" name="pcID" value="'.$pID.'" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="tID" id="tID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('TEXT_SELECT_PROJECT_TYPE').' -</option>
							'.$flt_types.'
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
						<select name="active" id="active" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').' (vencidos)</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="status" id="status" class="form-control input-sm set-filter">
							<option value="9">- Pagamento -</option>
							<option value="0"'.($status == 0 ? ' selected' : '').'>'.JText::_('MSG_STATUS_NOT_VIEWED').'</option>
							<option value="1"'.($status == 1 ? ' selected' : '').'>'.JText::_('MSG_STATUS_VIEWED').'</option>
							<option value="2"'.($status == 2 ? ' selected' : '').'>'.JText::_('MSG_STATUS_PAID').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="sz" id="sz" class="form-control input-sm set-filter"'.$enableSizes.'>
							<option value="">- '.JText::_('TEXT_SHIRT').' -</option>
							'.$flt_sizes.'
						</select>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
					</div>
				</div>
				<hr />
				<div class="col-sm-2">
					'.$btnFile.'
				</div>
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
