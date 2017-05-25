<?php
defined('_JEXEC') or die;

// LOAD FILTER
require($APPNAME.'.filter.php');

// LIST

	// pagination var's
	$limitDef = !isset($_SESSION[$APPTAG.'plim']) ? $cfg['pagLimit'] : $_SESSION[$APPTAG.'plim'];
	$_SESSION[$APPTAG.'plim']	= $app->input->post->get('list-lim-'.$APPTAG, $limitDef, 'int');
	$lim	= $app->input->get('limit', ($_SESSION[$APPTAG.'plim'] !== 1 ? $_SESSION[$APPTAG.'plim'] : 10000000), 'int');
	$lim0	= $app->input->get('limitstart', 0, 'int');

	$query = '
		SELECT SQL_CALC_FOUND_ROWS
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T3.name') .' project,
			'. $db->quoteName('T2.name') .' department,
			'. $db->quoteName('T1.type') .',
			'. $db->quoteName('T1.subject') .',
			'. $db->quoteName('T1.description') .',
			'. $db->quoteName('T1.end_date') .',
			'. $db->quoteName('T1.priority') .',
			'. $db->quoteName('T1.status') .',
			'. $db->quoteName('T1.state') .',
			'. $db->quoteName('T1.created_date') .',
			'. $db->quoteName('T4.name') .' user
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_departments') .' T2
			ON T2.id = T1.department_id
			LEFT JOIN '. $db->quoteName('#__envolute_projects') .' T3
			ON T3.id = T1.project_id
			LEFT JOIN '. $db->quoteName('#__users') .' T4
			ON T4.id = T1.created_by
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
		<th width="30" class="hidden-print"><input type="checkbox" id="'.$APPTAG.'_checkAll" /></th>
		<th width="50" class="hidden-print">'.$$SETOrder('#', 'T1.id', $APPTAG).'</th>
	';
	$adminView['head']['actions'] = '
		<th class="text-center hidden-print" width="60">'.$$SETOrder(JText::_('TEXT_ACTIVE'), 'T1.state', $APPTAG).'</th>
		<th class="text-center hidden-print" width="100">'.JText::_('TEXT_ACTIONS').'</th>
	';
endif;

// VIEW
$html = '
	<form id="form-list-'.$APPTAG.'" method="post">
		<table class="table table-striped table-hover table-condensed">
			<thead>
				<tr>
					'.$adminView['head']['info'].'
					<th width="120">
						'.$$SETOrder(JText::_('FIELD_LABEL_DEPARTMENT'), 'T2.name', $APPTAG).'
						[ <small class="hasTooltip" title="'.JText::_('TEXT_ORDER_BY').'">'.$$SETOrder(JText::_('FIELD_LABEL_TYPE'), 'T1.type', $APPTAG).'</small> ]
					</th>
					<th class="text-center" width="40">'.$$SETOrder('<span class="base-icon-bell hasTooltip" title="'.JText::_('FIELD_LABEL_PRIORITY').'"></span>', 'T1.priority', $APPTAG).'</th>
					<th>
						'.JText::_('FIELD_LABEL_SUBJECT').'
						[ <small class="hasTooltip" title="'.JText::_('TEXT_ORDER_BY').'">'.$$SETOrder(JText::_('FIELD_LABEL_PROJECT'), 'T3.name', $APPTAG).'</small> ]
					</th>
					<th>'.JText::_('TEXT_ATTACHMENT').'s</th>
					<th width="150"><span class="base-icon-calendar"></span> '.$$SETOrder(JText::_('FIELD_LABEL_DATE'), 'T1.start_date', $APPTAG).'</th>
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
			JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$listFiles = '';
			for($i = 0; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					$listFiles .= '
						<a href="'.JURI::root(true).'/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
						</a>
					';
				endif;
			}
		endif;

		$adminView['list']['info'] = $adminView['list']['actions'] = '';
		if($hasAdmin) :
			$adminView['list']['info'] = '
				<td class="check-row hidden-print"><input type="checkbox" name="'.$APPTAG.'_ids[]" class="'.$APPTAG.'-chk" value="'.$item->id.'" /></td>
				<td class="hidden-print">'.$item->id.'</td>
			';
			$adminView['list']['actions'] = '
				<td class="text-center hidden-print">
					<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span>
					</a>
				</td>
				<td class="text-center hidden-print">
					<a href="#" class="base-icon-comment-empty btn btn-xs btn-info hasTooltip" onclick="comments_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-comments" title="'.JText::_('TEXT_COMMENTS').$comments.'"></a>
					<a href="#" class="btn btn-xs btn-warning" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil hasTooltip" title="'.JText::_('TEXT_EDIT').'"></span></a>
					<a href="#" class="btn btn-xs btn-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash hasTooltip" title="'.JText::_('TEXT_DELETE').'"></span></a>
				</td>
			';
		endif;

		// get comments
		$query = '
		SELECT COUNT(*) FROM '. $db->quoteName('#__envolute_rel_tickets_comments') .' WHERE '. $db->quoteName('ticket_id') .' = '. $item->id;
		$db->setQuery($query);
		$comments = $db->loadResult();
		$comments = $comments ? ' ('.$comments.')' : '';

		switch ($item->type) {
			case '0':
				$stext = JText::_('FIELD_LABEL_INFO_DESC');
				$slabel = JText::_('FIELD_LABEL_INFO');
				$sClass = 'base-icon-info-circled text-info';
				break;
			case '1':
				$stext = JText::_('FIELD_LABEL_HELP_DESC');
				$slabel = JText::_('FIELD_LABEL_HELP');
				$sClass = 'base-icon-lifebuoy text-live';
				break;
			case '2':
				$stext = JText::_('FIELD_LABEL_ISSUE_DESC');
				$slabel = JText::_('FIELD_LABEL_ISSUE');
				$sClass = 'base-icon-bug text-danger';
				break;
			case '3':
				$stext = JText::_('FIELD_LABEL_REQUEST_DESC');
				$slabel = JText::_('FIELD_LABEL_REQUEST');
				$sClass = 'base-icon-star text-live';
				break;
			default:
				$stext = '';
				$sClass = '';
		}
		$type = '<span class="'.$sClass.' cursor-help hasTooltip" title="'.$stext.'"><small class="text-muted">'.$slabel.'</small></span>';
		// priority
		switch ($item->priority) {
			case '0':
				$ptext = JText::_('FIELD_LABEL_PRIORITY').' '.JText::_('FIELD_LABEL_LOW');
				$pClass = 'base-icon-attention text-success';
				break;
			case '1':
				$ptext = JText::_('FIELD_LABEL_PRIORITY').' '.JText::_('FIELD_LABEL_MEDIUM');
				$pClass = 'base-icon-attention text-live';
				break;
			case '2':
				$ptext = JText::_('FIELD_LABEL_PRIORITY').' '.JText::_('FIELD_LABEL_HIGHT');
				$pClass = 'base-icon-attention text-danger';
				break;
			default:
				$ptext = '';
				$pClass = '';
		}
		$priority = '<span class="'.$pClass.' cursor-help hasTooltip" title="'.$ptext.'"></span>';
		$subject = '<a href="#" class="setPopover" data-content="<small class=\'font-featured\'>'.$item->description.'</small>">'.$item->subject.'</a>';
		$rowState = $item->state == 0 ? 'danger' : '';
		$html .= '
			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
				'.$adminView['list']['info'].'
				<td>
					'.baseHelper::nameFormat($item->department).'<br />
					'.$type.'
				</td>
				<td class="text-center">'.$priority.'</td>
				<td>
					'.$subject.'
					<br /><small class="text-live font-featured cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_PROJECT').'">'.baseHelper::nameFormat($item->project).'</small>
				</td>
				<td>'.$listFiles.'</td>
				<td>
					'.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'
					'.($item->end_date != '0000-00-00 00:00:00' ? '<br /><small class="base-icon-right-big text-danger font-featured cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_END_DATE').'">'.baseHelper::dateFormat($item->end_date, 'd/m/Y H:i', true, '-').'</small>' : '').'
				</td>
				'.$adminView['list']['actions'].'
			</tr>
		';
	}

