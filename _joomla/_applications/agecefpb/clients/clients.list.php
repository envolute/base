<?php
defined('_JEXEC') or die;

// LOAD FILTER
require($PATH_APP_FILE.'.filter.php');

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T1.name') .',
			'. $db->quoteName('T2.name') .' user,
			'. $db->quoteName('T1.usergroup') .',
			'. $db->quoteName('T3.title') .' type,
			'. $db->quoteName('T1.cx_role') .',
			'. $db->quoteName('T1.cx_situated') .',
			'. $db->quoteName('T1.access') .',
			'. $db->quoteName('T1.reasonStatus') .',
			'. $db->quoteName('T1.created_date') .',
			'. $db->quoteName('T1.created_by') .',
			'. $db->quoteName('T1.alter_date') .',
			'. $db->quoteName('T1.alter_by') .',
			'. $db->quoteName('T1.state') .'
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT OUTER JOIN '. $db->quoteName('#__users') .' T2
			ON T2.id = T1.user_id
			LEFT OUTER JOIN '. $db->quoteName('#__usergroups') .' T3
			ON T3.id = T1.usergroup
		WHERE
			'.$where.$orderList;
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

// ADMIN VIEW
$adminView = array();
$adminView['head']['info'] = $adminView['head']['actions'] = '';
if($hasAdmin) :
	$adminView['head']['info'] = '
		<th width="30" class="d-print-none"><input type="checkbox" id="'.$APPTAG.'_checkAll" /></th>
		<th width="50" class="d-none d-lg-table-cell d-print-none">'.baseAppHelper::linkOrder('#', 'T1.id', $APPTAG).'</th>
	';
	$adminView['head']['actions'] = '
		<th class="text-center d-none d-lg-table-cell d-print-none" width="60">'.baseAppHelper::linkOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
		<th width="100" class="text-center d-print-none">'.JText::_('TEXT_ACTIONS').'</th>
	';
endif;

// VIEW
$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<table class="table table-striped table-hover table-sm">
			<thead>
				<tr>
					'.$adminView['head']['info'].'
					<th>'.JText::_('FIELD_LABEL_NAME').'</th>
					<th width="30" class="d-none d-lg-table-cell text-center">&#160;</td>
					<th>'.JText::_('TEXT_TYPE').'</th>
					<th>'.JText::_('TEXT_STATUS').'</th>
					<th width="120" class="d-none d-lg-table-cell">'.JText::_('TEXT_CREATED_DATE').'</th>
					'.$adminView['head']['actions'].'
				</tr>
			</thead>
			<tbody>
';

if($num_rows) : // verifica se existe

	// pagination
	$db->setQuery('SELECT FOUND_ROWS();');  //no reloading the query! Just asking for total without limit
	jimport('joomla.html.pagination');
	$found_rows = $db->loadResult();
	$pageNav = new JPagination($found_rows , $lim0, $lim );

	foreach($res as $item) {

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');

			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 32, 32).'" class="d-none d-md-inline img-fluid rounded-circle float-left mr-2" />';

			// Arquivos -> Grupo de imagens ('#'.$APPTAG.'-files-group')
			// Obs: para pegar todas as imagens basta remover o 'grupo' ('#'.$APPTAG.'-files-group')
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id, '#'.$APPTAG.'-files-group');
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
		if($hasAdmin) :
			$adminView['list']['info'] = '
				<td class="check-row d-print-none"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" /></td>
				<td class="d-none d-lg-table-cell d-print-none">'.$item->id.'</td>
			';
			$adminView['list']['actions'] = '
				<td class="text-center d-none d-lg-table-cell d-print-none">
					<a href="#" class="hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span>
					</a>
				</td>
				<td class="text-center d-print-none">
					<a href="#" class="btn btn-xs btn-success hasTooltip" title="'.JText::_('TEXT_PHONES').'" onclick="phones_listReload(false, false, false, true, \'client_id\', '.$item->id.')" data-toggle="modal" data-target="#modal-list-phones"><span class="base-icon-phone"></span></a>
					<a href="#" class="btn btn-xs btn-warning hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span></a>
					<a href="#" class="btn btn-xs btn-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash"></span></a>
				</td>
			';
		endif;

		if($item->access == 0) :
			$reason = !empty($item->reasonStatus) ? '<div class="small text-muted text-truncate">'.$item->reasonStatus.'</div>' : '';
			// Check if user exist
			if(empty($item->user)) $status = '<span class="base-icon-attention text-live"> '.JText::_('TEXT_PENDING').'</span>';
			else $status = '<span class="base-icon-attention text-live"> '.JText::_('TEXT_BLOCKED').'</span>';
			$status .= $reason;
		else :
			// Check if user exist
			if(empty($item->user)) $status = '<span class="base-icon-cancel text-danger"> '.JText::_('TEXT_NO_USER_ASSOC').'</span><div class="small text-muted text-truncate">'.JText::_('TEXT_NO_USER_ASSOC_DESC').'</div>';
			else $status = '<span class="base-icon-ok text-success"> '.JText::_('TEXT_APPROVED').'</span>';
		endif;
		$urlViewData = JURI::root().'apps/clients/ficha?uID='.$item->id.'&sUD=on';
		$status = $item->state == 0 ? '<span class="base-icon-attention text-live"> '.JText::_('TEXT_BLOCKED').'</span>' : $status;
		$rowState	= $item->state == 0 ? 'table-danger' : '';
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
				<td>'.$img.$item->name.'<div class="small text-muted">'.$item->cx_role.' - '.$item->cx_situated.'</td>
				<td class="d-none d-lg-table-cell text-center"><a href="'.$urlViewData.'" target="_blank" class="base-icon-doc-text hasTooltip" title="'.JText::_('TEXT_VIEW_CLIENT_DATA').'"></a></td>
				<td>'.$item->type.'</td>
				<td>'.$status.'</td>
				<td class="d-none d-lg-table-cell">
					'.baseHelper::dateFormat($item->created_date, 'd/m/Y').'
					<a href="#" class="base-icon-info-circled setPopover" title="'.JText::_('TEXT_REGISTRATION_INFO').'" data-content="'.$regInfo.'" data-placement="top"></a>
				</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="9">
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

return $htmlFilter.$html;

?>
