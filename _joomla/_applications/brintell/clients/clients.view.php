<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = 1; // Público -> acesso aberto a todos

// IMPORTANTE:
// Como outras Apps serão carregadas, através de "require", dentro dessa aplicação.
// As variáveis php da App principal serão sobrescritas após as chamadas das outras App.
// Dessa forma, para manter as variáveis, necessárias, da aplicação principal é necessário
// atribuir à variáveis personalizadas. Caso seja necessário, declare essas variáveis abaixo...
$MAINAPP	= $APPNAME;
$MAINTAG	= $APPTAG;
$PROJECT	= $cfg['project'];

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
require_once('clients.select.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query	= '
		SELECT
			T1.*,
			'. $db->quoteName('T2.name') .' groupName
		FROM '.
			$db->quoteName($cfg['mainTable']) .' T1
			LEFT JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
			ON T2.id = T1.group_id AND T2.state = 1
		WHERE '. $db->quoteName('T1.id') .' = '. $vID
	;
	try {
		$db->setQuery($query);
		$view = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	$client = '';
	if(!empty($view->name)) : // verifica se existe

		// define permissões de execução
		$canEdit	= ($cfg['canEdit'] || $view->created_by == $user->id);
		$canDelete	= ($cfg['canDelete'] || $view->created_by == $user->id);

		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $view->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $img = '<img src="images/apps/'.$APPPATH.'/'.$img['filename'].'" class="w-100 img-fluid b-all p-1 mb-3" />';
		else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"><small>'.JText::_('TEXT_NO_IMAGE').'</small></span></div></div></div>';

		// Contrato -> (index = 1)
		$doc = uploader::getFile($cfg['fileTable'], '', $view->id, 1, $cfg['uploadDir']);
		if(!empty($doc) && $hasAdmin) :
			$doc = '
				<a class="btn btn-info btn-sm btn-block my-1" href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($doc['filename']).'&mt='.base64_encode($doc['mimetype']).'&tag='.base64_encode($APPNAME).'">
					<span class="base-icon-doc-text hasTooltip" title="'.$doc['filename'].'<br />'.((int)($doc['filesize'] / 1024)).'kb"> '.JText::_('FIELD_LABEL_DOC').'</span>
				</a>
			';
		endif;

		$razao	= !empty($view->company_name) ? '<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_COMPANY_NAME').'</label><p class="text-truncate">'.baseHelper::nameFormat($view->company_name).'</p>' : '';
		$site	= !empty($view->website) ? '<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_WEBSITE').'</label><p class="text-truncate"><a href="'.$view->website.'" class="new-window" target="_blank">'.$view->website.'</a></p>' : '';
		$email	= !empty($view->email) ? '<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMAIL').'</label><p class="text-truncate"><a href="mailto:'.$view->email.'" class="mr-3">'.$view->email.'</a></p>' : '';
		$cnpj	= !empty($view->cnpj) ? '<label class="label-xs text-muted">CNPJ</label><p>'.$view->cnpj.'</p>' : '';
		$date	= $view->due_date != 0 ? '<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_DUE_DATE').'</label><p>'.($view->due_date < 10 ? '0' : '').$view->due_date.'</p>' : '';
		$since	= $view->start_date != '0000-00-00' ? '<label class="label-xs text-muted">'.JText::_('TEXT_SINCE').'</label><p>'.baseHelper::dateFormat($view->due_date).'</p>' : '';
		$agree	= $view->portfolio == 1 ? '<li><span class="text-success text-uppercase"><span class="base-icon-ok"></span> '.JText::_('FIELD_LABEL_PORTFOLIO').'</span></li>' : '';
		$gName	= !empty($view->groupName) ? '<li>'.$view->groupName.'</li>' : '';
		$lProj	= '
			<a class="btn btn-success btn-block my-1 base-icon-cogs" href="'.JURI::root().'dashboard">
				'.JText::_('TEXT_PROJECTS').'
			</a>
		';

		$info1 = '';
		if(!empty($site))	$info1 .= $razao;
		if(!empty($site))	$info1 .= $site;
		if(!empty($email))	$info1 .= $email;
		if(!empty($info1))	$info1 = '<div class="col-sm-8">'.$info1.'</div>';

		$info2 = '';
		if(!empty($cnpj) && $hasAdmin)	$info2 .= $cnpj;
		if(!empty($since) && $hasAdmin)	$info2 .= $since;
		if(!empty($date) && $hasAdmin)	$info2 .= $date;
		if(!empty($info2))	$info2 = '<div class="col-sm">'.$info2.'</div>';

		// Info Container
		if(!empty($info1) || !empty($info2)) $info = '<div class="row">'.$info1.$info2.'</div>';

		// description
		$description = !empty($view->description) ? '<div id="'.$MAINTAG.'-desc">'.$view->description.'</div>' : '';

		echo '
			<div class="row">
				<div class="col-sm-2 col-md-3 col-xl-2 d-none d-sm-block">
					'.$img.$doc.$lProj.'
				</div>
				<div class="col">
					<ul class="set-list inline bordered list-trim text-muted small mb-2 pb-1 b-bottom b-bottom-dotted">
						'.$agree.$gName.'
					</ul>
					<h1 class="mt-0">
						'.baseHelper::nameFormat($view->name).'
					</h1>
					'.$description.'
					<hr class="mt-2" />
					<div class="row">
						<div class="col-lg-8">
							'.$info.'
							<hr class="hr-tag b-top-dashed b-primary">
							<span class="badge badge-primary">'.JText::_('TEXT_LOCATIONS').'</span>
							<div id="'.$MAINTAG.'TabViewLocation">
		';
								// Locations
								$_locationsIsPublic			= 1; // public view
								$_locationsOnlyBR			= false;
								$_locationsStateDef			= '';
								$_locationsListFull			= false;
								$_locationsAddText			= false;
								$_locationsShowAddBtn		= false;
								$_locationsRelTag			= 'clients';
								$_locationsRelTable			= '#__'.$PROJECT.'_rel_clients_locations';
								$_locationsAppNameId		= 'location_id';
								$_locationsRelNameId		= 'client_id';
								$_locationsRelListNameId	= 'client_id';
								$_locationsRelListId		= $view->id;

								require(JPATH_APPS.DS.'_locations/_locations.php');
								echo '	<a href="#" class="btn btn-sm btn-success base-icon-plus" onclick="_locations_setRelation('.$view->id.')" data-toggle="modal" data-target="#modal-_locations" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_INSERT_LOCATION').'</a>';
		echo '
							</div>
							<hr class="d-sm-none" />
						</div>
						<div class="col-lg-4">
		';

							// Staff
							$clientsStaffIsPublic			= 1; // public view
							$clientsStaffAdminGroups		= array(12,15); // Analyst, Client Manager
							$clientsStaffListFull			= false;
							$clientsStaffAddText			= false;
							$clientsStaffShowAddBtn			= false;
							$clientsStaffRelTag				= 'clients';
							$clientsStaffRelListNameId		= 'client_id';
							$clientsStaffRelListId			= $view->id;
							$clientsStaffOnlyChildList		= true;
							$clientsStaffHideParentField	= true;
							echo '
								<h6 class="page-header base-icon-users-alt">
									'.JText::_('TEXT_STAFF').'
									<a href="#" class="btn btn-xs btn-success float-right" onclick="clientsStaff_setParent('.$view->id.')" data-toggle="modal" data-target="#modal-clientsStaff" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_STAFF_MEMBER').'"></span></a>
								</h6>
							';
							require(JPATH_APPS.DS.'clientsStaff/clientsStaff.php');

							if($hasAdmin) {
								// Phones
								$_callCentersListFull				= false;
								$_callCentersAddText				= false;
								$_callCentersShowAddBtn				= false;
								$_callCentersRelTag					= 'clients';
								$_callCentersRelTable				= '#__'.$PROJECT.'_rel_clients_callCenters';
								$_callCentersAppNameId				= 'callCenter_id';
								$_callCentersRelNameId				= 'client_id';
								$_callCentersRelListNameId			= 'client_id';
								$_callCentersRelListId				= $view->id;
								echo '
									<h6 class="page-header base-icon-phone-squared pt-2">
										'.JText::_('TEXT_CALL_CENTERS').'
										<a href="#" class="btn btn-xs btn-success float-right" onclick="_callCenters_setRelation('.$view->id.')" data-toggle="modal" data-target="#modal-_callCenters" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_CALL_CENTER').'"></span></a>
									</h6>
								';
								require(JPATH_APPS.DS.'_callCenters/_callCenters.php');
							}

							if($hasAdmin) {
								// Banks Accounts
								$_banksAccountsListFull			= false;
								$_banksAccountsAddText			= false;
								$_banksAccountsShowAddBtn		= false;
								$_banksAccountsRelTag			= 'clients';
								$_banksAccountsRelTable			= '#__'.$PROJECT.'_rel_clients_banksAccounts';
								$_banksAccountsAppNameId		= 'bankAccount_id';
								$_banksAccountsRelNameId		= 'client_id';
								$_banksAccountsRelListNameId	= 'client_id';
								$_banksAccountsRelListId		= $view->id;
								echo '
									<h6 class="page-header base-icon-bank pt-2">
										'.JText::_('TEXT_BANKS_ACCOUNTS').'
										<a href="#" class="btn btn-xs btn-success float-right" onclick="_banksAccounts_setRelation('.$view->id.')" data-toggle="modal" data-target="#modal-_banksAccounts" data-backdrop="static" data-keyboard="false"><span class="base-icon-plus hasTooltip" title="'.JText::_('TEXT_INSERT_BANK_ACCOUNT').'"></span></a>
									</h6>
								';
								require(JPATH_APPS.DS.'_banksAccounts/_banksAccounts.php');
							}

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
