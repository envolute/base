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
$invID = $app->input->get('invID', 0, 'int'); // invoice ID
$uID = $app->input->get('uID', 0, 'int'); // user ID
$uID = ($hasAdmin && $uID > 0) ? $uID : $user->id;
// LINK TO INVOICE
$urlToInvoices = JURI::root().'apps/clients/invoices'.($uID != $user->id ? '?uID='.$uID : '');

$redir = false;
if($invID > 0) :

	// DATABASE CONNECT
	$db = JFactory::getDbo();

	// GET INVOICE DATA
	$query = '
		SELECT * FROM '.$db->quoteName('vw_'.$cfg['project'].'_transactions_invoices_total').'
		WHERE '. $db->quoteName('invoice_id') .' = '. $invID .' AND '. $db->quoteName('user_id') .' = '. $uID
	;
	$db->setQuery($query);
	$invoice = $db->loadObject();

	if(!empty($invoice->invoice_id)) :

		// Invoice Status
		$unpaid = ($invoice->unpaid == 1) ? ' <div class="base-icon-attention text-danger text-sm"> '.JText::_('TEXT_UNPAID').(!empty($invoice->reason) ? '<span class="text-muted"> - '.$invoice->reason.'</span>' : '').'</div>' : '';

		// GET INVOICE DETAILS DATA
		$query = '
			SELECT * FROM '.$db->quoteName('vw_'.$cfg['project'].'_transactions_invoice').'
			WHERE '.$db->quoteName('invoice_id') .' = '. $invID .' AND '. $db->quoteName('user_id') .' = '. $uID
		;
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

			// Header
			$html .= '
				<div class="row pb-2">
					<div class="col-md">
						<h2 class="m-0">'.JText::_('TEXT_CLIENT_INVOICE').'</h2>
						<div class="text-muted">'.$invoice->invoice_desc.$unpaid.'</div>
					</div>
					<div class="col-md align-self-center text-muted text-md-right pt-2">
						<h6 class="m-0">
							'.$invoice->client_name.'
							<div class="small">'.$invoice->client_number.'</div>
						</h6>
					</div>
				</div>
				<hr class="mt-0 mb-2" />
				<div class="text-lg b-bottom b-dashed pb-2 mb-3">
					<a href="'.$urlToInvoices.'" class="base-icon-left-big hidden-print"><span class="d-none d-sm-inline"> '.JText::_('TEXT_RETURN_TO_INVOICES').'</span></a>
					<span class="float-right">'.JText::_('FIELD_LABEL_DUE_DATE').': '.baseHelper::dateFormat($invoice->due_date).'</span>
				</div>
				<ul class="set-list bordered mb-4">
			';

			foreach($res as $item) {

				$urlToPhoneInvoice = JURI::root().'apps/clients/phonesinvoices/details?invID='.$item->phoneInvoice_id.'&pID='.$item->phone_id.($item->user_id != $user->id ? '&uID='.$item->user_id : '');
				$desc = !empty($item->phoneInvoice_id) ? '<a href="'.$urlToPhoneInvoice.'" class="new-window" target="_blank">'.$item->description.'</a>' : $item->description;
				$info = !empty($desc) ? $desc : '';
				$info .= !empty($item->doc_number) ? ' - '.$item->doc_number : '';
				$installment = ($item->installments_total > $item->installment) ? '('.$item->installment.'/'.$item->installments_total.')' : '';

				$html .= '
					<li>
						<div class="row">
							<div class="col-6 col-sm-7">
								<h5 class="mb-1">'.baseHelper::nameFormat($item->provider_name).'</h5>
								<div class="text-sm text-muted">'.$info.'</div>
							</div>
							<div class="col-2">'.$installment.'</div>
							<div class="col text-right">
								<h5 class="mb-1">'.baseHelper::priceFormat($item->price).'</h5>
								<div class="text-sm text-muted">'.baseHelper::dateFormat($item->date_installment).'</div>
							</div>
						</a>
					</li>
				';
			}
			$html .= '</ul>';
			// Valores adicionais
			$html .= '
				<hr class="b-top-2 border-primary" />
				<div class="row text-xl">
					<div class="col-6">'.JText::_('TEXT_TOTAL').'</div>
					<div class="col-6 text-right text-live"><small class="text-muted">R$</small> '.baseHelper::priceFormat($invoice->total).'</div>
				</div>
			';
		endif;

		echo $html;

	else : // invoice empty

		$redir = true;

	endif;

else :

	$redir = true;

endif;

if($redir) :
	// redireciona para a listagem de faturas
	$app->enqueueMessage(JText::_('MSG_NO_INVOICE'), 'warning');
	$app->redirect(JURI::root(true).'/apps/clients/invoices'.($uID != $user->id ? '?uID='.$uID : ''));
	exit();
endif;
?>
