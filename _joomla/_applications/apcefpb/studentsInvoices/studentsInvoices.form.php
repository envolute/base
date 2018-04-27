<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_sports') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$sports = $db->loadObjectList();

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
			<label><?php echo JText::_('FIELD_LABEL_SPORT'); ?></label>
			<select name="sport_id" id="<?php echo $APPTAG?>-sport_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-month">
				<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
				<?php
					foreach ($sports as $obj) {
						echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
					}
				?>
			</select>
		</div>
	</div>
</div>
<div class="form-group">
	<hr class="hr-tag" />
	<span class="base-icon-info-circled badge badge-warning cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></span>
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
</div>
