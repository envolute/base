<?php
defined('_JEXEC') or die;

// CLIENTS
$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$clients = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-lg-4">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_LOGO'); ?></label>
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
		<div class="form-group">
			<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_START_DATE'); ?>"><?php echo JText::_('TEXT_SINCE'); ?></label>
			<input type="text" name="start_date" id="<?php echo $APPTAG?>-start_date" class="form-control field-date" data-convert="true" />
		</div>
	</div>
	<div class="col-lg-8">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
			<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
		</div>
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
			<div class="input-group">
				<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id">
					<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
					<?php
						foreach ($clients as $obj) {
							echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
				<span class="input-group-btn">
					<button type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-clients" data-backdrop="static" data-keyboard="false"></button>
					<button type="button" class="base-icon-cog btn btn-warning hasTooltip" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="clients_listReload(false)" data-toggle="modal" data-target="#modal-list-clients" data-backdrop="static" data-keyboard="false"></button>
				</span>
			</div>
		</div>
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
			<textarea rows="5" name="description" id="<?php echo $APPTAG?>-description" class="form-control"></textarea>
		</div>
	</div>
</div>
