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
$urlToView = JURI::root().'apps/'.$APPNAME.'/agreement-view';

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

// FILTER
$filter = !empty($gID) ? $db->quoteName('T1.group_id') .' = '. $gID . ' AND ' : '';

// GROUPS
$query = '
	SELECT
		DISTINCT(T1.group_id) id, T2.name
	FROM
		'. $db->quoteName($cfg['mainTable']) .' T1
		JOIN '. $db->quoteName($cfg['mainTable'].'_groups') .' T2
		ON T2.id = T1.group_id
	 ORDER BY T2.name
';
$db->setQuery($query);
$groups = $db->loadObjectList();
$grplist = '';
foreach ($groups as $obj) {
	$grplist .= '<option value="'.$obj->id.'"'.($gID == $obj->id ? ' selected' : '').'>'.baseHelper::nameFormat($obj->name).'</option>';
}

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
		'. $filter .'
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

$btnReset = '';
if($gID) :
	$btnReset = '
		<span class="input-group-btn">
			<a href="'.JURI::current().'" class="btn btn-danger base-icon-cancel hasTooltip" title="'.JText::_('TEXT_SHOW_ALL').'"></a>
		</span>
	';
endif;

$html = '
	<script>
	jQuery(function() {
		// SELECT USER -> Selecionar um usuário no formulário de edição
		window.'.$APPTAG.'_selectGroup = function(el) {
			var val = jQuery(el).val();
			location.href = "'.JURI::current().'"+((!isEmpty(val) && val != 0) ? "?gID="+val : "");
		};
	});
	</script>

	<div id="agreements-filter" class="hidden-print my-3">
		<div class="input-group mx-auto mw-100" style="width:400px;">
			<span class="input-group-addon base-icon-filter cursor-help hasTooltip" title="'.JText::_('TEXT_FILTER_TO_GROUP').'"></span>
			<select name="pID" id="'.$APPTAG.'-pID" class="form-control" onchange="'.$APPTAG.'_selectGroup(this)">
				<option value="0">- '.JText::_('TEXT_ALL_GROUPS').' -</option>
				'.$grplist.'
			</select>
			'.$btnReset.'
		</div>
	</div>
';

if($num_rows) : // verifica se existe
	$html .= '<div id="agreements-list" class="row pt-4">';
	foreach($res as $item) {

		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		// Imagem Principal -> Primeira imagem (index = 0)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 0, $cfg['uploadDir']);
		if(!empty($img)) $img = '<img src=\''.baseHelper::thumbnail('images/apps/'.$APPPATH.'/'.$img['filename'], 150, 150).'\' class=\'img-fluid mb-3 mx-auto\' />';
		else $img = '<div class="image-file mb-3 mx-auto" style="width:150px;"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';

		$html .= '
			<div class="agreements-item col-md-4 col-xl-3 text-center my-4">
				<a href="'.$urlToView.'?vID='.$item->id.'">
					'.$img.'
					<h5>'.baseHelper::nameFormat($item->name).'</h5>
				</a>
			</div>
		';
	}
	$html .= '</div>';
else :
	$html = '<p class="base-icon-info-circled alert alert-warning m-0"> '.JText::_('MSG_LISTNOREG').'</p>';
endif;

echo $html;
?>
