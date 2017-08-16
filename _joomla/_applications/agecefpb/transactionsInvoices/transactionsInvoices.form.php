<?php
defined('_JEXEC') or die;

// FORM
?>


<div class="row">
	<div class="col-sm-6 col-lg-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_GROUP'); ?></label>
			<span class="btn-group btn-group-justified" data-toggle="buttons">
				<label class="btn btn-default btn-active-success">
					<input type="radio" name="group_id" id="<?php echo $APPTAG?>-group_id-0" value="0" class="auto-tab" data-target="#<?php echo $APPTAG?>-due_date" />
					<?php echo JText::_('FIELD_LABEL_GROUP_0'); ?>
				</label>
				<label class="btn btn-default btn-active-success">
					<input type="radio" name="group_id" id="<?php echo $APPTAG?>-group_id-1" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-due_date" />
					<?php echo JText::_('FIELD_LABEL_GROUP_1'); ?>
				</label>
			</span>
		</div>
	</div>
	<div class="col-sm-6 col-lg-4">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_DUE_DATE'); ?></label>
			<input type="text" name="due_date" id="<?php echo $APPTAG?>-due_date" class="form-control field-date" data-convert="true" />
		</div>
	</div>
</div>
<div class="form-group">
	<hr class="hr-tag" />
	<span class="base-icon-info-circled badge badge-warning cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></span>
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
</div>
