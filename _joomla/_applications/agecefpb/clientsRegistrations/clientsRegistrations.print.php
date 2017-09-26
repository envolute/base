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

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

//joomla get request data
$input      = $app->input;

// params requests
$sUD		= $input->get('sUD', '', 'str');
$uID		= $input->get('uID', '', ($sUD == 'on' ? 'int' : 'str'));
if($sUD == 'on') :
	$uID	= $input->get('uID', '', 'int');
else :
	$uID	= is_numeric(base64_decode($uID)) ? base64_decode($uID) : '';
endif;

if(!empty($uID) || !empty($dOC)) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	$where = !empty($uID) ? $db->quoteName('T1.id') .' = '. $uID : '';
	// acesso após a aprovação
	if($sUD != 'on') $where .= ' AND '.$db->quoteName('T1.user_id') .' = 0 AND '.$db->quoteName('T1.access') .' = 0';

	// GET DATA
	$query = '
		SELECT T1.*, '. $db->quoteName('T2.title') .' type
		FROM '.$db->quoteName($cfg['mainTable']).' T1
			LEFT OUTER JOIN '. $db->quoteName('#__usergroups') .' T2
			ON T2.id = T1.usergroup
		WHERE '.$where.'
		ORDER BY '.$db->quoteName('T1.id').' DESC
		LIMIT 1
	';
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

		$children = '';
		if($item->children > 0) :
			$children = '
				<div class="col-4">
					<label class="label-sm">'.JText::_('FIELD_LABEL_CHILDREN').':</label>
					<p>'.$item->children.'</p>
				</div>
			';
		endif;
		$partner = '';
		if(!empty($item->partner)) :
			$partner = '
				<div class="col">
					<label class="label-sm">'.JText::_('FIELD_LABEL_PARTNER').':</label><p>'.baseHelper::nameFormat($item->partner).'</p>
				</div>
			';
		endif;
		$phones = !empty($item->phone1) ? $item->phone1 : '';
		$phones .= !empty($item->phone2) ? (!empty($phones) ? '<br />' : '').$item->phone2 : '';
		$phones .= !empty($item->phone3) ? (!empty($phones) ? '<br />' : '').'(fixo) '.$item->phone3 : '';

		$html .= '
				<div class="row">
					<div class="col-8">
						<label class="label-sm">'.JText::_('FIELD_LABEL_NAME').':</label>
						<p> '.baseHelper::nameFormat($item->name).'</p>
					</div>
					<div class="col-4">
						<label class="label-sm">'.JText::_('TEXT_USER_TYPE').':</label>
						<p>'.baseHelper::nameFormat($item->type).'</p>
					</div>
					<div class="col-8">
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
							'.$children.$partner.'
						</div>
					</div>
					<div class="col-4">
						<label class="label-sm">'.JText::_('FIELD_LABEL_BIRTHDAY').':</label>
						<p>'.baseHelper::dateFormat($item->birthday).'</p>
						<label class="label-sm">CPF:</label>
						<p>'.$item->cpf.'</p>
						<label class="label-sm">RG:</label>
						<p>'.$item->rg.' / '.$item->rg_orgao.'</p>
					</div>
				</div>
				<hr />
				<div class="row">
					<div class="col-8">
						<label class="label-sm">'.JText::_('FIELD_LABEL_ADDRESS').':</label>
						<p>
							'.baseHelper::nameFormat($item->address).', '.$item->address_number.(!empty($item->address_info) ? ', '.$item->address_info : '').'<br />
							'.(!empty($item->zip_code) ? $item->zip_code.', ' : '').baseHelper::nameFormat($item->address_district).', '.baseHelper::nameFormat($item->address_city).'
						</p>
					</div>
					<div class="col-4">
						<label class="label-sm">'.JText::_('FIELD_LABEL_PHONE').'(s):</label>
						<p>'.$phones.'</p>
					</div>
				</div>
				<div class="row">
		';
		if($item->usergroup != 13) :
			$html .= '
					<div class="col-8">
						<hr class="hr-tag" />
						<span class="badge badge-primary">'.JText::_('TEXT_DATA_EMPLOYEE').'</span>
						<div class="row">
							<div class="col-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_STATUS_EMPLOYEE').':</label>
								<p>'.($item->usergroup == 11 ? JText::_('TEXT_EFFECTIVE') : JText::_('TEXT_RETIRED')).'</p>
							</div>
			';
		endif;
		if($item->usergroup == 11) :
			$html .= '
							<div class="col-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_EMAIL').':</label>
								<p>'.$item->cx_email.'</p>
							</div>
							<div class="col-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_SITUATED').':</label>
								<p>'.$item->cx_situated.'</p>
							</div>
			';
		endif;
		if($item->usergroup != 13) :
				$html .= '
							<div class="col-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_CODE').':</label>
								<p>'.$item->cx_code.'</p>
							</div>
							<div class="col-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_ADMISSION_DATE').':</label>
								<p>'.baseHelper::dateFormat($item->cx_date).'</p>
							</div>
							<div class="col-4">
								<label class="label-sm">'.JText::_('FIELD_LABEL_ROLE').':</label>
								<p>'.$item->cx_role.'</p>
							</div>
						</div>
					</div>
			';
		endif;
		$html .= '
					<div class="col-'.($item->usergroup == 13 ? 12 : 4).'">
						<hr class="hr-tag" />
						<span class="badge badge-primary">'.JText::_('TEXT_ACCOUNT_DATA').'</span>
						<label class="label-sm">Conta Bancária:</label>
						<p>
							'.JText::_('FIELD_LABEL_AGENCY').': <strong>'.$item->agency.'</strong><br />
							'.JText::_('FIELD_LABEL_ACCOUNT').': <strong>'.$item->account.'</strong><br />
							'.JText::_('FIELD_LABEL_OPERATION').': <strong>'.$item->operation.'</strong>
						</p>
					</div>
				</div>
		';
		if($item->user_id == 0) :
			$html .= '
				<div class="p-3 b-all bg-gray-200 my-5">
					<span class="base-icon-check mr-1 d-print-none"></span>'.JText::sprintf('MSG_AUTHORIZE', $item->name, $item->cx_code).'
				</div>
				<div class="row pt-5">
					<div class="col-6 text-center">
						<hr class="mb-2" />'.JText::_('TEXT_SIGNATURE').'
					</div>
					<div class="col-6 text-center">
						<hr class="mb-2" />'.JText::_('TEXT_PLACE_DATE').'
					</div>
				</div>
			';
		endif;
	else :
		$app->enqueueMessage(JText::_('MSG_DATA_NOT_AVAILABLE'), 'warning');
		$app->redirect(JURI::root(true));
		exit();
	endif;

	?>
	<div class="d-print-none">
		<button type="button" class="btn btn-lg btn-success base-icon-print btn-icon" onclick="javascript:window.print()"><?php echo JText::_('TEXT_PRINT_DATA')?></button>
		<hr />
	</div>
	<div class="pb-4">
		<img class="float-left mr-5" src="images/template/logos/logo-footer.png" style="width:110px;">
		<span class="float-right" style="position: relative; top: -40px;"><?php echo utf8_encode(strftime('%A, %d de %B de %Y', strtotime('today'))); ?></span>
		<h4 class="pt-3 mb-2 lh-1">#NOME# <small class="font-featured">( CNPJ: #CNPJ# )</small></h4>
		<p>
			#RAZAO-SOCIAL#<br />
			#ENDERECO-LINHA1#<br />
			#ENDERECO-LINHA2# - #TELEFONES#
		</p>
		<hr />
	</div>
	<div id="<?php echo $APPTAG?>-view-data" class="clearfix">
		<?php echo $html?>
	</div>
<?php
else :
	$app->redirect(JURI::root(true));
	exit();
endif;
?>
