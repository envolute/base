<?php
defined('_JEXEC') or die;

// FORM
?>


<div class="row">
	<div class="col-sm-6 col-lg-4">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_DUE_DATE'); ?></label>
			<input type="text" name="due_date" id="<?php echo $APPTAG?>-due_date" class="form-control field-date" data-convert="true" />
		</div>
	</div>
	<div class="col-sm-6 col-lg-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
			<select name="description" id="<?php echo $APPTAG?>-description" class="form-control auto-tab" data-target="#<?php echo $APPTAG?>-group-desc">
				<!-- <option value="" data-target-display="false">- <?php echo JText::_('TEXT_SELECT'); ?> -</option> -->
				<?php
				for($i = 0; $i < count($preDesc); $i++) {
					echo '<option value="'.$preDesc[$i].'" data-target-display="false">'.$preDesc[$i].'</option>';
				}
				?>
				<option value="0" data-target-display="true"><?php echo JText::_('TEXT_OTHER'); ?></option>
			</select>
		</div>
	</div>
	<div id="<?php echo $APPTAG?>-group-desc" class="col-lg-8 ml-lg-auto" hidden>
		<div class="form-group">
			<input type="text" name="custom_desc" id="<?php echo $APPTAG?>-custom_desc" class="form-control" maxlength="30" placeholder="<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>" />
		</div>
	</div>
</div>
<div class="form-group">
	<hr class="hr-tag" />
	<span class="base-icon-info-circled badge badge-warning cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></span>
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
</div>
