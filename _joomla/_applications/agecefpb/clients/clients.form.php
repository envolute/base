<?php
defined('_JEXEC') or die;

// GRUPOS DE CLIENTES/ASSOCIADOS
$query = 'SELECT * FROM '. $db->quoteName('#__usergroups') .' WHERE '. $db->quoteName('parent_id') .' = 10 ORDER BY id';
$db->setQuery($query);
$userGrps = $db->loadObjectList();

// USUÁRIOS SEM CLIENTES ASSOCIADOS
// IMPORTANTE:
// 1 - Não lista usuários do grupo 'Desenvolvedor' => '8'
// 2 - Não lista usuários que já estão associados a um 'client'
$query = '
	SELECT DISTINCT(T1.id), T1.name, T1.email
	FROM '. $db->quoteName('#__users') .' T1
		JOIN '. $db->quoteName('#__user_usergroup_map') .' T2
		ON T2.user_id = T1.id
		LEFT OUTER JOIN '. $db->quoteName($cfg['mainTable']) .' T3
		ON T3.user_id = T1.id
	WHERE T2.group_id <> 8 AND T3.name IS NULL
	ORDER BY T1.name
';
$db->setQuery($query);
$users = $db->loadObjectList();

// FORM
?>

<div class="row">
	<div class="col-lg-9 b-right b-dashed">
		<div class="row">
			<div class="col-sm-8">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<div class="input-group">
						<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
						<span class="btn-group input-group-btn" data-toggle="buttons">
							<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('MSG_NAME_CARD'); ?>">
								<input type="checkbox" name="toggleName" id="<?php echo $APPTAG?>-toggleName" value="1" class="no-validate auto-tab" data-target="<?php echo $APPTAG?>-group-nameCard" data-target-display="true" />
								<span class="base-icon-plus icon-default"></span>
							</label>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-group-nameCard" class="form-group" hidden>
					<label class="iconTip hasTooltip" title="<?php echo JText::_('MSG_NAME_CARD'); ?>"><?php echo JText::_('FIELD_LABEL_NAME_CARD'); ?></label><br />
					<input type="text" name="name_card" id="<?php echo $APPTAG?>-name_card" class="form-control upper" maxlength="30" />
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('TEXT_USER_TYPE'); ?></label>
					<select name="usergroup" id="<?php echo $APPTAG?>-usergroup" class="form-control field-id" onchange="<?php echo $APPTAG?>_setType(this.value)">
						<?php
							foreach ($userGrps as $obj) {
								echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->title).'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-12">
				<fieldset id="<?php echo $APPTAG?>-group-caixa" class="fieldset-embed pt-3 px-3 pb-0">
					<div class="row">
						<div class="col-md-8 <?php echo $APPTAG?>-group-only-effective">
							<div class="form-group">
								<label>E-mail Caixa</label>
								<div class="input-group">
									<input type="text" name="cx_email" id="<?php echo $APPTAG?>-cx_email" class="form-control" />
									<span class="input-group-addon">@caixa.gov.br</span>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-md-4">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_CODE'); ?></label>
								<input type="text" name="cx_code" id="<?php echo $APPTAG?>-cx_code" class="form-control upper" />
							</div>
						</div>
						<div class="col-sm-6 col-md-4">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_ADMISSION_DATE'); ?></label>
								<input type="text" name="cx_date" id="<?php echo $APPTAG?>-cx_date" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<div class="col-sm-6 col-md-4">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_ROLE'); ?></label>
								<input type="text" name="cx_role" id="<?php echo $APPTAG?>-cx_role" class="form-control upper" />
							</div>
						</div>
						<div class="col-sm-6 col-md-4">
							<div class="form-group <?php echo $APPTAG?>-group-only-effective">
								<label class="field-required iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_SITUATED_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_SITUATED'); ?></label>
								<input type="text" name="cx_situated" id="<?php echo $APPTAG?>-cx_situated" class="form-control upper" />
							</div>
						</div>
					</div>
				</fieldset>
			</div>
			<div class="col-sm-8">
				<div class="form-group field-required">
					<label>E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
					<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
					<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
				</div>
			</div>
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
			<div class="col-sm-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" class="auto-tab" data-target="<?php echo $APPTAG?>-marital_status" value="1" />
							<?php echo JText::_('TEXT_GENDER_1_ABBR'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" class="auto-tab" data-target="<?php echo $APPTAG?>-marital_status" value="2" />
							<?php echo JText::_('TEXT_GENDER_2_ABBR'); ?>
						</label>
					</span>
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
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_MARITAL_STATUS'); ?></label>
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
			<div id="<?php echo $APPTAG?>-group-partner" class="col-12" hidden>
				<div class="form-group">
					<label class="field-required"><?php echo JText::_('FIELD_LABEL_PARTNER'); ?></label>
					<input type="text" name="partner" id="<?php echo $APPTAG?>-partner" class="form-control upper" />
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-3">
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
		<div id="<?php echo $APPTAG?>-group-btnPrint" class="form-group" hidden>
			<label class="d-block text-center pt-1"><?php echo JText::_('TEXT_CLIENT_CARD'); ?></label>
			<button type="button" class="btn btn-lg btn-block btn-success base-icon-print btn-icon" onclick="<?php echo $APPTAG?>_printCard()"> <?php echo JText::_('TEXT_PRINT'); ?></button>
		</div>
	</div>
</div>
<hr class="hr-tag" />
<span class="badge badge-primary"><?php echo JText::_('TEXT_CONTACT_DATA'); ?></span>
<div class="row">
	<div class="col-md-7 col-lg-9">
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
	<div class="col-md-5 col-lg-3 b-left b-dashed">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_CELLPHONE'); ?> 1</label>
			<div class="input-group">
				<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone0" class="form-control field-phone" />
				<span class="input-group-btn btn-group" data-toggle="buttons">
					<label class="btn btn-outline-success btn-active-success base-icon-whatsapp">
						<input type="checkbox" id="<?php echo $APPTAG?>-wcheck0" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-whatsapp0" data-target-value="1" data-target-value-reset="" />
						<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp0" />
					</label>
				</span>
			</div>
		</div>
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_CELLPHONE'); ?> 2</label>
			<div class="input-group">
				<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone1" class="form-control field-phone" />
				<span class="input-group-btn btn-group" data-toggle="buttons">
					<label class="btn btn-outline-success btn-active-success base-icon-whatsapp">
						<input type="checkbox" id="<?php echo $APPTAG?>-wcheck1" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-whatsapp1" data-target-value="1" data-target-value-reset="" />
						<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp1" />
					</label>
				</span>
			</div>
		</div>
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_PHONE_FIXED'); ?></label>
			<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone2" class="form-control field-phone" />
			<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp2" />
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-9">
		<hr class="hr-tag" />
		<span class="badge badge-primary"><?php echo JText::_('TEXT_ACCOUNT_DATA'); ?></span>
		<div class="row">
			<div class="col-sm-3 col-lg-2">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_AGENCY'); ?></label>
					<input type="text" name="agency" id="<?php echo $APPTAG?>-agency" class="form-control numeric length-fixed" data-length="4" maxlength="4" />
				</div>
			</div>
			<div class="col-sm-2">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_OPERATION'); ?></label>
					<input type="text" name="operation" id="<?php echo $APPTAG?>-operation" class="form-control numeric length-fixed" data-length="3" maxlength="3" />
				</div>
			</div>
			<div class="col-sm-4 col-lg-3">
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('TEXT_ONLY_NUMBERS'); ?>"><?php echo JText::_('FIELD_LABEL_ACCOUNT'); ?></label>
					<input type="text" name="account" id="<?php echo $APPTAG?>-account" class="form-control numeric length-fixed" data-length="10" maxlength="10" />
				</div>
			</div>
		</div>
	</div>
	<div class="col-sm-9 col-lg-3">
		<hr class="hr-tag" />
		<span class="badge badge-primary"><?php echo JText::_('TEXT_CLIENT_CREDIT_CARD'); ?></span>
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_CARD_LIMIT'); ?></label>
			<div class="input-group">
				<span class="input-group-addon">R$</span>
				<input type="text" name="card_limit" id="<?php echo $APPTAG?>-card_limit" class="form-control field-price" data-convert="true" />
				<span class="input-group-addon base-icon-info-circled cursor-help hasPopover" data-placement="top" data-content="<?php echo JText::_('MSG_MEMBERSHIP_BENEFITS_DESC')?>"></span>
			</div>
		</div>
	</div>
</div>
<hr />
<fieldset class="fieldset-embed fieldset-sm pb-0">
	<legend>
		<span class="<?php echo $APPTAG?>-no-user"><?php echo JText::_('TEXT_DATA_REGISTRATION'); ?></span>
		<span class="<?php echo $APPTAG?>-is-user" hidden><?php echo JText::_('TEXT_ACCESS_STATUS'); ?></span>
	</legend>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group">
				<span class="btn-group btn-group-justified" data-toggle="buttons">
					<label class="btn btn-default btn-active-danger">
						<input type="radio" name="access" id="<?php echo $APPTAG?>-access-0" value="0" onchange="<?php echo $APPTAG?>_accessForm(0)" class="auto-tab" data-target="#<?php echo $APPTAG?>-reasonStatus" />
						<span class="<?php echo $APPTAG?>-no-user"><?php echo JText::_('TEXT_PENDING'); ?></span>
						<span class="<?php echo $APPTAG?>-is-user" hidden><?php echo JText::_('TEXT_BLOCKED'); ?></span>
					</label>
					<label class="btn btn-default btn-active-success">
						<input type="radio" name="access" id="<?php echo $APPTAG?>-access-1" value="1" onchange="<?php echo $APPTAG?>_accessForm(1)" />
						<span class="<?php echo $APPTAG?>-no-user"><?php echo JText::_('TEXT_APPROVED'); ?></span>
						<span class="<?php echo $APPTAG?>-is-user" hidden><?php echo JText::_('TEXT_ACTIVE'); ?></span>
					</label>
				</span>
				<input type="hidden" name="user_id" id="<?php echo $APPTAG?>-user_id" />
				<input type="hidden" name="username" id="<?php echo $APPTAG?>-username" />
				<input type="hidden" name="cusername" id="<?php echo $APPTAG?>-cusername" />
			</div>
		</div>
		<div class="col-md-6">
			<div id="<?php echo $APPTAG?>-reasonStatus-group" class="collapse <?php echo $APPTAG?>-is-user">
				<input type="text" name="reasonStatus" id="<?php echo $APPTAG?>-reasonStatus" class="form-control mb-3" maxlength="50" placeholder="<?php echo JText::_('FIELD_LABEL_REASON'); ?>" />
			</div>
		</div>
		<div class="col-12">
			<div id="accessFields" class="collapse">
				<hr class="mt-0 new-user-data" hidden />
				<div class="row">
					<div class="col-lg-6 new-user-data" hidden>
						<div class="form-group no-margin">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_USER_ACCESS_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_USER_ACCESS'); ?></label>
							<select name="newUser" id="<?php echo $APPTAG?>-newUser" class="form-control">
								<option value="0"><?php echo JText::_('TEXT_NEW').' '.JText::_('TEXT_USER'); ?></option>
								<?php
									foreach ($users as $obj) {
										echo '<option value="'.$obj->id.'" data-name="'.$obj->name.'" data-email="'.$obj->email.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-12 edit-user-data">
						<hr class="hr-tag" />
						<span class="badge badge-primary"><?php echo JText::_('TEXT_PASSWORD_RESET'); ?></span>
					</div>
					<div class="col-sm-6 col-lg-3">
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
					<div class="col-sm-6 col-lg-3">
						<div class="form-group">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('MSG_REPASSWORD'); ?>"><?php echo JText::_('FIELD_LABEL_REPASSWORD'); ?></label>
							<input type="password" name="repassword" id="<?php echo $APPTAG?>-repassword" class="form-control" />
						</div>
					</div>
					<div class="col-12 new-user-data" hidden>
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_REGISTRATION_EMAIL_CONFIRM'); ?></label>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="checkbox" name="emailConfirm" id="<?php echo $APPTAG?>-emailConfirm" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-emailInfo" data-target-disabled="false" />
								</span>
								<input type="text" name="emailInfo" id="<?php echo $APPTAG?>-emailInfo" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_REGISTRATION_EMAIL_INFO'); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</fieldset>
