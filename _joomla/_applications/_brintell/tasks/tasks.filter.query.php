<?php
defined('_JEXEC') or die;

// QUERY FOR LIST
$where = '';

$query = 'SELECT id FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE user_id = '.$user->id;
$db->setQuery($query);
$myID = $db->loadResult();

// filter params

	// STATE -> select
	$active	= $app->input->get('active', 1, 'int');
	$where .= $db->quoteName('T1.state').' = '.$active;
	// PROJECT
	$pID	= $app->input->get('pID', 0, 'int');
	$fProj	= ($pID > 0) ? $pID : $app->input->get('fProj', 0, 'int');
	if($fProj != 0) $where .= ' AND '.$db->quoteName('T1.project_id').' = '.$fProj;
	// ASSIGN TO
	// Visão geral das tasks pelo Admin (todas as tasks)
	// Ou visão de projeto por todos (todas do projeto)
	$assigned = '';
	if($hasAdmin || $pID > 0) :
		$fAssign = $app->input->get('fAssign', array(), 'array');
		for($i = 0; $i < count($fAssign); $i++) {
			$assigned .= ($i == 0) ? ' AND (' : ' OR ';
			$assigned .= 'FIND_IN_SET ('.$fAssign[$i].', T1.assign_to)';
			$assigned .= ($i == (count($fAssign) - 1)) ? ')' : '';
		}
	// Visão geral das tasks pelo dev
	// Mostra apenas as tasks do próprio usuário
	else :
		$fAssign = $myID;
		$assigned = ' AND FIND_IN_SET ('.$fAssign.', '.$db->quoteName('T1.assign_to').')';
	endif;
	$where .= $assigned;
	// TYPE
	$fType	= $app->input->get('fType', 9, 'int');
	if($fType != 9) $where .= ' AND '.$db->quoteName('T1.priority').' = '.$fType;
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
	// VISIBILITY
	$fView	= $app->input->get('fView', 2, 'int');
	if($fView != 2) $where .= ' AND '.$db->quoteName('T1.visibility').' = '.$fView;
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
		'T1.requests'		=> 'FIELD_LABEL_REQUESTS_IDS',
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

	$orderDef = 'T1.priority DESC, T1.created_date DESC'; // não utilizar vírgula no inicio ou fim
	if(!isset($_SESSION[$APPTAG.'oF'])) : // DEFAULT ORDER
		$_SESSION[$APPTAG.'oF'] = 'T1.status';
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
