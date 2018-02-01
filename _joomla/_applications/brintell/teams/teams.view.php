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

// Get request data
$vID = $app->input->get('vID', 0, 'int'); // VIEW 'ID'
if($vID == 0) $vID = $user->id;

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

// Admin Actions
// require_once('_contacts.select.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' role,
			'. $db->quoteName('T3.username') .'
		FROM
			'.$db->quoteName($cfg['mainTable']).' T1
			LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_roles') .' T2
			ON T2.id = T1.role_id
			LEFT JOIN '. $db->quoteName('#__users') .' T3
			ON T3.id = T1.user_id
		WHERE '.$db->quoteName('T1.user_id') .' = '. $vID
	;
	try {
		$db->setQuery($query);
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if(!empty($item->name)) : // verifica se existe

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 300).'" class="img-fluid b-all b-all-dashed p-1" />';
			else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';

			// Arquivos -> Grupo de imagens ('#'.$APPTAG.'-files-group')
			// Obs: para pegar todas as imagens basta remover o 'grupo' ('#'.$APPTAG.'-files-group')
			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
			$listFiles = array();
			for($i = 1; $i < count($files[$item->id]); $i++) {
				if(!empty($files[$item->id][$i]->filename)) :
					$fLab = ($files[$item->id][$i]->index == 1) ? JText::_('FIELD_LABEL_RESUME') : '';
					$fTip = ($files[$item->id][$i]->index == 2) ? JText::_('FIELD_LABEL_CONTRACT') : $files[$item->id][$i]->filename;
					$listFiles[$files[$item->id][$i]->index] .= '
						<a href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$fTip.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"> '.$fLab.'</span>
						</a>
					';
				endif;
			}
		endif;

		// Email, profissão
		$info1 = '';
		if(!empty($item->email)) :
			$info1 .= '
				<div class="col-sm-8">
					<label class="label-sm">'.JText::_('FIELD_LABEL_EMAIL').':</label>
					<p>'.$item->email.'</p>
				</div>
			';
		endif;
		if(!empty($item->occupation)) :
			$info1 .= '
				<div class="col">
					<label class="label-sm">'.JText::_('FIELD_LABEL_OCCUPATION').':</label>
					<p> '.baseHelper::nameFormat($item->occupation).'</p>
				</div>
			';
		endif;
		if(!empty($info1)) $info1 = '<div class="row">'.$info1.'</div>';

		// Birthday, CPF, RG, gênero, estado civil, filhos, conjuge
		$info2 = '';
		if(!empty($item->birthday) && $item->birthday != '0000-00-00') :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-sm">'.JText::_('TEXT_BIRTHDAY').':</label>
					<p class="base-icon-birthday text-success"> '.baseHelper::dateFormat($item->birthday, JText::_('TEXT_BIRTHDAY_FORMAT')).'</p>
				</div>
			';
		endif;
		if($hasAdmin && !empty($item->cpf)) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-sm">CPF:</label>
					<p>'.$item->cpf.'</p>
				</div>
			';
		endif;
		if($hasAdmin && !empty($item->cnpj)) :
			$contract = isset($listFiles[2]) ? $listFiles[2] : '';
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-sm">CNPJ:</label>
					<p>'.$item->cnpj.' '.$contract.'</p>
				</div>
			';
		endif;
		if($item->marital_status > 0) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-sm">'.JText::_('FIELD_LABEL_MARITAL_STATUS').':</label>
					<p>'.JText::_('TEXT_MARITAL_STATUS_'.$item->marital_status).'</p>
				</div>
			';
		endif;
		if($item->children > 0) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-sm">'.JText::_('FIELD_LABEL_CHILDREN').':</label>
					<p>'.$item->children.'</p>
				</div>
			';
		endif;
		if(!empty($info2)) $info2 = '<div class="row">'.$info2.'</div>';

		// Extra Info
		$about_me = '';
		if(!empty($item->about_me)) :
			$about_me .= '
				<div class="'.$APPTAG.'-profile-about font-condensed text-primary px-3 py-2 mb-3 bg-white rounded set-shadow">'.$item->about_me.'</div>
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
			$cDiv = (!empty($phones) && !empty($chats)) ? '<hr class="b-top-dashed my-2" />' : '';
			$contact .= '
				<div class="pb-2 mb-2 b-bottom b-bottom-dashed">
					<hr class="hr-tag b-top-dashed b-primary">
					<span class="badge badge-primary"> '.JText::_('TEXT_CONTACT_DATA').'</span>
					'.$phones.$cDiv.$chats.'
				</div>
			';
		endif;

		// Weblinks
		$wTxt = explode(';', $item->weblink_text);
		$wUrl = explode(';', $item->weblink_url);
		$links = (count($wUrl) > 0) ? '<ul class="set-list bordered">' : '';
		for($i = 0; $i < count($wUrl); $i++) {
			$text = !empty($wTxt[$i]) ? $wTxt[$i] : $wUrl[$i];
			$links .= '<li> <a href="'.$wUrl[$i].'" class="base-icon-link" target="_blank"> '.baseHelper::nameFormat($text).'</a></li>';
		}
		if(count($wUrl) > 0) $links .= '</ul>';

		// Address
		$addressInfo = !empty($item->address_info) ? ', '.$item->address_info : '';
		$addressNumber = !empty($item->address_number) ? ', '.$item->address_number : '';
		$addressZip = !empty($item->zip_code) ? $item->zip_code.', ' : '';
		$addressDistrict = !empty($item->address_district) ? baseHelper::nameFormat($item->address_district) : '';
		$addressCity = !empty($item->address_city) ? ', '.baseHelper::nameFormat($item->address_city) : '';
		$addressState = !empty($item->address_state) ? ', '.baseHelper::nameFormat($item->address_state) : '';
		$addressCountry = !empty($item->address_country) ? ', '.baseHelper::nameFormat($item->address_country) : '';

		// Endereço
		$address = '';
		if(!empty($item->address)) :
			$address .= '
				<hr class="hr-tag b-top-dashed">
				<span class="badge badge-primary"> '.JText::_('FIELD_LABEL_ADDRESS').'</span>
				<p>
						'.baseHelper::nameFormat($item->address).$addressNumber.$addressInfo.'<br />
						'.$addressZip.$addressDistrict.$addressCity.$addressState.$addressCountry.'
				</p>
			';
		endif;

		$bankAccount_info = '';
		if($hasAdmin && !empty($item->bank_name) && !empty($item->account)) :
			$bankAccount_info = '
				<hr class="hr-tag b-top-dashed">
				<span class="badge badge-primary text-uppercase"> '.JText::_('TEXT_BANKS_ACCOUNT').'</span>
				<h6 class="mb-1 base-icon-bank"> '.$item->bank_name.'</h6>
				<div class="d-flex">
					<div><label class="label-sm">'.JText::_('FIELD_LABEL_AGENCY').'</label>'.$item->agency.'</div>
					<div class="px-3"><label class="label-sm">'.JText::_('FIELD_LABEL_OPERATION').'</label>'.$item->operation.'</div>
					<div><label class="label-sm">'.JText::_('FIELD_LABEL_ACCOUNT').'</label>'.$item->account.'</div>
				</div>
			';
		endif;

		// Acesso
		$access = ($item->access == 1 && !empty($item->user_id)) ? ' <span class="base-icon-plug text-success cursor-help hasTooltip" title="'.JText::_('TEXT_CONNECTED_USER').'"></span>' : '';

		$nickname = !empty($item->nickname) ? ' <small>('.baseHelper::nameFormat($item->nickname).')</small>' : '';
		$resume = ($hasAdmin && isset($listFiles[1])) ? '<hr class="my-2" />'.$listFiles[1] : '';
		$gender = '';
		if($item->gender > 0) :
			$gIcon = ($item->gender == 1) ? 'base-icon-male text-blue' : 'base-icon-female text-pink';
			$gender = '<span class="'.$gIcon.' cursor-help hasTooltip" title="'.JText::_('TEXT_GENDER_'.$item->gender).'"></span> ';
		endif;

		$avatar = '';
		if($vID != $user->id) :
			$avatar = '
				<div class="col-4 col-sm-2 mb-4 mb-md-0">
					<div style="max-width: 300px">'.$img.'</div>
					<div class="text-sm text-live pt-2">
						'.(!empty($item->username) ? '@'.$item->username : '').$resume.'
					</div>
				</div>
			';
		endif;

		echo '
			<div class="row">
				<div class="col-lg-9">
					<div class="row">
						'.$avatar.'
						<div class="col-sm">
							'.$about_me.'
							<div class="row">
								<div class="col-md-8">
									<label class="label-sm">'.JText::_('FIELD_LABEL_NAME').': '.$access.'</label>
									<p>'.$gender.baseHelper::nameFormat($item->name).$nickname.'</p>
								</div>
								<div class="col-md-4">
									<label class="label-sm">'.JText::_('FIELD_LABEL_ROLE').':</label>
									<p>'.baseHelper::nameFormat($item->role).'</p>
								</div>
							</div>
							'.$info1.$info2.$address.'
						</div>
					</div>
				</div>
				<div class="col">
					'.$contact.$links.$bankAccount_info.'
				</div>
			</div>
		';

	else :
		echo '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_ITEM_NOT_AVAILABLE').'</p>';
	endif;

else :

	echo '<h4 class="alert alert-warning">'.JText::_('MSG_NO_ITEM_SELECTED').'</h4>';

endif;
?>
