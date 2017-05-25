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
		'. $db->quoteName('T2.name') .' category,
		'. $db->quoteName('T1.type') .' type,
		'. $db->quoteName('T1.url') .',
		'. $db->quoteName('T1.description') .',
		'. $db->quoteName('T1.paid') .',
		'. $db->quoteName('T1.period') .',
		'. $db->quoteName('T1.due_date') .',
		'. $db->quoteName('T1.currency') .',
		'. $db->quoteName('T1.price') .',
		'. $db->quoteName('T1.start_date') .',
		'. $db->quoteName('T1.user') .',
		'. $db->quoteName('T1.password') .',
		'. $db->quoteName('T1.note') .',
		'. $db->quoteName('T1.state')
	;
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_categories') .' T2
				ON T2.id = T1.category_id
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T3
				ON '.$db->quoteName('T3.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T3.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= '
			FROM
				'. $db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_categories') .' T2
				ON T2.id = T1.category_id
			WHERE '. $db->quoteName($rNID) .' = '. $rID .' AND T1.status < 2';
		endif;
	else :
		$query .= '
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_categories') .' T2
			ON T2.id = T1.category_id';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		endif;
	endif;
	$query .= ' ORDER BY '. $db->quoteName('T1.start_date') .' DESC';
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

			switch ($item->period) {
				case '1':
					$period = JText::_('FIELD_LABEL_MONTH');
					break;
				case '2':
					$period = JText::_('FIELD_LABEL_QUARTERLY');
					break;
				case '3':
					$period = JText::_('FIELD_LABEL_SEMESTER');
					break;
				case '4':
					$period = JText::_('FIELD_LABEL_YEARLY');
					break;
				default:
					$period = '-';
			}
			switch ($item->currency) {
				case 'USD':
					$currency = '&euro; ';
					break;
				case 'EUR':
					$currency = 'U$ ';
					break;
				default:
					$currency = 'R$ ';
			}
			$paid = '';
			if($item->paid == 1) :
				$paid = (!empty($item->price) && $item->price != '0.00') ? '<strong>'.JText::_('FIELD_LABEL_PRICE').'</strong>: '.baseHelper::priceFormat($item->price, false, $currency, false) : '';
				$paid .= !empty($item->due_date) ? '<br /><strong>'.JText::_('FIELD_LABEL_DUE_DATE').'</strong>: '.$item->due_date : '';
				$paid .= $item->start_date != '0000-00-00' ? '<br /><strong>'.JText::_('FIELD_LABEL_START_DATE').'</strong>: '.baseHelper::dateFormat($item->start_date, 'd/m/Y') : '';
				$paid = '<span class="base-icon-money text-success cursor-help right-space-sm hasPopover" title="<strong>'.$period.'</strong>" data-content="<small>'.$paid.'</small>"></span>';
			endif;
			$access = '';
			if(!empty($item->user) || !empty($item->password)) :
				$access = !empty($item->user) ? '<strong>'.JText::_('TEXT_USER').'</strong>: '.$item->user : '';
				$access .= !empty($item->password) ? '<br /><strong>'.JText::_('FIELD_LABEL_PASSWORD').'</strong>: '.$item->password : '';
				$access = '<span class="base-icon-lock text-live cursor-pointer right-space-sm" data-toggle="popover" data-content="<small>'.$access.'</small>"></span>';
			endif;
			$type = '<span class="label label-'.($item->type == 0 ? 'warning' : 'success').'">'.JText::_('FIELD_LABEL_TYPE_'.($item->type == 0 ? 'LINK' : 'SERVICE')).'</span> ';
			$note = !empty($item->note) ? ' <div class="small text-muted font-featured">'.$item->note.'</div>' : '';
			$btnState = $hasAdmin ? '<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'"><span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span></a> ' : '';
			$btnEdit = $hasAdmin ? '<a href="#" class="base-icon-pencil text-live hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"></a> ' : '';
			$btnDelete = $hasAdmin ? '<a href="#" class="base-icon-trash text-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"></a>' : '';
			$rowState = $item->state == 0 ? 'danger' : '';
			$html .= '
				<li class="'.$rowState.'">
					<span class="pull-right">'.$btnState.$btnEdit.$btnDelete.'</span>
					'.$type.'<a href="'.$item->url.'" target="_blank">'.$item->description.'</a>
					<div class="text-muted font-featured clearfix">
						'.$note.$paid.$access.'
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
