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
	$query = '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' project
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			LEFT OUTER JOIN '. $db->quoteName('#__'.$cfg['project'].'_projects') .' T2
			ON T2.id = T1.project_id AND T2.state = 1
		WHERE
			'.$where.$orderList;
	;
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
		$html .= '<div class="row py-2 mb-4">';
		$status		= 9;
		$counter	= 0;
		foreach($res as $item) {

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
				// Imagem Principal -> Primeira imagem (index = 0)
				$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
				if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 48, 48);
				else $imgPath = $_ROOT.'images/apps/icons/folder_48.png';
				$img = '<img src="'.$imgPath.'" class="img-fluid mr-2" style="width:48px; height:48px;" />';
			endif;

			$urlViewData = $_ROOT.'apps/'.$APPPATH.'/view?pID='.$item->id;
			$urlViewProject = $_ROOT.'apps/projects/view?vID='.$item->project_id;
			// $rowState = $item->state == 0 ? 'danger bg-light text-muted' : 'primary bg-white';
			$itemStatus = '';
			switch($item->status) {
				case '1': // active
					$itemStatus = 'primary';
					$iconStatus = 'off';
					break;
				case '2': // paused
					$itemStatus = 'primary-light';
					$iconStatus = 'pause';
					break;
				case '3': // completed
					$itemStatus = 'success';
					$iconStatus = 'circle';
					break;
				case '4': // closed
					$itemStatus = 'success';
					$iconStatus = 'ok';
					break;
				default:
					$itemStatus = 'live';
					$iconStatus = 'clock';
			}

			// define as colunas por status
			if($status !== $item->status) :
				if($counter > 0) $html .= '</div>';
				$html .= '
					<div id="'.$APPTAG.'-item-status-'.$item->status.'" class="tasks-col col-sm-4 col-md-3 pb-3">
						<h6 class="text-center bg-'.$itemStatus.' rounded py-2 set-shadow-right">
							<span class="base-icon-'.$iconStatus.'"></span> '.JText::_('TEXT_STATUS_'.$item->status).'
						</h6>
				';
				$status = $item->status;
			endif;
			$dtContent = !empty($item->status_desc) ? $item->status_desc : '';
			$btnStatus = '<a href="#" id="'.$APPTAG.'-item-'.$item->id.'-status" class="btn btn-xs btn-link base-icon-'.$iconStatus.' text-'.$itemStatus.' hasPopover" title="<strong>'.JText::_('TEXT_STATUS_'.$item->status).'</strong>" data-id="'.$item->id.'" data-status="'.$item->status.'" data-content="'.$dtContent.'" onclick="'.$APPTAG.'_setStatusModal(this)"></a>';

			$deadline = $item->deadline != '0000-00-00' ? '<small class="badge badge-secondary text-danger ml-auto mr-2 cursor-help hasTooltip" title="'.JText::_('FIELD_LABEL_DEADLINE').'">'.baseHelper::dateFormat($item->deadline).'</small>' : '';

			// Resultados
			$html .= '
				<div id="'.$APPTAG.'-item-'.$item->id.'" class="pos-relative rounded b-top-2 b-'.$itemStatus.' bg-white mb-3 set-shadow">
					<a href="'.$urlViewData.'" class="d-block lh-1-2 py-3 px-2">
						'.baseHelper::nameFormat($item->subject).'
					</a>
					<span class="d-flex justify-content-between align-items-center text-muted pl-2 b-top">
						<a href="'.$urlViewProject.'" class="small hasTooltip" title="'.JText::_('FIELD_LABEL_PROJECT').'">'.baseHelper::nameFormat($item->project).'</a>
						'.$deadline.'
						<span class="btn-group">
							'.$btnStatus.'
							<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
								<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span>
							</a>
							<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil text-live"></span></a>
							<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash text-danger"></span></a>
						</span>
					</span>
				</div>
			';

			$counter++;
		}
		$html .= '</div>';
	else :
		if($noReg) $html = '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_LISTNOREG').'</p>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
