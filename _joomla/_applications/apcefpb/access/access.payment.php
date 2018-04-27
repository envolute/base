<?php
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
		T1.*,
		'. $db->quoteName('T2.name') .' client
	FROM
		'. $db->quoteName($cfg['mainTable']) .' T1
		LEFT JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T2
		ON T2.id = T1.client_id
	WHERE '. $db->quoteName('T1.id') .' = '.$uID
;
$db->setQuery($query);
$item = $db->loadObject();

if(!empty($item->guestName)) : // verifica se existe

	// CLIENT
	if($item->client_id > 0 && !empty($item->client)) :
		$client = '<label class="label-xs text-muted">'.JText::_('FIELD_LABEL_CLIENT').': </label>'.baseHelper::nameFormat($item->client);
	else :
		$text = !empty($item->client_desc) ? $item->client_desc : JText::_('TEXT_SELECT_ACCESS_FREE');
		$client = '<span class="text-live base-icon-thumbs-up-alt"> '.$text.'</span>'; //$item->client_desc;
	endif;
	// guests
	$guests = '';
	$total_amount = 0;
	$gName = explode(';', $item->guestName);
	$totalGuests = count($gName);
	if($totalGuests > 0 && !empty($item->guestName) && $item->guestName != ';') :
		$gAge = explode(';', $item->guestAge);
		$gNote = explode(';', $item->guestNote);
		$gTax = explode(';', $item->guestTax);
		for($i = 0; $i < count($gName); $i++) {
			if(!empty($gName[$i]) && $gTax[$i] == 1) :
				$total_amount += $item->tax_price;
				$guests .= '<li>'.baseHelper::nameFormat($gName[$i]).'<span class="float-right font-weight-bold"> R$ '.baseHelper::priceFormat($item->tax_price).'</span></li>';
			endif;
		}
	endif;

	$s = (count($gName) > 1) ? 's' : '';

	if($item->id == 0) :
		$txt = '
			<div class="mt-1 text-danger text-sm font-weight-bold">
				<span class="base-icon-attention"></span> Usuário'.$s.' convidado'.$s.' pela diretoria
			</div>
		';
	else :
		$txt = $client;
	endif;

	$html = '
	<div class="to-print">
		<div class="bottom-expand mb-3 clearfix" style="border-bottom: 2px solid">
			<div class="font-lg font-weight-bold">APCEF/PB</div>
		</div>
		<h4 class="page-header">Taxa de utilização da Piscina <span class="float-right">N&ordm; '.$item->id.'</span></h4>
		<div class="mb-3 pb-2 b-bottom b-inherit b-bottom-dashed">
			<h6>'.$txt.'</h6>
		</div>
	';
	if(!empty($guests)) :
		$html .= '
			<label class="label-xs text-muted">Convidado'.$s.':</label>
			<ul class="set-list bordered mb-3">
				'.$guests.'
				<li class="text-right text-lg font-weight-bold">Valor Total: R$ '.baseHelper::priceFormat($total_amount).'</li>
			</ul>
		';
	endif;

	$dh = explode(' ', $item->accessDate);
	$dt = explode('-', $dh[0]);
	$dia = $dt[2];
	$mes = baseHelper::getMonthName($dt[1]);
	$ano = $dt[0];
	$html .= '
			<div class="pt-3 b-top b-inherit b-top-dashed">
				<p class="text-sm mb-3">Senhor Caixa,<br />Bloqueto pessoal nominal e intransferível, favor receber apenas na data de utilização, SOB PENA DE NULIDADE.</p>
				<p class="text-right font-weight-bold">João Pessoa, '.$dia.' de '.$mes.' de '.$ano.'</p>
			</div>
		</div>
	';

else :
	$html = '<p class="alert alert-info alert-icon m-0">'.JText::_('MSG_ACCESS_NO_GUESTS').'</p>';
endif;

echo $html;

?>
