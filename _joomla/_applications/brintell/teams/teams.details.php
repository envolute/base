<?php
/* SISTEMA PARA CADASTRO DE PROJETOS
 * AUTOR: IVO JUNIOR
 * EM: 29/01/2018
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = true; // Público -> acesso aberto a todos

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
$MAINTAG	= $APPTAG;

// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_CORE.DS.'helpers/base.php');
JLoader::register('baseAppHelper', JPATH_CORE.DS.'helpers/apps.php');

$app = JFactory::getApplication('site');

// GET CURRENT USER'S DATA
$user = JFactory::getUser();
$groups = $user->groups;

// init general css/js files
require(JPATH_CORE.DS.'apps/_init.app.php');

//joomla get request data
$input		= $app->input;

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

// database connect
$db		= JFactory::getDbo();

// GET DATA
$query	= '
	SELECT
		T1.*,
		'. $db->quoteName('T2.username') .',
		'. $db->quoteName('T3.name') .' role
	FROM '. $db->quoteName($cfg['mainTable']) .' T1
		LEFT JOIN '. $db->quoteName('#__users') .' T2
		ON '.$db->quoteName('T2.id') .' = T1.user_id
		LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_roles') .' T3
		ON '.$db->quoteName('T3.id') .' = T1.role_id AND T3.state = 1
	WHERE T1.user_id = '.$user->id
;
try {
	$db->setQuery($query);
	$item = $db->loadObject();
} catch (RuntimeException $e) {
	echo $e->getMessage();
	return;
}

$html = '';
if(!empty($item->name)) :



	if($cfg['hasUpload']) :
		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> Primeira imagem (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 128, 128);
		else $imgPath = $_ROOT.'images/apps/icons/user_'.$item->gender.'.png';
		$img = '<figure><img src="'.$imgPath.'" width="128" height="128" class="img-fluid rounded-circle" /></figure>';
	endif;

	$name = baseHelper::nameFormat($item->name);
	if(!empty($item->nickname)) :
		$name = '<div class="small text-muted lh-1">'.baseHelper::nameFormat($item->name).'</div>'.baseHelper::nameFormat($item->nickname);
	endif;
	$job = (!empty($item->role)) ? baseHelper::nameFormat($item->role) : (!empty($item->occupation) ? baseHelper::nameFormat($item->occupation) : '');

	$children = $info = '';
	if($item->children > 0) :
		$childLabel = ($item->children > 1) ? 'TEXT_CHILDREN' : 'TEXT_CHILD';
		$children = ' <small>('.$item->children.' '.JText::_($childLabel).')</small>';
	endif;
	$info .= ($item->marital_status > 0) ? '<div class="text-sm">'.JText::_('TEXT_MARITAL_STATUS_'.$item->marital_status).$children.'</div>' : '';
	// Birthday
	if(!empty($item->birthday) && $item->birthday != '0000-00-00') :
		$info .= '
			<div>
				<span class="base-icon-birthday hasTooltip" title="'.JText::_('YEXT_BIRTHDAY').'"></span>
				'.baseHelper::dateFormat($item->birthday, 'M d').'
			</div>
		';
	endif;

	// Contact data
	$contact = '';
	// Phones
	$ph = explode(';', $item->phone);
	$wp = explode(';', $item->whatsapp);
	$pd = explode(';', $item->phone_desc);
	$phones = '';
	for($i = 0; $i < count($ph); $i++) {
		$whapps = $wp[$i] == 1 ? ' <span class="base-icon-whatsapp text-success cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_WHATSAPP').'"></span>' : '';
		$phDesc = !empty($pd[$i]) ? '<div class="small text-muted">'.$pd[$i].'</div>' : '';
		$phones .= '<div class="lh-1-3 pb-1">'.$ph[$i].$whapps.$phDesc.'</div>';
	}
	// Chats
	$cName = explode(';', $item->chat_name);
	$cUser = explode(';', $item->chat_user);
	$chats = '';
	for($i = 0; $i < count($cName); $i++) {
		if(!empty($cName[$i])) :
			$chats .= '
				<div class="pb-1">
					'.baseHelper::nameFormat($cName[$i]).'<span class="float-right mr-2">'.$cUser[$i].'</span>
				</div>
			';
		endif;
	}

	if(!empty($phones) || !empty($chats)) :
		$cDiv = (!empty($phones) && !empty($chats)) ? '<hr class="b-top-dashed b-gray-900 my-2" />' : '';
		$contact .= '
			<div class="text-sm pb-2 mb-2 b-bottom b-bottom-dashed b-gray-900">
				<hr class="hr-tag b-top-dashed b-gray-900">
				<span class="badge badge-primary"> '.JText::_('TEXT_CONTACT_DATA').'</span>
				'.$phones.$cDiv.$chats.'
			</div>
		';
	endif;

	// Weblinks
	$wTxt = explode(';', $item->weblink_text);
	$wUrl = explode(';', $item->weblink_url);
	$links = (count($wUrl) > 0) ? '<ul class="set-list list-sm bordered text-sm">' : '';
	for($i = 0; $i < count($wUrl); $i++) {
		$text = !empty($wTxt[$i]) ? $wTxt[$i] : $wUrl[$i];
		$links .= '<li><a href="'.$wUrl[$i].'" target="_blank">'.$text.'</a></li>';
	}
	if(count($wUrl) > 0) $links .= '</ul>';

	// Address
	$addressInfo = !empty($item->address_info) ? ', '.$item->address_info : '';
	$addressNumber = !empty($item->address_number) ? '<br />N&ordm;: '.$item->address_number : '';
	$addressZip = !empty($item->zip_code) ? $item->zip_code.'<br />' : '';
	$addressDistrict = !empty($item->address_district) ? baseHelper::nameFormat($item->address_district) : '';
	$addressCity = !empty($item->address_city) ? ', '.baseHelper::nameFormat($item->address_city) : '';
	$addressState = !empty($item->address_state) ? ', '.baseHelper::nameFormat($item->address_state) : '';
	$addressCountry = !empty($item->address_country) ? '<br />'.baseHelper::nameFormat($item->address_country) : '';

	// Endereço
	$address = '';
	if(!empty($item->address)) :
		$address .= '
			<hr class="hr-tag b-top-dashed b-gray-900">
			<span class="badge badge-primary"> '.JText::_('FIELD_LABEL_ADDRESS').'</span>
			<p class="text-sm">
					'.$addressZip.baseHelper::nameFormat($item->address).$addressNumber.$addressInfo.'<br />
					'.$addressDistrict.$addressCity.$addressState.$addressCountry.'
			</p>
		';
	endif;

	// Extra Info
	// $about_me = '';
	// if(!empty($item->about_me)) :
	// 	$about_me .= '
	// 		<div class="contact-extra-info">
	// 			<label class="label-sm">'.JText::_('FIELD_LABEL_EXTRA_INFO').':</label>
	// 			<div class="mb-4">'.$item->about_me.'</div>
	// 		</div>
	// 	';
	// endif;

	$html .= '
		<div id="'.$APPTAG.'-details-view">
			<div class="px-2">
				<div class="text-center py-2 mb-3 bg-dark-opacity-45 clearfix">
					'.$img.'
					<h5 class="text-gray-200 mb-0 lh-1-2">'.$name.'</h5>
					<div class="small text-primary">'.$job.'</div>
					<div class="text-sm mt-2 pt-1 b-top b-top-dashed b-gray-900">'.$item->email.'</div>
				</div>
				'.$info.$contact.$links.$address.'
			</div>
		</div>
	';

else :

	$app->enqueueMessage(JText::_('MSG_NOT_PERMISSION'), 'warning');
	$app->redirect(JURI::root(true));
	exit();

endif;

echo $html;

?>
