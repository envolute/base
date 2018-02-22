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
		SELECT SQL_CALC_FOUND_ROWS
			T1.*,
			'. $db->quoteName('T2.name') .' client
		FROM
			'. $db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
			ON T2.id = T1.client_id AND T2.state = 1
		WHERE
			'.$where.$orderList;
	;
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' client
	';
	if(!empty($rID) && $rID !== 0) :
		if(isset($_SESSION[$RTAG.'RelTable']) && !empty($_SESSION[$RTAG.'RelTable'])) :
			$query .= ' FROM '.
				$db->quoteName($cfg['mainTable']) .' T1
				LEFT OUTER JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
				ON T2.id = T1.client_id AND T2.state = 1
				JOIN '. $db->quoteName($_SESSION[$RTAG.'RelTable']) .' T3
				ON '.$db->quoteName('T3.'.$_SESSION[$RTAG.'AppNameId']) .' = T1.id
			WHERE '.$where.' AND '. $db->quoteName('T3.'.$_SESSION[$RTAG.'RelNameId']) .' = '. $rID.$orderList
			;
		else :
			$query .= ' FROM '. $db->quoteName($cfg['mainTable']) .' T1
				LEFT OUTER JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
				ON T2.id = T1.client_id AND T2.state = 1
				WHERE '.$where.' AND '. $db->quoteName($rNID) .' = '. $rID.$orderList
			;
		endif;
	else :
		$query .= ' FROM '. $db->quoteName($cfg['mainTable']) .' T1
			LEFT OUTER JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
			ON T2.id = T1.client_id AND T2.state = 1
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
		$html .= '<div class="row py-3 mb-5">';
		foreach($res as $item) {

			if($cfg['hasUpload']) :
				JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
				// Imagem Principal -> Primeira imagem (index = 0)
				$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
				if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 48, 48);
				else $imgPath = $_ROOT.'images/apps/icons/folder_48.png';
				$img = '<img src="'.$imgPath.'" class="img-fluid b-all-2 b-white" style="width:48px; height:48px;" />';
			endif;

			$rowState = $item->state == 0 ? 'danger bg-light text-muted' : 'primary bg-white';
			$urlViewData = $_ROOT.'apps/'.$APPPATH.'/view?pID='.$item->id;
			$urlViewClient = $_ROOT.'apps/clients/view?vID='.$item->client_id;
			// Resultados
			$html .= '
				<div id="'.$APPTAG.'-item-'.$item->id.'" class="col-sm-4 col-md-3 pb-3">
					<div class="pos-relative rounded b-top-2 b-'.$rowState.' set-shadow">
						<a href="'.$urlViewData.'" class="d-flex align-items-center">
							'.$img.'
							<h6 class="px-2 m-0">'.baseHelper::nameFormat($item->name).'</h6>
						</a>
						<span class="d-flex justify-content-between align-items-center text-muted px-1 b-top">
							<a href="'.$urlViewClient.'" class="text-sm pl-1 hasTooltip" title="'.JText::_('FIELD_LABEL_CLIENT').'">'.baseHelper::nameFormat($item->client).'</a>
							<span class="btn-group">
								<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('MSG_ACTIVE_INACTIVE_ITEM').'" onclick="'.$APPTAG.'_setState('.$item->id.')" id="'.$APPTAG.'-state-'.$item->id.'">
									<span class="'.($item->state == 1 ? 'base-icon-ok text-success' : 'base-icon-cancel text-danger').'"></span>
								</a>
								<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_EDIT').'" onclick="'.$APPTAG.'_loadEditFields('.$item->id.', false, false)"><span class="base-icon-pencil text-live"></span></a>
								<a href="#" class="btn btn-xs btn-link hasTooltip" title="'.JText::_('TEXT_DELETE').'" onclick="'.$APPTAG.'_del('.$item->id.', false)"><span class="base-icon-trash text-danger"></span></a>
							</span>
						</span>
					</div>
				</div>
			';
		}
		$html .= '</div>';
	else :
		if($noReg) $html = '<div class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_LISTNOREG').'</div>';
	endif;

	echo $html;

else :

	# Otherwise, bad request
	header('status: 400 Bad Request', true, 400);

endif;

?>
