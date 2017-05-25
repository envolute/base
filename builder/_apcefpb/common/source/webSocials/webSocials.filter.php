<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// WEB SOCIAL NAME
	$socialID	= $app->input->get('sID', 0, 'int');
	if(isset($socialID) && $socialID != 0) $where .= ' AND '.$db->quoteName('T1.socialName_id').' = '.$socialID;

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T2.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.page').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.username').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T4.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T6.name').') LIKE LOWER("%'.$search.'%")';
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
	$flt_social = '';
	$query = 'SELECT * FROM '. $db->quoteName('#__apcefpb_web_socials_names') .' ORDER BY name';
	$db->setQuery($query);
	$socials = $db->loadObjectList();
	foreach ($socials as $obj) {
		$flt_social .= '<option value="'.$obj->id.'"'.($obj->id == $socialID ? ' selected = "selected"' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
	}

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						<select name="sID" id="sID" class="form-control input-sm set-filter">
							<option value="">- '.JText::_('FIELD_LABEL_SOCIAL').' -</option>
							'.$flt_social.'
						</select>
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
