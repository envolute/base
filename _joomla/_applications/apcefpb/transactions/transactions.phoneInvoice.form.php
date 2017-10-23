<?php
defined('_JEXEC') or die;

$query = '
	SELECT
		'. $db->quoteName('T1.id') .',
		'. $db->quoteName('T1.due_date') .'
	FROM
		'. $db->quoteName('#__'.$cfg['project'].'_phones_invoices') .' T1
	WHERE T1.state = 1 ORDER BY T1.due_date DESC
';
$db->setQuery($query);
$invoices = $db->loadObjectList();
?>

<div class="modal-header">
	<h5 class="modal-title"><?php echo JText::_('FORM_PHONE_INVOICED_TITLE'); ?></h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
	<fieldset id="form-<?php echo $APPTAG?>-phoneInvoice" method="post">
		<div class="row">
			<div class="col-sm-7">
				<div class="form-group">
					<select name="phoneInvoiceID" id="<?php echo $APPTAG?>-phoneInvoiceID" class="form-control">
						<option value="0">- <?php echo JText::_('TEXT_SELECT_INVOICE'); ?> -</option>
						<?php
							foreach ($invoices as $obj) {
								echo '<option value="'.$obj->id.'">'.baseHelper::dateFormat($obj->due_date).'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-sm-5 text-right">
				<div class="form-group">
					<button type="button" name="btn-<?php echo $APPTAG?>-phoneInvoice-add" id="btn-<?php echo $APPTAG?>-phoneInvoice-add" class="btn btn-block btn-success" onclick="<?php echo $APPTAG?>_phoneInvoice()">
						<?php echo JText::_('TEXT_CREATE_TRANSACTIONS'); ?>
					</button>
				</div>
			</div>
		</div>
	</fieldset>
</div>
