<?php
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO: Libera o acesso aos clients
// Atribui aos clientes o perfil de visualizador só para esse código
unset($cfg['groupId']['viewer']); // Limpa os valores padrão
$cfg['groupId']['viewer'][]	= 11; // Associado -> Efetivo
$cfg['groupId']['viewer'][]	= 12; // Associado -> Aposentado
$cfg['groupId']['viewer'][]	= 13; // Associado -> Contribuinte

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

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

// database connect
$db = JFactory::getDbo();

// GET DATA
$query = '
	SELECT
	  '. $db->quoteName('T1.id') .',
	  '. $db->quoteName('T1.name') .',
	  '. $db->quoteName('T1.card_name') .',
	  '. $db->quoteName('T1.cx_code') .',
	  '. $db->quoteName('T3.username') .' code,
	  '. $db->quoteName('T2.title') .' grp
	FROM
	  '. $db->quoteName($cfg['mainTable']) .' T1
	  LEFT JOIN '. $db->quoteName('#__usergroups') .' T2
	  ON T2.id = T1.usergroup
	  LEFT JOIN '. $db->quoteName('#__users') .' T3
	  ON T3.id = T1.user_id
	WHERE '. $db->quoteName('T1.id') .' = '.$uID
;
$db->setQuery($query);
$item = $db->loadObject();

if(!empty($item->name)) : // verifica se existe

	JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
	// Imagem Principal -> Primeira imagem (index = 0)
	$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
	if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 300, 400).'" style="float:left; width:68px; height:90px; border:2px solid #f60" />';

	$grp = '<span class="left-space text-upper">'.$item->grp.'</span>';
	$matricula = '';
	if(!empty($item->cx_matricula)) :
		$matricula = '<div style="text-align:right;">Mat. <strong>'.$item->cx_matricula.'</strong>'.$grp.'</div>';
		$grp = '';
	endif;
	$doc = JFactory::getDocument();
	$doc->addStyleDeclaration('body{ overflow: hidden!important; }');
	$html = '
		<div id="'.$APPTAG.'-card" style="padding:20px 0 0 1px; font-size:11px;">
			'.$img.'
			<div style="padding:65px 0 0; text-align:right;">'.(!empty($item->card_name) ? $item-> : $item->name).'</div>
			<div style="text-align:right;">Cód. <strong>'.$item->code.'</strong>'.$grp.'</div>
			'.$matricula.'
		</div>
		<script>jQuery(window).on('load', function() { print() });</script>
	';
else :
	$html = '<p class="alert alert-info alert-icon no-margin">'.JText::_('MSG_EMPTY_CARD').'</p>';
endif;

echo $html;

?>
