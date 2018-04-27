<?php
defined('_JEXEC') or die;

// GROUPS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$groups = $db->loadObjectList();

// BANKS
$query = 'SELECT * FROM '. $db->quoteName('#__base_banks') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$banks = $db->loadObjectList();

// FORM
?>

<div class="row">
	<div class="col-lg-9 b-right b-right-dashed">
		<div class="row">
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_GROUP'); ?></label>
					<div class="input-group">
						<select name="group_id" id="<?php echo $APPTAG?>-group_id" class="form-control field-id">
							<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
							<?php
								foreach ($groups as $obj) {
									echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
								}
							?>
						</select>
						<span class="input-group-btn">
							<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-employeesGroups" data-backdrop="static" data-keyboard="false"></button>
							<button type="button" class="base-icon-cog btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="employeesGroups_listReload(false)" data-toggle="modal" data-target="#modal-list-employeesGroups" data-backdrop="static" data-keyboard="false"></button>
						</span>
					</div>
				</div>
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
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('MSG_CARD_NAME'); ?>"><?php echo JText::_('FIELD_LABEL_CARD_NAME'); ?></label>
					<input type="text" name="card_name" id="<?php echo $APPTAG?>-card_name" class="form-control upper" maxlength="30" />
				</div>
				<div class="form-group field-required">
					<label class="label-sm">E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
					<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
							<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
						</div>
					</div>
					<div class="col-sm-6">
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
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_OCCUPATION'); ?></label>
							<input type="text" name="occupation" id="<?php echo $APPTAG?>-occupation" class="form-control upper" />
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_START_DATE'); ?></label>
							<input type="text" name="start_date" id="<?php echo $APPTAG?>-start_date" class="form-control field-date" data-convert="true" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="label-sm field-required-disabled"><?php echo JText::_('FIELD_LABEL_PLACE_BIRTH'); ?></label>
					<input type="text" name="place_birth" id="<?php echo $APPTAG?>-place_birth" class="form-control upper" />
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_CHILDREN'); ?></label>
							<select type="text" name="children" id="<?php echo $APPTAG?>-children" class="form-control">
								<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<?php
									for($i = 1; $i < 20; $i++) {
										echo '<option value="'.$i.'">'.$i.'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_MARITAL_STATUS'); ?></label>
							<select name="marital_status" id="<?php echo $APPTAG?>-marital_status" class="form-control auto-tab" data-target="<?php echo $APPTAG?>-group-partner">
								<option value="0" data-target-display="false"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<option value="1" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_1'); ?></option>
								<option value="2" data-target-display="true"><?php echo JText::_('TEXT_MARITAL_STATUS_2'); ?></option>
								<option value="3" data-target-display="true"><?php echo JText::_('TEXT_MARITAL_STATUS_3'); ?></option>
								<option value="4" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_4'); ?></option>
								<option value="5" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_5'); ?></option>
							</select>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4 b-left b-left-dashed">
				<div class="form-group field-required-disabled">
					<label class="label-sm">CPF</label>
					<input type="text" name="cpf" id="<?php echo $APPTAG?>-cpf" class="form-control field-cpf" />
					<input type="hidden" name="ccpf" id="<?php echo $APPTAG?>-ccpf" />
				</div>
				<div class="form-group field-required-disabled">
					<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('TEXT_ONLY_NUMBERS'); ?>">RG</label>
					<input type="text" name="rg" id="<?php echo $APPTAG?>-rg" class="form-control numeric" />
				</div>
				<div class="form-group field-required-disabled">
					<label class="label-sm">RG Orgão</label>
					<input type="text" name="rg_orgao" id="<?php echo $APPTAG?>-rg_orgao" class="form-control upper" />
				</div>
				<div class="form-group">
					<label class="label-sm">N&ordm; do PIS</label>
					<input type="text" name="pis" id="<?php echo $APPTAG?>-pis" class="form-control upper" />
				</div>
				<div class="form-group">
					<label class="label-sm">N&ordm; da CTPS</label>
					<input type="text" name="ctps" id="<?php echo $APPTAG?>-ctps" class="form-control upper" />
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
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_DRIVER_CARD'); ?></label>
					<select name="driver" id="<?php echo $APPTAG?>-driver" class="form-control" multiple="multiple">
						<option value="A">A - Motocicleta</option>
						<option value="B">B - Automóvel</option>
						<option value="C">C - Caminhão</option>
						<option value="D">D - Ônibus</option>
						<option value="E">E - 2 Reboques</option>
					</select>
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
			<label class="label-sm d-block text-center pt-1"><?php echo JText::_('TEXT_CARD'); ?></label>
			<button type="button" class="btn btn-lg btn-block btn-success base-icon-print btn-icon" onclick="<?php echo $APPTAG?>_printCard()"> <?php echo JText::_('TEXT_PRINT'); ?></button>
		</div>
	</div>
</div>
<div id="<?php echo $APPTAG?>-group-partner" hidden>
	<hr class="hr-tag" />
	<span class="badge badge-primary"><?php echo JText::_('TEXT_PARTNER_DATA'); ?></span>
	<div class="row">
		<div class="col-lg-6">
			<div class="form-group">
				<label class="label-sm field-required-disabled"><?php echo JText::_('FIELD_LABEL_PARTNER'); ?></label>
				<input type="text" name="partner" id="<?php echo $APPTAG?>-partner" class="form-control upper" />
			</div>
		</div>
		<div class="col-sm-6 col-lg-3">
			<div class="form-group no-margin">
				<label class="label-sm">CPF</label>
				<input type="text" name="partner_cpf" id="<?php echo $APPTAG?>-partner_cpf" class="form-control field-cpf" />
			</div>
		</div>
		<div class="col-sm-6 col-lg-3">
			<div class="form-group no-margin">
				<label class="label-sm"><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
				<input type="text" name="partner_birthday" id="<?php echo $APPTAG?>-partner_birthday" class="form-control field-date birthday" data-convert="true" />
			</div>
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
					<input type="text" name="phone_desc[]" id="<?php echo $APPTAG?>-phone_desc" class="form-control upper" placeholder="<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>" maxlength="50" />
				</div>
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-phoneGroups" class="newFieldsGroup"></div>
	</div>
</div>
<hr class="hr-tag" />
<span class="badge badge-primary"><?php echo JText::_('TEXT_ACCOUNT_DATA'); ?></span>
<div class="row">
	<div class="col-lg-5">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_BANK'); ?></label>
			<div class="input-group">
				<select name="bank_id" id="<?php echo $APPTAG?>-bank_id" class="form-control field-id">
					<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
					<?php
						foreach ($banks as $obj) {
							echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
				<span class="input-group-btn">
					<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-_banks" data-backdrop="static" data-keyboard="false"></button>
					<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="_banks_listReload(false)" data-toggle="modal" data-target="#modal-list-_banks" data-backdrop="static" data-keyboard="false"></button>
				</span>
			</div>
		</div>
	</div>
	<div class="col-sm-4 col-lg-2">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_AGENCY'); ?></label>
			<input type="text" name="agency" id="<?php echo $APPTAG?>-agency" class="form-control" />
		</div>
	</div>
	<div class="col-sm-4 col-lg-2">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_OPERATION'); ?></label>
			<input type="text" name="operation" id="<?php echo $APPTAG?>-operation" class="form-control" />
		</div>
	</div>
	<div class="col-sm-4 col-lg-3">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_ACCOUNT'); ?></label>
			<input type="text" name="account" id="<?php echo $APPTAG?>-account" class="form-control" />
		</div>
	</div>
</div>
