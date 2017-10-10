<?php
defined('_JEXEC') or die;

$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T1.due_date') .',
		IF(`T1`.`custom_desc` <> "", `T1`.`custom_desc`, `T1`.`description`) invoice_desc
	FROM
		'. $db->quoteName($cfg['mainTable'].'_invoices') .' T1
	WHERE T1.state = 1 ORDER BY T1.due_date DESC, T1.description ASC, T1.custom_desc ASC
';
$db->setQuery($query);
$invoices = $db->loadObjectList();
?>

<div class="modal-header">
	<h5 class="modal-title"><?php echo JText::_('FORM_INVOICED_TITLE'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
									$desc = ' - '.baseHelper::nameFormat($obj->invoice_desc, 20);
									echo '<option value="'.$obj->id.'">'.baseHelper::dateFormat($obj->due_date).$desc.'</option>';
								}
							?>
						</select>
						<span class="input-group-btn">
							<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-transactionsInvoices" data-backdrop="static" data-keyboard="false"></button>
							<button type="button" class="base-icon-pencil btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>_editInvoice()"></button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-sm-3 text-right">
				<div class="form-group">
					<button type="button" name="btn-<?php echo $APPTAG?>-invoice-add" id="btn-<?php echo $APPTAG?>-invoice-add" class="btn btn-block btn-success" onclick="<?php echo $APPTAG?>_invoice()">
						<?php echo JText::_('TEXT_TO_ASSIGN'); ?>
					</button>
				</div>
			</div>
		</div>
	</fieldset>
</div>
