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
		'. $db->quoteName('T1.task_id') .',
		'. $db->quoteName('T2.title') .' task,
		'. $db->quoteName('T2.price') .' taskPrice,
		'. $db->quoteName('T3.name') .' user,
		'. $db->quoteName('T4.price') .' servicePrice,
		'. $db->quoteName('T4.price_fixed') .' servicePrice,
		'. $db->quoteName('T7.name') .' service,
		'. $db->quoteName('T5.id') .' project_id,
		'. $db->quoteName('T5.name') .' project,
		'. $db->quoteName('T5.price_local') .' priceLocal,
		'. $db->quoteName('T5.price_remote') .' priceRemote,
		'. $db->quoteName('T6.name') .' client,
		'. $db->quoteName('T1.work_location') .',
		'. $db->quoteName('T1.date') .',
		'. $db->quoteName('T1.start_hour') .',
		'. $db->quoteName('T1.end_hour') .',
		'. $db->quoteName('T1.time') .',
		'. $db->quoteName('T1.total_time') .',
		'. $db->quoteName('T1.hours') .',
		'. $db->quoteName('T1.billable') .',
		'. $db->quoteName('T1.billed') .',
		'. $db->quoteName('T1.billed_date') .',
		'. $db->quoteName('T1.note') .',
		'. $db->quoteName('T1.state')
	;
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName('#__envolute_tasks') .' T2
				ON T2.id = T1.task_id
				LEFT JOIN '. $db->quoteName('#__users') .' T3
				ON T3.id = T1.user_id
				JOIN '. $db->quoteName('#__envolute_services') .' T4
				ON T4.id = T2.service_id
				JOIN '. $db->quoteName('#__envolute_projects') .' T5
				ON T5.id = T4.project_id
				JOIN '. $db->quoteName('#__envolute_clients') .' T6
				ON T6.id = T5.client_id
				JOIN '. $db->quoteName('#__envolute_clients') .' T7
				ON T7.id = T6.type
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T8
				ON '.$db->quoteName('T8.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T2.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= '
			FROM
				'. $db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName('#__envolute_tasks') .' T2
				ON T2.id = T1.task_id
				LEFT JOIN '. $db->quoteName('#__users') .' T3
				ON T3.id = T1.user_id
				JOIN '. $db->quoteName('#__envolute_services') .' T4
				ON T4.id = T2.service_id
				JOIN '. $db->quoteName('#__envolute_projects') .' T5
				ON T5.id = T4.project_id
				JOIN '. $db->quoteName('#__envolute_clients') .' T6
				ON T6.id = T5.client_id
				JOIN '. $db->quoteName('#__envolute_clients') .' T7
				ON T7.id = T6.type
			WHERE '. $db->quoteName($rNID) .' = '. $rID;
		endif;
	else :
		$query .= '
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__envolute_tasks') .' T2
			ON T2.id = T1.task_id
			LEFT JOIN '. $db->quoteName('#__users') .' T3
			ON T3.id = T1.user_id
			JOIN '. $db->quoteName('#__envolute_services') .' T4
			ON T4.id = T2.service_id
			JOIN '. $db->quoteName('#__envolute_projects') .' T5
			ON T5.id = T4.project_id
			JOIN '. $db->quoteName('#__envolute_clients') .' T6
			ON T6.id = T5.client_id
			JOIN '. $db->quoteName('#__envolute_clients') .' T7
			ON T7.id = T6.type
		';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		endif;
	endif;
	$query .= ' ORDER BY '. $db->quoteName('T1.date') .' DESC, '. $db->quoteName('T1.task_id') .' ASC';
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

			$task = '<span class="strong cursor-help hasPopover" data-placement="top" data-content="<strong>'.JText::_('FIELD_LABEL_SERVICE').'</strong><div class=\'small\'>'.baseHelper::nameFormat($item->service).'</div><strong>'.JText::_('FIELD_LABEL_PROJECT').'</strong><div class=\'small\'>'.baseHelper::nameFormat($item->project).'</div>">#'.$item->task_id.' - '.baseHelper::nameFormat($item->task).'</span>';
			$billed = ($item->billed == 1) ? '<span class="base-icon-money text-success hasTooltip" title="'.JText::_('FIELD_LABEL_BILLED').'"></span>' : '';
			$pHour = $item->servicePrice != '0.00' ? $item->servicePrice : (isset($_SESSION['default_price_hour']) ? $_SESSION['default_price_hour'] : '0.00');
			$price_hour = '<span class="cursor-help hasTooltip" title="'.($item->servicePrice != '0.00' ? JText::_('MSG_SERVICE_PRICE_HOUR').'">'.baseHelper::priceFormat($pHour) : JText::_('MSG_DEFAULT_PRICE_HOUR').'">'.(isset($_SESSION['default_price_hour']) ? baseHelper::priceFormat($pHour) : '-')).'</span>';
			if($item->taskPriceFixed != '0.00') :
				$price = $item->taskPriceFixed;
				$taskPrice = ' &raquo; R$ <span class="text-live">'.baseHelper::priceFormat($price).'</span> <span class="text-xs cursor-help hasTooltip" title="'.JText::_('MSG_TASK_PRICE_FIXED').'">('.strtolower(JText::_('TEXT_FIXED_ABBR')).')</span>';
			elseif($item->servicePriceFixed == 1) :
				$price = $item->servicePrice;
				$taskPrice = ' &raquo; R$ <span class="text-live">'.baseHelper::priceFormat($price).'</span> <span class="text-xs cursor-help hasTooltip" title="'.JText::_('MSG_SERVICE_PRICE_FIXED').'">('.strtolower(JText::_('TEXT_FIXED_ABBR')).')</span>';
			else :
				$price = ($item->hours * $pHour);
				$taskPrice = '(x '.$price_hour.') &raquo; R$ <span class="text-live">'.baseHelper::priceFormat($price).'</span>';
			endif;
			if($item->billable == 0) $taskPrice = $billed = '';
			$taskUser = !empty($item->user) ? ' <span class="base-icon-user text-live cursor-help hasTooltip" title="'.JText::_('TEXT_REGISTERED_BY').'<br />'.baseHelper::nameFormat($item->user).'"></span>' : '';
			$btnState = $hasAdmin ? '<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'"><span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span></a> ' : '';
			$btnEdit = $hasAdmin ? '<a href="#" class="base-icon-pencil text-live hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"></a> ' : '';
			$btnDelete = $hasAdmin ? '<a href="#" class="base-icon-trash text-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"></a>' : '';
			$rowState = $item->state == 0 ? 'danger' : '';
			$html .= '
				<li class="'.$rowState.'">
					<div class="clearfix">
						<span class="pull-right">'.$btnState.$btnEdit.$btnDelete.'</span>
						'.$task.'
					</div>
					<div>
						<small class="text-muted font-featured">
							<span class="base-icon-calendar cursor-help hasTooltip" title="'.JText::_('MSG_DATA_ACTIVITY').'"></span> '.baseHelper::dateFormat($item->date, 'd/m/Y').$taskUser.' &raquo; <span class="base-icon-clock cursor-help hasTooltip" title="'.JText::_('MSG_TIME_WORKED').'"></span> '.substr($item->total_time, 0, 5).' '.$taskPrice.' '.$billed.'
						</small>
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
