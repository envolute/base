<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

  // STATE -> select
  $where .= $db->quoteName('T3.state').' = 1';
  // GENDER -> select
  $fGender	= $app->input->get('fGender', '', 'string');
  if(isset($fGender) && !empty($fGender)) $where .= ' AND '.$db->quoteName('T1.sexo').' = '.$db->quote($fGender);
  // PROJECTS
  $p = base64_decode($app->input->get('p', '', 'string'));
  $p = (isset($p) && !empty($p)) ? $p : 0;
	$pID	= $app->input->get('pID', $p, 'int');
	if(isset($pID) && $pID != 0) $where .= ' AND '.$db->quoteName('T1.project_id').' = '.$pID;
  // CATEGORIES
  $cID	= $app->input->get('cID', 0, 'int');
	if(isset($cID) && $cID != 0) $where .= ' AND '.$db->quoteName('T3.projectType_id').' = '.$cID;
  // FAIXAS
	$f	= $app->input->get('f', '', 'string');
	if(isset($f) && !empty($f)) $where .= ' AND '.$db->quoteName('T1.faixa').' = '.$db->quote($f);

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.numero').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.nome').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.equipe').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T1.colocacao'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'ResultsoF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'ResultsoF'] = '';
			$_SESSION[$APPTAG.'ResultsoT'] = 'DESC';
	endif;
	if(!empty($ordf)) :
		$_SESSION[$APPTAG.'ResultsoF'] = $ordf;
		$_SESSION[$APPTAG.'ResultsoT'] = $ordt;
	endif;
	$orderList = !empty($_SESSION[$APPTAG.'ResultsoF']) ? $db->quoteName($_SESSION[$APPTAG.'ResultsoF']).' '.$_SESSION[$APPTAG.'ResultsoT'] : '';
	// fixa a ordenação em caso de itens com o mesmo valor (ex: mesma data)
	$orderList .= (!empty($orderList) && !empty($orderDef) ? ', ' : '').$orderDef;
	$orderList .= (!empty($orderList) ? ', ' : '').$db->quoteName('T1.id').' DESC';
	// set order by
	$orderList = !empty($orderList) ? ' ORDER BY '.$orderList : '';

	$SETOrder = $APPTAG.'setOrder';
	$$SETOrder = function($title, $col, $APPTAG) {
		$tp = 'ASC';
		$icon = '';
		if($col == $_SESSION[$APPTAG.'ResultsoF']) :
			$tp = ($_SESSION[$APPTAG.'ResultsoT'] == 'DESC' || empty($_SESSION[$APPTAG.'ResultsoT'])) ? 'ASC' : 'DESC';
			$icon = ' <span class="'.($tp == 'ASC' ? 'base-icon-down-dir' : 'base-icon-up-dir').'"></span>';
		endif;
		return '<a href="#" onclick="results_setListOrder(\''.$col.'\', \''.$tp.'\')">'.$title.$icon.'</a>';
	};

// FILTER'S DINAMIC FIELDS

	// projects -> select
	$flt_project = '';
	$query = '
  SELECT DISTINCT(T1.id), T1.name
  FROM '. $db->quoteName('#__zenite_projects') .' T1
    JOIN '. $db->quoteName('#__zenite_projects_results') .' T2
    ON T2.project_id = T1.id
  WHERE T1.state = 1 AND T2.state = 1
  ORDER BY T1.date DESC';
	$db->setQuery($query);
	$projects = $db->loadObjectList();
	foreach ($projects as $obj) {
		$flt_project .= '<option value="'.$obj->id.'"'.($obj->id == $pID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

  // projects -> select
	$flt_category = '';
  if($pID != 0) :
  	$query = '
    SELECT
      '. $db->quoteName('T1.id') .',
      '. $db->quoteName('T2.name') .' category,
      '. $db->quoteName('T3.name') .' disability,
      '. $db->quoteName('T1.distance') .',
      '. $db->quoteName('T1.distance_unit') .'
    FROM
      '. $db->quoteName('#__zenite_projects_types') .' T1
      JOIN '. $db->quoteName('#__zenite_projects_categories') .' T2
      ON T2.id = T1.category_id
      LEFT JOIN '. $db->quoteName('#__zenite_disabilities') .' T3
      ON T3.id = T1.disability_id
    WHERE
      T1.project_id = '.$pID.' AND T1.state = 1
    ORDER BY '. $db->quoteName('T2.name') .' ASC, '. $db->quoteName('T1.distance') .' ASC,'. $db->quoteName('T1.id') .' ASC';
  	$db->setQuery($query);
  	$categories = $db->loadObjectList();
  	foreach ($categories as $obj) {
      $d = !empty($obj->disability) ? ' ('.baseHelper::nameFormat($obj->disability).')' : '';
  		$flt_category .= '<option value="'.$obj->id.'"'.($obj->id == $cID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->category).' '.$obj->distance.($obj->distance_unit == 0 ? ' m' : ' Km').$d.'</option>';
  	}
  endif;

  // faixa -> select
	$flt_faixa = '';
	$query = 'SELECT DISTINCT(faixa) faixa FROM '. $db->quoteName('#__zenite_results') .' WHERE project_id = '.$pID.' ORDER BY faixa ASC';
	$db->setQuery($query);
	$faixas = $db->loadColumn();
	foreach ($faixas as $obj) {
    if(!empty($obj)) $flt_faixa .= '<option value="'.$obj.'"'.($obj == $f ? ' selected = "selected"' : '').'>'.$obj.'</option>';
	}

// VIEW
$htmlFilter = '
	<form id="filter-result" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-4 col-md-5">
					<div class="form-group">
						<select name="pID" id="pID" class="form-control input-sm set-filter">
							<option value="">- Evento -</option>
							'.$flt_project.'
						</select>
					</div>
				</div>
        <div class="col-sm-4 col-md-5">
					<div class="form-group">
						<select name="cID" id="cID" class="form-control input-sm set-filter">
							<option value="">- Modalidade -</option>
							'.$flt_category.'
						</select>
					</div>
				</div>
        <div class="col-sm-4 col-md-2">
					<div class="form-group">
						<select name="fGender" id="fGender" class="form-control input-sm set-filter">
							<option value="">- Sexo -</option>
							<option value="M"'.($fGender == 'M' ? ' selected' : '').'>Masculino</option>
							<option value="F"'.($fGender == 'F' ? ' selected' : '').'>Feminino</option>
						</select>
					</div>
				</div>
        <div class="col-sm-4 col-md-5">
					<div class="form-group">
						<select name="f" id="f" class="form-control input-sm set-filter">
							<option value="">- Faixa -</option>
							'.$flt_faixa.'
						</select>
					</div>
				</div>
				<div class="col-sm-4 col-md-5">
					<div class="form-group">
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
					</div>
				</div>
				<div class="col-sm-2 col-md-2">
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
