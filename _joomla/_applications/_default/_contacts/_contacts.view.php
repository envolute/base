<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

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

// Get request data
$vID = $app->input->get('vID', 0, 'int'); // VIEW 'ID'

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
require_once('_contacts.select.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' group_name
		FROM
			'.$db->quoteName($cfg['mainTable']).' T1
			LEFT OUTER JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON T2.id = T1.group_id
		WHERE '.$db->quoteName('T1.id') .' = '. $vID
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
		endif;

		// Address
		$addressInfo = !empty($view->address_info) ? ', '.$view->address_info : '';
		$addressNumber = !empty($view->address_number) ? ', '.$view->address_number : '';
		$addressZip = !empty($view->zip_code) ? $view->zip_code.', ' : '';
		$addressDistrict = !empty($view->address_district) ? baseHelper::nameFormat($view->address_district) : '';
		$addressCity = !empty($view->address_city) ? ', '.baseHelper::nameFormat($view->address_city) : '';
		$addressState = !empty($view->address_state) ? ', '.($view->onlyBR ? $view->address_state : baseHelper::nameFormat($view->address_state)) : '';
		$addressCountry = (!empty($view->address_country) && !$view->onlyBR) ? ', '.baseHelper::nameFormat($view->address_country) : '';
		// Phones
		$ph = explode(';', $view->phone);
		$wp = explode(';', $view->whatsapp);
		$pd = explode(';', $view->phone_desc);
		$phones = '';
		for($i = 0; $i < count($ph); $i++) {
			$whapps = $wp[$i] == 1 ? ' <span class="base-icon-whatsapp text-success cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_WHATSAPP').'"></span>' : '';
			$phDesc = !empty($pd[$i]) ? '<div class="small text-muted pb-1">'.$pd[$i].'</div>' : '';
			$phones .= '<div class="pb-1">'.$ph[$i].$whapps.$phDesc.'</div>';
		}
		// Chats
		$cName = explode(';', $view->chat_name);
		$cUser = explode(';', $view->chat_user);
		$chats = '';
		for($i = 0; $i < count($cName); $i++) {
			if(!empty($cName[$i])) $chats .= '<div class="pb-1"><strong>'.$cName[$i].'</strong>: '.$cUser[$i].'</div>';
		}
		// Weblinks
		$wTxt = explode(';', $view->weblink_text);
		$wUrl = explode(';', $view->weblink_url);
		$links = '';
		for($i = 0; $i < count($wUrl); $i++) {
			$text = !empty($wTxt[$i]) ? $wTxt[$i] : $wUrl[$i];
			$links .= '
				<div class="pb-1">
					<a href="'.$wUrl[$i].'" target="_blank">'.$text.'</a>
				</div>
			';
		}

		// Email, profissão
		$info1 = '';
		if(!empty($view->email)) :
			$info1 .= '
				<div class="col-sm-8">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMAIL').':</label>
					<p>'.$view->email.'</p>
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
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_BIRTHDAY').':</label>
					<p>'.baseHelper::dateFormat($view->birthday).'</p>
				</div>
			';
		endif;
		if(!empty($view->cpf)) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-xs text-muted">CPF:</label>
					<p>'.$view->cpf.'</p>
				</div>
			';
		endif;
		if(!empty($view->rg)) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-xs text-muted">RG:</label>
					<p>'.$view->rg.' / '.$view->rg_orgao.'</p>
				</div>
			';
		endif;
		if($view->gender > 0) :
			$info2 .= '
				<div class="col-6 col-sm-4">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_GENDER').':</label>
					<p>'.JText::_('TEXT_GENDER_'.$view->gender).'</p>
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
		if(!empty($view->partner)) :
			$info2 .= '
				<div class="col-12">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PARTNER').':</label><p>'.baseHelper::nameFormat($view->partner).'</p>
				</div>
			';
		endif;
		if(!empty($info2)) $info2 = '<div class="row">'.$info2.'</div>';

		// Endereço
		$address = '';
		if(!empty($view->address)) :
			$address .= '
				<div class="contact-address">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ADDRESS').':</label>
					<p>
						'.baseHelper::nameFormat($view->address).$addressNumber.$addressInfo.'<br />
						'.$addressZip.$addressDistrict.$addressCity.$addressState.$addressCountry.'
					</p>
				</div>
			';
		endif;

		// Extra Info
		$extra_info = '';
		if(!empty($view->extra_info)) :
			$extra_info .= '
				<div class="contact-extra-info">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EXTRA_INFO').':</label>
					<div class="mb-4">'.$view->extra_info.'</div>
				</div>
			';
		endif;

		// Acesso
		$access = ($view->access == 1 && !empty($view->user_id)) ? ' <span class="base-icon-plug text-success cursor-help hasTooltip" title="'.JText::_('TEXT_CONNECTED_USER').'"></span>' : '';

		echo '
			<div class="row">
				<div class="col-lg-9">
					<div class="row">
						<div class="col-4 col-sm-2 mb-4 mb-md-0">
							<div style="max-width: 300px">'.$img.'</div>
						</div>
						<div class="col-sm">
							<div class="row">
								<div class="col-md-8">
									<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_NAME').':</label>
									<p> '.baseHelper::nameFormat($view->name).$access.'</p>
								</div>
								<div class="col-md-4">
									<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_GROUP').':</label>
									<p> '.baseHelper::nameFormat($view->group_name).'</p>
								</div>
							</div>
							'.$info1.$info2.$address.$extra_info.'
						</div>
					</div>
				</div>
				<div class="col">
					<hr class="d-md-none my-2" />

		';
					// Contacts Data
					if(!empty($phones) || !empty($chats) || !empty($links)) :
						echo '
							<h6 class="page-header base-icon-phone-squared"> '.JText::_('TEXT_CONTACT_DATA').'</h6>
							<div class="mb-5">'.$phones.$chats.$links.'</div>
						';
					endif;

					// Banks Accounts
					$_banksAccountsListFull			= false;
					$_banksAccountsAddText			= false;
					$_banksAccountsShowAddBtn		= false;
					$_banksAccountsRelTag			= 'contacts';
					$_banksAccountsRelTable			= '#__base_rel_contacts_banksAccounts';
					$_banksAccountsAppNameId		= 'bankAccount_id';
					$_banksAccountsRelNameId		= 'contact_id';
					$_banksAccountsRelListNameId	= 'contact_id';
					$_banksAccountsRelListId		= $view->id;
					echo '
						<h6 class="page-header base-icon-bank">
							'.JText::_('TEXT_BANKS_ACCOUNTS').'
							<a href="#" class="btn btn-xs btn-success float-right" onclick="_banksAccounts_setRelation('.$view->id.')" data-toggle="modal" data-target="#modal-_banksAccounts" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_BANK_ACCOUNT').'"></span></a>
						</h6>
					';
					require(JPATH_APPS.DS.'_banksAccounts/_banksAccounts.php');

		echo '
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
