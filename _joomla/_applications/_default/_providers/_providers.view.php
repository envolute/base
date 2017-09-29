<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

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
$p = $app->input->get('p', 0, 'int');

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

if($p != 0) :

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
		WHERE '. $db->quoteName('T1.id') .' = '. $p
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
		if(!empty($img)) $img = '<img src=\''.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 200, 200).'\' class=\'img-fluid float-sm-left mb-4\' />';

		// Contrato -> (index = 1)
		$doc = uploader::getFile($cfg['fileTable'], '', $item->id, 1, $cfg['uploadDir']);
		if(!empty($doc)) :
			$doc = '
				<a href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
					<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
				</a>
			';
		endif;

		$site	= !empty($item->website) ? '<a href="'.$item->website.'" class="mr-3 new-window" target="_blank">'.$item->website.'</a>' : '';
		$email	= !empty($item->email) ? '<a href="mailto:'.$item->email.'" class="mr-3">'.$item->email.'</a>' : '';
		$cnpj	= !empty($item->cnpj) ? '<label class="label-sm mt-3">CNPJ</label>'.$item->cnpj : '';
		$insM	= !empty($item->insc_municipal) ? '<label class="label-sm mt-3">Inscrição Municipal</label>'.$item->insc_municipal : '';
		$insE	= !empty($item->insc_estadual) ? '<label class="label-sm mt-3">Inscrição Estadual</label>'.$item->insc_estadual : '';
		$date	= $item->due_date != 0 ? '<label class="label-sm mt-3">'.JText::_('FIELD_LABEL_DUE_DATE').'</label>'.($item->due_date < 10 ? '0' : '').$item->due_date : '';
		$docs	= !empty($doc) ? '<label class="label-sm mt-3">'.JText::_('FIELD_LABEL_DOC').'</label>'.$doc : '';
		$agree	= $item->agreement == 1 ? '<span class="badge badge-success float-right">'.JText::_('FIELD_LABEL_AGREEMENT').'</span>' : '';
		// Web
		$web	= (!empty($site) || !empty($email)) ? '<div class="text-md text-muted mt-2">'.$site.$email.'</div>' : '';

		echo '
			<div class="row">
				<div class="col-sm-4 col-md-3 col-xl-2">
					'.$img.'
					<div>'.$cnpj.$insM.$insE.$date.$docs.'</div>
					<hr class="d-sm-none" />
				</div>
				<div class="col-sm-8 col-md-9 col-xl-10">
					<h2 class="page-header mt-0">
						'.baseHelper::nameFormat($item->name).$agree.$web.'
					</h2>
					<div class="row">
						<div class="col-lg-8">
							'.$info.'
		';

							// description
							if(!empty($item->description)) echo $item->description.'<hr />';

		echo '
							<!-- Nav tabs -->
							<ul class="nav nav-tabs" id="'.$APPTAG.'TabInfo" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="'.$APPTAG.'Tab-address" href="#'.$APPTAG.'TabAddress" data-toggle="tab" role="tab" aria-controls="address" aria-expanded="true">
										<span class="base-icon-location"></span> '.JText::_('TEXT_ADDRESSES').'
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="'.$APPTAG.'Tab-service" href="#'.$APPTAG.'TabService" data-toggle="tab" role="tab" aria-controls="service">
										<span class="base-icon-doc-text"></span> '.JText::_('FIELD_LABEL_SERVICE').'
									</a>
								</li>
							</ul>

							<!-- Tab panes -->
							<div class="tab-content" id="'.$APPTAG.'TabContent">
								<div class="tab-pane fade show active" id="'.$APPTAG.'TabAddress" role="tabpanel" aria-labelledby="'.$APPTAG.'Tab-address">
		123';
									// Addresses
									$_addressesShowlist				= false;
									$_addressesListFull				= false;
									$_addressesAddText				= false;
									$_addressesShowAddBtn			= false;
									$_addressesRelTag				= 'providers';
									$_addressesRelTable				= '#__base_rel_providers_addresses';
									$_addressesAppNameId			= 'address_id';
									$_addressesRelNameId			= 'provider_id';
									$_addressesRelListNameId		= 'provider_id';
									$_addressesRelListId			= $item->id;
									echo '	<a href="#" class="btn btn-xs btn-success mb-2 base-icon-plus" onclick="_addresses_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-_addresses" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_INSERT_ADDRESS').'</a>';
									require(JPATH_APPS.DS.'_addresses/_addresses.php');

		echo '
								</div>
								<div class="tab-pane fade" id="'.$APPTAG.'TabService" role="tabpanel" aria-labelledby="'.$APPTAG.'Tab-service">
		234';
									// Termos da parceria
									if(!empty($item->service_desc)) :
										echo '<h4 class="mt-4 page-header">'.JText::_('FIELD_LABEL_SERVICE').'</h4>';
										echo $item->service_desc;
									endif;

	echo '
								</div>
							</div>
							<script>
								jQuery(function() {
									setTimeout(function() {
										jQuery("#'.$APPTAG.'Tab-service a").tab("show");
									}, 4000);
								});
							</script>
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
							<h6 class="page-header base-icon-user mb-0">
								'.JText::_('TEXT_CONTACTS').'
								<a href="#" class="btn btn-xs btn-success float-right" onclick="_providersContacts_setParent('.$item->id.')" data-toggle="modal" data-target="#modal-_providersContacts" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_CONTACT').'"></span></a>
							</h6>
						';
						require(JPATH_APPS.DS.'_providersContacts/_providersContacts.php');

						// Phones
						$_phonesListFull				= false;
						$_phonesAddText					= false;
						$_phonesShowAddBtn				= false;
						$_phonesRelTag					= 'providers';
						$_phonesRelTable				= '#__base_rel_providers_phones';
						$_phonesAppNameId				= 'phone_id';
						$_phonesRelNameId				= 'provider_id';
						$_phonesRelListNameId			= 'provider_id';
						$_phonesRelListId				= $item->id;
						echo '
							<h6 class="page-header base-icon-phone-squared mb-0">
								'.JText::_('TEXT_PROVIDER_PHONES').'
								<a href="#" class="btn btn-xs btn-success float-right" onclick="_phones_setRelation('.$item->id.')" data-toggle="modal" data-target="#modal-_phones" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_PHONE').'"></span></a>
							</h6>
						';
						require(JPATH_APPS.DS.'_phones/_phones.php');

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
							<h6 class="page-header base-icon-bank mb-0">
								'.JText::_('TEXT_ACCOUNTS').'
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

	echo '<h4 class="alert alert-warning">'.JText::_('MSG_NOT_PROVIDER_SELECTED').'</h4>';

endif;
?>
