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
		'. $db->quoteName('T1.name') .',
		'. $db->quoteName('T2.name') .' grp,
		'. $db->quoteName('T2.overtime') .',
		'. $db->quoteName('T1.birthday') .',
		'. $db->quoteName('T1.end_date') .',
		IF('.$db->quoteName('T1.end_date').' <= NOW() && '. $db->quoteName('T2.overtime') .' > 0, 1, 0) finished,
		'. $db->quoteName('T1.state')
	;
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
				ON '.$db->quoteName('T2.id') .' = T1.group_id
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T3
				ON '.$db->quoteName('T3.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.
				$db->quoteName('T3.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID
			;
		else :
			$query .= ' FROM
				'. $db->quoteName($cfg['mainTable']) .' T1
				JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
				ON '.$db->quoteName('T2.id') .' = T1.group_id
			WHERE '. $db->quoteName($rNID) .' = '. $rID;
		endif;
	else :
		$query .= ' FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON '.$db->quoteName('T2.id') .' = T1.group_id';
		if($oCHL) :
			$query .= ' WHERE 1=0';
			$noReg = false;
		endif;
	endif;
	$query .= ' ORDER BY '. $db->quoteName('id') .' DESC';
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
			$end_date = $item->overtime > 0 ? baseHelper::dateFormat($item->end_date) : 'Ilimitado';
			$rowState = $item->state == 0 ? 'danger' : ($item->finished == 1 ? 'warning' : '');
			$html .= '
				<li class="'.$rowState.'">
					'.$img.'
					<div class="pull-right">'.$btnState.$btnEdit.$btnDelete.'</div>
					'.baseHelper::nameFormat($item->name).'<br />
					<div class="small text-muted font-featured">
						'.baseHelper::nameFormat($item->grp).'<span class="left-space-xs right-space-xs">-</span> Nasc. '.baseHelper::dateFormat($item->birthday).' &raquo; <span class="cursor-help '.($item->finished == 1 ? 'text-danger' : '').' hasTooltip" title="Vencimento do período de dependente">Venc. '.$end_date.'</span>
						'.($item->finished == 1 ? ' <span class="base-icon-attention text-live cursor-help hasTooltip" title="Período de Dependência encerrado!"></span>' : '').'
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
