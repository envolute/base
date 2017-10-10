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

// PARAMS FROM MODULE
$itemsTotal		= $items_total;		// Total de itens carregados
$itemsIds		= $items_ids;		// Ids dos itens que devem ser visualizados
$categIds		= $categ_ids;		// Ids das categorias dos itens que devem ser visualizados
$itemsOrder		= $items_order;		// ordem dos itens
$imgWidth		= $image_width;		// largura da imagem
$imgHeight		= $image_height;	// altura da imagem
$showTitle		= $show_title;		// mostrar o título/nome do item
$showCateg		= $show_categ;		// mostrar a categoria/grupo do item
$showDesc		= $show_desc;		// mostrar a descrição do item
$showUser		= $show_user;		// mostrar o usuário/autor do item
$showValue		= $show_value;		// mostrar o valor do item

$where	= !empty($itemsIds) ? ' AND '. $db->quoteName('T1.id') .' IN ('.$itemsIds.')' : '';
$where	.= !empty($categIds) ? ' AND '. $db->quoteName('T2.id') .' IN ('.$categIds.')' : '';
$order	= !empty($itemsOrder) ? ' ORDER BY '. $itemsOrder : '';
$limit	= !empty($itemsTotal) ? ' LIMIT '. $itemsTotal : '';

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
		'.$where.$order.$limit.'
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
		// Banner Principal (index = 2)
		$img = uploader::getFile($cfg['fileTable'], '', $item->id, 2, $cfg['uploadDir']);
		if(!empty($img)) :

			// Imagem
			$path = 'images/apps/'.$APPPATH.'/'.$img['filename'];
			if(!empty($imgWidth) && !empty($imgHeight)) $path = baseHelper::thumbnail($path, $imgWidth, $imgHeight);
			$img = '<img src="'.$path.'" class="img-fluid mx-auto" />';
			// Título
			$title = $showTitle ? '<figcaption>'.$item->name.'</figcaption>' : '';

			$html .= '
				<li class="agreements-brand clearfix">
					<figure class="img-fluid">
						<a href="'.$urlToView.'?vID='.$item->id.'">
							'.$img.'
						</a>
						'.$title.'
					</figure>
				</li>
			';

		endif;
	}

endif;

echo $html;
?>
