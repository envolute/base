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
$uID = $app->input->get('uID', 0, 'int');
$uID = ($hasAdmin && $uID > 0) ? $uID : $user->id;

// LINK TO EDIT
$urlEdit = JURI::root().'user/edit-client-profile'.($uID != $user->id ? '?uID='.$uID : '');

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

// Admin Actions
require_once(JPATH_APPS.DS.'clients/clients.select.user.php');

if(isset($user->id) && $user->id) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = '
		SELECT T1.*,
		IF(T1.agency <> "" AND T1.account <> "" AND T1.operation <> "", 1, 0) account_info,
		'. $db->quoteName('T2.title') .' type
		FROM '.$db->quoteName($cfg['mainTable']).' T1
			LEFT OUTER JOIN '. $db->quoteName('#__usergroups') .' T2
			ON T2.id = T1.usergroup
		WHERE '.$db->quoteName('T1.user_id') .' = '. $uID
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
			if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 300).'" class="img-fluid b-all b-all-dashed p-1" />';
			else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';
		endif;

		$undefined = '<span class="base-icon-attention text-live"> '.JText::_('TEXT_UNDEFINED').'</span>';
		$partner = '';
		if(!empty($item->partner)) :
			$partner = '
				<div class="col-12">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_PARTNER').':</label><p>'.baseHelper::nameFormat($item->partner).'</p>
				</div>
			';
		endif;
		// Address
		$addressInfo = !empty($item->address_info) ? ', '.$item->address_info : '';
		$addressNumber = !empty($item->address_number) ? ', '.$item->address_number : '';
		$addressZip = !empty($item->zip_code) ? $item->zip_code.', ' : '';
		$addressDistrict = !empty($item->address_district) ? baseHelper::nameFormat($item->address_district) : '';
		$addressCity = !empty($item->address_city) ? ', '.baseHelper::nameFormat($item->address_city) : '';
		$addressState = !empty($item->address_state) ? ', '.$item->address_state : '';
		$addressCountry = !empty($item->address_country) ? ', '.baseHelper::nameFormat($item->address_country) : '';
		// Phones
		$phones = '';
		$ph = explode(';', $item->phone);
		if(!empty($item->phone) && $item->phone != ';') :
			$wp = explode(';', $item->whatsapp);
			$pd = explode(';', $item->phone_desc);
			for($i = 0; $i < count($ph); $i++) {
				$whapps = $wp[$i] == 1 ? ' <span class="base-icon-whatsapp text-success cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_WHATSAPP').'"></span>' : '';
				$phDesc = !empty($pd[$i]) ? '<div class="small text-muted lh-1 mb-2">'.$pd[$i].'</div>' : '';
				$phones .= '<div>'.$ph[$i].$whapps.$phDesc.'</div>';
			}
		endif;

		// Tratamento de campos obrigatórios
		$gender = ($item->gender == 0) ? $undefined : JText::_('TEXT_GENDER_'.$item->gender);
		$mStatus = ($item->marital_status == 0) ? $undefined : JText::_('TEXT_MARITAL_STATUS_'.$item->marital_status);
		$mother = empty($item->mother_name) ? $undefined : baseHelper::nameFormat($item->mother_name);
		$father = empty($item->father_name) ? $undefined : baseHelper::nameFormat($item->father_name);
		$birthday = (empty($item->birthday) || $item->birthday == '0000-00-00') ? $undefined : baseHelper::dateFormat($item->birthday);
		$place = empty($item->place_birth) ? $undefined : baseHelper::nameFormat($item->place_birth);
		$cpf = empty($item->cpf) ? $undefined : $item->cpf;
		$rg = empty($item->rg) ? $undefined : $item->rg.' / '.$item->rg_orgao;
		$address = empty($item->address) ? $undefined : baseHelper::nameFormat($item->address).$addressNumber.$addressInfo.'<br />'.$addressZip.$addressDistrict.$addressCity.$addressState;
		$phones = empty($phones) ? $undefined : $phones;

		$html .= '
			<a href="'.$urlEdit.'" class="pos-absolute pos-right-gutter zindex-1 btn btn-warning b-2 base-icon-pencil float-md-right mb-2"> '.JText::_('TEXT_EDIT').'</a>
			<div class="row">
				<div class="col-sm-4 col-md-2 mb-4 mb-md-0">
					<div style="max-width: 300px">'.$img.'</div>
				</div>
				<div class="col-sm-8 col-md-6">
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_NAME').':</label>
					<p> '.baseHelper::nameFormat($item->name).'</p>
					<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMAIL').':</label>
					<p>'.$item->email.'</p>
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
							<p>'.$item->children.'</p>
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
							<p> '.baseHelper::nameFormat($item->type).'</p>
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
		if($item->usergroup != 13) :
			$html .= '
					<div class="col-md-8">
						<hr class="hr-tag" />
						<span class="badge badge-primary">'.JText::_('TEXT_DATA_EMPLOYEE').'</span>
						<div class="row">
							<div class="col-sm-4">
								<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_STATUS_EMPLOYEE').':</label>
								<p>'.($item->usergroup == 11 ? JText::_('TEXT_EFFECTIVE') : JText::_('TEXT_RETIRED')).'</p>
							</div>
			';
		endif;
		if($item->usergroup == 11) :
			$cx_email = empty($item->cx_email) ? $undefined : $item->cx_email;
			$cx_situated = empty($item->cx_situated) ? $undefined : $item->cx_situated;
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
		if($item->usergroup != 13) :
			$cx_code = empty($item->cx_code) ? $undefined : $item->cx_code;
			$cx_date = (empty($item->cx_date) || $item->cx_date == '0000-00-00') ? $undefined : baseHelper::dateFormat($item->cx_date);
			$cx_role = empty($item->cx_role) ? $undefined : $item->cx_role;
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
		$accountData = '
			<label class="label-xs text-muted">Conta Bancária:</label>
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

		// DEPENDENTES
		$query	= '
			SELECT
				T1.*,
				'. $db->quoteName('T2.name') .' grp,
				'. $db->quoteName('T2.overtime') .',
				IF('.$db->quoteName('T1.end_date').' <= NOW() && '. $db->quoteName('T2.overtime') .' > 0, 1, 0) finished
			FROM '. $db->quoteName('#__'.$cfg['project'].'_dependents') .' T1
				JOIN '. $db->quoteName('#__'.$cfg['project'].'_dependents_groups') .' T2
				ON '.$db->quoteName('T2.id') .' = T1.group_id
			WHERE
				'. $db->quoteName('T1.client_id') .' = '. $item->id .'
				 AND T1.state = 1
			ORDER BY '. $db->quoteName('T1.name') .' ASC
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

		if($num_rows) : // verifica se existe
			$html .= '
				<h5 class="page-header pt-4 base-icon-users"> '.JText::_('TEXT_DEPENDENTS').'</h5>
				<ul class="set-list list-lg bordered">
			';
			foreach($res as $dep) {

				if($cfg['hasUpload']) :

					// Imagem Principal -> Primeira imagem (index = 0)
					$img = uploader::getFile('#__'.$cfg['project'].'_dependents_files', '', $dep->id, 0, JPATH_BASE.DS.'images/apps/dependents/');
					if(!empty($img)) :
						$imagePath = baseHelper::thumbnail('images/apps/dependents/'.$img['filename'], 32, 32);
					else :
						$imagePath = 'images/template/'.($dep->gender == 1 ? 'man' : 'woman').'.png';
					endif;
					$img = '<img src="'.$imagePath.'" style="width:32px; height:32px;" class="d-none d-md-inline img-fluid rounded-circle float-left mr-2" />';
				endif;

				$name = baseHelper::nameFormat($dep->name);
				$limite = '';
				if($dep->overtime > 0) :
					$end_date = baseHelper::dateFormat($dep->end_date);
					if($dep->finished == 1) :
						$name = '<span class="base-icon-cancel" style="text-decoration:line-through"> '.$name.'</span>';
						$limite = ' &raquo; <span class="text-danger cursor-help hasTooltip" title="'.JText::sprintf('MSG_DEPENDENT_FINISHED', $dep->overtime, $dep->grp).'"><span class="base-icon-attention text-live"></span> '.JText::_('TEXT_FINISHED').': '.$end_date.'</span>';
					else :
						$limite = ' &raquo; <span class="text-success cursor-help hasTooltip" title="'.JText::sprintf('MSG_DEPENDENT_PERIOD', $dep->overtime, $dep->grp).'">'.JText::_('TEXT_DUE_DATE_ABBR').': '.$end_date.'</span>';
					endif;
				endif;
				$docs = '';
				if($dep->docs == 0) :
					$docs = '<small class="text-danger"><span class="base-icon-attention text-live"></span> '.JText::_('MSG_NO_DOCUMENTS').'</small>';
				endif;
				$email = !empty($dep->email) ? '<div class="text-sm text-muted mt-2 base-icon-email"> '.$dep->email.'</div>' : '';
				// Phones
				$phones = '';
				$ph = explode(';', $dep->phone);
				if(!empty($dep->phone) && $dep->phone != ';') :
					$wp = explode(';', $dep->whatsapp);
					$pd = explode(';', $dep->phone_desc);
					$phones .= '<ul class="set-list inline bordered text-sm text-muted mt-2 list-trim"> ';
					for($i = 0; $i < count($ph); $i++) {
						$whapps = $wp[$i] == 1 ? ' <span class="base-icon-whatsapp text-success cursor-help hasTooltip" title="'.JText::_('TEXT_HAS_WHATSAPP').'"></span>' : '';
						$phDesc = !empty($pd[$i]) ? '<br /><small>'.$pd[$i].'</small>' : '';
						$phones .= '<li>'.$ph[$i].$whapps.$phDesc.'</li>';
					}
					$phones .= '</ul>';
				endif;

				$rowState = ($dep->state == 0 || $dep->finished == 1) ? 'text-danger' : '';
				$html .= '
					<li class="'.$rowState.'">
						'.$img.$name.'
						<div class="small">
							'.baseHelper::nameFormat($dep->grp).' - '.JText::_('TEXT_BIRTHDAY_ABBR').' '.baseHelper::dateFormat($dep->birthday).$limite.'
						</div>
						'.$docs.$email.$phones.'
					</li>
				';
			}
			$html .= '</ul>';
		else :
			$html .= '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_NO_DEPENDENTS').'</p>';
		endif;

	else :
		// ACCESS
		if($hasAdmin) :
			// O perfil é visualizado apenas por associados.
			// Usuários administradores "$hasAdmin" (não associados) só podem
			// visualizar seus dados ou editar seu perfil, na administração...
			echo '<div class="alert alert-warning base-icon-attention"> '.JText::_('MSG_IS_ADMIN').'</div>';
		else :
			// $app->enqueueMessage(JText::_('MSG_NOT_PERMISSION'), 'warning');
			// $app->redirect(JURI::root(true));
			// exit();
			echo $query;
		endif;
	endif;

	// Mensagem de sucesso após a atualização dos dados
	if(isset($_SESSION[$APPTAG.'EditSuccess']) && $_SESSION[$APPTAG.'EditSuccess']) :
		echo '<h5 class="alert alert-success base-icon-ok"> '.JText::_('MSG_EDIT_SUCCESS').'</h5>';
		unset($_SESSION[$APPTAG.'EditSuccess']);
	endif;

	?>

	<div id="<?php echo $APPTAG?>-view-data" class="clearfix">
		<?php echo $html?>
	</div>

<?php
else :

	echo '<h4 class="alert alert-warning">'.JText::_('MSG_NOT_PERMISSION').'</h4>';

endif;
?>
