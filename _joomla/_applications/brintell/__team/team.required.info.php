<?php
/* CLIENTS
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
 * Verifica se existem campos obrigatórios pendentes
 * Caso não existam, mostrauma mensagem ao usuário
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

if(isset($user->id) && $user->id) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET DATA
	$query = 'SELECT * FROM '.$db->quoteName($cfg['mainTable']).' WHERE '.$db->quoteName('user_id') .' = '. $uID;
	try {
		$db->setQuery($query);
		$item = $db->loadObject();
	} catch (RuntimeException $e) {
		echo $e->getMessage();
		return;
	}

	if(!empty($item->name)) : // verifica se existe

		// Required info
		$required[] = $item->cpf;
		$required[] = $item->rg;
		$required[] = $item->rg_orgao;
		$required[] = $item->place_birth;
		$required[] = $item->marital_status;
		$required[] = $item->gender;
		$required[] = $item->mother_name;
		$required[] = $item->father_name;
		$required[] = $item->address;
		$required[] = $item->address_number;
		$required[] = $item->address_district;
		$required[] = $item->address_city;
		$required[] = $item->agency;
		$required[] = $item->account;
		$required[] = $item->operation;

		$incomplete = false;
		for($i = 0; $i < count($required); $i++) {
			if(empty($required[$i]) || $required[$i] === 0 || $item->birthday == '0000-00-00') $incomplete = true;
		}
		// Show incomplete data message
		if($incomplete) :
			echo '
				<div class="alert alert-info alert-dismissible fade show m-0">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					'.JText::sprintf('MSG_INCOMPLETE_DATA_VIEW', baseHelper::nameFormat($item->name), $urlEdit).'
				</div>
			';
		endif;

	endif;

endif;
?>
