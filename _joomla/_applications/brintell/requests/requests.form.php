<?php
defined('_JEXEC') or die;

// CLIENTS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_projects') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$projects = $db->loadObjectList();

// TAGS
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_tags') .' WHERE '. $db->quoteName('state') .' = 1 ORDER BY name';
$db->setQuery($query);
$tags = $db->loadObjectList();

// CREATED BY
$author = '';
if($hasAuthor) :
	$query	= '
		SELECT
			T1.*
		FROM '. $db->quoteName('#__'.$cfg['project'].'_teams') .' T1
		WHERE T1.user_id = '.$user->id
	;
	$db->setQuery($query);
	$obj = $db->loadObject();
	if(!empty($obj->name)) : // verifica se existe
		$name = baseHelper::nameFormat((!empty($obj->nickname) ? $obj->nickname : $obj->name));

		// Imagem Principal -> Primeira imagem (index = 0)
		JLoader::register('uploader', JPATH_CORE.DS.'helpers/files/upload.php');
		$img = uploader::getFile('#__brintell_teams_files', '', $obj->id, 0, JPATH_BASE.DS.'images/apps/teams/');
		if(!empty($img)) $imgPath = baseHelper::thumbnail('images/apps/teams/'.$img['filename'], 45, 45);
		else $imgPath = JURI::root().'images/apps/icons/user_'.$obj->gender.'.png';
		$img = '<img src="'.$imgPath.'" class="img-fluid rounded float-left mr-2 mb-2" style="width:45px; height:45px;" />';

		$author = '
			<div class="mb-3 b-bottom b-primary-lighter clearfix">
				'.$img.'
				<h5 class="font-condensed">'.$name.'</h5>
			</div>
		';
	endif;
