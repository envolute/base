<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// CATEGORY
	$categID	= $app->input->get('cID', 0, 'int');
	if($categID != 0) $where .= ' AND '.$db->quoteName('T1.category_id').' = '.$categID;
	// TYPE -> select
	$fType	= $app->input->get('fType', 2, 'int');
	$where .= ($fType == 2) ? '' : ' AND '.$db->quoteName('T1.type').' = '.$fType;
	// PAID -> select
	$fPaid	= $app->input->get('fPaid', 2, 'int');
	$where .= ($fPaid == 2) ? '' : ' AND '.$db->quoteName('T1.paid').' = '.$fPaid;
	// PERIOD -> select
	$fPeriod	= $app->input->get('fPeriod', 9, 'int');
	$where .= ($fPeriod == 9) ? '' : ' AND '.$db->quoteName('T1.period').' = '.$fPeriod;
	// CURRENCY -> select
	$fCurr	= $app->input->get('fCurr', '', 'string');
	$where .= empty($fCurr) ? '' : ' AND '.$db->quoteName('T1.currency').' = '.$db->quote($fCurr);
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.start_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.url').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.user').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.password').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T2.name'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T1.start_date';
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

	// categories -> select
  $flt_categ = '';
  $query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_categories') .' ORDER BY name';
  $db->setQuery($query);
  $categories = $db->loadObjectList();
  foreach ($categories as $obj) {
    $flt_categ .= '<option value="'.$obj->id.'"'.($obj->id == $categID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
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
							<option value="">- '.JText::_('FIELD_LABEL_CATEGORY').' -</option>
							'.$flt_categ.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fType" id="fType" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('FIELD_LABEL_TYPE').' -</option>
							<option value="0"'.($fType == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_TYPE_LINK').'</option>
							<option value="1"'.($fType == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_TYPE_SERVICE').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fPaid" id="fPaid" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('FIELD_LABEL_PAID').' -</option>
							<option value="0"'.($fPaid == 0 ? ' selected' : '').'>'.JText::_('TEXT_FREE').'</option>
							<option value="1"'.($fPaid == 1 ? ' selected' : '').'>'.JText::_('TEXT_PAID').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fPeriod" id="fPeriod" class="form-control input-sm set-filter">
							<option value="9">- '.JText::_('FIELD_LABEL_PERIOD').' -</option>
							<option value="1"'.($fPeriod == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_MONTH').'</option>
							<option value="2"'.($fPeriod == 2 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_QUARTERLY').'</option>
							<option value="3"'.($fPeriod == 3 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_SEMESTER').'</option>
							<option value="4"'.($fPeriod == 4 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_YEARLY').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fCurr" id="fCurr" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_CURRENCY').' -</option>
							<option value="BRL"'.($fCurr == 'BRL' ? ' selected' : '').'>R$</option>
							<option value="USD"'.($fCurr == 'USD' ? ' selected' : '').'>U$</option>
							<option value="EUR"'.($fCurr == 'EUR' ? ' selected' : '').'>&euro;</option>
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
