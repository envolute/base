<?php
defined('_JEXEC') or die;

// FORM
?>
<div class="row">
	<div class="col-sm-2">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_COUNTRY_CODE'); ?></label>
			<input type="text" name="country_code" id="<?php echo $APPTAG?>-country_code" class="form-control" maxlength="5" />
		</div>
	</div>
	<div class="col-sm-5">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_PHONE_NUMBER'); ?></label>
			<input type="text" name="phone_number" id="<?php echo $APPTAG?>-phone_number" class="form-control field-phone" data-toggle-mask="true" />
		</div>
	</div>
	<div class="col-sm-5">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_OPERATOR'); ?></label>
			<div class="input-group">
				<input type="text" name="operator" id="<?php echo $APPTAG?>-operator" class="form-control upper" />
				<span class="input-group-btn btn-group" data-toggle="buttons">
					<label class="btn btn-outline-success btn-block btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">
						<input type="checkbox" name="whatsapp" id="<?php echo $APPTAG?>-whatsapp" value="1" />
						<span class="base-icon-whatsapp icon-default"></span>
					</label>
				</span>
			</div>
		</div>
	</div>
	<div class="col-12">
		<div class="form-group">
			<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DESCRIPTION_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
			<div class="input-group">
				<input type="text" name="description" id="<?php echo $APPTAG?>-description" class="form-control" />
				<span class="input-group-btn btn-group" data-toggle="buttons">
					<label class="btn btn-default btn-block btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_MAIN_DESC'); ?>">
						<span class="base-icon-cancel"></span>
						<input type="checkbox" name="main" id="<?php echo $APPTAG?>-main" value="1" /><?php echo JText::_('FIELD_LABEL_MAIN'); ?>
					</label>
				</span>
			</div>
		</div>
	</div>
</div>
