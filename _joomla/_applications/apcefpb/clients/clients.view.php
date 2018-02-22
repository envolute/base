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
require_once('clients.select.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = '
		SELECT
			T1.*,
			IF(T1.agency <> "" AND T1.account <> "" AND T1.operation <> "", 1, 0) account_info,
			'. $db->quoteName('T2.title') .' type
		FROM
			'.$db->quoteName($cfg['mainTable']).' T1
			LEFT OUTER JOIN '. $db->quoteName('#__usergroups') .' T2
			ON T2.id = T1.usergroup
		WHERE '.$db->quoteName('T1.id') .' = '. $vID
	;
	try {
		$db->setQuery($query);
		$view = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	$html = '';
	if(!empty($view->name)) : // verifica se existe

		// define permissões de execução
		$canEdit	= ($cfg['canEdit'] || $view->created_by == $user->id);
		$canDelete	= ($cfg['canDelete'] || $view->created_by == $user->id);

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $view->id, 0, $cfg['uploadDir']);
			if(!empty($img)) {
				$img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 300).'" class="img-fluid b-all b-all-dashed p-1" />';
				$printCard = '
					<div class="pt-3">
						<label class="label-xs text-muted">'.JText::_('TEXT_CLIENT_CARD').'</label>
						<button type="button" class="btn btn-lg btn-block btn-success base-icon-print btn-icon" onclick="'.$APPTAG.'_printCard('.$view->id.')"> '.JText::_('TEXT_PRINT').'</button>
					</div>
				';
			} else {
				$img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';
				$printCard = '';
			}
		endif;

		$undefined = '<span class="base-icon-attention text-live"> '.JText::_('TEXT_UNDEFINED').'</span>';
		$partner = '';
		if(!empty($view->partner)) :
			$partner = '
				<div class="col-12">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PARTNER').':</label><p>'.baseHelper::nameFormat($view->partner).'</p>
				</div>
			';
		endif;
		// Address
		$addressInfo = !empty($view->address_info) ? ', '.$view->address_info : '';
		$addressNumber = !empty($view->address_number) ? ', '.$view->address_number : '';
		$addressZip = !empty($view->zip_code) ? $view->zip_code.', ' : '';
		$addressDistrict = !empty($view->address_district) ? baseHelper::nameFormat($view->address_district) : '';
		$addressCity = !empty($view->address_city) ? ', '.baseHelper::nameFormat($view->address_city) : '';
		$addressState = !empty($view->address_state) ? ', '.$view->address_state : '';
		$addressCountry = !empty($view->address_country) ? ', '.baseHelper::nameFormat($view->address_country) : '';
		// Phones
		$phones = '';
		$ph = explode(';', $view->phone);
		if(!empty($view->phone) && $view->phone != ';') :
			$wp = explode(';', $view->whatsapp);
			$pd = explode(';', $view->phone_desc);
			for($i = 0; $i < count($ph); $i++) {
				$whapps = $wp[$i] == 1 ? ' <span class="base-icon-whatsapp text-success cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_WHATSAPP').'"></span>' : '';
				$phDesc = !empty($pd[$i]) ? '<div class="small text-muted lh-1 mb-2">'.$pd[$i].'</div>' : '';
				$phones .= '<div>'.$ph[$i].$whapps.$phDesc.'</div>';
			}
		endif;
		$required = array($view->cpf, $view->rg, $view->rg_orgao, $view->place_birth, $view->marital_status, $view->gender, $view->mother_name, $view->father_name, $view->address, $view->address_number, $view->address_district, $view->address_city, $view->agency, $view->account, $view->operation);
		$incomplete = false;
		for($i = 0; $i < count($required); $i++) {
			if(empty($required[$i]) || $required[$i] === 0) $incomplete = true;
		}
		if(empty($view->birthday) || $view->birthday == '0000-00-00') $incomplete = true;
		// Show incomplete data message
		if($incomplete) echo '<div class="alert alert-warning base-icon-attention"> '.JText::_('MSG_INCOMPLETE_DATA').'</div>';

		// Tratamento de campos obrigatórios
		$gender = ($view->gender == 0) ? $undefined : JText::_('TEXT_GENDER_'.$view->gender);
		$mStatus = ($view->marital_status == 0) ? $undefined : JText::_('TEXT_MARITAL_STATUS_'.$view->marital_status);
		$mother = empty($view->mother_name) ? $undefined : baseHelper::nameFormat($view->mother_name);
		$father = empty($view->father_name) ? $undefined : baseHelper::nameFormat($view->father_name);
		$birthday = (empty($view->birthday) || $view->birthday == '0000-00-00') ? $undefined : baseHelper::dateFormat($view->birthday);
		$place = empty($view->place_birth) ? $undefined : baseHelper::nameFormat($view->place_birth);
		$cpf = empty($view->cpf) ? $undefined : $view->cpf;
		$rg = empty($view->rg) ? $undefined : $view->rg.' / '.$view->rg_orgao;
		$address = empty($view->address) ? $undefined : baseHelper::nameFormat($view->address).$addressNumber.$addressInfo.'<br />'.$addressZip.$addressDistrict.$addressCity.$addressState;
		$phones = empty($phones) ? $undefined : $phones;

		$html .= '
			<div class="row">
				<div class="col-sm-4 col-md-2 mb-4 mb-md-0">
					<div style="max-width: 300px">'.$img.'</div>
					'.$printCard.'
				</div>
				<div class="col-sm-8 col-md-6">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_NAME').':</label>
					<p> '.baseHelper::nameFormat($view->name).'</p>
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMAIL').':</label>
					<p>'.$view->email.'</p>
					<div class="row">
						<div class="col-4">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_GENDER').':</label>
							<p>'.$gender.'</p>
						</div>
						<div class="col-4">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_MARITAL_STATUS').':</label>
							<p>'.$mStatus.'</p>
						</div>
						<div class="col-4">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_CHILDREN').':</label>
							<p>'.$view->children.'</p>
						</div>
						'.$partner.'
						<div class="col-lg-6">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_MOTHER_NAME').':</label>
							<p>'.$mother.'</p>
						</div>
						<div class="col-lg-6">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_FATHER_NAME').':</label>
							<p>'.$father.'</p>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="row">
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-xs text-muted">'.JText::_('TEXT_USER_TYPE').':</label>
							<p> '.baseHelper::nameFormat($view->type).'</p>
						</div>
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_BIRTHDAY').':</label>
							<p>'.$birthday.'</p>
						</div>
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PLACE_BIRTH').':</label>
							<p>'.$place.'</p>
						</div>
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-xs text-muted">CPF:</label>
							<p>'.$cpf.'</p>
						</div>
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-xs text-muted">RG:</label>
							<p>'.$rg.'</p>
						</div>
					</div>
				</div>
			</div>
			<hr />
			<div class="row">
				<div class="col-md-8">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ADDRESS').':</label>
					<p>'.$address.'</p>
				</div>
				<div class="col-md">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PHONE').'(s):</label>
					'.$phones.'
				</div>
			</div>
			<div class="row">
		';
		if($view->usergroup != 13) :
			$html .= '
					<div class="col-md-8">
						<hr class="hr-tag" />
						<span class="badge badge-primary">'.JText::_('TEXT_DATA_EMPLOYEE').'</span>
						<div class="row">
							<div class="col-sm-4">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_STATUS_EMPLOYEE').':</label>
								<p>'.($view->usergroup == 11 ? JText::_('TEXT_EFFECTIVE') : JText::_('TEXT_RETIRED')).'</p>
							</div>
			';
		endif;
		if($view->usergroup == 11) :
			$cx_email = empty($view->cx_email) ? $undefined : $view->cx_email;
			$cx_situated = empty($view->cx_situated) ? $undefined : $view->cx_situated;
			$html .= '
							<div class="col-sm-6 col-md-4">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMAIL').' Caixa:</label>
								<p>'.$cx_email.'</p>
							</div>
							<div class="col-6 col-sm-4">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_SITUATED').':</label>
								<p>'.$cx_situated.'</p>
							</div>
			';
		endif;
		if($view->usergroup != 13) :
			$cx_code = empty($view->cx_code) ? $undefined : $view->cx_code;
			$cx_date = (empty($view->cx_date) || $view->cx_date == '0000-00-00') ? $undefined : baseHelper::dateFormat($view->cx_date);
			$cx_role = empty($view->cx_role) ? $undefined : $view->cx_role;
			$html .= '
							<div class="col-6 col-md-4">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_CODE').':</label>
								<p>'.$cx_code.'</p>
							</div>
							<div class="col-6 col-sm-4">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ADMISSION_DATE').':</label>
								<p>'.$cx_date.'</p>
							</div>
							<div class="col-6 col-sm-4">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ROLE').':</label>
								<p>'.$cx_role.'</p>
							</div>
						</div>
					</div>
			';
		endif;

		// DADOS DE COBRANÇA
		$accountData = '
			<label class="label-xs text-muted">Conta Bancária:</label>
			<p>
				'.JText::_('FIELD_LABEL_AGENCY').': <strong>'.$view->agency.'</strong><br />
				'.JText::_('FIELD_LABEL_ACCOUNT').': <strong>'.$view->account.'</strong><br />
				'.JText::_('FIELD_LABEL_OPERATION').': <strong>'.$view->operation.'</strong>
			</p>
		';
		if($view->enable_debit == 1 && !empty($view->agency) && !empty($view->account) && !empty($view->operation)) :
			$debit = '
				<div class="mb-2"><span class="base-icon-ok-circled text-success"> '.JText::_('TEXT_DEBIT_ACTIVE').'</span></div>
				'.$accountData
			;
		else :
			if($view->enable_debit == 0) :
				$debitMsg = 'TEXT_DEBIT_NOT_ENABLE';
				$accountData = '';
			elseif($view->account_info == 0) :
				$debitMsg = 'TEXT_INCOMPLETE_ACCOUNT_INFORMATION';
			endif;
			$debit = '
				<div class="mb-2"><span class="base-icon-cancel-circled text-danger"> '.JText::_($debitMsg).'</span></div>
				'.$accountData
			;
		endif;
		$html .= '
				<div class="col">
					<hr class="hr-tag" />
					<span class="badge badge-primary">'.JText::_('TEXT_PAYMENT_DATA').'</span>
					'.$debit.'
				</div>
			</div>
		';

		echo $html;

		// DEPENDENTES
		// Contacts
		$dependentsListFull		= false;
		$dependentsShowAddBtn	= false;
		$dependentsRelTag		= 'clients';
		$dependentsRelListNameId= 'client_id';
		$dependentsRelListId	= $view->id;
		$dependentsOnlyChildList= true;
		echo '
			<h4 class="page-header base-icon-users pt-5">
				'.JText::_('TEXT_DEPENDENTS').'
				<a href="#" class="btn btn-xs btn-success float-right base-icon-plus" onclick="dependents_setParent('.$view->id.')" data-toggle="modal" data-target="#modal-dependents" data-backdrop="static" data-keyboard="false"> '.JText::_('TEXT_ADD').'</a>
			</h4>
		';
		require(JPATH_APPS.DS.'dependents/dependents.php');

	else :
		echo '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_ITEM_NOT_AVAILABLE').'</p>';
	endif;

else :

	echo '<h4 class="alert alert-warning">'.JText::_('MSG_NO_ITEM_SELECTED').'</h4>';

endif;
?>
