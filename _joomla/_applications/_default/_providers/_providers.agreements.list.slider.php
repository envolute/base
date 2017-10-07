<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');

// ACESSO
$cfg['isPublic'] = true; // Público -> acesso aberto a todos

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
$gID = $app->input->get('gID', 0, 'int');

// list view
$urlToView = JURI::root().'apps/base-providers/agreement-view';

// Carrega o arquivo de tradução
// OBS: para arquivos externos com o carregamento do framework '_init.joomla.php' (geralmente em 'ajax')
// a language 'default' não é reconhecida. Sendo assim, carrega apenas 'en-GB'
// Para possibilitar o carregamento da language 'default' de forma dinâmica,
// é necessário passar na sessão ($_SESSION[$APPTAG.'langDef'])
if(isset($_SESSION[$APPTAG.'langDef'])) :
	$lang->load('base_apps', JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
	$lang->load('base_'.$APPNAME, JPATH_BASE, $_SESSION[$APPTAG.'langDef'], true);
endif;

// DATABASE CONNECT
$db = JFactory::getDbo();

// GET DATA
$query	= '
	SELECT
		T1.*,
		'. $db->quoteName('T2.name') .' group_name
	FROM '.
		$db->quoteName($cfg['mainTable']) .' T1
		JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
		ON T2.id = T1.group_id AND T2.state = 1
	WHERE
		'. $db->quoteName('T1.agreement') .' = 1 AND
		'. $db->quoteName('T1.state') .' = 1
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

	foreach($res as $item) {

		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> Primeira imagem (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 150, 150).'" class="img-fluid mb-3 mx-auto" />';
		else $img = '<div class="image-file mb-3 mx-auto" style="width:150px;"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';

		$html .= '
			<li class="agreements-brand clearfix">
				<figure class="img-fluid">
					<a href="'.$urlToView.'?vID='.$item->id.'">
						'.$img.'
						<figcaption>'.baseHelper::nameFormat($item->name).'</figcaption>
					</a>
				</figure>
			</li>
		';
	}

endif;

echo $html;
?>
