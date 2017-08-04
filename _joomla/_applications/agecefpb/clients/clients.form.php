<?php
defined('_JEXEC') or die;

// USUÁRIOS SEM CLIENTES ASSOCIADOS
$query = '
	SELECT T1.id, T1.name, T1.email
	FROM '. $db->quoteName('#__users') .' T1
		LEFT OUTER JOIN '. $db->quoteName($cfg['mainTable']) .' T2
		ON T2.user_id = T1.id
	WHERE T2.name IS NULL
	ORDER BY T1.name
';
$db->setQuery($query);
$users = $db->loadObjectList();

// FORM
?>

<!-- Nav tabs -->
<ul class="nav nav-tabs">
	<li class="nav-item">
		<a class="nav-link active" href="#tab-main" data-toggle="tab" role="tab"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#tab-address" data-toggle="tab" role="tab"><?php echo JText::_('TEXT_CONTACT_DATA'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#tab-account-info" data-toggle="tab" role="tab"><?php echo JText::_('TEXT_ACCOUNT_DATA'); ?></a>
	</li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active" id="tab-main" role="tabpanel">
		<div class="row">
			<div class="col-sm-9">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
				</div>
				<div class="form-group field-required">
					<label>E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
					<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
				</div>
				<div class="row">
					<div class="col-lg-4">
						<div class="form-group field-required">
							<label>CPF</label>
							<input type="text" name="cpf" id="<?php echo $APPTAG?>-cpf" class="form-control field-cpf" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group field-required">
							<label>RG</label>
							<input type="text" name="rg" id="<?php echo $APPTAG?>-rg" class="form-control" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group field-required">
							<label>RG Orgão</label>
							<input type="text" name="rg_orgao" id="<?php echo $APPTAG?>-rg_orgao" class="form-control upper" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
							<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" class="auto-tab" data-target="<?php echo $APPTAG?>-marital_status" value="1" />
									<?php echo JText::_('TEXT_MALE_ABBR'); ?>
								</label>
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" class="auto-tab" data-target="<?php echo $APPTAG?>-marital_status" value="2" />
									<?php echo JText::_('TEXT_FEMALE_ABBR'); ?>
								</label>
							</span>
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_MARITAL_STATUS'); ?></label>
							<select name="marital_status" id="<?php echo $APPTAG?>-marital_status" class="form-control auto-tab" data-target="<?php echo $APPTAG?>-group-partner">
								<option value="" data-target-display="false"><?php echo JText::_('TEXT_SELECT'); ?></option>
								<option value="SOLTEIRO" data-target-display="false">Solteiro</option>
								<option value="CASADO" data-target-display="true">Casado</option>
								<option value="UNIÃO ESTÁVEL" data-target-display="true">União Estável</option>
								<option value="DIVORCIADO" data-target-display="false">Divorciado</option>
								<option value="VIÚVO" data-target-display="false">Viúvo</option>
							</select>
						</div>
					</div>
					<div id="<?php echo $APPTAG?>-group-partner" class="col-12" hidden>
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_PARTNER'); ?></label>
							<input type="text" name="partner" id="<?php echo $APPTAG?>-partner" class="form-control upper" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_PHOTO'); ?></label>
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
			</div>
		</div>
		<div class="row pt-3">
			<div class="col-lg-6">
				<fieldset class="fieldset-embed fieldset-sm">
					<legend><?php echo JText::_('TEXT_DATA_EMPLOYEE'); ?></legend>
					<div id="<?php echo $APPTAG?>-group-emailCaixa" class="form-group">
						<label>E-mail Caixa</label>
						<div class="input-group">
							<input type="text" name="cx_email" id="<?php echo $APPTAG?>-cx_email" class="form-control" />
							<span class="input-group-addon">@caixa.gov.br</span>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_CODE'); ?></label>
								<input type="text" name="cx_code" id="<?php echo $APPTAG?>-cx_code" class="form-control upper" />
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_ADMISSION_DATE'); ?></label>
								<input type="text" name="cx_date" id="<?php echo $APPTAG?>-cx_date" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_ROLE'); ?></label>
								<input type="text" name="cx_role" id="<?php echo $APPTAG?>-cx_role" class="form-control upper" />
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_SITUATED'); ?></label>
								<input type="text" name="cx_situated" id="<?php echo $APPTAG?>-cx_situated" class="form-control upper" />
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="col-lg-6">
				<fieldset class="fieldset-embed fieldset-sm">
					<legend><?php echo JText::_('TEXT_DATA_REGISTRATION'); ?></legend>
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_ACCESS_STATUS'); ?></label>
						<span class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default btn-active-danger">
								<input type="radio" name="access" id="<?php echo $APPTAG?>-access-0" value="0" onchange="<?php echo $APPTAG?>_accessForm(0)" class="auto-tab" data-target="#<?php echo $APPTAG?>-statusReason" />
								<?php echo JText::_('TEXT_PENDING'); ?>
							</label>
							<label class="btn btn-default btn-active-success">
								<input type="radio" name="access" id="<?php echo $APPTAG?>-access-1" value="1" onchange="<?php echo $APPTAG?>_accessForm(1)" />
								<?php echo JText::_('TEXT_APPROVED'); ?>
							</label>
						</span>
						<input type="hidden" name="user_id" id="<?php echo $APPTAG?>-user_id" />
					</div>
					<div id="reasonStatus" class="collapse">
						<input type="text" name="statusReason" id="<?php echo $APPTAG?>-statusReason" class="form-control" maxlength="50" placeholder="<?php echo JText::_('FIELD_LABEL_REASON'); ?>" />
					</div>
					<div id="accessFields" class="collapse">
						<div class="row">
							<div class="col-12 new-user-data" hidden>
								<div class="form-group no-margin">
									<div class="input-group">
										<select name="newUser" id="<?php echo $APPTAG?>-newUser" class="form-control">
											<option value="0"><?php echo JText::_('TEXT_NEW').' '.JText::_('TEXT_USER'); ?></option>
											<?php
												foreach ($users as $obj) {
													echo '<option value="'.$obj->id.'" data-name="'.$obj->name.'" data-email="'.$obj->email.'">'.baseHelper::nameFormat($obj->name).'</option>';
												}
											?>
										</select>
										<span class="input-group-addon hasPopover" title="<?php echo JText::_('FIELD_LABEL_USER_ACCESS'); ?>" data-content="<?php echo JText::_('FIELD_LABEL_USER_ACCESS_DESC'); ?>" data-placement="top">
											<span class="base-icon-info-circled"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-12 edit-user-data">
								<hr class="hr-tag" />
								<span class="badge badge-primary"><?php echo JText::_('TEXT_PASSWORD_RESET'); ?></span>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="d-block">
										<?php echo JText::_('FIELD_LABEL_PASSWORD'); ?>
										<small class="text-live font-condensed float-right pt-1"><?php echo JText::_('TEXT_OPTIONAL'); ?></small>
									</label>
									<div class="input-group">
										<input type="password" name="password" id="<?php echo $APPTAG?>-password" class="form-control" />
										<span class="input-group-addon hasPopover" data-content="<?php echo JText::_('MSG_PASSWORD_INFO'); ?>" data-placement="top">
											<span class="base-icon-info-circled"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label class="iconTip hasTooltip" title="<?php echo JText::_('MSG_REPASSWORD'); ?>"><?php echo JText::_('FIELD_LABEL_REPASSWORD'); ?></label>
									<input type="password" name="repassword" id="<?php echo $APPTAG?>-repassword" class="form-control" />
								</div>
							</div>
							<div class="col-12 new-user-data" hidden>
								<div class="form-group m-0">
									<label><?php echo JText::_('FIELD_LABEL_REGISTRATION_EMAIL_CONFIRM'); ?></label>
									<div class="input-group input-group-sm">
										<span class="input-group-addon">
											<input type="checkbox" name="emailConfirm" id="<?php echo $APPTAG?>-emailConfirm" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-emailInfo" data-target-disabled="false" />
										</span>
										<input type="text" name="emailInfo" id="<?php echo $APPTAG?>-emailInfo" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_REGISTRATION_EMAIL_INFO'); ?>" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
	</div>
	<div class="tab-pane" id="tab-address" role="tabpanel">
		<div class="row">
			<div class="col-md-8">
				<div class="row">
					<div class="col-lg-3">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE'); ?></label>
							<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" class="form-control field-cep" />
						</div>
					</div>
					<div class="col-lg-7">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STREET'); ?></label>
							<input type="text" name="address" id="<?php echo $APPTAG?>-address" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-2">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_NUMBER'); ?></label>
							<input type="text" name="address_number" id="<?php echo $APPTAG?>-address_number" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-12">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_INFO'); ?></label>
							<input type="text" name="address_info" id="<?php echo $APPTAG?>-address_info" class="form-control" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
							<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
							<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 b-left b-dashed">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_PHONE'); ?> 1</label>
					<input type="text" name="phone" id="<?php echo $APPTAG?>-phone" class="form-control field-phone" />
				</div>
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_PHONE'); ?> 2</label>
					<input type="text" name="phone" id="<?php echo $APPTAG?>-phone" class="form-control field-phone" />
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane" id="tab-account-info" role="tabpanel">
		<div class="row">
			<div class="col-sm-3">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_AGENCY'); ?></label>
					<input type="text" name="agency" id="<?php echo $APPTAG?>-agency" class="form-control length-fixed" data-length="4" maxlength="4" />
				</div>
			</div>
			<div class="col-sm-2">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_OPERATION'); ?></label>
					<input type="text" name="operation" id="<?php echo $APPTAG?>-operation" class="form-control length-fixed" data-length="3" maxlength="3" />
				</div>
			</div>
			<div class="col-sm-3">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_ACCOUNT'); ?></label>
					<input type="text" name="account" id="<?php echo $APPTAG?>-account" class="form-control length-fixed" data-length="10" maxlength="10" />
				</div>
			</div>
		</div>
	</div>
</div>
