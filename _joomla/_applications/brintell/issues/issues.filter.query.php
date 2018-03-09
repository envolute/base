<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

${$APPTAG.'Archive'} = isset(${$APPTAG.'Archive'}) ? ${$APPTAG.'Archive'} : 0;

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 1, 'int');
	$where .= $db->quoteName('T1.state').' = '.(${$APPTAG.'Archive'} ? 0 : $active);
	// CLIENT
	if($hasClient) {
		$where .= ' AND '.$db->quoteName('T1.client_id').' = '.$client_id;
	} else {
		$fClient = $app->input->get('fClient', 0, 'int');
		if($fClient != 0) $where .= ' AND '.$db->quoteName('T1.client_id').' = '.$fClient;
	}
	// PROJECT
	$pID	= $app->input->get('pID', 0, 'int');
	$fProj	= ($pID > 0) ? $pID : $app->input->get('fProj', 0, 'int');
	if($fProj != 0) $where .= ' AND '.$db->quoteName('T1.project_id').' = '.$fProj;
	// CREATED BY
	$createdBy = '';
	$fCreated = $app->input->get('fCreated', array(), 'array');
	if(!empty($fCreated)) {
		$fCreatedIds = implode(',', $fCreated);
		$createdBy .= ' AND T1.created_by IN ('.$fCreatedIds.')';
	// Se for um client, visualiza apenas as issues de sua equipe
	} else if($hasClient) {
		$createdBy .= ' AND T1.author_type = 2';
	}
	$where .= $createdBy;
	// TYPE
	$fType	= $app->input->get('fType', 9, 'int');
	if($fType != 9) $where .= ' AND '.$db->quoteName('T1.type').' = '.$fType;
	// PRIORITY
	$fPrior	= $app->input->get('fPrior', 9, 'int');
	if($fPrior != 9) $where .= ' AND '.$db->quoteName('T1.priority').' = '.$fPrior;
	// TAGS
	$fTags = $app->input->get('fTags', array(), 'array');
	$tags = '';
	for($i = 0; $i < count($fTags); $i++) {
		$tags .= ($i == 0) ? ' AND (' : ' OR ';
		$tags .= 'FIND_IN_SET ("'.$fTags[$i].'", '.$db->quoteName('T1.tags').')';
		$tags .= ($i == (count($fTags) - 1)) ? ')' : '';
	}
	$where .= $tags;
	// DEADLINE DATE
	$dateMin	= $app->input->get('dateMin', '', 'string');
	$dateMax	= $app->input->get('dateMax', '', 'string');
	$dtmin = !empty($dateMin) ? $dateMin : '0000-00-00';
	$dtmax = !empty($dateMax) ? $dateMax : '9999-12-31';
	if(!empty($dateMin) || !empty($dateMax)) $where .= ' AND '.$db->quoteName('T1.deadline').' BETWEEN '.$db->quote($dtmin).' AND '.$db->quote($dtmax);

	// Search 'Text fields'
	$search	= $app->input->get('fSearch', '', 'string');
	$sQuery = ''; // query de busca
	$sLabel = array(); // label do campo de busca
	$searchFields = array(
		'T1.subject'		=> 'FIELD_LABEL_SUBJECT',
		'T1.description'	=> 'FIELD_LABEL_DESCRIPTION'
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

	unset($_SESSION[$APPTAG.'oF']);
	$orderDef = 'T1.priority DESC, T1.created_date DESC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
		$_SESSION[$APPTAG.'oF'] = 'T1.type';
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
