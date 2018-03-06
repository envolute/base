<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = 3; // Público -> Todos podem Editar

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
$MAINAPP	= $APPNAME;
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

// Verifica se o acesso é feito por um cliente
$hasClient = array_intersect($groups, $cfg['groupId']['client']); // se está na lista de grupos permitidos

// Get request data
$vID = $app->input->get('vID', 0, 'int'); // VIEW 'ID'
if($hasClient && $vID == 0) {
	$app->redirect(JURI::root(true).'/apps/clients/staff/view');
	exit();
} else {
	$vID = ($vID > 0) ? $vID : $user->id;
}

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$MAINTAG.'langDef'])
if(isset($_SESSION[$MAINTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$MAINTAG.'langDef'], true);
endif;

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' role,
			'. $db->quoteName('T3.username') .',
			'. $db->quoteName('T4.title') .' usergroup
		FROM
			'.$db->quoteName($cfg['mainTable']).' T1
			LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_roles') .' T2
			ON T2.id = T1.role_id
			LEFT JOIN '. $db->quoteName('#__users') .' T3
			ON T3.id = T1.user_id
			LEFT JOIN '. $db->quoteName('#__usergroups') .' T4
			ON T4.id = T1.usergroup
		WHERE '.$db->quoteName('T1.user_id') .' = '. $vID
	;
	try {
		$db->setQuery($query);
		$view = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if(!empty($view->name)) : // verifica se existe

		// define permissões de execução
		$canEdit	= ($cfg['canEdit'] || $view->created_by == $user->id);
		$canDelete	= ($cfg['canDelete'] || $view->created_by == $user->id);

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $view->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 300).'" class="img-fluid b-all b-all-dashed p-1" />';
			else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';

			// Arquivos -> Grupo de imagens ('#'.$APPTAG.'-files-group')
			// Obs: para pegar todas as imagens basta remover o 'grupo' ('#'.$APPTAG.'-files-group')
			$files[$view->id] = uploader::getFiles($cfg['fileTable'], $view->id);
			$listFiles = array();
			for($i = 1; $i < count($files[$view->id]); $i++) {
				if(!empty($files[$view->id][$i]->filename)) :
					$fLab = ($files[$view->id][$i]->index == 1) ? JText::_('FIELD_LABEL_RESUME') : '';
					$fTip = ($files[$view->id][$i]->index == 2) ? JText::_('FIELD_LABEL_CONTRACT') : $files[$view->id][$i]->filename;
					$listFiles[$files[$view->id][$i]->index] .= '
						<a class="btn btn-sm btn-primary btn-block my-2" href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$view->id][$i]->filename).'&mt='.base64_encode($files[$view->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$fTip.'<br />'.((int)($files[$view->id][$i]->filesize / 1024)).'kb"> '.$fLab.'</span>
						</a>
					';
				endif;
			}
		endif;

		// Email, profissão
		$info1 = '';
		if(!empty($view->email)) :
			$info1 .= '
				<div class="col-sm-4">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMAIL').':</label>
					<p>'.$view->email.'</p>
				</div>
			';
		endif;
		if(!empty($view->role)) :
			$info1 .= '
				<div class="col-md-4">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ROLE').':</label>
					<p>'.baseHelper::nameFormat($view->role, null, JText::_('TEXT_UNDEFINED')).'</p>
				</div>
			';
		endif;
		if(!empty($view->occupation)) :
			$info1 .= '
				<div class="col">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_OCCUPATION').':</label>
					<p> '.baseHelper::nameFormat($view->occupation).'</p>
				</div>
			';
		endif;
		if(!empty($info1)) $info1 = '<div class="row">'.$info1.'</div>';

		// Birthday, CPF, RG, gênero, estado civil, filhos, conjuge
		$info2 = '';
		if(!empty($view->birthday) && $view->birthday != '0000-00-00') :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-xs text-muted">'.JText::_('TEXT_BIRTHDAY').':</label>
					<p class="base-icon-birthday text-success"> '.baseHelper::dateFormat($view->birthday, JText::_('TEXT_BIRTHDAY_FORMAT')).'</p>
				</div>
			';
		endif;
		if($hasAdmin && !empty($view->cpf)) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-xs text-muted">CPF:</label>
					<p>'.$view->cpf.'</p>
				</div>
			';
		endif;
		if($hasAdmin && !empty($view->cnpj)) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-xs text-muted">CNPJ:</label>
					<p>'.$view->cnpj.'</p>
				</div>
			';
		endif;
		if($view->marital_status > 0) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_MARITAL_STATUS').':</label>
					<p>'.JText::_('TEXT_MARITAL_STATUS_'.$view->marital_status).'</p>
				</div>
			';
		endif;
		if($view->children > 0) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_CHILDREN').':</label>
					<p>'.$view->children.'</p>
				</div>
			';
		endif;
		if(!empty($info2)) $info2 = '<div class="row">'.$info2.'</div>';

		// Extra Info
		$about_me = '';
		if(!empty($view->about_me)) :
			$about_me .= '
				<div class="'.$APPTAG.'-profile-about font-condensed text-primary px-3 py-2 mb-3 bg-white rounded set-shadow">'.nl2br($view->about_me).'</div>
			';
		endif;

		// Phones
		$phones = '';
		if(!empty($view->phone)) :
			$ph = explode(';', $view->phone);
			$wp = explode(';', $view->whatsapp);
			$pd = explode(';', $view->phone_desc);
			for($i = 0; $i < count($ph); $i++) {
				$whapps = $wp[$i] == 1 ? ' <span class="base-icon-whatsapp text-success cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_WHATSAPP').'"></span>' : '';
				$phDesc = !empty($pd[$i]) ? '<div class="small text-muted">'.$pd[$i].'</div>' : '';
				$phones .= '<div class="lh-1-3 pb-1">'.$ph[$i].$whapps.$phDesc.'</div>';
			}
		endif;
		// Chats
		$chats = '';
		if(!empty($view->chat_user) && !$hasClient) :
			$cName = explode(';', $view->chat_name);
			$cUser = explode(';', $view->chat_user);
			for($i = 0; $i < count($cName); $i++) {
				if(!empty($cName[$i])) :
					$chats .= '
						<div class="pb-1">
							'.baseHelper::nameFormat($cName[$i]).'<span class="float-right mr-2">'.$cUser[$i].'</span>
						</div>
					';
				endif;
			}
		endif;
		// Contact data
		$contact = '';
		if((!empty($phones) || !empty($chats)) && !$hasClient) :
			$cDiv = (!empty($phones) && !empty($chats)) ? '<hr class="b-top-dashed my-2" />' : '';
			$contact .= '
				<hr class="hr-tag b-top-dashed b-primary">
				<span class="badge badge-primary"> '.JText::_('TEXT_CONTACT_DATA').'</span>
				'.$phones.$cDiv.$chats.'
			';
		endif;

		// Weblinks
		$links = '';
		if(!empty($view->weblink_url)) :
			$wTxt = explode(';', $view->weblink_text);
			$wUrl = explode(';', $view->weblink_url);
			$links = (count($wUrl) > 0) ? '<ul class="set-list bordered b-top b-top-dashed pt-1">' : '';
			for($i = 0; $i < count($wUrl); $i++) {
				$text = !empty($wTxt[$i]) ? $wTxt[$i] : $wUrl[$i];
				$links .= '<li> <a href="'.$wUrl[$i].'" class="base-icon-link" target="_blank"> '.baseHelper::nameFormat($text).'</a></li>';
			}
			if(count($wUrl) > 0) $links .= '</ul>';
		endif;

		// Address
		$addressInfo = !empty($view->address_info) ? ', '.$view->address_info : '';
		$addressNumber = !empty($view->address_number) ? ', '.$view->address_number : '';
		$addressZip = !empty($view->zip_code) ? $view->zip_code.', ' : '';
		$addressDistrict = !empty($view->address_district) ? baseHelper::nameFormat($view->address_district) : '';
		$addressCity = !empty($view->address_city) ? ', '.baseHelper::nameFormat($view->address_city) : '';
		$addressState = !empty($view->address_state) ? ', '.baseHelper::nameFormat($view->address_state) : '';
		$addressCountry = !empty($view->address_country) ? ', '.baseHelper::nameFormat($view->address_country) : '';

		// Endereço
		$address = '';
		if(!empty($view->address)) :
			$address .= '
				<div class="col-lg">
					<hr class="hr-tag b-top-dashed">
					<span class="badge badge-primary"> '.JText::_('FIELD_LABEL_ADDRESS').'</span>
					<p>
							'.baseHelper::nameFormat($view->address).$addressNumber.$addressInfo.'<br />
							'.$addressZip.$addressDistrict.$addressCity.$addressState.$addressCountry.'
					</p>
				</div>
			';
		endif;

		$bankAccount_info = '';
		if($hasAdmin && !empty($view->bank_name) && !empty($view->account)) :
			$bankAccount_info = '
				<hr class="hr-tag b-top-dashed">
				<span class="badge badge-primary text-uppercase"> '.JText::_('TEXT_BANKS_ACCOUNT').'</span>
				<h6 class="mb-1 base-icon-bank"> '.$view->bank_name.'</h6>
				<div class="d-flex">
					<div><label class="label-xs text-muted">'.JText::_('FIELD_LABEL_AGENCY').'</label>'.$view->agency.'</div>
					<div class="px-3"><label class="label-xs text-muted">'.JText::_('FIELD_LABEL_OPERATION').'</label>'.$view->operation.'</div>
					<div><label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ACCOUNT').'</label>'.$view->account.'</div>
				</div>
			';
		endif;

		// Acesso
		$access = ($view->access == 1 && !empty($view->user_id)) ? ' <span class="badge badge-info base-icon-plug cursor-help hasTooltip" title="'.JText::_('TEXT_ACCESS_GROUP').'"> '.$view->usergroup.'</span>' : '';

		$nickname = !empty($view->nickname) ? ' <small>('.baseHelper::nameFormat($view->nickname).')</small>' : '';
		// files info
		$files = '';
		if($hasAdmin || $view->user_id == $user->id) {
			if(isset($listFiles[1])) $files .= $listFiles[1]; // currículo
			if(isset($listFiles[2])) $files .= $listFiles[2]; // contrato
		}
		if(!empty($files)) $files = '<hr class="my-2" />'.$files;

		$edit = ($hasAdmin || $view->user_id == $user->id) ? '<hr class="my-2" /><a href="'.JURI::root().'apps/'.$APPNAME.'/edit-profile" class="btn btn-sm btn-warning btn-block base-icon-pencil"> '.JText::_('TEXT_EDIT').'</a>' : '';

		$gender = '';
		if($view->gender > 0) :
			$gIcon = ($view->gender == 1) ? 'base-icon-male text-blue' : 'base-icon-female text-pink';
			$gender = '<span class="'.$gIcon.' cursor-help hasTooltip" title="'.JText::_('TEXT_GENDER_'.$view->gender).'"></span> ';
		endif;

		$extraInfo = '';
		if(!empty($contact) || !empty($links) || !empty($bankAccount_info)) {
			$extraInfo = '
				<div class="col'.(!empty($address) ? '-sm-4' : '').'">
					'.$contact.$links.$bankAccount_info.'
				</div>
			';
		}

		echo '
			<div class="row">
				<div class="col-4 col-sm-2 mb-4 mb-md-0">
					<div style="max-width: 300px">'.$img.'</div>
					<div class="text-sm">
						'.$files.$edit.'
					</div>
				</div>
				<div class="col-sm">
					'.$about_me.'
					<div class="row">
						<div class="col-md-8">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_NAME').':</label>
							<p>'.$gender.baseHelper::nameFormat($view->name).$nickname.'</p>
						</div>
						<div class="col-md-4">
							<label class="label-xs text-muted">'.JText::_('TEXT_USERNAME').':</label>
							<p>'.(!empty($view->username) ? $view->username.' '.$access : '<span class="text-danger">'.JText::_('TEXT_ACCESS_DANIED').'</span>').'</p>
						</div>
					</div>
					'.$info1.$info2.'
					<div class="row">
						'.$address.$extraInfo.'
					</div>
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
