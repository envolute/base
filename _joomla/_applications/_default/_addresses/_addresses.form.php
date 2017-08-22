<?php
defined('_JEXEC') or die;

// FORM
?>
<div class="row">
	<div class="col-sm-3">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE'); ?></label>
			<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" class="form-control field-cep" />
		</div>
	</div>
	<div class="col-sm-9">
		<div class="form-group">
			<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_MAIN_DESC'); ?>">
				<?php echo JText::_('FIELD_LABEL_MAIN'); ?>
			</label>
			<div class="input-group">
				<span class="btn-group input-group-btn" data-toggle="buttons">
					<label class="btn btn-danger btn-active-success">
						<input type="checkbox" name="main" id="<?php echo $APPTAG?>-main" value="1" class="mr-1 auto-tab" data-target="<?php echo $APPTAG?>-description" data-target-disabled="true" />
						<span class="base-icon-cancel"></span>
					</label>
				</span>
				<input type="text" name="description" id="<?php echo $APPTAG?>-description" class="form-control upper" maxlength="30" placeholder="<?php echo JText::_('FIELD_LABEL_MAIN_PLACEHOLDER'); ?>" disabled="disabled" />
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-9">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STREET'); ?></label>
			<input type="text" name="address" id="<?php echo $APPTAG?>-address" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-3">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_ADDRESS_NUMBER'); ?></label>
			<input type="text" name="address_number" id="<?php echo $APPTAG?>-address_number" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-12">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_ADDRESS_INFO'); ?></label>
			<input type="text" name="address_info" id="<?php echo $APPTAG?>-address_info" class="form-control" />
		</div>
	</div>
	<div class="col-sm-5">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
			<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-5">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
			<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-5">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STATE'); ?></label>
			<input type="text" name="address_state" id="<?php echo $APPTAG?>-address_state" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-5">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_ADDRESS_COUNTRY'); ?></label>
			<input type="text" name="address_country" id="<?php echo $APPTAG?>-address_country" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-2">
		<div class="form-group">
			<label class="iconTip hasTooltip" title="<?php echo JText::_('TEXT_MAP_DESC'); ?>"><?php echo JText::_('TEXT_MAP'); ?></label>
			<div class="btn-group w-100" data-toggle="buttons">
				<label class="btn btn-warning btn-block btn-active-success">
					<input type="checkbox" name="map" id="<?php echo $APPTAG?>-map" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-map-group" data-target-display="true" />
					<span class="base-icon-location icon-default"></span>
				</span>
			</div>
		</div>
	</div>
</div>
<div id="<?php echo $APPTAG?>-map-group" hidden>
	<hr class="hr-tag" />
	<span class="badge badge-warning"> <?php echo JText::_('TEXT_MAP'); ?></span>
	<div class="row">
		<div class="col-sm-5">
			<div class="form-group">
				<label>Latitude</label>
				<input type="text" name="latitude" id="<?php echo $APPTAG?>-latitude" class="form-control" />
			</div>
		</div>
		<div class="col-sm-5">
			<div class="form-group">
				<label>Longitude</label>
				<input type="text" name="longitude" id="<?php echo $APPTAG?>-longitude" class="form-control" />
			</div>
		</div>
		<div class="col-12">
			<div class="form-group">
				<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ADDRESS_URL_MAP_DESC'); ?>">
					<?php echo JText::_('FIELD_LABEL_ADDRESS_URL_MAP'); ?>
				</label>
				<input type="text" name="url_map" id="<?php echo $APPTAG?>-url_map" class="form-control field-url" />
			</div>
		</div>
	</div>
</div>
