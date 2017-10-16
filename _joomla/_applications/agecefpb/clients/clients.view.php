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
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	$html = '';
	if(!empty($item->name)) : // verifica se existe

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 300).'" class="img-fluid b-all b-dashed p-1" />';
			else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';
		endif;

		$partner = '';
		if(!empty($item->partner)) :
			$partner = '
				<div class="col">
					<label class="label-sm">'.JText::_('FIELD_LABEL_PARTNER').':</label><p>'.baseHelper::nameFormat($item->partner).'</p>
				</div>
			';
		endif;
		// Address
		$addressInfo = !empty($item->address_info) ? ', '.$item->address_info : '';
		$addressNumber = !empty($item->address_number) ? ', '.$item->address_number : '';
		$addressZip = !empty($item->zip_code) ? $item->zip_code.', ' : '';
		$addressDistrict = !empty($item->address_district) ? baseHelper::nameFormat($item->address_district) : '';
		$addressCity = !empty($item->address_city) ? ', '.baseHelper::nameFormat($item->address_city) : '';
		$addressState = !empty($item->address_state) ? ', '.baseHelper::nameFormat($item->address_state) : '';
		$addressCountry = !empty($item->address_country) ? ', '.baseHelper::nameFormat($item->address_country) : '';
		// Phones
		$phones = !empty($item->phone1) ? $item->phone1 : '';
		$phones .= !empty($item->phone2) ? (!empty($phones) ? '<br />' : '').$item->phone2 : '';
		$phones .= !empty($item->phone3) ? (!empty($phones) ? '<br />' : '').'(fixo) '.$item->phone3 : '';

		$html .= '
			<div class="row">
				<div class="col-sm-4 col-md-2 mb-4 mb-md-0">
					<div style="max-width: 300px">'.$img.'</div>
				</div>
				<div class="col-sm-8 col-md-6">
					<label class="label-sm">'.JText::_('FIELD_LABEL_NAME').':</label>
					<p> '.baseHelper::nameFormat($item->name).'</p>
					<label class="label-sm">'.JText::_('FIELD_LABEL_EMAIL').':</label>
					<p>'.$item->email.'</p>
					<div class="row">
						<div class="col-4">
							<label class="label-sm">'.JText::_('FIELD_LABEL_GENDER').':</label>
							<p>'.JText::_('TEXT_GENDER_'.$item->gender).'</p>
						</div>
						<div class="col-4">
							<label class="label-sm">'.JText::_('FIELD_LABEL_MARITAL_STATUS').':</label>
							<p>'.JText::_('TEXT_MARITAL_STATUS_'.$item->marital_status).'</p>
						</div>
						<div class="col-4">
							<label class="label-sm">'.JText::_('FIELD_LABEL_CHILDREN').':</label>
							<p>'.$item->children.'</p>
						</div>
						'.$partner.'
					</div>
				</div>
				<div class="col-md-4">
					<div class="row">
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-sm">'.JText::_('TEXT_USER_TYPE').':</label>
							<p> '.baseHelper::nameFormat($item->type).'</p>
						</div>
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-sm">'.JText::_('FIELD_LABEL_BIRTHDAY').':</label>
							<p>'.baseHelper::dateFormat($item->birthday).'</p>
						</div>
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-sm">CPF:</label>
							<p>'.$item->cpf.'</p>
						</div>
						<div class="col-6 col-sm-4 col-md-12">
							<label class="label-sm">RG:</label>
							<p>'.$item->rg.' / '.$item->rg_orgao.'</p>
						</div>
					</div>
				</div>
			</div>
			<hr />
			<div class="row">
				<div class="col-md-8">
					<label class="label-sm">'.JText::_('FIELD_LABEL_ADDRESS').':</label>
					<p>
						'.baseHelper::nameFormat($item->address).$addressNumber.$addressInfo.'<br />
						'.$addressZip.$addressDistrict.$addressCity.$addressState.$addressCountry.'
					</p>
				</div>
				<div class="col-md">
					<label class="label-sm">'.JText::_('FIELD_LABEL_PHONE').'(s):</label>
					<p>'.$phones.'</p>
				</div>
			</div>
			<div class="row">
		';
		if($item->usergroup != 13) :
			$html .= '
					<div class="col-md-8">
						<hr class="hr-tag" />
						<span class="badge badge-primary">'.JText::_('TEXT_DATA_EMPLOYEE').'</span>
						<div class="row">
							<div class="col-sm-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_STATUS_EMPLOYEE').':</label>
								<p>'.($item->usergroup == 11 ? JText::_('TEXT_EFFECTIVE') : JText::_('TEXT_RETIRED')).'</p>
							</div>
			';
		endif;
		if($item->usergroup == 11) :
			$html .= '
							<div class="col-sm-6 col-md-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_EMAIL').':</label>
								<p>'.$item->cx_email.'</p>
							</div>
							<div class="col-6 col-sm-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_SITUATED').':</label>
								<p>'.$item->cx_situated.'</p>
							</div>
			';
		endif;
		if($item->usergroup != 13) :
				$html .= '
							<div class="col-6 col-md-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_CODE').':</label>
								<p>'.$item->cx_code.'</p>
							</div>
							<div class="col-6 col-sm-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_ADMISSION_DATE').':</label>
								<p>'.baseHelper::dateFormat($item->cx_date).'</p>
							</div>
							<div class="col-6 col-sm-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_ROLE').':</label>
								<p>'.$item->cx_role.'</p>
							</div>
						</div>
					</div>
			';
		endif;

		// DADOS DE COBRANÇA
		$accountData = '
			<label class="label-sm">Conta Bancária:</label>
			<p>
				'.JText::_('FIELD_LABEL_AGENCY').': <strong>'.$item->agency.'</strong><br />
				'.JText::_('FIELD_LABEL_ACCOUNT').': <strong>'.$item->account.'</strong><br />
				'.JText::_('FIELD_LABEL_OPERATION').': <strong>'.$item->operation.'</strong>
			</p>
		';
		if($item->enable_debit == 1 && !empty($item->agency) && !empty($item->account) && !empty($item->operation)) :
			$debit = '
				<div class="mb-2"><span class="base-icon-ok-circled text-success"> '.JText::_('TEXT_DEBIT_ACTIVE').'</span></div>
				'.$accountData
			;
		else :
			if($item->enable_debit == 0) :
				$debitMsg = 'TEXT_DEBIT_NOT_ENABLE';
				$accountData = '';
			elseif($item->account_info == 0) :
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

	else :
		echo '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_ITEM_NOT_AVAILABLE').'</p>';
	endif;

else :

	echo '<h4 class="alert alert-warning">'.JText::_('MSG_NO_ITEM_SELECTED').'</h4>';

endif;
?>
