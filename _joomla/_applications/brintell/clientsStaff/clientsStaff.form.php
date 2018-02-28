<?php
defined('_JEXEC') or die;

// CLIENTS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$clients = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-sm-9">
		<div class="row">
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
					<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-name">
						<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
						<?php
							foreach ($clients as $obj) {
								echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
							}
						?>
					</select>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group field-required">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" value="1" />
							<?php echo JText::_('TEXT_GENDER_1'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" value="2" />
							<?php echo JText::_('TEXT_GENDER_2'); ?>
						</label>
					</span>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label class="label-sm">E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
					<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_ROLE'); ?></label>
					<input type="text" name="role" id="<?php echo $APPTAG?>-role" class="form-control upper" />
				</div>
			</div>
		</div>
		<hr class="hr-tag" />
		<span class="badge badge-primary"><?php echo JText::_('TEXT_ACCESS_STATUS'); ?></span>
		<div id="<?php echo $APPTAG?>-access-group" class="row" hidden>
			<div class="col-md-6">
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
			<div class="col-md-6">
				<div id="<?php echo $APPTAG?>-reasonStatus-group" class="collapse <?php echo $APPTAG?>-is-user">
					<input type="text" name="reasonStatus" id="<?php echo $APPTAG?>-reasonStatus" class="form-control mb-3" maxlength="50" placeholder="<?php echo JText::_('FIELD_LABEL_REASON'); ?>" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<div id="<?php echo $APPTAG?>-accessFields" class="collapse">
					<hr class="mt-0" hidden />
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group field-required">
								<label class="label-sm iconTip hasPopover" title="<?php echo JText::_('TEXT_USER_ACCESS_LEVEL')?>" data-content="<?php echo JText::_('MSG_USER_ACCESS_LEVEL')?>"><?php echo JText::_('FIELD_LABEL_USERGROUP'); ?></label>
								<span class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="usergroup" id="<?php echo $APPTAG?>-usergroup_16" value="16" />
										<?php echo JText::_('TEXT_CLIENT_16'); ?>
									</label>
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="usergroup" id="<?php echo $APPTAG?>-usergroup_15" value="15" />
										<?php echo JText::_('TEXT_CLIENT_15'); ?>
									</label>
								</span>
							</div>
							<input type="hidden" name="cusergroup" id="<?php echo $APPTAG?>-cusergroup" />
						</div>
						<div class="col-sm-6 col-lg new-user" hidden>
							<div class="form-group field-required">
								<label class="label-sm"><?php echo JText::_('FIELD_LABEL_USERNAME'); ?></label>
								<div class="input-group">
									<input type="text" name="username" id="<?php echo $APPTAG?>-username" class="form-control field-username lower" />
									<span class="input-group-addon hasPopover" data-content="<?php echo JText::_('MSG_USERNAME_INFO'); ?>" data-placement="top">
										<span class="base-icon-info-circled"></span>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
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
						<div class="col-lg-6">
							<div class="form-group">
								<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('MSG_REPASSWORD'); ?>"><?php echo JText::_('FIELD_LABEL_REPASSWORD'); ?></label>
								<input type="password" name="repassword" id="<?php echo $APPTAG?>-repassword" class="form-control" />
							</div>
						</div>
						<div class="col-12 new-user" hidden>
							<div class="form-group">
								<label class="label-sm"><?php echo JText::_('FIELD_LABEL_REGISTRATION_EMAIL_CONFIRM'); ?></label>
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
	<div class="col-sm-3">
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
	</div>
</div>