else : // num_rows = 0

	$html .= '
		<tr>
			<td colspan="11">
				<div class="alert alert-warning alert-icon no-margin">'.JText::_('MSG_LISTNOREG').'</div>
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

	// PAGINAÇÃO
		// stats
		$listStart	= $lim0 + 1;
		$listEnd		= $lim0 + $num_rows;
	if($found_rows != $num_rows) :
		$html .= '
			<div class="base-app-pagination pull-left">
				'.$pageNav->getListFooter().'
				<div class="list-stats small text-muted">
					'.JText::sprintf('LIST_STATS', $listStart, $listEnd, $found_rows).'
				</div>
			</div>
		';
	endif;

	$html .= '
		<form id="form-order-'.$APPTAG.'" action="'.$_SERVER['REQUEST_URI'].'" class="pull-right form-inline" method="post">
			<input type="hidden" name="'.$APPTAG.'oF" id="'.$APPTAG.'oF" value="'.$_SESSION[$APPTAG.'oF'].'" />
			<input type="hidden" name="'.$APPTAG.'oT" id="'.$APPTAG.'oT" value="'.$_SESSION[$APPTAG.'oT'].'" />
		</form>
	';

	// ITENS POR PÁGINA
	// seta o parametro 'start = 0' na URL sempre que o limit for refeito
	// isso evita erro quando estiver navegando em páginas subjacentes
	$a = preg_replace("#\?start=.*#", '', $_SERVER['REQUEST_URI']);
	$a = preg_replace("#&start=.*#", '', $a);

	$html .= '
		<form id="form-limit-'.$APPTAG.'" action="'.$a.'" class="pull-right form-inline hidden-print" method="post">
			<label>'.JText::_('LIST_PAGINATION_LIMIT').'</label>
			<select name="list-lim-'.$APPTAG.'" onchange="'.$APPTAG.'_setListLimit()">
				<option value="5" '.($_SESSION[$APPTAG.'plim'] === 5 ? 'selected' : '').'>5</option>
				<option value="20" '.($_SESSION[$APPTAG.'plim'] === 20 ? 'selected' : '').'>20</option>
				<option value="50" '.($_SESSION[$APPTAG.'plim'] === 50 ? 'selected' : '').'>50</option>
				<option value="100" '.($_SESSION[$APPTAG.'plim'] === 100 ? 'selected' : '').'>100</option>
				<option value="1" '.($_SESSION[$APPTAG.'plim'] === 1 ? 'selected' : '').'>Todos</option>
			</select>
		</form>
	';

endif;

return $htmlFilter.$html;

?>
