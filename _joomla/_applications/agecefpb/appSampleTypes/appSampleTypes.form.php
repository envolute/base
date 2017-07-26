<?php
defined('_JEXEC') or die;

// FORM
?>
<div class="form-group field-required">
	<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
	<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
</div>
<div class="form-group">
	<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
	<textarea name="description" id="<?php echo $APPTAG?>-description" rows="6" class="form-control"></textarea>
</div>
