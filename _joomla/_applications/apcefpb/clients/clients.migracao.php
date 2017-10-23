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

// DATABASE CONNECT
$db = JFactory::getDbo();

$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable']) .' ORDER BY id';
$db->setQuery($query);
$res = $db->loadObjectList();

$count = 0;
foreach($res as $item) {
	$p = explode(',', $item->phones);
	$w = explode(',', $item->whatsapp);
	for($i = 1; $i <= 3; $i++) {
		$h = $i - 1;
		${'phone'.$i} = (isset($p[$h]) && !empty($p[$h])) ? $p[$h] : '';
		${'whapp'.$i} = (isset($w[$h]) && !empty($w[$h])) ? $w[$h] : 0;
	}

	$query = '
		UPDATE '. $db->quoteName($cfg['mainTable']) .' SET
			phone1 = "'.$phone1.'",
			phone2 = "'.$phone2.'",
			phone3 = "'.$phone3.'",
			whatsapp1 = '.$whapp1.',
			whatsapp2 = '.$whapp2.',
			whatsapp3 = '.$whapp3.'
		WHERE id = '.$item->id.'
	';
	$db->setQuery($query);
	$db->execute();
	try {

		echo '<p>Migração Realizada: nome = '.$item->name.' => "'.$phone1.'", "'.$phone2.'", "'.$phone3.'", '.$whapp1.', '.$whapp2.', '.$whapp3.'</p>';
		$count++;

	} catch (RuntimeException $e) {

		echo '<p>Erro: id = '.$item->id.' => '.$e->getCode().'. '.$e->getMessage().'</p>';

	}

}
echo 'Total: '.$count;

?>
