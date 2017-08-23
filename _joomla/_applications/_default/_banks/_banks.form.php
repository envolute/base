<?php
defined('_JEXEC') or die;

// FORM
?>
<div class="row">
	<div class="col-sm-4">
		<div class="form-group field-required">
			<label class="iconTip cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_CODE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_CODE'); ?></label>
			<input type="text" name="code" id="<?php echo $APPTAG?>-code" class="form-control" />
		</div>
	</div>
	<div class="col-sm-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
		</div>
	</div>
</div>
