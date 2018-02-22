<?php
// BLOCK DIRECT ACCESS
if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) AND strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest") :

	// load Joomla's framework
	// _DIR_ => apps/THIS_APP
	require(__DIR__.'/../../libraries/envolute/_init.joomla.php');
	$app = JFactory::getApplication('site');
	defined('_JEXEC') or die;

	$ajaxRequest = true;
	require('config.php');

	// IMPORTANTE: Carrega o arquivo 'helper' do template
	JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');

	//joomla get request data
	$input		= $app->input;

	// params requests
	$APPTAG		= $input->get('aTag', $APPTAG, 'str');
	$RTAG		= $input->get('rTag', $APPTAG, 'str');
	$aFLT		= $input->get('aFTL', 0, 'bool'); // ajax filter
	$oCHL		= $input->get('oCHL', 0, 'bool');
	$oCHL		= $_SESSION[$RTAG.'OnlyChildList'] ? $_SESSION[$RTAG.'OnlyChildList'] : $oCHL;
	$rNID		= $input->get('rNID', '', 'str');
	$rNID		= !empty($_SESSION[$RTAG.'RelListNameId']) ? $_SESSION[$RTAG.'RelListNameId'] : $rNID;
	$rID		= $input->get('rID', 0, 'int');
	$rID		= !empty($_SESSION[$RTAG.'RelListId']) ? $_SESSION[$RTAG.'RelListId'] : $rID;

	// Carrega o arquivo de tradução
	// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
	// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
	// Para possibilitar o carregamento da language 'default' de forma dinâmica,
	// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
	if(isset($_SESSION[$APPTAG.'langDef'])) :
		$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
		$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	endif;

	// get current user's data
	$user		= JFactory::getUser();
	$groups		= $user->groups;

	// verifica o acesso
	$hasGroup	= array_intersect($groups, $cfg['groupId']['viewer']); // se está na lista de grupos permitidos
	$hasAdmin	= array_intersect($groups, $cfg['groupId']['admin']); // se está na lista de administradores permitidos

	// database connect
	$db		= JFactory::getDbo();

	// LOAD FILTER
	$fQuery = $PATH_APP_FILE.'.filter.query.php';
	if($aFLT && file_exists($fQuery)) require($fQuery);

	// GET DATA
	$noReg	= true;
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T1.name') .',
			'. $db->quoteName('T2.name') .' grp,
			'. $db->quoteName('T2.overtime') .',
			IF('.$db->quoteName('T1.end_date').' <= NOW() && '. $db->quoteName('T2.overtime') .' > 0, 1, 0) finished
	';
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
				ON '.$db->quoteName('T2.id') .' = T1.group_id
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T3
				ON '.$db->quoteName('T3.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T4.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= ' FROM '. $db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
				ON '.$db->quoteName('T2.id') .' = T1.group_id
				WHERE '. $db->quoteName($rNID) .' = '. $rID
			;
		endif;
	else :
		$query .= ' FROM '. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.group_id
		';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		endif;
	endif;
	$query	.= ' ORDER BY '. $db->quoteName('T1.name') .' ASC';
	try {
		$db->setQuery($query);
		$db->execute();
		$num_rows = $db->getNumRows();
		$res = $db->loadObjectList();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	$html = '';
	if($num_rows) : // verifica se existe
		$html .= '<ul class="set-list list-lg bordered">';
		foreach($res as $item) {

			// só permite a impressão da carteira de associado com foto
			$printCard = '<button type="button" class="btn btn-xs btn-default base-icon-print ml-2 hasTooltip disabled" title="'.JText::_('MSG_CARD_NO_PHOTO').'"></button>';

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');

				// Imagem Principal -> Primeira imagem (index = 0)
				$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
				if(!empty($img)) :
					$imagePath = baseHelper::thumbnail($_ROOT.'images/apps/'.$APPPATH.'/'.$img['filename'], 32, 32);
					$printCard = '<button type="button" class="btn btn-xs btn-outline-primary base-icon-print ml-2 hasTooltip" title="'.JText::_('TEXT_CLIENT_CARD').'" onclick="'.$APPTAG.'_printCard('.$item->id.')"></button>';
				else :
					$imagePath = $_ROOT.'images/template/'.($item->gender == 1 ? 'man' : 'woman').'.png';
				endif;
				$img = '<img src="'.$imagePath.'" style="width:32px; height:32px;" class="d-none d-md-inline img-fluid rounded-circle float-left mr-2" />';

				// Arquivos -> Grupo de imagens ('#'.$APPTAG.'-files-group')
				// Obs: para pegar todas as imagens basta remover o 'grupo' ('#'.$APPTAG.'-files-group')
				$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id, '#'.$APPTAG.'-files-group');
				$listFiles = '';
				for($i = 0; $i < count($files[$item->id]); $i++) {
					if(!empty($files[$item->id][$i]->filename)) :
						$listFiles .= '
							<a href="'.$_ROOT.'apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
								<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
							</a>
						';
					endif;
				}
			endif;

			$inactive = ($item->state == 0) ? ' <span class="badge badge-danger">'.JText::_('TEXT_INACTIVE').'</span>' : '';
			$name = baseHelper::nameFormat($item->name).$inactive;
			$limite = '';
			if($item->overtime > 0) :
				$end_date = baseHelper::dateFormat($item->end_date);
				if($item->finished == 1) :
					$name = '<span class="base-icon-cancel" style="text-decoration:line-through"> '.$name.'</span>';
					$limite = ' &raquo; <span class="text-danger cursor-help hasTooltip" title="'.JText::sprintf('MSG_DEPENDENT_FINISHED', $item->overtime, $item->grp).'"><span class="base-icon-attention text-live"></span> '.JText::_('TEXT_FINISHED').': '.$end_date.'</span>';
				else :
					$limite = ' &raquo; <span class="text-success cursor-help hasTooltip" title="'.JText::sprintf('MSG_DEPENDENT_PERIOD', $item->overtime, $item->grp).'">'.JText::_('TEXT_DUE_DATE_ABBR').': '.$end_date.'</span>';
				endif;
			endif;
			$docs = '';
			if($item->docs == 0) :
				$docs = '<small class="text-danger"><span class="base-icon-attention text-live"></span> '.JText::_('MSG_NO_DOCUMENTS').'</small>';
			endif;
			$email = !empty($item->email) ? '<div class="text-sm text-muted mt-2 base-icon-email"> '.$item->email.'</div>' : '';
			// Phones
			$phones = '';
			$ph = explode(';', $item->phone);
			if(!empty($item->phone) && $item->phone != ';') :
				$wp = explode(';', $item->whatsapp);
				$pd = explode(';', $item->phone_desc);
				$phones .= '<ul class="set-list inline bordered text-sm text-muted mt-2 list-trim"> ';
				for($i = 0; $i < count($ph); $i++) {
					$whapps = $wp[$i] == 1 ? ' <span class="base-icon-whatsapp text-success cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_WHATSAPP').'"></span>' : '';
					$phDesc = !empty($pd[$i]) ? '<br /><small>'.$pd[$i].'</small>' : '';
					$phones .= '<li>'.$ph[$i].$whapps.$phDesc.'</li>';
				}
				$phones .= '</ul>';
			endif;
			$note = !empty($item->note) ? '<div class="text-sm text-live mt-2 base-icon-info-circled"> '.$item->note.'</div>' : '';

			$btnState = $hasAdmin ? '<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'"><span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span></a> ' : '';
			$btnEdit = $hasAdmin ? '<a href="#" class="base-icon-pencil text-live ml-1 hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"></a> ' : '';
			$btnDelete = $hasAdmin ? '<a href="#" class="base-icon-trash text-danger ml-1 hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"></a>' : '';
			$rowState = ($item->state == 0 || $item->finished == 1) ? ' text-danger' : '';
			$html .= '
				<li class="'.$rowState.'">
					<div class="float-right">'.$btnState.$btnEdit.$btnDelete.$printCard.'</div>
					'.$img.$name.'
					<div class="small">
						'.baseHelper::nameFormat($item->grp).' - '.JText::_('TEXT_BIRTHDAY_ABBR').' '.baseHelper::dateFormat($item->birthday).$limite.'
					</div>
					'.$docs.$email.$phones.$note.'
				</li>
			';
		}
		$html .= '</ul>';
	else :
		if($noReg) $html = '<div class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_LISTNOREG').'</div>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
