<?php
defined('_JEXEC') or die;

$query = '
SELECT
	'. $db->quoteName('T1.id') .',
	'. $db->quoteName('T2.name').' project,
	'. $db->quoteName('T3.name').' client,
	'. $db->quoteName('T1.due_date') .',
	'. $db->quoteName('T1.month') .',
	'. $db->quoteName('T1.year') .'
FROM
	'. $db->quoteName('#__envolute_invoices') .' T1
	JOIN '. $db->quoteName('#__envolute_projects').' T2
	ON T2.id = T1.project_id
	JOIN '. $db->quoteName('#__envolute_clients').' T3
	ON T3.id = T2.client_id
WHERE T1.state = 1 AND T1.paid = 0 ORDER BY T1.due_date DESC, T3.name ASC, T2.name ASC';
$db->setQuery($query);
$invoices = $db->loadObjectList();
?>

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title"><?php echo JText::_('FORM_INVOICED_TITLE'); ?></h4>
</div>
<div class="modal-body">
	<fieldset id="form-<?php echo $APPTAG?>-invoice" method="post">
		<div class="row">
			<div class="col-sm-9">
				<div class="form-group">
					<div class="input-group">
						<select name="invoiceID" id="<?php echo $APPTAG?>-invoiceID" class="form-control">
							<option value="0">- <?php echo JText::_('TEXT_SELECT_INVOICE'); ?> -</option>
							<?php
								foreach ($invoices as $obj) {
									echo '<option value="'.$obj->id.'">'.baseHelper::dateFormat($obj->due_date, 'd.m').' - '.baseHelper::nameFormat($obj->project).' ['.baseHelper::nameFormat($obj->client).']</option>';
								}
							?>
						</select>
						<span class="input-group-btn">
							<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-invoices" data-backdrop="static" data-keyboard="false"></button>
							<button type="button" class="base-icon-pencil btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>_editInvoice()"></button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-sm-3 text-right">
				<div class="form-group">
					<button type="button" name="btn-<?php echo $APPTAG?>-invoice-add" id="btn-<?php echo $APPTAG?>-invoice-add" class="btn btn-block btn-success" onclick="<?php echo $APPTAG?>_invoice(true)">
            <?php echo JText::_('TEXT_ADD_TO_INVOICE'); ?>
          </button>
				</div>
			</div>
		</div>
	</fieldset>
</div>
