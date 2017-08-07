<?php
defined('_JEXEC') or die;

// OPERATORS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_operators') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$operators = $db->loadObjectList();

// FORM
?>
<div class="form-group field-required">
	<label><?php echo JText::_('FIELD_LABEL_OPERATOR'); ?></label>
	<div class="input-group">
		<select name="operator_id" id="<?php echo $APPTAG?>-operator_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-name">
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
	<div class="col-lg-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
		</div>
	</div>
	<div class="col-lg-4">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
			<input type="text" name="price" id="<?php echo $APPTAG?>-price" class="form-control field-price" data-convert="true" />
		</div>
	</div>
</div>
<div class="form-group">
	<hr />
	<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
	<textarea name="description" id="<?php echo $APPTAG?>-description" rows="4" class="form-control field-html"></textarea>
</div>
