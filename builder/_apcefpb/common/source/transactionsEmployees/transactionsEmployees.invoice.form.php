<?php
defined('_JEXEC') or die;

$query = '
SELECT
	'. $db->quoteName('T1.id') .',
	'. $db->quoteName('T1.month') .',
	'. $db->quoteName('T1.year') .'
FROM
	'. $db->quoteName('#__apcefpb_invoices_employees') .' T1
WHERE T1.state = 1 ORDER BY T1.year DESC, T1.month DESC';
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
			<div class="col-sm-8">
				<div class="form-group">
					<div class="input-group">
						<select name="invoiceID" id="<?php echo $APPTAG?>-invoiceID" class="form-control">
							<option value="0">- <?php echo JText::_('TEXT_SELECT_INVOICE'); ?> -</option>
							<?php
								foreach ($invoices as $obj) {
									echo '<option value="'.$obj->id.'">'.baseHelper::getMonthName($obj->month).' de '.$obj->year.'</option>';
								}
							?>
						</select>
						<span class="input-group-btn">
							<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-invoicesEmployees" data-backdrop="static" data-keyboard="false"></button>
							<button type="button" class="base-icon-pencil btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>_editInvoice()"></button>
						</span>
					</div>
				</div>
			</div>
			<div class="col-sm-4 text-right">
				<div class="form-group">
					<button type="button" name="btn-<?php echo $APPTAG?>-invoice-add" id="btn-<?php echo $APPTAG?>-invoice-add" class="btn btn-block btn-success" onclick="<?php echo $APPTAG?>_invoice()">
            <span class="base-icon-plus btn-icon"></span>
            <?php echo JText::_('TEXT_ADD_TO_INVOICE'); ?>
          </button>
				</div>
			</div>
		</div>
	</fieldset>
</div>
