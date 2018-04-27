<?php
defined('_JEXEC') or die;

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS T1.*
		FROM '. $db->quoteName($cfg['mainTable']) .' T1
		WHERE '.$where.$orderList
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

if($num_rows) : // verifica se existe

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
					<th class="text-center d-print-none" width="70">'.JText::_('TEXT_ACTIONS').'</th>
				';
			endif;

			// VIEW
			$html .= '
				<form id="form-list-'.$APPTAG.'" method="post" class="pt-3">
					<table class="table table-striped table-hover table-sm">
						<thead>
							<tr>
								'.$adminView['head']['info'].'
								<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_NAME'), 'T1.name', $APPTAG).'</th>
								<th width="120" class="d-none d-lg-table-cell">'.JText::_('TEXT_CREATED_DATE').'</th>
								'.$adminView['head']['actions'].'
							</tr>
						</thead>
						<tbody>
			';
		}

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$listFiles = '';
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					$listFiles .= '
						<a href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
						</a>
					';
				endif;
			}
		endif;

		$adminView['list']['info'] = $adminView['list']['actions'] = '';
		$btnDelete = $canDelete ? '<a href="#" class="btn btn-xs btn-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash"></span></a>' : '';
		if($canEdit) :
			$adminView['list']['info'] = '
				<td class="check-row d-print-none"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="checkAll-child" value="'.$item->id.'" onchange="'.$APPTAG.'_setBtnStatus()" /></td>
				<td class="d-none d-lg-table-cell d-print-none">'.$item->id.'</td>
			';
			$adminView['list']['actions'] = '
				<td class="text-center d-none d-lg-table-cell d-print-none">
					<a href="#" class="hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span>
					</a>
				</td>
				<td class="text-center d-print-none">
					<a href="#" class="btn btn-xs btn-warning hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span></a>
					'.$btnDelete.'
				</td>
			';
		endif;

		$rowState = $item->state == 0 ? 'table-danger' : '';
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
				<td>'.baseHelper::nameFormat($item->name).'</td>
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
			<td colspan="7">
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
