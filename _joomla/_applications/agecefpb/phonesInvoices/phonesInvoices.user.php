<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
defined('_JEXEC') or die;
$ajaxRequest = false;
require('config.php');
$cfg['isPublic'] = false; // Público -> acesso aberto a todos

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

// Get request data
$pID = $app->input->get('pID', 0, 'int'); // phone ID
$uID = $app->input->get('uID', 0, 'int'); // user ID
$uID = ($hasAdmin && $uID > 0) ? $uID : $user->id;

// Admin Actions
require_once(JPATH_APPS.DS.'clients/clients.select.user.php');

if($pID > 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET PHONE DATA
	$query = '
		SELECT
			'. $db->quoteName('T1.id') .',
			'. $db->quoteName('T2.name') .' plan,
			'. $db->quoteName('T3.id') .' providerID,
			'. $db->quoteName('T3.name') .' provider,
			'. $db->quoteName('T4.name') .' client,
			'. $db->quoteName('T1.phone_number') .'
		FROM '. $db->quoteName('#__'.$cfg['project'].'_phones') .' T1
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_phones_plans') .' T2
			ON T2.id = T1.plan_id AND T2.state = 1
			JOIN '. $db->quoteName('#__base_providers') .' T3
			ON T3.id = T2.provider_id AND T3.state = 1
			JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T4
			ON T4.id = T1.client_id AND T4.state = 1
		WHERE '.
			$db->quoteName('T1.id') .' = '. $pID .' AND '.
			$db->quoteName('T4.user_id') .' = '. $uID
	;
	$db->setQuery($query);
	$phone = $db->loadObject();

	$html = '';
	if(!empty($phone->phone_number)) :

		// FILTER
		$where = '';

			// PHONES
			$flt_phone = '';
			$query = '
				SELECT
					'. $db->quoteName('T1.id') .',
					'. $db->quoteName('T1.phone_number') .'
				FROM '.$db->quoteName('#__'.$cfg['project'].'_phones').' T1
					JOIN '. $db->quoteName('#__'.$cfg['project'].'_phones_plans') .' T2
					ON T2.id = T1.plan_id AND T2.state = 1
					JOIN '. $db->quoteName('#__'.$cfg['project'].'_clients') .' T3
					ON T3.id = T1.client_id AND T3.state = 1
				WHERE
					'.$db->quoteName('T3.user_id') .' = '. $uID . ' AND
					'.$db->quoteName('T1.state') .' = 1
			';
			$db->setQuery($query);
			$phones = $db->loadObjectList();
			foreach ($phones as $obj) {
				$flt_phone .= '<option value="'.$obj->id.'"'.($obj->id == $pID ? ' selected = "selected"' : '').'>'.$obj->phone_number.'</option>';
			}

			// YEARS -> select
			$fYear	= $app->input->get('fYear', 0, 'int');
			if($fYear != 0) $where .= ' AND YEAR(due_date) = '.$fYear;
			// Select available years
			$flt_year = '';
			$query = '
				SELECT DISTINCT(YEAR(due_date)) year
				FROM '.$db->quoteName('vw_'.$cfg['project'].'_phones_invoices_phone_total').'
				WHERE '.$db->quoteName('phone_id') .' = '. $phone->id . '
				ORDER BY due_date DESC
			';
			$db->setQuery($query);
			$years = $db->loadObjectList();
			$cYear = $fYear;
			foreach ($years as $obj) {
				// Ano mais recente disponível
				if($cYear == 0) $cYear = $obj->year;
				// Listas de anos do filtro
				$flt_year .= '<option value="'.$obj->year.'"'.($obj->year == $cYear ? ' selected = "selected"' : '').'>'.$obj->year.'</option>';
			}

		// Imagem Principal -> Primeira imagem (index = 0)
		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		$img = uploader::getFile('#__base_providers_files', '', $phone->providerID, 0, JPATH_BASE.DS.'images/apps/_providers/');
		if(!empty($img)) $img = '<img src="'.JURI::root().'images/apps/_providers/'.$img['filename'].'" style="height:64px;" class="img-fluid float-left mr-2" />';

		// Header
		$html .= '
			<div class="clearfix mb-2">
				'.$img.'
				<h4 class="float-right text-right mt-2 mb-0">
					'.$phone->phone_number.'
					<div class="text-md text-muted">'.JText::_('TEXT_PLAN').': '.$phone->plan.'</div>
				</h4>
			</div>
		';

		// Filtro
		$selectYear = !empty($flt_year) ? '<select name="fYear" id="fYear" class="form-control-lg set-filter">'.$flt_year.'</select>' : '';
		$html .= '
			<hr class="mt-0 mb-2" />
			<form id="filter-'.$APPTAG.'" class="text-center b-bottom b-dashed pb-2 hidden-print" method="get">
				<select name="pID" id="pID" class="form-control-lg set-filter">
					'.$flt_phone.'
				</select>
				'.$selectYear.'
				<input type="hidden" name="uID" value="'.$uID.'" />
			</form>
			<script>
				var formFilter = jQuery("#filter-'.$APPTAG.'");
				jQuery(function() {
					formFilter.find(".set-filter").change(function() {
					  setTimeout(function() { formFilter.submit() }, 100);
					});
				});
			</script>
		';

		if($cYear != 0) :

			// GET DATA
			$query = '
				SELECT * FROM '.$db->quoteName('vw_'.$cfg['project'].'_phones_invoices_phone_total').'
				WHERE '.$db->quoteName('phone_id') .' = '. $phone->id .' AND YEAR(due_date) = '.$cYear.$where.'
				ORDER BY '. $db->quoteName('due_date') .' DESC
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
				$html .= '<ul class="set-list bordered list-lg text-lg">';
				foreach($res as $item) {
					// LINK TO INVOICE
					$urlToInvoiceDetail = JURI::root().'services/mobile-invoices/invoice-details?invID='.$item->invoice_id.'&pID='.$item->phone_id.($uID != $user->id ? '&uID='.$uID : '');
					$html .= '
						<li>
							<div class="row">
								<div class="col-6">
									<span class="base-icon-calendar"></span>
									<a href="'.$urlToInvoiceDetail.'">'.baseHelper::dateFormat($item->due_date).'</span></a>
								</div>
								<div class="col-6 text-right">
									<a href="'.$urlToInvoiceDetail.'">R$ '.baseHelper::priceFormat($item->total).'</a>
								</div>
							</a>
						</li>
					';
				}
				$html .= '</ul>';
			else :
				$html .= '<p class="base-icon-info-circled alert alert-warning"> '.JText::sprintf('MSG_LISTNOREG', $phone->phone_number).'</p>';
			endif;

			echo $html;

		else : // Year == 0

			echo $html.'<p class="alert alert-warning base-icon-attention"> '.JText::sprintf('MSG_LISTNOREG', $phone->phone_number).'</p>';

		endif;

	else : // phone empty

		echo '<p class="alert alert-warning base-icon-attention"> '.JText::_('MSG_NO_PLAN').'</p>';

	endif;

else :

	echo '<p class="alert alert-warning base-icon-attention"> '.JText::_('MSG_SELECT_PLAN').'</p>';

endif;
?>
