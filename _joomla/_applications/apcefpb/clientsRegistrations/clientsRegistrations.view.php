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
		$view = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	$html = '';
	if(!empty($view->name)) : // verifica se existe

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $view->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 300).'" class="img-fluid b-all b-all-dashed p-1" />';
			else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';
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
			<a href="'.$urlEdit.'" class="pos-absolute pos-right-gutter zindex-1 btn btn-warning b-2 base-icon-pencil float-md-right mb-2"> '.JText::_('TEXT_EDIT').'</a>
			<div class="row">
				<div class="col-sm-4 col-md-2 mb-4 mb-md-0">
					<div style="max-width: 300px">'.$img.'</div>
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
				'. $db->quoteName('T1.client_id') .' = '. $view->id .'
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
