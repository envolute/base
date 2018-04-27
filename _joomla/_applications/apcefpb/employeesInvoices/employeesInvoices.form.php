<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_sports') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$sports = $db->loadObjectList();

// FORM
?>


<div class="row">
	<div class="col-sm-4">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_DUE_DATE'); ?></label>
			<input type="text" name="due_date" id="<?php echo $APPTAG?>-due_date" class="form-control field-date" data-convert="true" />
		</div>
	</div>
	<div class="col-md">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
			<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
		</div>
	</div>
</div>
