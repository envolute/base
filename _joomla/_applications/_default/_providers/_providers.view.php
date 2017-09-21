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
		// Imagem Principal -> Primeira imagem (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $img = '<img src=\''.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 200, 200).'\' class=\'img-fluid float-sm-left mb-4\' />';

		$site	= !empty($item->website) ? '<a href="'.$item->website.'" class="mr-3 new-window" target="_blank">'.$item->website.'</a>' : '';
		$email	= !empty($item->email) ? '<a href="mailto:'.$item->email.'" class="mr-3">'.$item->email.'</a>' : '';
		$cnpj	= !empty($item->cnpj) ? '<label class="label-sm mt-3">CNPJ</label>'.$item->cnpj : '';
		$insM	= !empty($item->insc_municipal) ? '<label class="label-sm mt-3">Inscrição Municipal</label>'.$item->insc_municipal : '';
		$insE	= !empty($item->insc_estadual) ? '<label class="label-sm mt-3">Inscrição Estadual</label>'.$item->insc_estadual : '';
		$date	= $item->due_date != 0 ? '<label class="label-sm mt-3">'.JText::_('FIELD_LABEL_DUE_DATE').'</label>'.($item->due_date < 10 ? '0' : '').$item->due_date : '';
		$agree	= $item->agreement == 1 ? '<span class="badge badge-success float-right">'.JText::_('FIELD_LABEL_AGREEMENT').'</span>' : '';
		// Web
		$web	= (!empty($site) || !empty($email)) ? '<div class="text-md text-muted mt-2">'.$site.$email.'</div>' : '';
		// description
		$info = '';
		if(!empty($item->description)) $info .= $item->description.'<hr />';
		if(!empty($item->service_desc)) :
			$info .= '<h5 class="mt-4">'.JText::_('FIELD_LABEL_SERVICE').'</h5>';
			$info .= $item->service_desc;
		endif;

		echo '
			<div class="row">
				<div class="col-sm-4 col-md-3 col-xl-2">
					'.$img.'
					<div>'.$cnpj.$insM.$insE.$date.'</div>
					<hr class="d-sm-none" />
				</div>
				<div class="col-sm-8 col-md-9 col-xl-10">
					<h2 class="page-header mt-0">
						'.baseHelper::nameFormat($item->name).$agree.$web.'
					</h2>
					<div class="row">
						<div class="col-lg-8">
							'.$info.'
							<hr class="d-sm-none" />
						</div>
						<div class="col-lg-4">
		';

						// Phones
						$_phonesListFull	= false;
						$_phonesAddText		= false;
						$_phonesRelTag		= 'providers';
						$_phonesRelTable	= '#__base_rel_providers_phones';
						$_phonesAppNameId	= 'phone_id';
						$_phonesRelNameId	= 'provider_id';
						echo '<h6 class="mb-3">'.JText::_('TEXT_PROVIDER_PHONES').'</h6>';
						require(JPATH_APPS.DS.'_phones/_phones.php');

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
