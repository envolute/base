<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 2, 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// UF -> select
	$fUF	= $app->input->get('fUF', '', 'string');
	if(!empty($fUF)) $where .= ' AND '.$db->quoteName('T1.address_state').' = '.$fUF;

	// search
	$search	= $app->input->get('fSearch', '', 'string');
	if(!empty($search)) :
		$where .= ' AND (';
		$where .= 'LOWER('.$db->quoteName('T1.description').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.zip_code').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.address').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.address_info').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.address_district').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.address_city').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T1.address_state').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T3.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ' OR LOWER('.$db->quoteName('T5.name').') LIKE LOWER("%'.$search.'%")';
		$where .= ')';
	endif;

// ORDER BY

	$ordf	= $app->input->get($APPTAG.'oF', '', 'string'); // campo a ser ordenado
	$ordt	= $app->input->get($APPTAG.'oT', '', 'string'); // tipo de ordem: 0 = 'ASC' default, 1 = 'DESC'

	$orderDef = 'owner ASC, T1.main DESC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
			$_SESSION[$APPTAG.'oF'] = 'T1.id';
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

// VIEW
$htmlFilter = '
	<form id="filter-'.$APPTAG.'" class="hidden-print" method="get">
		<fieldset class="fieldset-embed filter '.((isset($_GET[$APPTAG.'_filter']) || $cfg['showFilter']) ? '' : 'closed').'">
			<input type="hidden" name="'.$APPTAG.'_filter" value="1" />

			<div class="row">
				<div class="col-sm-4 col-sm-offset-2">
					<div class="form-group">
						<input type="text" name="fSearch" value="'.$search.'" class="form-control input-sm field-search width-full" />
					</div>
				</div>
				<div class="col-sm-2">
					<div class="form-group">
						<select name="fUF" id="fUF" class="form-control input-sm set-filter">
							<option value="">- UF -</option>
							<option value="AC"'.($fUF == 'AC' ? ' selected' : '').'>AC</option>
							<option value="AL"'.($fUF == 'AL' ? ' selected' : '').'>AL</option>
							<option value="AP"'.($fUF == 'AP' ? ' selected' : '').'>AP</option>
							<option value="AM"'.($fUF == 'AM' ? ' selected' : '').'>AM</option>
							<option value="BA"'.($fUF == 'BA' ? ' selected' : '').'>BA</option>
							<option value="CE"'.($fUF == 'CE' ? ' selected' : '').'>CE</option>
							<option value="DF"'.($fUF == 'DF' ? ' selected' : '').'>DF</option>
							<option value="ES"'.($fUF == 'ES' ? ' selected' : '').'>ES</option>
							<option value="GO"'.($fUF == 'GO' ? ' selected' : '').'>GO</option>
							<option value="MA"'.($fUF == 'MA' ? ' selected' : '').'>MA</option>
							<option value="MT"'.($fUF == 'MT' ? ' selected' : '').'>MT</option>
							<option value="MS"'.($fUF == 'MS' ? ' selected' : '').'>MS</option>
							<option value="MG"'.($fUF == 'MG' ? ' selected' : '').'>MG</option>
							<option value="PA"'.($fUF == 'PA' ? ' selected' : '').'>PA</option>
							<option value="PB"'.($fUF == 'PB' ? ' selected' : '').'>PB</option>
							<option value="PR"'.($fUF == 'PR' ? ' selected' : '').'>PR</option>
							<option value="PE"'.($fUF == 'PE' ? ' selected' : '').'>PE</option>
							<option value="PI"'.($fUF == 'PI' ? ' selected' : '').'>PI</option>
							<option value="RJ"'.($fUF == 'RJ' ? ' selected' : '').'>RJ</option>
							<option value="RN"'.($fUF == 'RN' ? ' selected' : '').'>RN</option>
							<option value="RS"'.($fUF == 'RS' ? ' selected' : '').'>RS</option>
							<option value="RO"'.($fUF == 'RO' ? ' selected' : '').'>RO</option>
							<option value="RR"'.($fUF == 'RR' ? ' selected' : '').'>RR</option>
							<option value="SC"'.($fUF == 'SC' ? ' selected' : '').'>SC</option>
							<option value="SP"'.($fUF == 'SP' ? ' selected' : '').'>SP</option>
							<option value="SE"'.($fUF == 'SE' ? ' selected' : '').'>SE</option>
							<option value="TO"'.($fUF == 'TO' ? ' selected' : '').'>TO</option>
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
