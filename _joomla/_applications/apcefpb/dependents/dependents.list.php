<?php
defined('_JEXEC') or die;

// LIST

	// PAGINATION VAR's
	require(JPATH_CORE.DS.'apps/layout/list/pagination.vars.php');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			T1.*,
			'. $db->quoteName('T3.name') .' client,
			'. $db->quoteName('T2.name') .' grp,
			'. $db->quoteName('T2.overtime') .',
			IF('.$db->quoteName('T1.end_date').' <= NOW() && '. $db->quoteName('T2.overtime') .' > 0, 1, 0) finished
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON T2.id = T1.group_id
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T3
			ON T3.id = T1.client_id
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
					<th width="30" class="d-print-none"><input type="checkbox" id="'.$APPTAG.'_checkAll" /></th>
					<th width="50" class="d-none d-lg-table-cell d-print-none">'.baseAppHelper::linkOrder('#', 'T1.id', $APPTAG).'</th>
				';
				$adminView['head']['actions'] = '
					<th class="text-center d-none d-lg-table-cell d-print-none" width="60">'.baseAppHelper::linkOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
					<th width="70" class="text-center d-print-none">'.JText::_('TEXT_ACTIONS').'</th>
				';
			endif;

			// VIEW
			$html = '
				<form id="form-list-'.$APPTAG.'" method="post" class="pt-3">
					<table class="table table-striped table-hover table-sm">
						<thead>
							<tr>
								'.$adminView['head']['info'].'
								<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_NAME'), 'T1.name', $APPTAG).'</th>
								<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_CLIENT'), 'T3.name', $APPTAG).'</th>
								<th>'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_BIRTHDAY'), 'T2.name', $APPTAG).'</th>
								<th class="d-none d-lg-table-cell text-center">'.baseAppHelper::linkOrder(JText::_('FIELD_LABEL_DOCUMENTS'), 'T1.docs', $APPTAG).'</th>
								'.$adminView['head']['actions'].'
							</tr>
						</thead>
						<tbody>
			';
		}

		// só permite a impressão da carteira de associado com foto
		$printCard = '<button type="button" class="btn btn-xs btn-default base-icon-print hasTooltip disabled" title="'.JText::_('MSG_CARD_NO_PHOTO').'"></button>';

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');

			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
			if(!empty($img)) {
				$img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 34, 34).'" class="d-none d-md-inline img-fluid rounded float-left mr-2" />';
				$printCard = '<button type="button" class="btn btn-xs btn-outline-primary base-icon-print hasTooltip" title="'.JText::_('TEXT_CLIENT_CARD').'" onclick="'.$APPTAG.'_printCard('.$item->id.')"></button>';
			}

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
		if($canEdit) :
			$adminView['list']['info'] = '
				<td class="check-row d-print-none"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" /></td>
				<td class="d-none d-lg-table-cell d-print-none">'.$item->id.'</td>
			';
			$regInfo	= 'ID: <span class=&quot;text-live&quot;>#'.$item->id.'</span>';
			$regInfo	.= '<hr class=&quot;my-1&quot; />';
			$regInfo	.= JText::_('TEXT_CREATED_DATE').': '.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'<br />';
			$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->created_by)->name);
			if($item->alter_date != '0000-00-00 00:00:00') :
				$regInfo	.= '<hr class=&quot;my-1&quot; />';
				$regInfo	.= JText::_('TEXT_ALTER_DATE').': '.baseHelper::dateFormat($item->alter_date, 'd/m/Y H:i').'<br />';
				$regInfo	.= JText::_('TEXT_BY').': '.baseHelper::nameFormat(JFactory::getUser($item->alter_by)->name);
			endif;
			$adminView['list']['actions'] = '
				<td class="text-center d-none d-lg-table-cell d-print-none">
					<a href="#" class="hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span>
					</a>
				</td>
				<td class="text-center d-print-none">
					'.$printCard.'
					<a href="#" class="btn btn-xs btn-outline-primary base-icon-info-circled hasPopover" title="'.JText::_('TEXT_REGISTRATION_INFO').'" data-content="'.$regInfo.'" data-placement="top" data-trigger="click focus"></a>
					<a href="#" class="btn btn-xs btn-warning mt-1 hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span></a>
					<a href="#" class="btn btn-xs btn-danger mt-1 hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash"></span></a>
				</td>
			';
		endif;

		$gender = '<span '.($item->gender == 1 ? 'class="base-icon-male-symbol cursor-help text-primary hasTooltip" title="'.JText::_('TEXT_MALE').'"' : 'class="base-icon-female-symbol cursor-help text-pink hasTooltip" title="'.JText::_('TEXT_FEMALE').'"').'"></span> ';
		$email = !empty($item->email) ? ' <span class="small font-featured text-muted">('.$item->email.')</span>' : '';
		$docs = $item->docs == 0 ? '<span class="base-icon-cancel text-danger cursor-help hasTooltip" title="'.JText::_('MSG_NO_DOCUMENTS').'"></span>' : '<span class="base-icon-ok text-success cursor-help hasTooltip" title="'.JText::_('MSG_DOCUMENTS').'"></span>';
		$limite = '';
		if($item->overtime > 0) :
			$end_date = baseHelper::dateFormat($item->end_date);
			if($item->finished == 1) :
				$limite = '<br /><small class="text-danger cursor-help hasTooltip" title="'.JText::sprintf('MSG_DEPENDENT_FINISHED', $item->overtime, $item->grp).'"><span class="base-icon-attention text-live"></span> '.JText::_('TEXT_FINISHED').': '.$end_date.'</small>';
			else :
				$limite = '<br /><small class="text-success cursor-help hasTooltip" title="'.JText::sprintf('MSG_DEPENDENT_PERIOD', $item->overtime, $item->grp).'">'.JText::_('TEXT_DUE_DATE_ABBR').': '.$end_date.'</small>';
			endif;
		endif;
		$rowState = ($item->state == 0 || $item->finished == 1) ? 'table-danger' : '';
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
					'.$img.baseHelper::nameFormat($item->name).'
					<div class="small mt-1">
						'.$gender.baseHelper::nameFormat($item->grp).' '.$email.'
					</div>
				</td>
				<td class="d-none d-lg-table-cell">'.baseHelper::nameFormat($item->client).'</td>
				<td class="d-none d-lg-table-cell">
					'.baseHelper::dateFormat($item->birthday).$limite.'
				</td>
				<td class="text-center">'.$docs.'</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="8">
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