endif;

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link base-icon-attach" id="<?php echo $APPTAG?>Tab-attachments" data-toggle="tab" href="#<?php echo $APPTAG?>TabAttachments" role="tab" aria-controls="attachments"> <?php echo JText::_('TEXT_ATTACHMENTS'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-lg-8">
				<?php echo $author?>
				<div class="row">
					<div class="col-lg-6">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label>
							<span class="btn-group btn-group-justified" data-toggle="buttons">
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="type" id="<?php echo $APPTAG?>-type-0" value="0" onchange="<?php echo $APPTAG?>_setType(this.value)" />
									<?php echo JText::_('TEXT_TYPE_0'); ?>
								</label>
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="type" id="<?php echo $APPTAG?>-type-1" value="1" onchange="<?php echo $APPTAG?>_setType(this.value)" />
									<?php echo JText::_('TEXT_TYPE_1'); ?>
								</label>
								<label class="btn btn-default btn-active-success">
									<input type="radio" name="type" id="<?php echo $APPTAG?>-type-2" value="2" onchange="<?php echo $APPTAG?>_setType(this.value)" />
									<?php echo JText::_('TEXT_TYPE_2'); ?>
								</label>
							</span>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="form-group field-required">
							<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PROJECT'); ?></label>
							<select name="project_id" id="<?php echo $APPTAG?>-project_id" class="form-control field-id">
								<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
								<?php
									foreach ($projects as $obj) {
										echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
									}
								?>
							</select>
						</div>
					</div>
					<div class="col-12">
						<div class="alert alert-info <?php echo $APPTAG?>-groupType-1" hidden>Mostra os campos para Componentes</div>
						<div class="alert alert-warning <?php echo $APPTAG?>-groupType-2" hidden>Mostra os campos para Protótipos</div>
					</div>
				</div>
				<div class="form-group field-required">
					<label id="<?php echo $APPTAG?>-subject-label" class="label-sm"><?php echo JText::_('FIELD_LABEL_SUBJECT'); ?></label>
					<label id="<?php echo $APPTAG?>-component-label" class="label-sm" hidden><?php echo JText::_('FIELD_LABEL_COMPONENT_NAME'); ?></label>
					<input type="text" name="subject" id="<?php echo $APPTAG?>-subject" class="form-control" />
				</div>
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
					<textarea rows="8" name="description" id="<?php echo $APPTAG?>-description" class="form-control"></textarea>
				</div>
				<?php if($hasAuthor) :?>
					<input type="hidden" name="status" id="<?php echo $APPTAG?>-status" />
					<div class="form-group mb-1" hidden>
						<span class="btn-group" data-toggle="buttons">
							<label class="base-icon-danger btn btn-outline-danger btn-active-danger hasTooltip" title="<?php echo JText::_('FIELD_LABEL_MARK_AS_CLOSED_DESC')?>">
								<input type="checkbox" name="setClose" id="<?php echo $APPTAG?>-setClose" value="1" />
								<?php echo JText::_('FIELD_LABEL_MARK_AS_CLOSED')?>
							</label>
						</span>
					</div>
				<?php else :?>
					<div class="form-group mb-1">
						<label class="label-sm"><?php echo JText::_('FIELD_LABEL_STATUS'); ?></label>
						<span class="btn-group btn-group-justified" data-toggle="buttons">
							<?php
							for($i = 0; $i < 4; $i++) {
								$icon	= JText::_('TEXT_ICON_STATUS_'.$i);
								$color	= ($i == 0) ? 'warning' : JText::_('TEXT_COLOR_STATUS_'.$i);
								echo '
									<label class="base-icon-'.$icon.' btn btn-outline-'.$color.' btn-active-'.$color.' hasTooltip" title="'.JText::_('TEXT_STATUS_'.$i).'">
										<input type="radio" name="status" id="'.$APPTAG.'-status-'.$i.'" value="'.$i.'" />
									</label>
								';
							}
							?>
						</span>
					</div>
				<?php endif;?>
			</div>
			<div class="col-lg-4 b-left b-left-dashed">
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_PRIORITY'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-0" value="0" />
							<?php echo JText::_('TEXT_PRIORITY_0'); ?>
						</label>
						<label class="btn btn-default btn-active-warning">
							<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-1" value="1" />
							<?php echo JText::_('TEXT_PRIORITY_1'); ?>
						</label>
						<label class="btn btn-default btn-active-danger">
							<input type="radio" name="priority" id="<?php echo $APPTAG?>-priority-2" value="2" />
							<?php echo JText::_('TEXT_PRIORITY_2'); ?>
						</label>
					</span>
				</div>
				<div class="row">
					<div class="col-xl-6">
						<div class="form-group">
							<label class="label-sm iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DEADLINE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DEADLINE'); ?></label>
							<input type="text" name="deadline" id="<?php echo $APPTAG?>-deadline" class="form-control field-date" data-convert="true" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="label-sm"><?php echo JText::_('FIELD_LABEL_TAGS'); ?></label>
					<div class="input-group">
						<select name="tags[]" id="<?php echo $APPTAG?>-tags" class="form-control" multiple>
							<?php
								foreach ($tags as $obj) {
									echo '<option value="'.$obj->name.'">'.baseHelper::nameFormat($obj->name).'</option>';
								}
							?>
						</select>
						<span class="input-group-btn">
							<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Tags" data-backdrop="static" data-keyboard="false"></button>
						</span>
					</div>
				</div>
				<div class="form-group">
					<div id="<?php echo $APPTAG?>-alert-toDo">
						<label class="label-sm"><?php echo JText::_('TEXT_TODO_LIST'); ?></label>
						<div class="alert alert-info text-sm p-2"><?php echo JText::_('MSG_TODO_LIST_AFTER_SAVE'); ?></div>
					</div>
					<div id="<?php echo $APPTAG?>-btn-toDo" hidden>
						<hr />
						<button type="button" class="btn btn-primary btn-block text base-icon-list btn-icon" onclick="<?php echo $APPTAG?>_viewToDo()" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Todo" data-backdrop="static" data-keyboard="false"> <?php echo JText::_('TEXT_TODO_LIST')?></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabAttachments" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-attachments">
		<div class="form-group">
			<label class="label-sm"><?php echo JText::_('FIELD_LABEL_FILE'); ?></label>
			<div class="btn-file">
				<span class="btn-group">
					<button type="button" class="base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
				</span>
				<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control" hidden />
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-files-group" class="row"></div>
		<hr class="my-1" />
		<button type="button" class="base-icon-plus btn btn-sm btn-success btn-icon" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-files-group', 'file', 'col-md-6 col-lg-3')">
			<?php echo JText::_('TEXT_ADD'); ?>
		</button>
	</div>
</div>
