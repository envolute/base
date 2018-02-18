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
// require_once(JPATH_APPS.DS.'staff/staff.select.user.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = '
		SELECT
			T1.*
		FROM
			'.$db->quoteName($cfg['mainTable']).' T1
		WHERE '.$db->quoteName('T1.id') .' = '. $vID
	;
	try {
		$db->setQuery($query);
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if(!empty($item->name)) : // verifica se existe

		if($cfg['hasUpload']) :
			JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
			// Imagem Principal -> Primeira imagem (index = 0)
			$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
			if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 300).'" class="img-fluid b-all b-all-dashed p-1" />';
			else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';
		endif;

		// Email, profissão
		$info1 = '';
		if(!empty($item->email)) :
			$info1 .= '

			';
		endif;
		if(!empty($item->role)) :
			$info1 .= '

			';
		endif;
		if($item->gender > 0) :
			$info1 .= '

			';
		endif;
		if(!empty($info1)) $info1 = '<div class="row">'.$info1.'</div>';

		echo '
			<div class="row">
				<div class="col-4 col-sm-2 mb-4 mb-md-0">
					<div style="max-width: 300px">'.$img.'</div>
				</div>
				<div class="col-sm">
					<div class="row">
						<div class="col-sm-8">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_NAME').':</label>
							<p> '.baseHelper::nameFormat($item->name).'</p>
						</div>
						<div class="col-sm">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_GENDER').':</label>
							<p>'.JText::_('TEXT_GENDER_'.$item->gender).'</p>
						</div>
						<div class="col-sm-8">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMAIL').':</label>
							<p>'.$item->email.'</p>
						</div>
						<div class="col-sm">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ROLE').':</label>
							<p> '.baseHelper::nameFormat($item->role).'</p>
						</div>
					</div>
				</div>
			</div>
		';

	else :
		echo '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_ITEM_NOT_AVAILABLE').'</p>';
	endif;

else :

	$hasClient = array_intersect($groups, $cfg['groupId']['client']); // se está na lista de grupos permitidos
	if($hasClient) {
		echo '<h4 class="alert alert-warning">'.JText::_('MSG_NO_ITEM_SELECTED').'</h4>';
	} else {
		// Se não for cliente redireciona para o perfil do funcionário
		$app->redirect(JURI::root(true).'/apps/staff/profile');
		exit();
	}

endif;
?>
