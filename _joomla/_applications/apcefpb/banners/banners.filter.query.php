<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

// filter params

	// STATE -> select
	$active	= $app->input->get('active', ($cfg['canEdit'] ? 2 : 1), 'int');
	$where .= ($active == 2) ? $db->quoteName('T1.state').' != '.$active : $db->quoteName('T1.state').' = '.$active;
	// TAG
	$groupID	= $app->input->get('groupID', 0, 'int');
	if($groupID != 0) $where .= ' AND '.$db->quoteName('T1.group_id').' = '.$groupID;
	// START DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.start_date').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);
	// END DATE
	$endMin	= $app->input->get('endMin', '', 'string');
	$endMax	= $app->input->get('endMax', '', 'string');
	$etmin = !empty($endMin) ? $endMin : '0000-00-00';
	$etmax = !empty($endMax) ? $endMax : '9999-12-31';
	if(!empty($endMin) || !empty($endMax)) $where .= ' AND '.$db->quoteName('T1.end_date').' BETWEEN '.$db->quote($etmin).' AND '.$db->quote($etmax);

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.name'			=> 'FIELD_LABEL_NAME',
		'T1.url'			=> 'FIELD_LABEL_URL'
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

	$orderDef = 'T1.name'; // não utilizar vírgula no inicio ou fim
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

?>
