<?php
defined('_JEXEC') or die;

// OPERATORS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_phones_plans_operators') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$operators = $db->loadObjectList();

// FORM
?>

<div class="form-group field-required">
	<label><?php echo JText::_('FIELD_LABEL_OPERATOR'); ?></label>
	<div class="input-group">
		<select name="operator_id" id="<?php echo $APPTAG?>-operator_id" class="form-control field-id">
			<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
			<?php
				foreach ($operators as $obj) {
					echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
				}
			?>
		</select>
		<span class="input-group-btn">
			<button type="button" class="base-icon-plus btn btn-success hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Operators" data-backdrop="static" data-keyboard="false"></button>
			<button type="button" class="base-icon-cog btn btn-primary hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Operators_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Operators" data-backdrop="static" data-keyboard="false"></button>
		</span>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_DUE_DATE'); ?></label>
			<input type="text" name="due_date" id="<?php echo $APPTAG?>-due_date" class="form-control field-date" data-convert="true" />
		</div>
	</div>
	<div class="col-sm-4">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_FILE'); ?></label>
			<div class="btn-file">
				<span class="btn-group w-100">
					<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
				</span>
				<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_TAX'); ?></label>
			<input type="text" name="tax" id="<?php echo $APPTAG?>-tax" class="form-control field-price" data-convert="true" />
		</div>
	</div>
</div>
<hr class="mt-0" />
<button type="button" class="btn btn-success base-icon-arrows-cw btn-icon" onclick="<?php echo $APPTAG?>_invoiceDetails(0, true)"><?php echo JText::_('TEXT_RUN_INVOICE_DETAILS'); ?></button>
<div class="form-group">
	<hr class="hr-tag" />
	<span class="base-icon-info-circled badge badge-warning cursor-help hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>"> <?php echo JText::_('FIELD_LABEL_NOTE'); ?></span>
	<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_NOTE_DESC'); ?>" />
</div>
