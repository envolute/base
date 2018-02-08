<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// TAG -> select
	$tagVal = $_SESSION[$RTAG.'ListAllTags'] ? '' : $APPTAG;
	$fTag	= $app->input->get('fTag', $tagVal, 'string');
	if(!empty($fTag)) $where .= ' AND '.$db->quoteName('T1.tag').' = '.$db->quote($fTag);
	// MONTH -> select
	$fMonth	= $app->input->get('fMonth', 0, 'int');
	if($fMonth != 0) $where .= ' AND '.$db->quoteName('T1.month').' = '.$fMonth;
	// YEAR -> select
	$fYear	= $app->input->get('fYear', '', 'string');
	if(!empty($fYear)) $where .= ' AND '.$db->quoteName('T1.year').' = '.$fYear;
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
		$where .= 'LOWER('.$db->quoteName('T1.title').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.content').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = $_SESSION[$RTAG.'ListOrder']; // não utilizar vírgula no inicio ou fim
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

	// tags -> select
	$flt_tag = '';
	$query = 'SELECT DISTINCT('. $db->quoteName('tag') .') tag FROM '. $db->quoteName($cfg['mainTable']) .' ORDER BY tag ASC';
	$db->setQuery($query);
	$tags = $db->loadObjectList();
	foreach ($tags as $obj) {
		$flt_tag .= '<option value="'.$obj->tag.'"'.($obj->tag == $fTag ? ' selected = "selected"' : '').'>'.$obj->tag.'</option>';
	}

	// years -> select
	$flt_year = '';
	$query = 'SELECT DISTINCT('. $db->quoteName('year') .') years FROM '. $db->quoteName($cfg['mainTable']) .' ORDER BY year DESC';
	$db->setQuery($query);
	$years = $db->loadObjectList();
	foreach ($years as $obj) {
		$flt_year .= '<option value="'.$obj->years.'"'.($obj->years == $fYear ? ' selected = "selected"' : '').'>'.$obj->years.'</option>';
	}

// FILTER'S PARAMS CONFIG
	$dateFilter = '';
	if(!$_SESSION[$RTAG.'HideAllDateFields']) :
		if(!$_SESSION[$RTAG.'HideDateField']) :
			$dateFilter .= '
				<div class="col-sm-4">
					<div class="form-group">
						<span class="input-group">
							<span class="input-group-addon strong">'.JText::_('FIELD_LABEL_DATE').'</span>
							<input type="text" name="dateMin" value="'.$dateMin.'" class="form-control form-control-sm field-date" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="dateMax" value="'.$dateMax.'" class="form-control form-control-sm field-date" data-width="100%" data-convert="true" />
						</span>
					</div>
				</div>
			';
		endif;
		if(!$_SESSION[$RTAG.'HideMonthField']) :
			$dateFilter .= '
				<div class="col-sm-3">
					<div class="form-group">
						<select name="fMonth" id="fMonth" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_MONTH').' -</option>
							<option value="1"'.($fMonth == '1' ? ' selected = "selected"' : '').'>Janeiro</option>
							<option value="2"'.($fMonth == '2' ? ' selected = "selected"' : '').'>Fevereiro</option>
							<option value="3"'.($fMonth == '3' ? ' selected = "selected"' : '').'>Março</option>
							<option value="4"'.($fMonth == '4' ? ' selected = "selected"' : '').'>Abril</option>
							<option value="5"'.($fMonth == '5' ? ' selected = "selected"' : '').'>Maio</option>
							<option value="6"'.($fMonth == '6' ? ' selected = "selected"' : '').'>Junho</option>
							<option value="7"'.($fMonth == '7' ? ' selected = "selected"' : '').'>Julho</option>
							<option value="8"'.($fMonth == '8' ? ' selected = "selected"' : '').'>Agosto</option>
							<option value="9"'.($fMonth == '9' ? ' selected = "selected"' : '').'>Setembro</option>
							<option value="10"'.($fMonth == '10' ? ' selected = "selected"' : '').'>Outubro</option>
							<option value="11"'.($fMonth == '11' ? ' selected = "selected"' : '').'>Novembro</option>
							<option value="12"'.($fMonth == '12' ? ' selected = "selected"' : '').'>Dezembro</option>
						</select>
					</div>
				</div>
			';
		endif;
		if(!$_SESSION[$RTAG.'HideYearField']) :
			$dateFilter .= '
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fYear" id="fYear" class="form-control form-control-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_YEAR').' -</option>
							'.$flt_year.'
						</select>
					</div>
				</div>
			';
		endif;
	endif;

	$textFilter = '';
	if(!$_SESSION[$RTAG.'HideTitle'] || !$_SESSION[$RTAG.'HideDescriptionField']) :
		$textFilter = '
			<div class="col-sm-4">
				<div class="form-group">
					<input type="text" name="fSearch" value="'.$search.'" class="form-control form-control-sm field-search w-full" />
				</div>
			</div>
		';
	endif;

// VIEW
$adminView = array();
$adminView['tags'] = $adminView['active'] = '';
if($hasAdmin) :
	if($_SESSION[$RTAG.'ListAllTags']) :
		$adminView['tags'] = '
			<div class="col-sm-3">
				<div class="form-group">
					<select name="fTag" id="fTag" class="form-control form-control-sm set-filter">
						<option value="0">- '.JText::_('FIELD_LABEL_TAG').'s -</option>
						'.$flt_tag.'
					</select>
				</div>
			</div>
		';
	endif;
	$adminView['active'] = '
		<div class="form-group">
			<select name="active" id="active" class="form-control form-control-sm set-filter">
				<option value="2">- '.JText::_('TEXT_ALL').' -</option>
				<option value="1"'.($active == 1 ? ' selected' : '').'>'.JText::_('TEXT_ACTIVES').'</option>
				<option value="0"'.($active == 0 ? ' selected' : '').'>'.JText::_('TEXT_INACTIVES').'</option>
			</select>
		</div>
	';
endif;

$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print collapse '.((isset($_GET[$APPTAG.'_filter']) || $cfg['openFilter']) ? 'show' : '').'" method="get">
		<fieldset class="fieldset-embed fieldset-sm pt-3 pb-0">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				'.$adminView['tags'].'
				'.$dateFilter.'
				<div class="col-sm-2">
					'.$adminView['active'].'
				</div>
				'.$textFilter.'
				<div class="col-sm-3 float-right text-right">
					<div class="form-group">
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
