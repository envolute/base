<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_employees') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$employees = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName('#__base_providers') .' WHERE agreement = 0 AND state = 1 ORDER BY name';
$db->setQuery($query);
$providers = $db->loadObjectList();

// INVOICES
$query = '
	SELECT T1.*
	FROM '. $db->quoteName('#__'.$cfg['project'].'_employees_invoices') .' T1
	WHERE T1.state = 1 ORDER BY T1.due_date DESC
';
$db->setQuery($query);
$invoices = $db->loadObjectList();

// FORM
?>

<div class="form-group field-required">
	<label class="label-sm"><?php echo JText::_('FIELD_LABEL_EMPLOYEE'); ?></label>
	<select name="employee_id" id="<?php echo $APPTAG?>-employee_id" class="form-control field-id">
		<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
		<?php
			foreach ($employees as $obj) {
				echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
			}
		?>
	</select>
</div>
<div class="form-group field-required">
	<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PROVIDER'); ?></label>
	<select name="provider_id" id="<?php echo $APPTAG?>-provider_id" class="form-control field-id">
		<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
		<?php
			foreach ($providers as $obj) {
				echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
			}
		?>
	</select>
</div>
<div class="row">
	<div class="col-lg-6">
		<div class="form-group">
			<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_BUYER_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
			<input type="text" name="description" id="<?php echo $APPTAG?>-description" class="form-control" />
		</div>
	</div>
	<div class="col-md-6 col-lg-3">
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_DATE'); ?></label>
			<input type="text" name="date" id="<?php echo $APPTAG?>-date" class="form-control field-date" data-convert="true" />
		</div>
	</div>
	<div class="col-md-6 col-lg-3">
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
			<input type="text" name="price" id="<?php echo $APPTAG?>-price" class="form-control field-price" data-convert="true" />
		</div>
	</div>
	<div class="col-md-6 col-lg-6">
		<label class="label-sm"><?php echo JText::_('FIELD_LABEL_DOC_NUMBER'); ?></label>
		<input type="text" name="doc_number" id="<?php echo $APPTAG?>-doc_number" class="form-control" />
	</div>
	<div class="col-md-6 col-lg-6">
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_INVOICE'); ?></label>
			<select name="invoice_id" id="<?php echo $APPTAG?>-invoice_id" class="form-control field-id">
				<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
				<?php
					foreach ($invoices as $obj) {
						echo '<option value="'.$obj->id.'">'.baseHelper::dateFormat($obj->due_date).'</option>';
					}
				?>
			</select>
		</div>
	</div>
</div>
<div class="form-group">
	<label class="label-sm"><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
</div>
