<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
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
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.due_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T1.month DESC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T1.year';
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

		// years -> select
	  $flt_year = '';
	  $query = 'SELECT DISTINCT('. $db->quoteName('year') .') years FROM '. $db->quoteName($cfg['mainTable']) .' ORDER BY year DESC';
	  $db->setQuery($query);
	  $years = $db->loadObjectList();
	  foreach ($years as $obj) {
	    $flt_year .= '<option value="'.$obj->years.'"'.($obj->years == $fYear ? ' selected = "selected"' : '').'>'.$obj->years.'</option>';
	  }

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fMonth" id="fMonth" class="form-control input-sm set-filter">
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
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fYear" id="fYear" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_YEAR').' -</option>
							'.$flt_year.'
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
				<div class="col-sm-2 col-sm-offset-4">
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
