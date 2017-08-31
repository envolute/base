<?php
defined('_JEXEC') or die;

// FORM
?>
<input type="hidden" name="user_id" id="<?php echo $APPTAG?>-user_id" />
<input type="hidden" name="usergroup" id="<?php echo $APPTAG?>-usergroup" />
<input type="hidden" name="username" id="<?php echo $APPTAG?>-username" />
<input type="hidden" name="cusername" id="<?php echo $APPTAG?>-cusername" />
<div class="row">
	<div class="col-xl-4">
		<?php echo JText::_('MSG_'.($cfg['isEdit'] ? 'EDIT' : 'REGISTRATION').'_FORM')?>
	</div>
	<div class="col-xl-8">
		<fieldset class="fieldset-embed fieldset-sm pb-0">
			<legend><?php echo JText::_('TEXT_DATA_EMPLOYEE'); ?></legend>
			<div class="row">
				<div class="col-sm-5 col-md-6">
					<div class="form-group">
						<span class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default btn-active-success">
								<input type="radio" name="cx_status" id="<?php echo $APPTAG?>-cx_status-0" value="0" class="auto-tab" data-target="#<?php echo $APPTAG?>-group-emailCaixa" data-target-display="true" />
								<?php echo JText::_('TEXT_CX_STATUS_0'); ?>
							</label>
							<label class="btn btn-default btn-active-warning">
								<input type="radio" name="cx_status" id="<?php echo $APPTAG?>-cx_status-1" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-group-emailCaixa" data-target-display="false" />
								<?php echo JText::_('TEXT_CX_STATUS_1'); ?></span>
							</label>
						</span>
					</div>
				</div>
				<div class="col-sm-7 col-md-6">
					<div id="<?php echo $APPTAG?>-group-emailCaixa" class="form-group">
						<div class="input-group">
							<input type="text" name="cx_email" id="<?php echo $APPTAG?>-cx_email" class="form-control" placeholder="E-mail Caixa" />
							<span class="input-group-addon">@caixa.gov.br</span>
						</div>
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_CODE'); ?></label>
						<input type="text" name="cx_code" id="<?php echo $APPTAG?>-cx_code" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_ADMISSION_DATE'); ?></label>
						<input type="text" name="cx_date" id="<?php echo $APPTAG?>-cx_date" class="form-control field-date" data-convert="true" />
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_ROLE'); ?></label>
						<input type="text" name="cx_role" id="<?php echo $APPTAG?>-cx_role" class="form-control upper" />
					</div>
				</div>
				<div class="col-sm-6 col-md-3">
					<div class="form-group field-required">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_SITUATED_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_SITUATED'); ?></label>
						<input type="text" name="cx_situated" id="<?php echo $APPTAG?>-cx_situated" class="form-control upper" />
					</div>
				</div>
			</div>
		</fieldset>
		<div class="row">
			<div class="col-lg-9 b-right b-dashed b-primary">
				<div class="row">
					<div class="col-12">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
							<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
						</div>
					</div>
					<div class="col-12">
						<div class="form-group field-required">
							<label>E-mail</label>
							<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
							<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label>CPF</label>
							<input type="text" name="cpf" id="<?php echo $APPTAG?>-cpf" class="form-control field-cpf" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('TEXT_ONLY_NUMBERS'); ?>">RG</label>
							<input type="text" name="rg" id="<?php echo $APPTAG?>-rg" class="form-control numeric" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label>RG Orgão</label>
							<input type="text" name="rg_orgao" id="<?php echo $APPTAG?>-rg_orgao" class="form-control upper" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
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
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
							<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_CHILDREN'); ?></label>
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
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_MARITAL_STATUS'); ?></label>
							<select name="marital_status" id="<?php echo $APPTAG?>-marital_status" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-group-partner">
								<option value="0" data-target-display="false"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<option value="1" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_1'); ?></option>
								<option value="2" data-target-display="true"><?php echo JText::_('TEXT_MARITAL_STATUS_2'); ?></option>
								<option value="3" data-target-display="true"><?php echo JText::_('TEXT_MARITAL_STATUS_3'); ?></option>
								<option value="4" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_4'); ?></option>
								<option value="5" data-target-display="false"><?php echo JText::_('TEXT_MARITAL_STATUS_5'); ?></option>
							</select>
						</div>
					</div>
					<div id="<?php echo $APPTAG?>-group-partner" class="col-sm-8" hidden>
						<div class="form-group">
							<label class="field-required"><?php echo JText::_('FIELD_LABEL_PARTNER'); ?></label>
							<input type="text" name="partner" id="<?php echo $APPTAG?>-partner" class="form-control upper" />
						</div>
					</div>
				</div>
				<hr class="hr-tag" />
				<span class="badge badge-primary"><?php echo JText::_('TEXT_ADDRESS_DATA'); ?></span>
				<div class="row">
					<div class="col-sm-3">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE'); ?></label>
							<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" class="form-control field-cep" />
						</div>
					</div>
					<div class="col-sm-7">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STREET'); ?></label>
							<input type="text" name="address" id="<?php echo $APPTAG?>-address" class="form-control upper" />
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_NUMBER'); ?></label>
							<input type="text" name="address_number" id="<?php echo $APPTAG?>-address_number" class="form-control upper" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_INFO'); ?></label>
							<input type="text" name="address_info" id="<?php echo $APPTAG?>-address_info" class="form-control" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
							<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
							<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
						</div>
					</div>
				</div>
				<hr class="hr-tag" />
				<span class="badge badge-primary"><?php echo JText::_('TEXT_CONTACT_DATA'); ?></span>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_CELLPHONE'); ?> 1</label>
							<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone0" class="form-control field-phone" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_CELLPHONE'); ?> 2</label>
							<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone1" class="form-control field-phone" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_PHONE_FIXED'); ?></label>
							<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone2" class="form-control field-phone" />
						</div>
					</div>
				</div>
				<hr class="hr-tag" />
				<span class="badge badge-primary"><?php echo JText::_('TEXT_ACCOUNT_DATA'); ?></span>
				<div class="row">
					<div class="col-sm-3 col-lg-2">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_AGENCY'); ?></label>
							<input type="text" name="agency" id="<?php echo $APPTAG?>-agency" class="form-control numeric length-fixed" data-length="4" maxlength="4" />
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_OPERATION'); ?></label>
							<input type="text" name="operation" id="<?php echo $APPTAG?>-operation" class="form-control numeric length-fixed" data-length="3" maxlength="3" />
						</div>
					</div>
					<div class="col-sm-4 col-lg-3">
						<div class="form-group field-required">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('TEXT_ONLY_NUMBERS'); ?>"><?php echo JText::_('FIELD_LABEL_ACCOUNT'); ?></label>
							<input type="text" name="account" id="<?php echo $APPTAG?>-account" class="form-control numeric length-fixed" data-length="10" maxlength="10" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_PHOTO'); ?></label>
					<div class="image-file" style="max-width:250px">
						<a href="#" class="image-action">
							<div class="image-file-label">
								<span class="image-file-off base-icon-file-image"><small>250 x 250</small></span>
								<span class="image-file-on text-sm base-icon-ok" hidden></span>
								<span class="image-file-edit base-icon-pencil" hidden></span>
							</div>
						</a>
						<span class="btn-group mt-2"></span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="field-image" hidden />
					</div>
				</div>
			</div>
		</div>
		<hr />
		<div class="form-actions">
			<?php if($cfg['isEdit']) :?>
				<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="btn btn-lg btn-success base-icon-ok btn-icon" onclick="<?php echo $APPTAG?>_save(<?php echo $APPTAG?>_editSuccess);"> <?php echo JText::_('TEXT_SAVE'); ?></button>
				<button type="button" class="btn btn-lg btn-default base-icon-cancel" onclick="javascript:history.back()"> <?php echo JText::_('TEXT_CANCEL'); ?></button>
			<?php else :?>
				<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="btn btn-lg btn-success base-icon-ok btn-icon" onclick="<?php echo $APPTAG?>_save(<?php echo $APPTAG?>_confirmSuccess);"> <?php echo JText::_('TEXT_REGISTER'); ?></button>
			<?php endif;?>
		</div>
	</div>
</div>
