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

	// Define as variáveis se houver parâmetros dinâmicos
	if(isset($_SESSION[$APPTAG.'IsPublic'])) $cfg['isPublic'] = $_SESSION[$APPTAG.'IsPublic'];
	if(isset($_SESSION[$APPTAG.'ViewerGroups'])) $cfg['groupId']['viewer'] = $_SESSION[$APPTAG.'ViewerGroups'];
	if(isset($_SESSION[$APPTAG.'AuthorGroups'])) $cfg['groupId']['author'] = $_SESSION[$APPTAG.'AuthorGroups'];
	if(isset($_SESSION[$APPTAG.'EditorGroups'])) $cfg['groupId']['editor'] = $_SESSION[$APPTAG.'EditorGroups'];
	if(isset($_SESSION[$APPTAG.'AdminGroups'])) $cfg['groupId']['admin'] = $_SESSION[$APPTAG.'AdminGroups'];

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
	require(JPATH_CORE.DS.'apps/snippets/ajax/ajaxAccess.php');

	// database connect
	$db		= JFactory::getDbo();

	// verifica se é um cliente
	$hasClient	= array_intersect($groups, $cfg['groupId']['client']); // se está na lista de administradores permitidos
	// Get Client ID
	$client_id = 0;
	$client_users = '';
	if($hasClient) {
		$query = 'SELECT client_id FROM '. $db->quoteName('vw_'.$cfg['project'].'_teams') .' WHERE user_id = '.$user->id.' AND state = 1';
		$db->setQuery($query);
		$client_id = $db->loadResult();
	}
	// filtro de projetos e usuários do cliente
	$cProj = $client_id ? 'client_id = '.$client_id.' AND ' : '';

	// LOAD FILTER
	$fQuery = $PATH_APP_FILE.'.filter.query.php';
	if($aFLT && file_exists($fQuery)) require($fQuery);

	// GET DATA
	$noReg	= true;
	$query	= '
		SELECT T1.*
	';
	if(!empty($rNID) && (!empty($rID) && $rID !== 0)) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName('vw_'.$cfg['project'].'_'.$APPNAME) .' T1
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T2
				ON '.$db->quoteName('T2.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.$where.' AND '. $db->quoteName('T2.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID.$orderList
			;
		else :
			$query .= ' FROM '. $db->quoteName('vw_'.$cfg['project'].'_'.$APPNAME) .' T1
				WHERE '.$where.' AND '. $db->quoteName($rNID) .' = '. $rID.$orderList
			;
		endif;
	else :
		$query .= ' FROM '. $db->quoteName('vw_'.$cfg['project'].'_'.$APPNAME) .' T1
			WHERE '.$where.$orderList
		;
		if($oCHL) $noReg = false;
	endif;

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
		$html .= '
			<form id="form-list-'.$APPTAG.'" method="post">
				<div class="row set-height" data-offset-elements="#cmstools, #header, .baseContent > .page-header" data-offset="20">
		';
		$type		= 9;
		$counter	= 0;
		foreach($res as $item) {

			// define permissões de execução
			$canEdit	= ($cfg['canEdit'] || $item->created_by == $user->id);
			$canDelete	= ($cfg['canDelete'] || $item->created_by == $user->id);

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
				// Imagem Principal -> Primeira imagem (index = 0)
				$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
				if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 48, 48);
				else $imgPath = $_ROOT.'images/apps/icons/folder_48.png';
				$img = '<img src="'.$imgPath.'" class="img-fluid mr-2" style="width:48px; height:48px;" />';
			endif;

			$urlViewData	= $_ROOT.'apps/'.$APPPATH.'/view?vID='.$item->id;
			$urlViewProject	= $_ROOT.'apps/projects/view?pID='.$item->project_id;

			$colorType	= JText::_('TEXT_COLOR_TYPE_'.$item->type);
			$iconType		= JText::_('TEXT_ICON_TYPE_'.$item->type);

			// define as colunas por type
			if($type !== $item->type) :
				if($counter > 0) $html .= '</div>';
				$html .= '
					<div id="'.$APPTAG.'-item-type-'.$item->type.'" class="canban-col col-sm-6 col-lg-3 pb-3">
						<h6 class="text-center bg-'.$colorType.' rounded py-2 set-shadow-right cursor-help hasTooltip" title="'.JText::_('TEXT_TYPE_'.$item->type.'_DESC').'">
							<span class="base-icon-'.$iconType.'"></span> '.JText::_('TEXT_TYPE_'.$item->type).'
						</h6>
				';
				$type = $item->type;
			endif;

			$deadline = '';
			if($item->deadline != '0000-00-00 00:00:00') {
				$dt = explode(' ', $item->deadline);
				$dlDate = baseHelper::dateFormat($dt[0], 'd/m/y');
				$dlTime = ($dt[1] != '00:00:00') ? ' '.substr($dt[1], 0, 5).$item->timePeriod : '';
				$deadline = '<br />'.JText::_('FIELD_LABEL_DEADLINE').'<br />'.$dlDate.$dlTime;
			}

			$priority = '';
			if($item->priority == 0 && !empty($deadline)) $priority .= ' <small class="base-icon-attention text-primary cursor-help hasTooltip" title="'.JText::_('TEXT_PRIORITY_DESC_0').$deadline.'"></small>';
			else if($item->priority == 1) $priority .= ' <small class="base-icon-attention text-live cursor-help hasTooltip" title="'.JText::_('TEXT_PRIORITY_DESC_1').$deadline.'"></small>';
			else if($item->priority == 2) $priority .= ' <small class="base-icon-attention text-danger cursor-help hasTooltip" title="'.JText::_('TEXT_PRIORITY_DESC_2').$deadline.'"></small>';

			$regInfo	= '';
			$regInfo	.= JText::_('TEXT_CREATED_DATE').': <span class="text-live">'.baseHelper::dateFormat($item->created_date, 'd/m/Y H:i').'</span><br />';
			$regInfo	.= JText::_('TEXT_BY').': <span class="text-live">'.baseHelper::nameFormat(JFactory::getUser($item->created_by)->name).'</span>';
			if($item->alter_date != '0000-00-00 00:00:00') :
				$regInfo	.= '<hr class="my-1" />';
				$regInfo	.= JText::_('TEXT_ALTER_DATE').': <span class="text-live">'.baseHelper::dateFormat($item->alter_date, 'd/m/Y H:i').'</span><br />';
				$regInfo	.= JText::_('TEXT_BY').': <span class="text-live">'.baseHelper::nameFormat(JFactory::getUser($item->alter_by)->name).'</span>';
			endif;
			$regInfo = '<div class="small text-muted">'.$regInfo.'</div>';

			$btnActions = '<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_COPY_LINK_TO_SHARE').'" onclick="copyToClipboard(\''.$_ROOT.'apps/'.$APPPATH.'/view?vID='.$item->id.'\', \''.JText::_('MSG_COPY_LINK_TO_SHARE').'\')"><span class="base-icon-link"></span></a>';
			$appActions = '';
			if($cfg['canEdit'] || ($item->created_by == $user->id)) :
				if($item->state) {
					$appActions = '
						<a href="#" class="dropdown-item px-3 py-2 b-bottom text-sm text-live" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil"></span> '.JText::_('TEXT_EDIT').'</a>
						<a href="#" class="dropdown-item px-3 py-2 b-bottom text-sm text-danger" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash"></span> '.JText::_('TEXT_DELETE').'</a>
					';
				}
				$btnActions .= '
					<div class="dropdown">
						<button class="btn btn-xs btn-link base-icon-cog" type="button" id="'.$APPTAG.'BtnActions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
						<div class="dropdown-menu dropdown-menu-right text-sm p-0 set-shadow" aria-labelledby="'.$APPTAG.'BtnActions">
							'.$appActions.'
							<span class="dropdown-item px-3 py-2 b-bottom text-sm">
								<span class="float-right">Task ID: <span class="text-live">#'.$item->id.'</span></span>
								<span class="text-info base-icon-info-circled"></span>
							</span>
							<span class="dropdown-item p-2">
								'.$regInfo.'
							</span>
						</div>
					</div>
					<a href="#" class="px-2 ml-1 b-left" onclick="'.$APPTAG.'_confirmState('.$item->id.', '.$item->state.')" id="'.$APPTAG.'-state-'.$item->id.'">
						<span class="'.($item->state == 1 ? 'base-icon-toggle-on text-success' : 'base-icon-toggle-on text-danger').' hasTooltip" title="'.JText::_(($item->state == 1 ? 'MSG_CLOSED_ITEM' : 'MSG_ACTIVATE_ITEM')).'"></span>
					</a>
				';
			endif;

			// Created By
			$authorName = empty($item->author_nickname) ? $item->author_name : $item->author_nickname;
			$authorType = $item->author_type < 2 ? '&lt;br /&gt;'.JText::_('TEXT_BRINTELL') : '';
			$createdBy = '<span class="btn btn-xs btn-link base-icon-user cursor-help hasTooltip" title="'.baseHelper::nameFormat($authorName).$authorType.'"></span>';

			if($hasClient) {
				$toggleType = '<span id="'.$APPTAG.'-item-'.$item->id.'-type" class="base-icon-'.$iconType.' text-'.$colorType.' hasTooltip" title="'.JText::_('TEXT_TYPE_'.$item->type).'" data-id="'.$item->id.'" data-type="'.$item->type.'"></span>';
			} else {
				$toggleType = '<a href="#" id="'.$APPTAG.'-item-'.$item->id.'-type" class="base-icon-'.$iconType.' text-'.$colorType.' hasTooltip" title="'.JText::_('TEXT_TYPE_'.$item->type).'" data-id="'.$item->id.'" data-type="'.$item->type.'" onclick="'.$APPTAG.'_setTypeModal(this)"></a>';
			}

			$project_state = '';
			$project_desc = 'FIELD_LABEL_PROJECT';
			if($item->project_state == 0) {
				$project_state = ' text-danger';
				$project_desc = 'TEXT_INACTIVE_PROJECT';
			}

			// Resultados
			$html .= '
				<div id="'.$APPTAG.'-item-'.$item->id.'" class="pos-relative rounded b-top-2 b-'.$colorType.' bg-white mb-3 set-shadow">
					<div class="d-flex d-justify-content align-items-center lh-1-2">
						<div class="align-self-stretch py-3 px-2 bg-gray-200">
							'.$toggleType.'
						</div>
						<a href="#'.$APPTAG.'-item-view" class="set-base-modal text-sm text-'.$colorType.' py-1 px-2" onclick="'.$APPTAG.'_setItemView('.$item->id.')">
							'.baseHelper::nameFormat($item->subject).'
							<div class="pos-absolute pos-top-0 pos-right-0 mx-1">
								'.$priority.'
							</div>
						</a>
					</div>
					<span class="d-flex justify-content-between align-items-center text-muted pl-2 b-top">
						<a href="'.$urlViewProject.'" class="small lh-1'.$project_state.' hasTooltip" title="'.JText::_($project_desc).'">
							'.baseHelper::nameFormat($item->project_name).'
						</a>
						<span class="btn-group">
							'.$createdBy.$btnActions.'
						</span>
					</span>
				</div>
			';

			$counter++;
		}
		$html .= '
				</div>
			</form>
		';
	else :
		if($noReg) $html = '<div class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_LISTNOREG').'</div>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
