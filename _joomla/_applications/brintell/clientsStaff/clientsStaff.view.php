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
if($vID == 0) $vID = $user->id;

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
// require_once('_contacts.select.php');

if($vID != 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = '
		SELECT
			T1.*,
			'. $db->quoteName('T2.id') .' clientID,
			'. $db->quoteName('T2.name') .' client,
			'. $db->quoteName('T3.username') .'
		FROM
			'.$db->quoteName($cfg['mainTable']).' T1
			LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
			ON T2.id = T1.client_id
			LEFT JOIN '. $db->quoteName('#__users') .' T3
			ON T3.id = T1.user_id
		WHERE '.$db->quoteName('T1.user_id') .' = '. $vID
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

			// Arquivos -> Grupo de imagens ('#'.$APPTAG.'-files-group')
			// Obs: para pegar todas as imagens basta remover o 'grupo' ('#'.$APPTAG.'-files-group')
			$files[$view->id] = uploader::getFiles($cfg['fileTable'], $view->id);
			$listFiles = array();
			for($i = 1; $i < count($files[$view->id]); $i++) {
				if(!empty($files[$view->id][$i]->filename)) :
					$fLab = ($files[$view->id][$i]->index == 1) ? JText::_('FIELD_LABEL_RESUME') : '';
					$fTip = ($files[$view->id][$i]->index == 2) ? JText::_('FIELD_LABEL_CONTRACT') : $files[$view->id][$i]->filename;
					$listFiles[$files[$view->id][$i]->index] .= '
						<a href="'.JURI::root(true).'/apps/get-file?fn='.base64_encode($files[$view->id][$i]->filename).'&mt='.base64_encode($files[$view->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
							<span class="base-icon-attach hasTooltip" title="'.$fTip.'<br />'.((int)($files[$view->id][$i]->filesize / 1024)).'kb"> '.$fLab.'</span>
						</a>
					';
				endif;
			}
		endif;

		echo '
			<div class="row">
				<div class="col-4 col-sm-2 mb-4 mb-md-0">
					<div style="max-width: 300px">'.$img.'</div>
				</div>
				<div class="col-sm">
					<h3 class="page-header">'.JText::sprintf('TEXT_BELONGS_TO', $view->clientID, baseHelper::nameFormat($view->client)).'</h4>
					<div class="row">
						<div class="col-sm-8">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_NAME').':</label>
							<p>'.baseHelper::nameFormat($view->name).' ('.(!empty($view->username) ? $view->username : '').')</p>
						</div>
						<div class="col-sm">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_GENDER').':</label>
							<p>'.JText::_('TEXT_GENDER_'.$view->gender).'</p>
						</div>
						<div class="col-sm-8">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_EMAIL').':</label>
							<p>'.$view->email.'</p>
						</div>
						<div class="col-sm">
							<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_ROLE').':</label>
							<p>'.baseHelper::nameFormat($view->role, null, JText::_('TEXT_UNDEFINED')).'</p>
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
