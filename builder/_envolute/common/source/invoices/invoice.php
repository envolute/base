<?php
defined('_JEXEC') or die;

$ajaxRequest = false;
require('config.php');
// IMPORTANTE: Carrega o arquivo 'helper' do template
JLoader::register('baseHelper', JPATH_BASE.'/templates/base/source/helpers/base.php');

$app = JFactory::getApplication('site');

// init general css/js files
require(JPATH_BASE.'/templates/base/source/_init.app.php');

// get current user's data
$user = JFactory::getUser();
$groups = $user->groups;

//joomla get request data
$input = $app->input;

// Default Params
$id = $input->get('id', '', 'string');
$id = htmlspecialchars(base64_decode($id));

// database connect
$db = JFactory::getDbo();

$query = '
  SELECT
    '. $db->quoteName('T1.id') .',
    '. $db->quoteName('T1.project_id') .',
    '. $db->quoteName('T1.type') .' invoiceType,
    '. $db->quoteName('T2.name') .' project,
    '. $db->quoteName('T2.client_id') .',
    '. $db->quoteName('T3.name') .' client,
    '. $db->quoteName('T3.email') .',
    '. $db->quoteName('T3.email_payment') .',
    '. $db->quoteName('T3.type') .' clientType,
    '. $db->quoteName('T3.doc_number') .',
    '. $db->quoteName('T1.due_date') .',
    '. $db->quoteName('T1.price') .',
    '. $db->quoteName('T1.create_boleto') .',
    '. $db->quoteName('T1.description') .',
    '. $db->quoteName('T1.description1') .',
    '. $db->quoteName('T1.description2') .',
    '. $db->quoteName('T1.description3') .',
    '. $db->quoteName('T1.description4') .',
    '. $db->quoteName('T1.hosting') .',
    '. $db->quoteName('T1.discount') .',
    '. $db->quoteName('T1.discount_note') .',
    '. $db->quoteName('T1.tax') .',
    '. $db->quoteName('T1.tax_note') .',
    '. $db->quoteName('T1.assessment') .',
    '. $db->quoteName('T1.assessment_note') .',
    '. $db->quoteName('T1.url_boleto') .',
    '. $db->quoteName('T1.bankAccount_info') .',
    '. $db->quoteName('T1.paid') .',
    '. $db->quoteName('T1.paid_date') .'
  FROM
    '. $db->quoteName($cfg['mainTable']) .' T1
    JOIN '. $db->quoteName('#__envolute_projects').' T2
    ON T2.id = T1.project_id
    JOIN '. $db->quoteName('#__envolute_clients').' T3
    ON T3.id = T2.client_id
	WHERE
		T1.id = '.$id;
;
try {

	$db->setQuery($query);
	$inv = $db->loadObject();

} catch (RuntimeException $e) {
	 echo $e->getMessage();
	 return;
}

