<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = true; // Público -> acesso aberto a todos

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

// Get request data
$vID = $app->input->get('vID', 0, 'int'); // VIEW 'ID'

// list view
$urlToList = JURI::root().'apps/base-providers/agreements';

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' group_name
		FROM '.
			$db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON T2.id = T1.group_id AND T2.state = 1
		WHERE
			'. $db->quoteName('T1.id') .' = '. $vID .' AND
			'. $db->quoteName('T1.agreement') .' = 1 AND
			'. $db->quoteName('T1.state') .' = 1
	';
	try {
		$db->setQuery($query);
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	$provider = '';
	if(!empty($item->name)) : // verifica se existe

		// Call Centers
		$query	= '
			SELECT *
			FROM '.$db->quoteName('#__'.$cfg['project'].'_callCenters') .' T1
				JOIN '. $db->quoteName('#__'.$cfg['project'].'_rel_providers_callCenters') .' T2
				ON '.$db->quoteName('T2.callCenter_id') .' = T1.id
			WHERE '.$db->quoteName('T2.provider_id') .' = '. $vID .'
			ORDER BY '.$db->quoteName('T1.id') .' ASC
		';
		try {
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			$res = $db->loadObjectList();
		} catch (RuntimeException $e) {
			echo $e->getMessage();
			return;
		}

		$callCenters = '';
		if($num_rows) : // verifica se existe
			$callCenters .= '<h6 class="page-header mb-3 base-icon-phone-squared"> '.JText::_('TEXT_PROVIDER_CALL_CENTERS').'</h6>';
			$callCenters .= '<ul class="set-list list-lg list-trim mb-4">';
			foreach($res as $obj) {
				// Phones
				$ph = explode(';', $obj->phone);
				$wp = explode(';', $obj->whatsapp);
				$pd = explode(';', $obj->phone_desc);
				$phones = '';
				for($i = 0; $i < count($ph); $i++) {
					$whapps = $wp[$i] == 1 ? ' <span class="base-icon-whatsapp text-success cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_WHATSAPP').'"></span>' : '';
					$phDesc = !empty($pd[$i]) ? '<div class="small text-muted pb-1">'.$pd[$i].'</div>' : '';
					$phones .= '<div class="pb-1">'.$ph[$i].$whapps.$phDesc.'</div>';
				}
				// Emails
				$em = explode(';', $obj->email);
				$emails = '';
				for($i = 0; $i < count($em); $i++) {
					$emails .= !empty($em[$i]) ? '<div class="pb-1">'.$em[$i].'</div>' : '';
				}
				// Chats
				$cName = explode(';', $obj->chat_name);
				$cUser = explode(';', $obj->chat_user);
				$chats = '';
				for($i = 0; $i < count($em); $i++) {
					if(!empty($cName[$i])) $chats .= '<div class="pb-1"><strong>'.$cName[$i].'</strong>: '.$cUser[$i].'</div>';
				}
				// Weblinks
				$wTxt = explode(';', $obj->weblink_text);
				$wUrl = explode(';', $obj->weblink_url);
				$links = '';
				for($i = 0; $i < count($em); $i++) {
					$text = !empty($wTxt[$i]) ? $wTxt[$i] : $wUrl[$i];
					$links .= '
						<div class="pb-1">
							<a href="'.$wUrl[$i].'" target="_blank">'.$text.'</a>
						</div>
					';
				}
				// Extra info
				$info = !empty($obj->extra_info) ? '<div class="pt-1">'.$obj->extra_info.'</div>' : '';
				// Título
				$title = '';
				if($obj->showTitle == 1) $title = '<h6 class="font-weight-bold mb-2">'.$obj->title.'</h6>';
				$callCenters .= '
					<li>'.$title.$phones.$emails.$chats.$links.$info.'</li>
				';
			}
			$callCenters .= '</ul>';
			unset($obj); // reseta as informações contidas em item
		endif;

		// Localidades
		$query	= '
			SELECT T1.*
			FROM '.$db->quoteName('#__'.$cfg['project'].'_locations') .' T1
				JOIN '. $db->quoteName('#__'.$cfg['project'].'_rel_providers_locations') .' T2
				ON '.$db->quoteName('T2.location_id') .' = T1.id
			WHERE '.$db->quoteName('T2.provider_id') .' = '. $vID .' AND '.$db->quoteName('T1.isPublic') .' = 1
			ORDER BY '.$db->quoteName('T1.id') .' ASC
		';
		try {
			$db->setQuery($query);
			$db->execute();
			$num_rows = $db->getNumRows();
			$res = $db->loadObjectList();
		} catch (RuntimeException $e) {
			echo $e->getMessage();
			return;
		}

		$locations = '';
		if($num_rows) : // verifica se existe
			$locations .= '<h6 class="page-header mb-3 base-icon-location"> '.JText::_('TEXT_LOCATIONS').'</h6>';
			$locations .= '<ul class="set-list list-lg list-trim bordered mb-4">';
			foreach($res as $obj) {

				$title = !empty($obj->title) ? '<h6 class="strong mb-1">'.$lock.baseHelper::nameFormat($obj->title).'</h6>' : $lock;
				$addressInfo = !empty($obj->address_info) ? ', '.$obj->address_info : '';
				$addressNumber = !empty($obj->address_number) ? ', '.$obj->address_number : '';
				$addressZip = !empty($obj->zip_code) ? $obj->zip_code.', ' : '';
				$addressDistrict = !empty($obj->address_district) ? baseHelper::nameFormat($obj->address_district) : '';
				$addressCity = !empty($obj->address_city) ? ', '.baseHelper::nameFormat($obj->address_city) : '';
				$addressState = !empty($obj->address_state) ? ', '.($_SESSION[$RTAG.'OnlyBR'] ? $obj->address_state : baseHelper::nameFormat($obj->address_state)) : '';
				$addressCountry = (!empty($obj->address_country) && !$_SESSION[$RTAG.'OnlyBR']) ? ', '.baseHelper::nameFormat($obj->address_country) : '';
				$mapa = !empty($obj->url_map) ? ' <a href="'.$obj->url_map.'" class="badge badge-warning set-modal" title="'.JText::_('TEXT_MAP').'" data-modal-title="'.JText::_('TEXT_LOCATION').'" data-modal-iframe="true" data-modal-width="95%" data-modal-height="95%"><span class="base-icon-location"></span></a> ' : '';
				$extra = !empty($obj->extra_info) ? ' <div class="location-extra-info text-sm"> '.$obj->extra_info.'</div>' : '';

				$locations .= '
					<li>
						'.$title.$mapa.baseHelper::nameFormat($obj->address).$addressNumber.$addressInfo.'
						<br />'.
						$addressZip.$addressDistrict.$addressCity.$addressState.$addressCountry.$extra.'
					</li>
				';
			}
			$locations .= '</ul>';
			unset($obj); // reseta as informações contidas em item
		endif;

		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> Primeira imagem (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $img = '<img src="images/apps/'.$APPPATH.'/'.$img['filename'].'" class="w-100 img-fluid b-all bg-white p-1" />';

		$site	= !empty($item->website) ? '<a href="'.$item->website.'" class="new-window base-icon-globe" target="_blank"> '.JText::_('FIELD_LABEL_WEBSITE').'</a>' : '';
		$logo	= '';
		if(!empty($img)) :
			$logo .= '<div class="col d-none d-sm-block pr-0" style="flex: 0 0 120px;">';
			if(!empty($item->website)) $logo .= '<a href="'.$item->website.'" target="_blank">';
			$logo .= $img;
			if(!empty($item->website)) $logo .= '</a>';
			$logo .= '</div>';
		endif;
		$agree	= $item->agreement == 1 ? '<span class="badge badge-success float-right">'.JText::_('FIELD_LABEL_AGREEMENT').'</span>' : '';
		// DESCRIPTION
		$description = !empty($item->description) ? $item->description : '';

		$provider .= '
			<ul class="set-list inline bordered list-trim b-bottom b-dotted text-muted small mb-3 pb-2">
				<li><a href="'.$urlToList.'" class="text-uppercase base-icon-reply"> '.JText::_('TEXT_AGREEMENTS').'</a></li>
				<li><a href="'.$urlToList.'?gID='.$item->group_id.'">'.$item->group_name.'</a></li>
			</ul>
			<div id="agreement-header" class="row">
				'.$logo.'
				<div class="col">
					<h1 class="mt-0">
						'.baseHelper::nameFormat($item->name).'
					</h1>
					'.$description.'
				</div>
			</div>
		';

		// TAB => INFORMATIONS
		if(!empty($site) || !empty($callCenters) || !empty($locations) || !empty($item->service_desc)) :
			$provider .= '
				<hr />
				<!-- Nav tabs -->
				<ul class="nav nav-tabs mt-3" id="'.$APPTAG.'TabInfo" role="tablist">
			';
			$active	= ' active';
			$show	= ' show '.$active;
			// TABS
			if(!empty($item->service_desc)) :
				$provider .= '
					<li class="nav-item">
						<a class="nav-link'.$active.'" id="'.$APPTAG.'TabView-service" href="#'.$APPTAG.'TabViewService" data-toggle="tab" role="tab" aria-controls="service">
							<span class="base-icon-doc-text"></span> '.JText::_('FIELD_LABEL_SERVICE').'
						</a>
					</li>
				';
				$active = '';
			endif;
			if(!empty($callCenters) || !empty($locations)) :
				$provider .= '
					<li class="nav-item">
						<a class="nav-link'.$active.'" id="'.$APPTAG.'TabView-location" href="#'.$APPTAG.'TabViewLocation" data-toggle="tab" role="tab" aria-controls="location" aria-expanded="true">
							<span class="base-icon-location"></span> '.JText::_('TEXT_LOCATION_CONTACT').'
						</a>
					</li>
				';
				$active = '';
			endif;
			if(!empty($site)) $provider .= '<li class="nav-item d-none d-sm-block">'.$site.'</li>';

			$provider .= '
				</ul>
				<!-- Tab panes -->
				<div class="tab-content" id="'.$APPTAG.'TabContent">
			';

			// TABS CONTENT
			if(!empty($item->service_desc)) :
				$provider .= '
					<div class="tab-pane fade'.$show.'" id="'.$APPTAG.'TabViewService" role="tabpanel" aria-labelledby="'.$APPTAG.'TabView-service">
						'.$item->service_desc.'
					</div>
				';
				$show = '';
			endif;
			if(!empty($callCenters) || !empty($locations)) :
				$provider .= '
					<div class="tab-pane fade'.$show.'" id="'.$APPTAG.'TabViewLocation" role="tabpanel" aria-labelledby="'.$APPTAG.'TabView-location">
						<div class="row">
				';
				if(!empty($locations)) $provider .= '<div class="col-md-7">'.$locations.'</div>';
				if(!empty($callCenters)) $provider .= '<div class="col-md">'.$callCenters.'</div>';
				$provider .= '
						</div>
					</div>
				';
				$show = '';
			endif;
			$provider .= '</div>';
		endif;

	else :
		$provider = '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_ITEM_NOT_AVAILABLE').'</p>';
	endif;

	echo $provider;

else :

	echo '<h4 class="alert alert-warning">'.JText::_('MSG_NO_ITEM_SELECTED').'</h4>';

endif;
?>
