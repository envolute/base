<?php
defined('_JEXEC') or die;

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS T1.*
		FROM '. $db->quoteName('vw_'.$cfg['project'].'_'.$APPNAME) .' T1
		WHERE '.$where.$orderList;
	;
	try {

		$db->setQuery($query, $lim0, $lim);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

$html = '';
if($num_rows) : // verifica se existe

	// Filter Information
	if(${$APPTAG.'Archive'}) $html .= '<hr class="hr-tag b-danger" /><span class="badge badge-danger base-icon-box"> '.JText::_('TEXT_CLOSED').'</span>';

	// pagination
	$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
	jimport('joomla.html.pagination');
	$found_rows = $db->loadResult();
	$pageNav = new JPagination($found_rows , $lim0, $lim );

	$listCount = 0;
	foreach($res as $item) {

		// define permissões de execução
		$canEdit	= ($cfg['canEdit'] || $item->created_by == $user->id);
		$canDelete	= ($cfg['canDelete'] || $item->created_by == $user->id);
		$listCount++;

		if($listCount == 1) {

			// ADMIN VIEW
			$adminView = array();
			$adminView['head']['info'] = $adminView['head']['actions'] = '';
			if($canEdit) :
				$adminView['head']['info'] = '
					<th width="30" class="d-print-none"><input type="checkbox" class="input-checkAll" onchange="'.$APPTAG.'_setBtnStatus()" /></th>
					<th width="50" class="d-none d-lg-table-cell d-print-none">'.baseAppHelper::linkOrder('#', 'T1.id', $APPTAG).'</th>
				';
				$adminView['head']['actions'] = '
					<th class="text-center d-none d-lg-table-cell d-print-none" width="60">'.baseAppHelper::linkOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
				';
			endif;

			// VIEW
			$html .= '
				<form id="form-list-'.$APPTAG.'" method="post">
					<table class="table table-striped table-hover table-sm">
						<thead>
							<tr>
								'.$adminView['head']['info'].'
								<th>'.JText::_('FIELD_LABEL_SUBJECT').'</th>
								<th>'.JText::_('FIELD_LABEL_PROJECT').'</th>
								<th width="120" class="d-none d-lg-table-cell">'.JText::_('TEXT_CREATED_DATE').'</th>
								'.$adminView['head']['actions'].'
							</tr>
						</thead>
						<tbody>
			';
		}

		$adminView['list']['info'] = $adminView['list']['actions'] = '';
		if($canEdit) :
			$adminView['list']['info'] = '
				<td class="check-row d-print-none"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="checkAll-child" value="'.$item->id.'" onchange="'.$APPTAG.'_setBtnStatus()" /></td>
				<td class="d-none d-lg-table-cell d-print-none">'.$item->id.'</td>
			';
			$adminView['list']['actions'] = '
				<td class="text-center d-none d-lg-table-cell d-print-none">
					<a href="#" class="hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_confirmState('.$item->id.', '.$item->state.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-toggle-on text-success' : 'base-icon-toggle-on text-danger').' hasTooltip" title="'.JText::_(($item->state == 1 ? 'MSG_CLOSED_ITEM' : 'MSG_ACTIVATE_ITEM')).'"></span>
					</a>
				</td>
			';
		endif;

		$rowState = $item->state == 0 ? 'text-danger' : '';
		$regInfo	= JText::_('TEXT_CREATED_DATE').': '.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'<br />';
		$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->created_by)->name);
		if($item->alter_date != '0000-00-00 00:00:00') :
			$regInfo	.= '<hr class=&quot;my-2&quot; />';
			$regInfo	.= JText::_('TEXT_ALTER_DATE').': '.baseHelper::dateFormat($item->alter_date, 'd/m/Y H:i').'<br />';
			$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->alter_by)->name);
		endif;

		// Resultados
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				'.$adminView['list']['info'].'
				<td>
					<a href="#'.$APPTAG.'-item-view" class="set-base-modal" onclick="'.$APPTAG.'_setItemView('.$item->id.')">
						'.baseHelper::nameFormat($item->subject).'
					</a>
					<div><small class="text-muted">'.JText::_('TEXT_TYPE_'.$item->type).'</small></div>
				</td>
				<td>
					'.baseHelper::nameFormat($item->project_name).'
					<div><small class="text-muted">'.baseHelper::nameFormat($item->client_name).'</small></div>
				</td>
				<td class="d-none d-lg-table-cell">
					'.baseHelper::dateFormat($item->created_date, 'd/m/Y').'
					<a href="#" class="base-icon-info-circled hasPopover" title="'.JText::_('TEXT_REGISTRATION_INFO').'" data-content="'.$regInfo.'" data-placement="top"></a>
				</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="6">
				<div class="alert alert-warning alert-icon m-0">'.JText::_('MSG_LISTNOREG').'</div>
			</td>
		</tr>
	';

endif;

$html .= '
			</tbody>
		</table>
	</form>
';

if($num_rows) :

	// PAGINATION
	require(JPATH_CORE.DS.'apps/layout/list/pagination.php');

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/formOrder.php');

	// PAGE LIMIT
	require(JPATH_CORE.DS.'apps/layout/list/pageLimit.php');

endif;

return $html;

?>