if($inv->id != null) :

  // registros de atividades dessa fatura
  $query = '
    SELECT
      '. $db->quoteName('T1.id') .',
      '. $db->quoteName('T1.task_id') .',
      '. $db->quoteName('T2.title') .' task,
      '. $db->quoteName('T3.name') .' user,
      '. $db->quoteName('T4.name') .' service,
      '. $db->quoteName('T1.date') .',
      '. $db->quoteName('T1.start_hour') .',
      '. $db->quoteName('T1.end_hour') .',
      '. $db->quoteName('T1.time') .',
      SEC_TO_TIME(SUM(TIME_TO_SEC('. $db->quoteName('T1.total_time') .'))) total_time,
      '. $db->quoteName('T1.hours') .',
      '. $db->quoteName('T1.price_hour') .',
      SUM('. $db->quoteName('T1.price') .') price,
      '. $db->quoteName('T2.price') .' priceFixed,
      '. $db->quoteName('T1.billable') .',
      '. $db->quoteName('T1.state') .'
    FROM
      '. $db->quoteName('#__envolute_tasks_timer') .' T1
      JOIN '. $db->quoteName('#__envolute_tasks') .' T2
      ON T2.id = T1.task_id
      LEFT JOIN '. $db->quoteName('#__users') .' T3
      ON T3.id = T1.user_id
      JOIN '. $db->quoteName('#__envolute_services') .' T4
      ON T4.id = T2.service_id
      LEFT JOIN '. $db->quoteName($cfg['mainTable']) .' T5
      ON T5.id = T1.invoice_id
    WHERE
        T1.invoice_id = '.$id.'
    GROUP BY T2.title, T1.billable';
  ;

  $db->setQuery($query);
  $res = $db->loadObjectList();

  $payment = '';
  if($inv->paid == 0) :
    if(!empty($inv->url_boleto)) $payment .= ' <a href="'.$inv->url_boleto.'" target="_blank" class="base-icon-print btn btn-sm btn-success"> Imprimir Boleto</a>';
    if(!empty($inv->bankAccount_info)) $payment .= ' <a href="#bankData" class="set-modal base-icon-arrows-cw btn btn-sm btn-success" data-modal-inline="true" data-modal-title="Dados para Transferência" data-modal-width="600px" data-modal-height="80%"> Dados para Transferência</a><div style="display:none"><div id="bankData">'.$inv->bankAccount_info.'</div></div>';
  endif;

  $html = '
    <div class="base-app clearfix">
      <div class="list-toolbar floating hidden-print">
        '.($inv->paid == 0 ? '<strong class="base-icon-off text-live text-lg display-inline-block valign-middle right-space"> Aberta</strong>' : '<strong class="base-icon-ok-circled text-success text-lg display-inline-block valign-middle"> Paga em '.baseHelper::dateFormat($inv->paid_date).'!</strong>').'
        '.$payment.'
      </div>
      <div class="row">
        <div class="col-sm-8">
          <h4 class="no-margin-bottom">'.$inv->client.'</h4>
          <p><strong>Projeto:</strong> '.$inv->project.'</p>
        </div>
        <div class="col-sm-4 text-right text-left-xs">
          <h4 class="no-margin-bottom">Fat. ID: #'.$inv->id.'</h4>
          <p>Vencimento: '.baseHelper::dateFormat($inv->due_date).'</p>
        </div>
      </div>
      <hr class="hr-sm" />
  ';

  if($inv->invoiceType == 0) :
    $html .= '
      <p>
        <span class="text-sm text-muted font-featured">'.$inv->description.'</span>
      </p>
    ';
  endif;

  $html .= '
      <div class="row">
        <div class="col-md-8">
  ';

  $total = 0.00;
  if($inv->invoiceType == 1) :

    $total = $inv->price;

    // VIEW
    $html .= '
    	<table class="table table-striped table-hover table-condensed">
        <thead>
          <tr>
            <th>Demonstrativo</th>
            <th width="100" class="text-right">R$ Valor</th>
          </tr>
        </thead>
    		<tbody>
          <tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
            <td>'.$inv->description.'</td>
            <td class="strong text-right">'.baseHelper::priceFormat($total).'</td>
          </tr>
    ';

  else :

    // registros de atividades dessa fatura
    $query = '
      SELECT
        '. $db->quoteName('T1.id') .',
        '. $db->quoteName('T1.task_id') .',
        '. $db->quoteName('T2.title') .' task,
        '. $db->quoteName('T3.name') .' user,
        '. $db->quoteName('T4.name') .' service,
        '. $db->quoteName('T1.date') .',
        '. $db->quoteName('T1.start_hour') .',
        '. $db->quoteName('T1.end_hour') .',
        '. $db->quoteName('T1.time') .',
        SEC_TO_TIME(SUM(TIME_TO_SEC('. $db->quoteName('T1.total_time') .'))) total_time,
        '. $db->quoteName('T1.hours') .',
        '. $db->quoteName('T1.price_hour') .',
        SUM('. $db->quoteName('T1.price') .') price,
        '. $db->quoteName('T2.price') .' priceFixed,
        '. $db->quoteName('T1.billable') .',
        '. $db->quoteName('T1.state') .'
      FROM
        '. $db->quoteName('#__envolute_tasks_timer') .' T1
        JOIN '. $db->quoteName('#__envolute_tasks') .' T2
        ON T2.id = T1.task_id
        LEFT JOIN '. $db->quoteName('#__users') .' T3
        ON T3.id = T1.user_id
        JOIN '. $db->quoteName('#__envolute_services') .' T4
        ON T4.id = T2.service_id
        LEFT JOIN '. $db->quoteName($cfg['mainTable']) .' T5
        ON T5.id = T1.invoice_id
      WHERE
          T1.invoice_id = '.$id.'
      GROUP BY T2.title, T1.billable';
    ;
    $db->setQuery($query);
    $res = $db->loadObjectList();

    // cobrança de hospedagem
    $hosting = '';
    $hostPrice = (float)0.00;
    if($inv->hosting == 1) :
      $query = '
        SELECT
          SUM(IF('.$db->quoteName('T1.price').' != '.$db->quote('0.00').', '.$db->quoteName('T1.price').', '.$db->quoteName('T2.price').')) priceHost
        FROM
          '. $db->quoteName('#__envolute_hosts') .' T1
          JOIN '. $db->quoteName('#__envolute_hosts_plans') .' T2
          ON T2.id = T1.plan_id
        WHERE
          T1.project_id = '.$inv->project_id.' AND T1.state = 1';
      ;
      $db->setQuery($query);
      $hostPrice = $db->loadResult();
      $hosting = '
        <tr id="'.$APPTAG.'-item-0">
          <td><strong>#1 - </strong><span class="font-condensed">'.JText::_('FIELD_LABEL_HOSTING').'</span></td>
          <td class="strong text-right">'.baseHelper::priceFormat($hostPrice).'</td>
        </tr>
      ';

    endif;

    // VIEW
    $html .= '
    	<table class="table table-striped table-hover table-condensed">
        <thead>
          <tr>
            <th>'.JText::_('TEXT_SERVICE').'s</th>
            <th width="100" class="text-right">R$ Valor</th>
          </tr>
        </thead>
    		<tbody>
    ';

    foreach($res as $item) {

      // PRICE
      $total += ($item->billable == 0) ? $item->priceFixed : $item->price;

      // ATTACHMENTS
      if($cfg['hasUpload']) :
  			JLoader::register('uploader', JPATH_BASE.'/templates/base/source/helpers/upload.php');
  			$files[$item->id] = uploader::getFiles($cfg['fileTable'], $item->id);
  			$listFiles = '';
  			for($i = 0; $i < count($files[$item->id]); $i++) {
  				if(!empty($files[$item->id][$i]->filename)) :
  					$listFiles .= '
  						<a href="'.JURI::root(true).'/get-file?fn='.base64_encode($files[$item->id][$i]->filename).'&mt='.base64_encode($files[$item->id][$i]->mimetype).'&tag='.base64_encode($APPNAME).'">
  							<span class="base-icon-attach hasTooltip" title="'.$files[$item->id][$i]->filename.'<br />'.((int)($files[$item->id][$i]->filesize / 1024)).'kb"></span>
  						</a>
  					';
  				endif;
  			}
  		endif;
      // REPORT
  		$task = '<strong class="hidden-print">#'.$item->task_id.' - </strong><span class="font-condensed">'.baseHelper::nameFormat($item->task).'</span>';
  		$billed = ($item->billed == 1) ? '<span class="base-icon-ok text-success cursor-help hasTooltip" title="'.baseHelper::dateFormat($item->billed_date, 'd/m/Y H:i:s').'"></span>' : '<span class="base-icon-cancel text-danger"></span>';
  		// PRICE
  		// fixo, definido na tarefa
  		if($item->billable == 0) :
  			$taskPrice = baseHelper::priceFormat($item->priceFixed);
    		$priceHour = '';
  		else :
  	    $taskPrice = baseHelper::priceFormat($item->price);
    		$priceHour = baseHelper::priceFormat($item->price_hour);
  		endif;
  		$taskUser = !empty($item->user) ? ' <span class="base-icon-user text-live cursor-help hasTooltip" title="'.JText::_('TEXT_REGISTERED_BY').'<br />'.baseHelper::nameFormat($item->user).'"></span>' : '';
      $note = !empty($item->note) ? '<div class="small text-muted font-featured">'.$item->note.'</div>' : '';
      $rowState = $item->state == 0 ? 'danger' : '';
      if($taskPrice != '0,00') :
        $clock = ($item->billable) ? '<sup class="base-icon-clock text-xs text-live hidden-print hidden-xs cursor-help hasPopover" data-placement="left" data-content="<strong>Tempo</strong>: '.substr($item->total_time, 0, 5).' ('.$item->hours.' hr)<br /><strong>R$/hr</strong>: '.$priceHour.'"></sup> ' : '<sup class="base-icon-down-circled text-xs text-primary hidden-print hidden-xs cursor-help hasTooltip" data-placement="left" title="'.JText::_('MSG_TASK_PRICE_FIXED').'"></sup> ';
    		$html .= '
    			<tr id="'.$APPTAG.'-item-'.$item->id.'" class="'.$rowState.'">
    				<td>'.$task.$note.'</td>
    				<td class="strong text-right">'.$clock.$taskPrice.'</td>
    			</tr>
    		';
      endif;
    }

    // hospedagem
    $html .= $hosting;

  endif;

  // acrescenta taxa e multa
  $totalPay = $total + (float)($inv->tax + $inv->assessment);
  // hospedagem
  $totalPay += (float)$hostPrice;
  // desconto
  $totalPay -= $inv->discount;
  // total geral
  $totalPay = strval(number_format($totalPay, 2));

  $html .= '
      			</tbody>
      		</table>
        </div>
        <div class="col-md-4">
          <ul class="list text-right">
            <li><strong class="pull-left">Valor Total:</strong>'.baseHelper::priceFormat(($total + $hostPrice)).'</li>
            <li><strong class="pull-left">Desconto:</strong>'.baseHelper::priceFormat($inv->discount).'</li>
            <li><strong class="pull-left">Taxa:</strong>'.baseHelper::priceFormat($inv->tax).'</li>
            <li><strong class="pull-left">Multa:</strong>'.baseHelper::priceFormat($inv->assessment).'</li>
          </ul>
          <h1 class="text-right all-expand bg-primary text-success set-border-top top-space">
            <div class="text-lg pull-left top-space">Total</div>
            R$ '.baseHelper::priceFormat($totalPay).'
          </h1>
        </div>
      </div>
    </div>
  ';

else :

  $html = '
    <h4 class="alert alert-warning">
      <span class="base-icon-attention"></span> Está fatura não está disponível!
    </h4>
  ';

endif;

echo $html;

?>
