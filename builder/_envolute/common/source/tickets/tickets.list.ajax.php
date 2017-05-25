<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	// load Joomla's framework
	require_once('../load.joomla.php');
	$app = JFactory::getApplication('site');

	defined('_JEXEC') or die;
	$ajaxRequest = true;
	require('config.php');
	// IMPORTANTE: Carrega o arquivo 'helper' do template
	JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

	// Carrega o arquivo de tradução
	// OBS: para arquivos externos com o carregamento do framework 'load.joomla.php' (geralmente em 'ajax')
	// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
	// Para possibilitar o carregamento da language 'default' de forma dinâmica,
	// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
	if(isset($_SESSION[$APPTAG.'langDef']))
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);

	//joomla get request data
	$input      = $app->input;

	// params requests
	$APPTAG			= $input->get('aTag', $APPTAG, 'str');
	$RTAG				= $input->get('rTag', $APPTAG, 'str');
	$oCHL				= $input->get('oCHL', 0, 'bool');
	$oCHL				= $_SESSION[$RTAG.'OnlyChildList'] ? $_SESSION[$RTAG.'OnlyChildList'] : $oCHL;
	$rNID       = $input->get('rNID', '', 'str');
	$rNID				= !empty($_SESSION[$RTAG.'RelListNameId']) ? $_SESSION[$RTAG.'RelListNameId'] : $rNID;
	$rID      	= $input->get('rID', 0, 'int');
	$rID				= !empty($_SESSION[$RTAG.'RelListId']) ? $_SESSION[$RTAG.'RelListId'] : $rID;

	// get current user's data
	$user = JFactory::getUser();
	$groups = $user->groups;

	// verifica o acesso
	$hasGroup = array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
	$hasAdmin = array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos

	// database connect
	$db = JFactory::getDbo();

	// GET DATA
	$noReg = true;
	$query = '
	SELECT
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
		'. $db->quoteName('T4.name') .' user'
	;
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_departments') .' T2
				ON T2.id = T1.department_id
				JOIN '. $db->quoteName('#__envolute_projects') .' T3
				ON T3.id = T1.project_id
				JOIN '. $db->quoteName('#__users') .' T4
				ON T4.id = T1.created_by
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T5
				ON '.$db->quoteName('T5.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T5.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID .'
				AND T1.status < 2'
			;
		else :
			$query .= '
			FROM
				'. $db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_departments') .' T2
				ON T2.id = T1.department_id
				JOIN '. $db->quoteName('#__envolute_projects') .' T3
				ON T3.id = T1.project_id
				JOIN '. $db->quoteName('#__users') .' T4
				ON T4.id = T1.created_by
			WHERE '. $db->quoteName($rNID) .' = '. $rID .' AND T1.status < 2';
		endif;
	else :
		$query .= '
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_departments') .' T2
			ON T2.id = T1.department_id
			JOIN '. $db->quoteName('#__envolute_projects') .' T3
			ON T3.id = T1.project_id
			JOIN '. $db->quoteName('#__users') .' T4
			ON T4.id = T1.created_by';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		else :
			$query .= ' WHERE T1.status = 0';
		endif;
	endif;
	$query .= ' ORDER BY '. $db->quoteName('T1.alter_date') .' DESC, '. $db->quoteName('T1.created_date') .' DESC, '. $db->quoteName('T1.priority') .' DESC';
	try {

		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();

	} catch (RuntimeException $e) {
		 echo $e->getMessage();
		 return;
	}

	$html = '<span class="ajax-loader hide"></span>';

	if($num_rows) : // verifica se existe
		$html .= '<ul class="list list-striped list-hover">';
		foreach($res as $item) {

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
				$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
				$listFiles = '';
				for($i = 0; $i < count($files[$item->id]); $i++) {
					if(!empty($files[$item->id][$i]->filename)) :
						$listFiles .= '
							<a href="'.$_root.'get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
								<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
							</a>
						';
					endif;
				}
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
			$type = '<span class="'.$sClass.' cursor-help hasTooltip" title="'.$stext.'"></span> ';
			// priority
			switch ($item->priority) {
				case '0':
					$ptext = JText::_('FIELD_LABEL_PRIORITY').' '.JText::_('FIELD_LABEL_LOW');
					$pClass = '';
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
			$priority = !empty($pClass) ? '<span class="'.$pClass.' cursor-help hasTooltip" title="'.$ptext.'"></span> ' : '';
			$subject = '<a href="#" class="setPopover" data-content="<small class=\'font-featured\'>'.$item->description.'</small>">'.$item->subject.'</a>';
			$status = $item->status == 1 ? '<span class="base-icon-ok text-success cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_CLOSED').'"></span>' : '<span class="base-icon-off text-success cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_OPENED').'"></span>';
			$department = '<span class="base-icon-location cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DEPARTMENT').'"> '.baseHelper::nameFormat($item->department).'</span>';
			$user = '<span class="base-icon-user left-space-xs cursor-help hasTooltip" title="'.JText::_('TEXT_USER').'"> '.baseHelper::nameFormat($item->user).'</span>';
			$btnState = $hasAdmin ? '<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'"><span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span></a> ' : '';
			$btnEdit = $hasAdmin ? '<a href="#" class="base-icon-pencil text-live hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"></a> ' : '';
			$btnDelete = $hasAdmin ? '<a href="#" class="base-icon-trash text-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"></a>' : '';
			$rowState = $item->state == 0 ? 'danger' : '';
			$html .= '
				<li class="'.$rowState.'">
					<span class="pull-right">'.$btnState.$btnEdit.$btnDelete.'</span>
					'.$priority.$type.$subject.'
					<div class="text-xs text-muted font-featured clearfix">
						'.$department.'
						<a href="#" class="base-icon-comment-empty left-space-xs text-live" onclick="comments_listReload(false, false, false, false, false, '.$item->id.')" data-toggle="modal" data-target="#modal-list-comments"> '.JText::_('TEXT_COMMENTS').$comments.'</a>
						'.(!empty($listFiles) ? '<span class="left-space-xs">'.$listFiles.'</span>' : '').'
						<span class="base-icon-calendar left-space-xs"><span> '.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').' '.$user.'
					</div>
				</li>
			';
		}
		$html .= '</ul>';
	else :
		if($noReg) $html = '<p class="base-icon-info-circled alert alert-info no-margin"> '.JText::_('MSG_LISTNOREG').'</p>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
