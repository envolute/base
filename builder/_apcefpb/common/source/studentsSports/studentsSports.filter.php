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
	if($fStud != 0) $where .= ' AND '. $db->quoteName('T1.student_id').' = '.$fStud;
	// SPORT -> select
	$fSport	= $app->input->get('fSport', 0, 'int');
	if($fSport != 0) $where .= ' AND '. $db->quoteName('T1.sport_id').' = '.$fSport;
	// COUPON FREE -> select
	$fCoupon	= $app->input->get('fCoupon', 2, 'int');
	$where .= ($fCoupon == 2) ? '' : ' AND '. $db->quoteName('T1.coupon_free').' = '.$fCoupon;
	// GENDER -> select
	$fGender	= $app->input->get('fGender', 0, 'int');
	$where .= ($fGender == 0) ? '' : ' AND '. $db->quoteName('T2.gender').' = '.$fGender;
	// HEALTH -> select
	$fHealth	= $app->input->get('fHealth', 2, 'int');
	$where .= ($fHealth == 2) ? '' : $db->quoteName('T2.has_disease').' = '.$fHealth;
	// ALLERGY -> select
	$fAllergy	= $app->input->get('fAllergy', 2, 'int');
	$where .= ($fAllergy == 2) ? '' : $db->quoteName('T2.has_allergy').' = '.$fAllergy;
	// DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.registry_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T2.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.mother_name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.father_name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.email').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.email_optional').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.disease_desc').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T2.allergy_desc').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.note').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T3.name').') LIKE LOWER("%'.$search.'%")'; // sport
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'T3.name ASC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T2.name';
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

	// student -> select
  $flt_stud = '';
  $query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_students') .' ORDER BY name';
  $db->setQuery($query);
  $students = $db->loadObjectList();
  foreach ($students as $obj) {
    $flt_stud .= '<option value="'.$obj->id.'"'.($obj->id == $fStud ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
  }

	// sport -> select
  $flt_sport = '';
  $query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_sports') .' ORDER BY name';
  $db->setQuery($query);
  $sports = $db->loadObjectList();
  foreach ($sports as $obj) {
    $flt_sport .= '<option value="'.$obj->id.'"'.($obj->id == $fSport ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
  }

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-3">
					<div class="form-group">
						<select name="fStud" id="fStud" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_STUDENT').' -</option>
							'.$flt_stud.'
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<select name="fSport" id="fSport" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_SPORT').' -</option>
							'.$flt_sport.'
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fCoupon" id="fCoupon" class="form-control input-sm set-filter">
							<option value="2">- '.JText::_('FIELD_LABEL_TYPE').' -</option>
							<option value="1"'.($fCoupon == 1 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_COUPON_FREE').'</option>
							<option value="0"'.($fCoupon == 0 ? ' selected' : '').'>'.JText::_('FIELD_LABEL_PAYING').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fHealth" id="fHealth" class="form-control input-sm set-filter">
							<option value="2">- Doença -</option>
							<option value="1"'.($fHealth == 1 ? ' selected' : '').'>'.JText::_('TEXT_YES').'</option>
							<option value="0"'.($fHealth == 0 ? ' selected' : '').'>'.JText::_('TEXT_NO').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fAllergy" id="fAllergy" class="form-control input-sm set-filter">
							<option value="2">- Alergia -</option>
							<option value="1"'.($fAllergy == 1 ? ' selected' : '').'>'.JText::_('TEXT_YES').'</option>
							<option value="0"'.($fAllergy == 0 ? ' selected' : '').'>'.JText::_('TEXT_NO').'</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fGender" id="fGender" class="form-control input-sm set-filter">
							<option value="0">- '.JText::_('FIELD_LABEL_GENDER').' -</option>
							<option value="1"'.($fGender == 1 ? ' selected' : '').'>'.JText::_('TEXT_MALE').'</option>
							<option value="2"'.($fGender == 2 ? ' selected' : '').'>'.JText::_('TEXT_FEMALE').'</option>
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
				<div class="col-sm-2">
					<div class="form-group">
            <input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
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
		'.$alertSync.'
	</form>
';

?>
