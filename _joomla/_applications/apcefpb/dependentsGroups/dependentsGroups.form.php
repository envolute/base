<?php
defined('_JEXEC') or die;

// FORM
?>
<div class="row">
	<div class="col-sm-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-4">
		<div class="form-group field-required">
			<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_OVERTIME_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_OVERTIME'); ?></label>
			<select name="overtime" id="<?php echo $APPTAG?>-overtime" class="form-control">
				<option value="0"><?php echo JText::_('TEXT_AGE_0'); ?></option>
				<option value="18"><?php echo JText::_('TEXT_AGE_18'); ?></option>
				<option value="24"><?php echo JText::_('TEXT_AGE_24'); ?></option>
			</select>
		</div>
	</div>
</div>
