<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__envolute_projects') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$projects = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_departments') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$departments = $db->loadObjectList();
?>

<form name="form-<?php echo $APPTAG?>" id="form-<?php echo $APPTAG?>" method="post" enctype="multipart/form-data">

	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">
			<?php
				echo JText::_('FORM_TITLE');
				if($cfg['showFormDesc']) :
					echo '<div class="small font-condensed">'.JText::_('FORM_DESCRIPTION').'</div>';
				endif;
			?>
		</h4>
	</div>
	<div class="modal-body">
		<fieldset>
			<div class="row">
				<div id="<?php echo $APPTAG?>-formPaginator" class="col-sm-4 hide">
					<div class="form-group field-required">
						<span class="input-group">
							<span class="input-group-btn">
								<button id="btn-<?php echo $APPTAG?>-prev" class="base-icon-left-open btn btn-sm btn-default" disabled></button>
							</span>
							<input type="text" name="id" id="<?php echo $APPTAG?>-id" class="form-control input-sm" readonly="readonly" />
							<input type="hidden" name="relationId" id="<?php echo $APPTAG?>-relationId" value="<?php echo $_SESSION[$RTAG.'RelId']?>" />
							<input type="hidden" name="<?php echo $APPTAG?>-prev" id="<?php echo $APPTAG?>-prev" />
							<input type="hidden" name="<?php echo $APPTAG?>-next" id="<?php echo $APPTAG?>-next" />
							<span class="input-group-btn">
								<button id="btn-<?php echo $APPTAG?>-next" class="base-icon-right-open btn btn-sm btn-default" disabled></button>
							</span>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-fieldState" class="col-sm-4">
					<div class="form-group">
						<span id="<?php echo $APPTAG?>-state-group" class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-sm btn-default btn-active-success strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-1" value="1" />
								<?php echo JText::_('TEXT_ACTIVE'); ?>
							</label>
							<label class="btn btn-sm btn-default btn-active-danger strong">
								<span class="base-icon-unset"></span>
								<input type="radio" name="state" id="<?php echo $APPTAG?>-state-0" value="0" /> <?php echo JText::_('TEXT_INACTIVE'); ?>
							</label>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-restart" class="col-sm-4 hide">
					<div class="form-group">
						<button type="button" id="btn-<?php echo $APPTAG?>-restart" class="base-icon-cw btn btn-sm btn-default btn-block">
							 <?php echo JText::_('TEXT_RESTART'); ?>
						</button>
					</div>
				</div>
			</div>

			<hr class="hr-xs" />

			<div class="row">
				<div class="col-sm-8">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_PROJECT'); ?></label>
						<select name="project_id" id="<?php echo $APPTAG?>-project_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-department_id">
							<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
							<?php
								foreach ($projects as $obj) {
									echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
								}
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group field-required">
						<label><?php echo JText::_('FIELD_LABEL_DEPARTMENT'); ?></label>
						<div class="input-group">
							<select name="department_id" id="<?php echo $APPTAG?>-department_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-subject">
								<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
								<?php
									foreach ($departments as $obj) {
										echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
							<span class="input-group-btn">
								<button type="button" class="base-icon-plus btn-add btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Departments" data-backdrop="static" data-keyboard="false"></button>
								<button type="button" class="base-icon-cog btn btn-primary hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Departments_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Departments" data-backdrop="static" data-keyboard="false"></button>
							</span>
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label><br />
						<span class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="base-icon-info-circled text-info btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_INFO_DESC'); ?>">
								<input type="radio" name="type" id="<?php echo $APPTAG?>-type-0" value="0" class="auto-tab" data-target="status_desc-group" />
							</label>
							<label class="base-icon-lifebuoy text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('FIELD_LABEL_HELP_DESC'); ?>">
								<input type="radio" name="type" id="<?php echo $APPTAG?>-type-1" value="1" class="auto-tab" data-target="status_desc-group" />
							</label>
							<label class="base-icon-bug text-danger btn btn-default btn-active-danger hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ISSUE_DESC'); ?>">
								<input type="radio" name="type" id="<?php echo $APPTAG?>-type-2" value="2" class="auto-tab" data-target="status_desc-group" />
							</label>
							<label class="base-icon-star text-live btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_REQUEST_DESC'); ?>">
								<input type="radio" name="type" id="<?php echo $APPTAG?>-type-3" value="3" class="auto-tab" data-target="status_desc-group" />
							</label>
						</span>
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_SUBJECT'); ?></label>
						<input name="subject" id="<?php echo $APPTAG?>-subject" class="form-control" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_PRIORITY'); ?></label>
						<span class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="base-icon-attention text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_LOW'); ?>">
								<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-0" value="0" />
							</label>
							<label class="base-icon-attention text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('FIELD_LABEL_MEDIUM'); ?>">
								<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-1" value="1" />
							</label>
							<label class="base-icon-attention text-danger btn btn-default btn-active-danger hasTooltip" title="<?php echo JText::_('FIELD_LABEL_HIGHT'); ?>">
								<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-2" value="2" />
							</label>
						</span>
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
						<textarea rows="8" name="description" id="<?php echo $APPTAG?>-description" class="form-control"></textarea>
					</div>
				</div>
				<div class="col-sm-4">
					<div id="<?php echo $APPTAG?>-status-group" class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_STATUS'); ?></label>
						<span class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default btn-active-success">
								<input type="radio" name="status" id="<?php echo $APPTAG?>-status-0" value="0" />
								<?php echo JText::_('FIELD_LABEL_OPENED'); ?>
							</label>
							<label class="btn btn-default btn-active-success">
								<input type="radio" name="status" id="<?php echo $APPTAG?>-status-1" value="1" />
								<?php echo JText::_('FIELD_LABEL_CLOSED'); ?>
							</label>
						</span>
					</div>
					<div id="<?php echo $APPTAG?>-emailAlert-group" class="form-group">
						<div class="hidden-xs"><label>&#160;</label></div>
						<div class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-warning btn-active-success">
								<span class="base-icon-cancel"></span>
								<input type="checkbox" name="emailAlert" id="<?php echo $APPTAG?>-emailAlert" value="1" />
								<?php echo JText::_('FIELD_LABEL_MAIL_ALERT'); ?>
							</label>
						</div>
					</div>
				</div>
			</div>
			<hr class="hr-label" />
			<span class="label label-warning"><?php echo JText::_('TEXT_ATTACHMENT')?>s</span>
			<div class="row">
				<div class="col-sm-8">
					<div class="form-group">
						<span class="btn-group">
							<button type="button" class="base-icon-search btn btn-default set-file-action"> <?php echo JText::_('TEXT_FILE_SELECT'); ?></button>
						</span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control element-invisible" />
					</div>
				</div>
				<div class="col-sm-4">
					<button type="button" class="base-icon-plus btn btn-block btn-success" onclick="<?php echo $APPTAG?>_setNewFile()">
						<?php echo JText::_('TEXT_ADD'); ?>
					</button>
				</div>
				<div id="<?php echo $APPTAG?>-files-group" class="col-sm-8"></div>
			</div>
		</fieldset>
	</div>
	<div class="modal-footer">
		<div class="pull-left bottom-space-xs text-left text-overflow">
			<span class="base-icon-ok-circled2 set-success text-success hide"></span>
			<span class="base-icon-cancel-circled set-error text-danger hide"></span>
			<span class="ajax-loader hide"></span>
		</div>
		<div class="pull-right">
			<button name="btn-<?php echo $APPTAG?>-save" id="btn-<?php echo $APPTAG?>-save" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPTAG?>_save();"> <?php echo JText::_('TEXT_SAVE'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-save-new" id="btn-<?php echo $APPTAG?>-save-new" class="base-icon-ok btn btn-success btn-sm" onclick="<?php echo $APPTAG?>_save('reset');"> <?php echo JText::_('TEXT_SAVENEW'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-delete" id="btn-<?php echo $APPTAG?>-delete" class="base-icon-trash btn btn-danger btn-sm hide" onclick="<?php echo $APPTAG?>_del(0, true)"> <?php echo JText::_('TEXT_DELETE'); ?></button>
			<button name="btn-<?php echo $APPTAG?>-cancel" id="btn-<?php echo $APPTAG?>-cancel" class="base-icon-cancel btn btn-default btn-sm" data-dismiss="modal"> <?php echo JText::_('TEXT_CANCEL'); ?></button>
		</div>
	</div>

</form>
