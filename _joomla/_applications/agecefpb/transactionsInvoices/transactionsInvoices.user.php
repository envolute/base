<?php
/* SISTEMA PARA CADASTRO DE TELEFONES
 * AUTOR: IVO JUNIOR
 * EM: 18/02/2016
*/
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
$uID = $app->input->get('uID', 0, 'int'); // user ID
$uID = ($hasAdmin && $uID > 0) ? $uID : $user->id;

// Admin Actions
require_once(JPATH_APPS.DS.'clients/clients.select.user.php');

if($uID > 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	$html = '';

	// FILTER
	$where = '';

	// YEARS -> select
	$fYear	= $app->input->get('fYear', 0, 'int');
	if($fYear != 0) $where .= ' AND YEAR(due_date) = '.$fYear;
	// Select available years
	$flt_year = '';
	$query = '
		SELECT DISTINCT(YEAR(due_date)) year
		FROM '.$db->quoteName('vw_'.$cfg['project'].'_transactions_invoices_total').'
		WHERE '.$db->quoteName('user_id') .' = '. $uID .'
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
	// Filtro
	$selectYear = !empty($flt_year) ? '<select name="fYear" id="fYear" class="form-control-lg set-filter">'.$flt_year.'</select>' : '';

	// Header
	$html .= '
		<div class="row no-gutters b-bottom b-bottom-dashed pb-2 hidden-print mb-2">
			<div class="col-sm-8">
				<h3 class="m-0">Faturas do Associado</h3>
				<div class="text-md text-muted">'.$clientName.'</div>
			</div>
			<div class="col-sm-4 text-right">
				<form id="filter-'.$APPTAG.'" method="get">
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
			</div>
		</div>
	';

	if($cYear != 0) :

		// GET DATA
		$query = '
			SELECT * FROM '.$db->quoteName('vw_'.$cfg['project'].'_transactions_invoices_total').'
			WHERE '.$db->quoteName('user_id') .' = '. $uID .' AND YEAR(due_date) = '.$cYear.$where.'
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
				$urlToInvoiceDetail = JURI::root().'apps/clients/invoices/details?invID='.$item->invoice_id.($uID != $user->id ? '&uID='.$uID : '');
				$unpaid = ($item->unpaid == 1) ? ' - <span class="base-icon-attention text-danger text-sm align-middle cursor-help hasTooltip" title="'.$item->reason.'"> '.JText::_('TEXT_UNPAID').'</span>' : '';
				$status = ($item->unpaid == 1) ? ' class="text-danger"' : '';
				$html .= '
					<li>
						<div class="row">
							<div class="col-6">
								<a'.$status.' href="'.$urlToInvoiceDetail.'">
									'.baseHelper::getMonthName($item->due_month).'
									'.$unpaid.'
									<div class="base-icon-calendar small text-muted">
										'.baseHelper::dateFormat($item->due_date).' - '.$item->invoice_desc.'
									</div>
								</a>
							</div>
							<div class="col-6 text-right">
								<a'.$status.' href="'.$urlToInvoiceDetail.'">R$ '.baseHelper::priceFormat($item->total).'</a>
							</div>
						</a>
					</li>
				';
			}
			$html .= '</ul>';
		else :
			$html .= '<p class="base-icon-info-circled alert alert-warning"> '.JText::_('MSG_LISTNOREG').'</p>';
		endif;

		echo $html;

	else : // Year == 0

		echo $html.'<p class="alert alert-warning base-icon-attention"> '.JText::_('MSG_LISTNOREG').'</p>';

	endif;

else :

	echo '<p class="alert alert-warning base-icon-attention"> '.JText::_('MSG_SELECT_USER').'</p>';

endif;
?>
