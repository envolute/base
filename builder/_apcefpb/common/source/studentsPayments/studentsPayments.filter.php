<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// STUDENT -> select
	$fStud	= $app->input->get('fStud', 0, 'int');
	if($fStud != 0) $where .= ' AND '.$db->quoteName('T3.id').' = '.$fStud;
	// INVOICE -> select
	$fInv	= $app->input->get('fInv', 0, 'int');
	if($fInv != 0) $where .= ' AND '.$db->quoteName('T1.invoice_id').' = '.$fInv;
	// SPORT -> select
	$fSport	= $app->input->get('fSport', 0, 'int');
	if($fSport != 0) $where .= ' AND '.$db->quoteName('T4.id').' = '.$fSport;
	// MONTH -> select
	$fMonth	= $app->input->get('fMonth', 0, 'int');
	$where .= ($fMonth == 0) ? '' : ' AND '.$db->quoteName('T5.month').' = '.$fMonth;
	// YEAR -> select
	$fYear	= $app->input->get('fYear', '', 'string');
	$where .= empty($fYear) ? '' : ' AND '.$db->quoteName('T5.year').' = '.$fYear;
	// price
	$priceMin	= $app->input->get('priceMin', '', 'string');
	$priceMax	= $app->input->get('priceMax', '', 'string');
	$prmin = !empty($priceMin) ? $priceMin : '0.00';
	$prmax = (!empty($priceMax) && $priceMax != '0.00') ? $priceMax : '9999999999.99';
	if(!empty($priceMin) || !empty($priceMax)) $where .= ' AND '.$db->quoteName('T1.price').' BETWEEN '.$prmin.' AND '.$prmax;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T5.month DESC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T5.year';
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

		// students -> select
	  $flt_student = '';
	  $query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_students') .' WHERE state = 1 ORDER BY name';
	  $db->setQuery($query);
	  $students = $db->loadObjectList();
	  foreach ($students as $obj) {
	    $flt_student .= '<option value="'.$obj->id.'"'.($obj->id == $fStud ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	  }

		// invoices -> select
	  $flt_invoice = '';
	  $query = '
		SELECT
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T2.name') .',
			'. $db->quoteName('T1.month') .',
			'. $db->quoteName('T1.year') .'
		FROM
			'. $db->quoteName('#__apcefpb_students_invoices') .' T1
			JOIN '. $db->quoteName('#__apcefpb_sports') .' T2
			ON T2.id = T1.sport_id
		WHERE T1.state = 1 ORDER BY T1.year DESC, T1.month DESC, T2.name ASC';
	  $db->setQuery($query);
	  $invoices = $db->loadObjectList();
	  foreach ($invoices as $obj) {
	    $flt_invoice .= '<option value="'.$obj->id.'"'.($obj->id == $fInv ? ' selected = "selected"' : '').'>'.baseHelper::getMonthName($obj->month).' de '.$obj->year.' - '.baseHelper::nameFormat($obj->name).'</option>';
	  }

		// sports -> select
	  $flt_sport = '';
	  $query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_sports') .' ORDER BY name';
	  $db->setQuery($query);
	  $sports = $db->loadObjectList();
	  foreach ($sports as $obj) {
	    $flt_sport .= '<option value="'.$obj->id.'"'.($obj->id == $fSport ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	  }

		// years -> select
	  $flt_year = '';
	  $query = 'SELECT DISTINCT('. $db->quoteName('year') .') years FROM '. $db->quoteName('#__apcefpb_students_invoices') .' ORDER BY year DESC';
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
				<div class="col-sm-4">
					<div class="form-group">
						<select name="fStud" id="fStud" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_STUDENT').' -</option>
							'.$flt_student.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="fInv" id="fInv" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_INVOICE').' -</option>
							'.$flt_invoice.'
						</select>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<select name="fSport" id="fSport" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_SPORT').' -</option>
							'.$flt_sport.'
						</select>
					</div>
				</div>
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
				<div class="col-sm-4">
					<div class="form-group">
            <span class="input-group">
              <span class="input-group-addon strong">R$</span>
							<input type="text" name="priceMin" value="'.$priceMin.'" class="form-control input-sm field-price" data-width="100%" data-convert="true" />
							<span class="input-group-addon">'.JText::_('TEXT_TO').'</span>
							<input type="text" name="priceMax" value="'.$priceMax.'" class="form-control input-sm field-price" data-width="100%" data-convert="true" />
						</span>
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
