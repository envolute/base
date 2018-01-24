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
require_once('_providers.select.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' grp
		FROM '.
			$db->quoteName($cfg['mainTable']) .' T1
			JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON T2.id = T1.group_id AND T2.state = 1
		WHERE '. $db->quoteName('T1.id') .' = '. $vID
	;
	try {
		$db->setQuery($query);
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	$provider = '';
	if(!empty($item->name)) : // verifica se existe

		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $img = '<img src="images/apps/'.$APPPATH.'/'.$img['filename'].'" class="w-100 img-fluid b-all p-1" />';
		else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"><small>'.JText::_('TEXT_NO_IMAGE').'</small></span></div></div></div>';

		// Contrato -> (index = 1)
		$doc = uploader::getFile($cfg['fileTable'], '', $item->id, 1, $cfg['uploadDir']);
		if(!empty($doc)) :
			$doc = '
				<a class="nav-link" href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($doc['filename']).'&mt='.base64_encode($doc['mimetype']).'&tag='.base64_encode($APPNAME).'">
					<span class="base-icon-doc-text hasTooltip" title="'.$doc['filename'].'<br />'.((int)($doc['filesize'] / 1024)).'kb"> '.JText::_('FIELD_LABEL_DOC').'</span>
				</a>
			';
		endif;

		$razao	= !empty($item->company_name) ? '<label class="label-sm">'.JText::_('FIELD_LABEL_COMPANY_NAME').'</label><p class="text-truncate">'.baseHelper::nameFormat($item->company_name).'</p>' : '';
		$site	= !empty($item->website) ? '<label class="label-sm">'.JText::_('FIELD_LABEL_WEBSITE').'</label><p class="text-truncate"><a href="'.$item->website.'" class="new-window" target="_blank">'.$item->website.'</a></p>' : '';
		$email	= !empty($item->email) ? '<label class="label-sm">'.JText::_('FIELD_LABEL_EMAIL').'</label><p class="text-truncate"><a href="mailto:'.$item->email.'" class="mr-3">'.$item->email.'</a></p>' : '';
		$cnpj	= !empty($item->cnpj) ? '<label class="label-sm">CNPJ</label><p>'.$item->cnpj.'</p>' : '';
		$insM	= !empty($item->insc_municipal) ? '<label class="label-sm">Inscrição Municipal</label><p>'.$item->insc_municipal.'</p>' : '';
		$insE	= !empty($item->insc_estadual) ? '<label class="label-sm">Inscrição Estadual</label><p>'.$item->insc_estadual.'</p>' : '';
		$date	= $item->due_date != 0 ? '<label class="label-sm">'.JText::_('FIELD_LABEL_DUE_DATE').'</label><p>'.($item->due_date < 10 ? '0' : '').$item->due_date.'</p>' : '';
		$agree	= $item->agreement == 1 ? '<span class="text-success text-uppercase"><span class="base-icon-ok"></span> '.JText::_('FIELD_LABEL_AGREEMENT').'</span>' : '';

		// contrato
		$docs = !empty($doc) ? '<li class="nav-item">'.$doc.'</li>' : '';

		$info1 = '';
		if(!empty($site))	$info1 .= $razao;
		if(!empty($site))	$info1 .= $site;
		if(!empty($email))	$info1 .= $email;
		if(!empty($date))	$info1 .= $date;
		if(!empty($info1))	$info1 = '<div class="col-sm-8">'.$info1.'</div>';

		$info2 = '';
		if(!empty($cnpj))	$info2 .= $cnpj;
		if(!empty($insM))	$info2 .= $insM;
		if(!empty($insE))	$info2 .= $insE;
		if(!empty($info2))	$info2 = '<div class="col-sm">'.$info2.'</div>';

		// Info Container
		if(!empty($info1) || !empty($info2)) $info = '<div class="row">'.$info1.$info2.'</div>';

		// description
		$description = !empty($item->description) ? '<div id="'.$MAINTAG.'-desc">'.$item->description.'</div>' : '';

		echo '
			<div class="row">
				<div class="col-sm-2 col-md-3 col-xl-2 d-none d-sm-block">
					'.$img.'
				</div>
				<div class="col">
					<ul class="set-list inline bordered list-trim text-muted small mb-2 pb-1 b-bottom b-bottom-dotted">
						<li>'.$agree.'</li>
						<li>'.$item->grp.'</li>
					</ul>
					<h1 class="mt-0">
						'.baseHelper::nameFormat($item->name).'
					</h1>
					'.$description.'
					<hr class="mt-2" />
					<div class="row">
						<div class="col-lg-8">
							'.$info.'
							<!-- Nav tabs -->
							<ul class="nav nav-tabs mt-3" id="'.$MAINTAG.'TabInfo" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="'.$MAINTAG.'TabView-location" href="#'.$MAINTAG.'TabViewLocation" data-toggle="tab" role="tab" aria-controls="location" aria-expanded="true">
										<span class="base-icon-location"></span> '.JText::_('TEXT_LOCATIONS').'
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="'.$MAINTAG.'TabView-service" href="#'.$MAINTAG.'TabViewService" data-toggle="tab" role="tab" aria-controls="service">
										<span class="base-icon-info-circled"></span> '.JText::_('FIELD_LABEL_SERVICE').'
									</a>
								</li>
								'.$docs.'
							</ul>

							<!-- Tab panes -->
							<div class="tab-content" id="'.$MAINTAG.'TabContent">
								<div class="tab-pane fade show active" id="'.$MAINTAG.'TabViewLocation" role="tabpanel" aria-labelledby="'.$MAINTAG.'TabView-location">
		';
									// Locations
									$_locationsOnlyBR				= true;
									$_locationsStateDef				= 'PB';
									$_locationsListFull				= false;
									$_locationsAddText				= false;
									$_locationsShowAddBtn			= false;
									$_locationsRelTag				= 'providers';
									$_locationsRelTable				= '#__base_rel_providers_locations';
									$_locationsAppNameId			= 'location_id';
									$_locationsRelNameId			= 'provider_id';
									$_locationsRelListNameId		= 'provider_id';
									$_locationsRelListId			= $item->id;

									require(JPATH_APPS.DS.'_locations/_locations.php');
									echo '	<a href="#" class="btn btn-sm btn-success base-icon-plus" onclick="_locations_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-_locations" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_INSERT_LOCATION').'</a>';
		echo '
								</div>
								<div class="tab-pane fade" id="'.$MAINTAG.'TabViewService" role="tabpanel" aria-labelledby="'.$MAINTAG.'TabView-service">
		';
									// Termos da parceria
									if(!empty($item->service_desc)) :
										echo '<h4 class="mt-4 page-header">'.JText::_('FIELD_LABEL_SERVICE').'</h4>';
										echo $item->service_desc;
									endif;
		echo '
								</div>
							</div>
							<hr class="d-sm-none" />
						</div>
						<div class="col-lg-4">
		';

							// Contacts
							$_providersContactsListFull		= false;
							$_providersContactsAddText		= false;
							$_providersContactsShowAddBtn	= false;
							$_providersContactsRelTag		= 'providers';
							$_providersContactsRelListNameId= 'provider_id';
							$_providersContactsRelListId	= $item->id;
							$_providersContactsOnlyChildList= true;
							echo '
								<h6 class="page-header base-icon-user">
									'.JText::_('TEXT_CONTACTS').'
									<a href="#" class="btn btn-xs btn-success float-right" onclick="_providersContacts_setParent('.$item->id.')" data-toggle="modal" data-target="#modal-_providersContacts" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_CONTACT').'"></span></a>
								</h6>
							';
							require(JPATH_APPS.DS.'_providersContacts/_providersContacts.php');

							// Phones
							$_callCentersListFull				= false;
							$_callCentersAddText				= false;
							$_callCentersShowAddBtn				= false;
							$_callCentersRelTag					= 'providers';
							$_callCentersRelTable				= '#__base_rel_providers_callCenters';
							$_callCentersAppNameId				= 'callCenter_id';
							$_callCentersRelNameId				= 'provider_id';
							$_callCentersRelListNameId			= 'provider_id';
							$_callCentersRelListId				= $item->id;
							echo '
								<h6 class="page-header base-icon-phone-squared pt-2">
									'.JText::_('TEXT_CALL_CENTERS').'
									<a href="#" class="btn btn-xs btn-success float-right" onclick="_callCenters_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-_callCenters" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_CALL_CENTER').'"></span></a>
								</h6>
							';
							require(JPATH_APPS.DS.'_callCenters/_callCenters.php');

							// Banks Accounts
							$_banksAccountsListFull			= false;
							$_banksAccountsAddText			= false;
							$_banksAccountsShowAddBtn		= false;
							$_banksAccountsRelTag			= 'providers';
							$_banksAccountsRelTable			= '#__base_rel_providers_banksAccounts';
							$_banksAccountsAppNameId		= 'bankAccount_id';
							$_banksAccountsRelNameId		= 'provider_id';
							$_banksAccountsRelListNameId	= 'provider_id';
							$_banksAccountsRelListId		= $item->id;
							echo '
								<h6 class="page-header base-icon-bank pt-2">
									'.JText::_('TEXT_BANKS_ACCOUNTS').'
									<a href="#" class="btn btn-xs btn-success float-right" onclick="_banksAccounts_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-_banksAccounts" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_BANK_ACCOUNT').'"></span></a>
								</h6>
							';
							require(JPATH_APPS.DS.'_banksAccounts/_banksAccounts.php');

		echo '
						</div>
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
