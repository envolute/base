<?php
defined('_JEXEC') or die;

// BANKS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_banks') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$banks = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-sm-7">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_BANK'); ?></label>
			<div class="input-group">
				<select name="bank_id" id="<?php echo $APPTAG?>-bank_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-agency">
					<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
					<?php
						foreach ($banks as $obj) {
							echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
				<span class="input-group-btn">
					<button type="button" class="base-icon-plus btn btn-success hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-_banks" data-backdrop="static" data-keyboard="false"></button>
					<button type="button" class="base-icon-cog btn btn-primary hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="_banks_listReload(false)" data-toggle="modal" data-target="#modal-list-_banks" data-backdrop="static" data-keyboard="false"></button>
				</span>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_AGENCY'); ?></label>
			<input type="text" name="agency" id="<?php echo $APPTAG?>-agency" class="form-control" />
		</div>
	</div>
	<div class="col-sm-3">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_OPERATION'); ?></label>
			<input type="text" name="operation" id="<?php echo $APPTAG?>-operation" class="form-control" />
		</div>
	</div>
	<div class="col-sm-5">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_ACCOUNT'); ?></label>
			<input type="text" name="account" id="<?php echo $APPTAG?>-account" class="form-control" />
		</div>
	</div>
</div>
<div class="form-group">
	<hr class="hr-tag" />
	<span class="base-icon-info-circled badge badge-warning cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></span>
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
</div>
