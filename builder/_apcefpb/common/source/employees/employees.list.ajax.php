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
		'. $db->quoteName('T2.name') .' grp,
		'. $db->quoteName('T3.username') .' username,
		'. $db->quoteName('T1.name') .',
		'. $db->quoteName('T1.email') .',
		'. $db->quoteName('T1.gender') .',
		'. $db->quoteName('T1.birthday') .',
		'. $db->quoteName('T1.marital_status') .',
		'. $db->quoteName('T1.occupation') .',
		'. $db->quoteName('T1.state')
	;
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
				ON T2.id = T1.group_id
				LEFT JOIN '. $db->quoteName('#__users') .' T3
				ON T3.id = T1.user_id
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T4
				ON '.$db->quoteName('T4.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T4.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= ' FROM
				'. $db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
				ON T2.id = T1.group_id
				LEFT JOIN '. $db->quoteName('#__users') .' T3
				ON T3.id = T1.user_id
			WHERE '. $db->quoteName($rNID) .' = '. $rID;
		endif;
	else :
		$query .= ' FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON T2.id = T1.group_id
			LEFT JOIN '. $db->quoteName('#__users') .' T3
			ON T3.id = T1.user_id';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		endif;
	endif;
	$query .= ' ORDER BY '. $db->quoteName('T1.name') .' ASC';
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
						$listFiles = '/images/uploads/'.$APPNAME.'/'.$files[$item->id][$i]->filename;
					endif;
				}
			endif;

			$img = !empty($listFiles) ? '<img class="img-responsive pull-left right-space" src="'.baseHelper::thumbnail($listFiles, 36, 36).'" />' : '';
			$gender = '<span '.($item->gender == 1 ? 'class="base-icon-male-symbol cursor-help text-primary hasTooltip" title="'.JText::_('FIELD_LABEL_GENDER_MALE').'"' : 'class="base-icon-female-symbol cursor-help text-danger hasTooltip" title="'.JText::_('FIELD_LABEL_GENDER_FEMALE').'"').'"></span> ';
			$occupation = !empty($item->occupation) ? ' - <span class="cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_OCCUPATION').'">'.baseHelper::nameFormat($item->occupation).'</small>' : '';
			$info = '';
			// if(!empty($item->cx_matricula)) :
			// 	$info = !empty($item->cx_matricula) ? '<strong>Matrícula Caixa</strong>: '.$item->cx_matricula : '';
			// 	$info .= !empty($item->cx_admissao) ? '<br /><strong>Admissão</strong>: '.baseHelper::dateFormat($item->cx_admissao) : '';
			// 	$info .= !empty($item->cx_lotacao) ? '<br /><strong>Lotação</strong>: '.$item->cx_lotacao : '';
			// 	$info .= !empty($item->cx_cargo) ? '<br /><strong>Cargo</strong>: '.baseHelper::nameFormat($item->cx_cargo) : '';
			//  	$info = ' <span class="base-icon-info-circled text-live cursor-help hasPopover" data-placement="top" title="<strong>Dados da Caixa</strong>" data-content="'.$info.'"></span>';
			// endif;
			$btnState = $hasAdmin ? '<a href="#" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'"><span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').' hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'"></span></a> ' : '';
			$btnEdit = $hasAdmin ? '<a href="#" class="base-icon-pencil text-live hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"></a> ' : '';
			$btnDelete = $hasAdmin ? '<a href="#" class="base-icon-trash text-danger hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"></a>' : '';
			$rowState = $item->state == 0 ? 'danger' : '';
			$html .= '
				<li class="'.$rowState.'">
					'.$img.'
					<div class="pull-right">'.$btnState.$btnEdit.$btnDelete.'</div>
					'.$gender.baseHelper::nameFormat($item->name).'
					<div class="small text-muted font-featured">
						'.baseHelper::nameFormat($item->grp).$occupation.'
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
