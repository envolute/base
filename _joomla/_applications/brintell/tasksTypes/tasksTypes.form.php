<?php
defined('_JEXEC') or die;

// FORM
?>
<div class="row">
	<div class="col-sm-6">
		<div class="form-group field-required">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			<label class="label-sm iconTip cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ICON_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_ICON'); ?></label>
			<div class="input-group">
				<input type="text" name="icon" id="<?php echo $APPTAG?>-icon" class="form-control" />
				<span class="input-group-btn">
					<a href="<?php echo JURI::root()?>templates/base/libs/content/baseicons/demo.html" class="btn btn-primary base-icon-eye hasTooltip" title="<?php echo JText::_('TEXT_ACCESS_ICONS_LIB'); ?>" target="_blank"></a>
				</span>
			</div>
		</div>
	</div>
</div>
