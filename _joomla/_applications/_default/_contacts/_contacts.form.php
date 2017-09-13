<?php
defined('_JEXEC') or die;

// GROUPS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_groups') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$groups = $db->loadObjectList();

// USUÁRIOS SEM CONTATOS ASSOCIADOS
// IMPORTANTE:
// 1 - Não lista usuários do grupo 'Desenvolvedor' => '8'
// 2 - Não lista usuários que já estão associados a um 'contato'
$query = '
	SELECT DISTINCT(T1.id), T1.name, T1.username
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

$query = 'SELECT * FROM '. $db->quoteName('#__usergroups') .' WHERE '. $db->quoteName('id') .' NOT IN (1, 3)';
$db->setQuery($query);
$userGrps = $db->loadObjectList();

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-contact" data-toggle="tab" href="#<?php echo $APPTAG?>TabContact" role="tab" aria-controls="contact"><?php echo JText::_('TEXT_CONTACT_DATA'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-sm-9">
				<div class="row">
					<div class="col-lg-8">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_GROUP'); ?></label>
							<div class="input-group">
								<select name="group_id" id="<?php echo $APPTAG?>-group_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-name">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
										foreach ($groups as $obj) {
											echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
										}
									?>
								</select>
								<span class="input-group-btn">
									<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Groups" data-backdrop="static" data-keyboard="false"></button>
									<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Groups_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Groups" data-backdrop="static" data-keyboard="false"></button>
								</span>
							</div>
						</div>
					</div>
					<div class="col-lg-8">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
							<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_NICKNAME_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_NICKNAME'); ?></label>
							<input type="text" name="nickname" id="<?php echo $APPTAG?>-nickname" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-8">
						<div class="form-group">
							<label>E-mail</label>
							<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
							<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
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
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
							<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_OCCUPATION'); ?></label>
							<input type="text" name="occupation" id="<?php echo $APPTAG?>-occupation" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-4">
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
						<div class="row">
							<div class="col-lg-8">
								<div class="form-group">
									<label><?php echo JText::_('FIELD_LABEL_PARTNER'); ?></label>
									<input type="text" name="partner" id="<?php echo $APPTAG?>-partner" class="form-control upper" />
								</div>
							</div>
							<div class="col-lg-4">
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
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label>CPF</label>
							<input type="text" name="cpf" id="<?php echo $APPTAG?>-cpf" class="form-control field-cpf" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('TEXT_ONLY_NUMBERS'); ?>">RG</label>
							<input type="text" name="rg" id="<?php echo $APPTAG?>-rg" class="form-control numeric" />
						</div>
					</div>
					<div class="col-sm-6 col-lg-4">
						<div class="form-group">
							<label>RG Orgão</label>
							<input type="text" name="rg_orgao" id="<?php echo $APPTAG?>-rg_orgao" class="form-control upper" />
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
		<div id="<?php echo $APPTAG?>-msg-relations" class="alert alert-info base-icon-info-circled">
			<span class="text-live font-featured"><?php echo JText::_('TEXT_BANKS_ACCOUNTS')?></span>
			<?php echo JText::_('MSG_ADD_AFTER_SAVE')?>
		</div>
		<div id="<?php echo $APPTAG?>-buttons-relations" hidden>
			<hr />
			<div class="row">
				<div class="col-6 col-lg-3 py-1">
					<button type="button" class="btn btn-primary btn-block base-icon-bank btn-icon" onclick="<?php echo $APPTAG?>_viewBanks()" data-toggle="modal" data-target="#modal-list-_banksAccountsContacts" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_BANKS_ACCOUNTS')?></button>
				</div>
			</div>
		</div>
		<hr class="hr-tag" />
		<span class="badge badge-primary"><?php echo JText::_('TEXT_ACCESS_STATUS'); ?></span>
		<div class="row pt-2">
			<div class="col-lg-9">
				<div class="row">
					<div class="col-lg-5">
						<div class="form-group">
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-active-danger">
									<input type="radio" name="access" id="<?php echo $APPTAG?>-access-0" value="0" onchange="<?php echo $APPTAG?>_accessForm(0)" class="auto-tab" data-target="#<?php echo $APPTAG?>-reasonStatus" />
									<span class="<?php echo $APPTAG?>-no-user"><?php echo JText::_('TEXT_NO_ACCESS'); ?></span>
									<span class="<?php echo $APPTAG?>-is-user" hidden><?php echo JText::_('TEXT_BLOCKED'); ?></span>
								</label>
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="access" id="<?php echo $APPTAG?>-access-1" value="1" onchange="<?php echo $APPTAG?>_accessForm(1)" />
									<span class="<?php echo $APPTAG?>-no-user"><?php echo JText::_('TEXT_ALLOW_ACCESS'); ?></span>
									<span class="<?php echo $APPTAG?>-is-user" hidden><?php echo JText::_('TEXT_ACTIVE'); ?></span>
								</label>
							</span>
							<input type="hidden" name="user_id" id="<?php echo $APPTAG?>-user_id" />
						</div>
					</div>
					<div class="col-lg-7">
						<div id="<?php echo $APPTAG?>-reasonStatus-group" class="<?php echo $APPTAG?>-is-user" hidden>
							<input type="text" name="reasonStatus" id="<?php echo $APPTAG?>-reasonStatus" class="form-control" maxlength="50" placeholder="<?php echo JText::_('FIELD_LABEL_REASON'); ?>" />
						</div>
						<div class="form-group no-margin <?php echo $APPTAG?>-user-fields" hidden>
							<div class="input-group">
								<select name="newUser" id="<?php echo $APPTAG?>-newUser" class="form-control auto-tab" data-target="<?php echo $APPTAG?>newUser-group" data-tab-disabled="true">
									<option value="0" data-target-display="true"><?php echo JText::_('TEXT_NEW').' '.JText::_('TEXT_USER'); ?></option>
									<?php
										foreach ($users as $obj) {
											$uGrps = JFactory::getUser($obj->id)->groups;
											// quando selecionado um usuário já exitente, esconde as opções 'username, usergroup e password'
											echo '<option value="'.$obj->id.'" data-target-display="false">'.baseHelper::nameFormat($obj->name).'</option>';
										}
									?>
								</select>
								<span class="input-group-addon hasPopover" title="<?php echo JText::_('FIELD_LABEL_USER_ACCESS'); ?>" data-content="<?php echo JText::_('FIELD_LABEL_USER_ACCESS_DESC'); ?>" data-placement="top">
									<span class="base-icon-info-circled"></span>
								</span>
							</div>
						</div>
					</div>
					<div id="<?php echo $APPTAG?>-unlink-msg" class="col-12" hidden>
						<div class="alert alert-info">
							<span class="text-lg">
								<?php echo JText::_('MSG_IS_USER_1'); ?>
								&quot;<span id="<?php echo $APPTAG?>_name_linked"></span>&quot;
							</span>
							<?php echo JText::_('MSG_IS_USER_2'); ?>
							<hr class="my-2 border-white" />
							<button type="button" class="btn btn-danger base-icon-cancel hasTooltip" title="<?php echo JText::_('FIELD_LABEL_UNLINK_USER_DESC'); ?>" onclick="<?php echo $APPTAG?>_userUnlink()"> <?php echo JText::_('FIELD_LABEL_UNLINK_USER'); ?></button>
						</div>
					</div>
					<div class="col-12 <?php echo $APPTAG?>-user-fields" hidden>
						<div id="<?php echo $APPTAG?>newUser-group" class="row">
							<div class="col-sm-5">
								<div class="form-group">
									<label><?php echo JText::_('FIELD_LABEL_USERNAME'); ?></label>
									<div class="input-group">
										<input type="text" name="username" id="<?php echo $APPTAG?>-username" class="form-control field-username lower" />
										<span class="input-group-addon hasPopover" data-content="<?php echo JText::_('MSG_USERNAME_INFO'); ?>" data-placement="top">
											<span class="base-icon-info-circled"></span>
										</span>
									</div>
								</div>
							</div>
							<div class="col-sm-7">
								<div class="form-group">
									<label><?php echo JText::_('FIELD_LABEL_USERGROUP'); ?></label>
									<select name="usergroups[]" id="<?php echo $APPTAG?>-usergroups" class="form-control" multiple>
										<?php
											foreach ($userGrps as $obj) {
												echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->title).'</option>';
											}
										?>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
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
							<div class="col-sm-5">
								<div class="form-group">
									<label class="iconTip hasTooltip" title="<?php echo JText::_('MSG_REPASSWORD'); ?>"><?php echo JText::_('FIELD_LABEL_REPASSWORD'); ?></label>
									<input type="password" name="repassword" id="<?php echo $APPTAG?>-repassword" class="form-control" />
								</div>
							</div>
							<div class="col-12">
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
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabContact" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-contact">
		<div class="row">
			<div class="col-md-8 col-lg-9">
				<div class="row">
					<div class="col-lg-3">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE'); ?></label>
							<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" class="form-control field-cep" />
						</div>
					</div>
					<div class="col-lg-7">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STREET'); ?></label>
							<input type="text" name="address" id="<?php echo $APPTAG?>-address" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-2">
						<div class="form-group">
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
					<div class="col-lg-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
							<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
							<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
						</div>
					</div>
					<div class="col-lg-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STATE'); ?></label>
							<input type="text" name="address_state" id="<?php echo $APPTAG?>-address_state" class="form-control upper" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-4 col-lg-3 b-left b-dashed">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_CELLPHONE'); ?> 1</label>
					<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone0" class="form-control field-phone" />
				</div>
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_CELLPHONE'); ?> 2</label>
					<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone1" class="form-control field-phone" />
				</div>
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_PHONE_FIXED'); ?></label>
					<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone2" class="form-control field-phone" />
				</div>
			</div>
		</div>
	</div>
</div>
