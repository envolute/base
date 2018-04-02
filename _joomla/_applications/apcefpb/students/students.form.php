<?php
defined('_JEXEC') or die;

// FORM
?>

<div class="row">
	<div class="col-lg-9 b-right b-right-dashed">
		<div class="row">
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<div class="input-group">
						<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
						<span class="btn-group input-group-btn" data-toggle="buttons">
							<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('MSG_CARD_NAME'); ?>">
								<input type="checkbox" name="toggleName" id="<?php echo $APPTAG?>-toggleName" value="1" class="no-validate auto-tab" data-target="<?php echo $APPTAG?>-group-nameCard" data-target-display="true" />
								<span class="base-icon-plus icon-default"></span>
							</label>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-group-nameCard" class="form-group" hidden>
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('MSG_CARD_NAME'); ?>"><?php echo JText::_('FIELD_LABEL_CARD_NAME'); ?></label><br />
					<input type="text" name="card_name" id="<?php echo $APPTAG?>-card_name" class="form-control upper" maxlength="30" />
				</div>
				<div class="form-group field-required">
					<label class="label-sm">E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
					<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
				</div>
				<div class="form-group">
					<label class="label-sm field-required"><?php echo JText::_('FIELD_LABEL_MOTHER_NAME'); ?></label>
					<input type="text" name="mother_name" id="<?php echo $APPTAG?>-mother_name" class="form-control upper" />
				</div>
				<div class="form-group">
					<label class="label-sm field-required"><?php echo JText::_('FIELD_LABEL_FATHER_NAME'); ?></label>
					<input type="text" name="father_name" id="<?php echo $APPTAG?>-father_name" class="form-control upper" />
				</div>
			</div>
			<div class="col-sm-4 b-left b-left-dashed">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
					<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
				</div>
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" value="1" />
							<?php echo JText::_('TEXT_GENDER_1_ABBR'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" value="2" />
							<?php echo JText::_('TEXT_GENDER_2_ABBR'); ?>
						</label>
					</span>
				</div>
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_BLOOD_TYPE'); ?></label>
					<select name="blood_type" id="<?php echo $APPTAG?>-blood_type" class="form-control">
						<option value=""><?php echo JText::_('TEXT_SELECT'); ?></option>
						<option value="O+">O+</option>
						<option value="A+">A+</option>
						<option value="B+">B+</option>
						<option value="O-">O-</option>
						<option value="A-">A-</option>
						<option value="B-">B-</option>
						<option value="AB+">AB+</option>
						<option value="AB-">AB-</option>
					</select>
				</div>
			</div>
		</div>
		<hr class="hr-sm" />
		<div class="row">
			<div class="col-sm-4">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_HAS_DISEASE'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<span class="base-icon-cancel"></span>
							<input type="radio" name="has_disease" id="<?php echo $APPTAG?>-has_disease0" class="auto-tab" data-target="<?php echo $APPTAG?>-disease_desc" data-target-disabled="true" value="0" />
							<?php echo JText::_('TEXT_NO'); ?>
						</label>
						<label class="btn btn-default btn-active-danger">
							<span class="base-icon-cancel"></span>
							<input type="radio" name="has_disease" id="<?php echo $APPTAG?>-has_disease1" class="auto-tab" data-target="<?php echo $APPTAG?>-disease_desc" data-target-disabled="false" value="1" />
							<?php echo JText::_('TEXT_YES'); ?>
						</label>
					</span>
				</div>
			</div>
			<div class="col-sm-8">
				<div class="form-group">
					<label class="label-sm">Qual?</label>
					<input type="text" name="disease_desc" id="<?php echo $APPTAG?>-disease_desc" class="form-control" disabled="disabled" />
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_HAS_ALLERGY'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<span class="base-icon-cancel"></span>
							<input type="radio" name="has_allergy" id="<?php echo $APPTAG?>-has_allergy0" class="auto-tab" data-target="<?php echo $APPTAG?>-allergy_desc" data-target-disabled="true" value="0" />
							<?php echo JText::_('TEXT_NO'); ?>
						</label>
						<label class="btn btn-default btn-active-danger">
							<span class="base-icon-cancel"></span>
							<input type="radio" name="has_allergy" id="<?php echo $APPTAG?>-has_allergy1" class="auto-tab" data-target="<?php echo $APPTAG?>-allergy_desc" data-target-disabled="false" value="1" />
							<?php echo JText::_('TEXT_YES'); ?>
						</label>
					</span>
				</div>
			</div>
			<div class="col-sm-8">
				<div class="form-group">
					<label class="label-sm">Qual?</label>
					<input type="text" name="allergy_desc" id="<?php echo $APPTAG?>-allergy_desc" class="form-control" />
				</div>
			</div>
			<div class="col-sm-12">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
					<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" />
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PHOTO'); ?></label>
			<div class="image-file">
				<a href="#" class="image-action">
					<div class="image-file-label">
						<span class="image-file-off base-icon-file-image"><small>200 x 200</small></span>
						<span class="image-file-on text-sm base-icon-ok" hidden></span>
						<span class="image-file-edit base-icon-pencil" hidden></span>
					</div>
				</a>
				<span class="btn-group mt-2"></span>
				<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="field-image" hidden />
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-group-btnPrint" class="form-group" hidden>
			<label class="d-block text-center pt-1"><?php echo JText::_('TEXT_CLIENT_CARD'); ?></label>
			<button type="button" class="btn btn-lg btn-block btn-success base-icon-print btn-icon" onclick="<?php echo $APPTAG?>_printCard()"> <?php echo JText::_('TEXT_PRINT'); ?></button>
		</div>
	</div>
</div>
<hr class="hr-tag" />
<span class="badge badge-primary"><?php echo JText::_('TEXT_LOCATION_DATA'); ?></span>
<div class="row">
	<div class="col-lg-9 b-right b-right-dashed">
		<div class="row">
			<div class="col-md-9">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_STREET'); ?></label>
					<input type="text" name="address" id="<?php echo $APPTAG?>-address" class="form-control upper" />
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_NUMBER'); ?></label>
					<input type="text" name="address_number" id="<?php echo $APPTAG?>-address_number" class="form-control upper" />
				</div>
			</div>
			<div class="col-md-9">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_INFO'); ?></label>
					<input type="text" name="address_info" id="<?php echo $APPTAG?>-address_info" class="form-control" />
				</div>
			</div>
			<div class="col-md-4 col-lg-3">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE'); ?></label>
					<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" class="form-control field-cep" />
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					<label class="label-sm">UF</label>
					<input type="text" name="address_state" id="<?php echo $APPTAG?>-address_state" class="form-control upper" size="2" maxlength="2" />
					<input type="hidden" name="address_country" id="<?php echo $APPTAG?>-address_country" />
				</div>
			</div>
			<div class="col-md-5">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
					<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
				</div>
			</div>
			<div class="col-md-5">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
					<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
				</div>
			</div>
		</div>
	</div>
</div>
<hr class="hr-tag" />
<span class="badge badge-primary"><?php echo JText::_('TEXT_CONTACT_DATA'); ?></span>
<div class="row">
	<div class="col-lg-9 b-right b-right-dashed">
		<div class="form-group">
			<button type="button" class="btn btn-xs btn-success base-icon-plus float-right" onclick="<?php echo $APPTAG?>_phoneAdd()"> <?php echo JText::_('TEXT_PHONES_ADD')?></button>
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PHONE'); ?></label>
			<div class="row">
				<div class="col-sm-5 col-lg-4">
					<div class="input-group">
						<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone" class="form-control field-phone" />
						<span class="input-group-btn btn-group" data-toggle="buttons">
							<label class="btn btn-outline-success btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">
								<input type="checkbox" name="wapp[]" id="<?php echo $APPTAG?>-wapp" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-whatsapp" data-target-value="1" data-target-value-reset="" data-tab-disabled="true" />
								<span class="base-icon-whatsapp icon-default"></span>
								<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp" />
							</label>
						</span>
					</div>
				</div>
				<div class="col-sm-7 col-lg-8 pt-2 pt-sm-0">
					<input type="text" name="phone_desc[]" id="<?php echo $APPTAG?>-phone_desc" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>" maxlength="50" />
				</div>
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-phoneGroups" class="newFieldsGroup"></div>
	</div>
</div>
