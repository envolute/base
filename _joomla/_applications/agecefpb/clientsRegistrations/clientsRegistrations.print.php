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
$rID		= $input->get('rID', '', 'str');
$rID		= is_numeric(base64_decode($rID)) ? base64_decode($rID) : '';
$dOC		= $input->get('dOC', '', 'str');
if(empty($rID)) :
	$dOC		= !empty($dOC) ? base64_decode($dOC) : '';
	$dOC		= is_numeric(baseHelper::alphaNum($dOC)) ? $dOC : '';
endif;

// DATABASE CONNECT
$db = JFactory::getDbo();

$where = !empty($dOC) ? $db->quoteName('T1.cpf') .' = '. $dOC : '';
$where = !empty($rID) ? $db->quoteName('T1.id') .' = '. $rID : $where;

// GET DATA
$query = '
	SELECT *
	FROM '.$db->quoteName($cfg['mainTable']).' T1
	WHERE '.$where.' AND '.$db->quoteName('T1.user_id') .' = 0 AND '.$db->quoteName('T1.access') .' = 0
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
		if(!empty($img)) $img = '<img src="'.baseHelper::thumbnail('images/apps/'.$cfg['parentApp'].'/'.$img['filename'], 200, 200).'" class="img-fluid b-all b-dashed p-1" />';
		else $img = '<div class="image-file"><div class="image-action"><div class="image-file-label"><span class="base-icon-file-image"></span></div></div></div>';
	endif;

	$html .= '
		<div class="row">
			<div class="col-sm-3">'.$img.'</div>
			<div class="col-sm-9 b-left b-dashed">
				<p><strong>Nome:</strong> '.baseHelper::nameFormat($item->name).'</p>
				<p><strong>Email:</strong> '.$item->email.'</p>
				<p>
					<strong>CPF:</strong> '.baseHelper::nameFormat($item->cpf).' /
					<strong>RG:</strong> '.baseHelper::nameFormat($item->rg).' /
					<strong>Orgão:</strong> '.baseHelper::nameFormat($item->rg_orgao).'
				</p>
				<p><strong>Gênero:</strong> '.$item->gender.'</p>
				<p><strong>Data de Nasc.:</strong> '.baseHelper::dateFormat($item->birthday).'</p>
				<p><strong>Estado Civil:</strong> '.$item->marital_status.'</p>
				<p><strong>Conjuge:</strong> '.baseHelper::nameFormat($item->partner).'</p>
				<p><strong>N&ordm; de Filhos:</strong> '.$item->children.'</p>
				<p>
					<strong>Endereço:</strong><br />
					'.baseHelper::nameFormat($item->address).', '.$item->address_number.(!empty($item->address_info) ? ', '.$item->address_info : '').'<br />
					'.$item->zip_code.', '.baseHelper::nameFormat($item->address_district).', '.baseHelper::nameFormat($item->address_city).'
				</p>
				<p><strong>Telefone(s):</strong> '.$item->phones.'</p>
				<p>
					<strong>Conta Bancária:</strong><br />
					Agência:'.$item->agency.', Conta Corrente:'.$item->account.', Operação:'.$item->operation.'
				</p>
				<p><strong>Nome:</strong> '.baseHelper::nameFormat($item->name).'</p>
				<p><strong>Nome:</strong> '.baseHelper::nameFormat($item->name).'</p>
				<p><strong>Nome:</strong> '.baseHelper::nameFormat($item->name).'</p>
			</div>
		</div>
	';
else :
	$html = '<p class="base-icon-info-circled alert alert-info m-0"> '.JText::_('MSG_LISTNOREG').'</p>';
endif;

?>

<div class="d-print-none">
	<button type="button" class="btn btn-lg btn-success base-icon-print btn-icon" onclick="javascript:window.print()"><?php echo JText::_('TEXT_PRINT_DATA')?></button>
	<hr />
</div>
<div id="<?php echo $APPTAG?>-view-data" class="clearfix">
	<?php echo $html?>
</div>
