<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', ($canEdit ? 2 : 1), 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// CLIENT
	$clientID	= $app->input->get('fClient', 0, 'int');
	if($clientID != 0) $where .= ' AND '.$db->quoteName('T5.id').' = '.$clientID;
	// PROJECT
	$projectID	= $app->input->get('fProj', 0, 'int');
	if($projectID != 0) $where .= ' AND '.$db->quoteName('T4.id').' = '.$projectID;
	// TASK
	$taskID	= $app->input->get('fTask', 0, 'int');
	if($taskID != 0) $where .= ' AND '.$db->quoteName('T1.task_id').' = '.$taskID;
	// ASSIGN TO
	// Visão geral das tasks pelo Admin (todas as tasks)
	// Visão geral das tasks pelo Developer (apenas as dele)
	// Ou visão de projeto por todos (todas do projeto)
	$assigned = '';
	if($hasAdmin) {
		$fAssign = $app->input->get('fAssign', array(), 'array');
		for($i = 0; $i < count($fAssign); $i++) {
			$assigned .= ($i == 0) ? ' AND (' : ' OR ';
			$assigned .= 'FIND_IN_SET ('.$fAssign[$i].', T1.user_id)';
			$assigned .= ($i == (count($fAssign) - 1)) ? ')' : '';
		}
	// Visão geral das tasks pelo dev
	// Mostra apenas as tasks do próprio usuário
	} else {
		$fAssign = $user->id;
		$assigned = ' AND '.$db->quoteName('T1.user_id').' = '.$user->id;
	}
	$where .= $assigned;
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.note'				=> 'FIELD_LABEL_NOTE'
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

	$orderDef = 'created_date DESC, task_id DESC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
		$_SESSION[$APPTAG.'oF'] = 'T1.date';
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

// ACTION

	$btnAction = $cfg['listFull'] ? 'type="submit"' : 'type="button" onclick="'.$APPTAG.'_listReload(false);"';

// FILTER'S DINAMIC FIELDS

	// CLIENTES -> select
	$flt_client = '';
	if($hasAdmin) :
		$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
		$db->setQuery($query);
		$clients = $db->loadObjectList();
		foreach ($clients as $obj) {
			$flt_client .= '<option value="'.$obj->id.'"'.($obj->id == $fClient ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
		}
		$flt_client = '
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_CLIENT').'</label>
					<select name="fClient" id="fClient" class="form-control form-control-sm set-filter">
						<option value="0">- '.JText::_('TEXT_ALL').' -</option>
						'.$flt_client.'
					</select>
				</div>
			</div>
		';
	endif;
	// projects -> select
	$flt_project = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_projects') .' ORDER BY name';
	$db->setQuery($query);
	$projects = $db->loadObjectList();
	foreach ($projects as $obj) {
		$flt_project .= '<option value="'.$obj->id.'"'.($obj->id == $projectID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}
	// tasks -> select
	// se não for um admin, mostra apenas as horas do usuário
	$flt_task = '';
	$taskFilter .= ($projectID > 0) ? 'project_id = '.$projectID.' AND ' : '';
	$taskFilter .= (!$hasAdmin) ? 'FIND_IN_SET ('.$fAssign[$i].', assign_to) AND ' : '';
	$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_tasks') .' WHERE '.$taskFilter.'state = 1 ORDER BY created_date DESC';
	$db->setQuery($query);
	$tasks = $db->loadObjectList();
	foreach ($tasks as $obj) {
		$flt_task .= '<option value="'.$obj->id.'"'.($obj->id == $taskID ? ' selected = "selected"' : '').'>#'.$obj->id.' - '.baseHelper::nameFormat($obj->subject).'</option>';
	}
	// ASSIGN TO -> select
	$flt_assign = '';
	if($hasAdmin) :
		$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE '. $db->quoteName('type') .' IN (0, 1) AND '. $db->quoteName('access') .' = 1 AND '. $db->quoteName('state') .' = 1 ORDER BY name';
		$db->setQuery($query);
		$assigned = $db->loadObjectList();
		foreach ($assigned as $obj) {
			$name = !empty($obj->nickname) ? $obj->nickname : $obj->name;
			$staff = ($obj->type == 1) ? '*' : '';
			$flt_assign .= '<option value="'.$obj->user_id.'"'.($obj->user_id == $fAssign ? ' selected = "selected"' : '').'>'.$staff.baseHelper::nameFormat($name).'</option>';
		}
		$flt_assign = '
			<div class="col-sm-6 col-md-3">
				<div class="form-group">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ASSIGN_TO').'</label>
					<select name="fAssign[]" id="fAssign" class="form-control form-control-sm set-filter" multiple>
						'.$flt_assign.'
					</select>
				</div>
			</div>
		';
	endif;

// VISIBILITY
// Elementos visíveis apenas quando uma consulta é realizada

	$hasFilter = $app->input->get($APPTAG.'_filter', 0, 'int');
	// Estado inicial dos elementos
	$btnClearFilter		= ''; // botão de resetar
	$textResults		= ''; // Texto informativo
	// Filtro ativo
	if($hasFilter || $cfg['ajaxFilter']) :
		$btnClearFilter = '
			<a href="'.JURI::current().'" class="btn btn-sm btn-danger base-icon-cancel-circled btn-icon">
				'.JText::_('TEXT_CLEAR').' '.JText::_('TEXT_FILTER').'
			</a>
		';
		$textResults = '<span class="base-icon-down-big text-muted d-none d-sm-inline"> '.JText::_('TEXT_SEARCH_RESULTS').'</span>';
	endif;

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				'.$flt_client.'
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PROJECT').'</label>
						<select name="fProj" id="fProj" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_project.'
						</select>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_TASK').'</label>
						<select name="fTask" id="fTask" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('TEXT_ALL').' -</option>
							'.$flt_task.'
						</select>
					</div>
				</div>
				'.$flt_assign.'
				<div class="col-sm-4 col-md-2">
					<div class="form-group">
						<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ITEM_STATE').'</label>
						<select name="active" id="active" class="form-control form-control-sm set-filter">
							<option value="2">- '.JText::_('TEXT_ALL').' -</option>
							<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
							<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-8 col-md-6 col-lg-4">
					<div class="form-group">
						<label class="label-xs text-muted text-truncate">'.implode(', ', $sLabel).'</label>
						<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm" />
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
						<button '.$btnAction.' id="'.$APPTAG.'-submit-filter" class="btn btn-sm btn-primary base-icon-search btn-icon">
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
