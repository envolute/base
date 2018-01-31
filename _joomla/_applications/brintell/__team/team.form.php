<?php
defined('_JEXEC') or die;

// GRUPOS DE CLIENTES/ASSOCIADOS
$query = 'SELECT * FROM '. $db->quoteName('#__usergroups') .' WHERE '. $db->quoteName('id') .' > 10 ORDER BY id';
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
	<div class="col-lg-9 b-right b-right-dashed">
		<div class="row">
			<div class="col-lg-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_GROUP'); ?></label>
					<select name="usergroup" id="<?php echo $APPTAG?>-usergroup" class="form-control field-id">
						<?php
							foreach ($userGrps as $obj) {
								echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->title).'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_ROLE'); ?></label>
					<input type="text" name="role" id="<?php echo $APPTAG?>-role" class="form-control upper" />
				</div>
			</div>
			<div class="col-lg-4">
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
		</div>
		<hr />
		<div class="row">
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
				<div class="form-group field-required">
					<label>E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
					<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
					<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
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
	</div>
</div>
<hr class="hr-tag" />
<span class="badge badge-primary"><?php echo JText::_('TEXT_CONTACT_DATA'); ?></span>
<div class="row">
	<div class="col-lg-9 b-right b-right-dashed">
		<div class="form-group">
			<button type="button" class="btn btn-xs btn-success base-icon-plus float-right" onclick="<?php echo $APPTAG?>_phoneAdd()"> <?php echo JText::_('TEXT_PHONES_ADD')?></button>
			<label><?php echo JText::_('FIELD_LABEL_PHONE'); ?></label>
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
<hr />
<fieldset class="fieldset-embed fieldset-sm pb-0">
	<legend>
		<span class="<?php echo $APPTAG?>-no-user"><?php echo JText::_('TEXT_DATA_REGISTRATION'); ?></span>
		<span class="<?php echo $APPTAG?>-is-user" hidden><?php echo JText::_('TEXT_ACCESS_STATUS'); ?></span>
	</legend>
	<div class="row">
		<div class="col-md-6 <?php echo $APPTAG?>-is-user">
			<div class="form-group">
				<span class="btn-group btn-group-justified" data-toggle="buttons">
					<label class="btn btn-default btn-active-danger">
						<input type="radio" name="access" id="<?php echo $APPTAG?>-access-0" value="0" onchange="<?php echo $APPTAG?>_accessForm(0)" class="auto-tab" data-target="#<?php echo $APPTAG?>-reasonStatus" />
						<?php echo JText::_('TEXT_BLOCKED'); ?>
					</label>
					<label class="btn btn-default btn-active-success">
						<input type="radio" name="access" id="<?php echo $APPTAG?>-access-1" value="1" onchange="<?php echo $APPTAG?>_accessForm(1)" />
						<?php echo JText::_('TEXT_ACTIVE'); ?>
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
