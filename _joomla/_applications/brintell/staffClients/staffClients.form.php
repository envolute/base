<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_clients') .' WHERE state = 1 ORDER BY name';
$db->setQuery($query);
$clients = $db->loadObjectList();

$query = 'SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_staff') .' WHERE type = 2 AND state = 1 ORDER BY name';
$db->setQuery($query);
$staff = $db->loadObjectList();

// FORM
?>
<div class="row">
	<div class="col-sm-10">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_STAFF_MEMBER'); ?></label>
			<select name="staff_id" id="<?php echo $APPTAG?>-staff_id" class="form-control field-id">
				<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
				<?php
					foreach ($staff as $obj) {
						echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
					}
				?>
			</select>
		</div>
	</div>
	<div class="col-sm-10">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_CLIENT'); ?></label>
			<select name="client_id" id="<?php echo $APPTAG?>-client_id" class="form-control field-id">
				<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
				<?php
					foreach ($clients as $obj) {
						echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
					}
				?>
			</select>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_DEPARTMENT'); ?></label>
			<input type="text" name="department" id="<?php echo $APPTAG?>-department" class="form-control upper" />
		</div>
	</div>
	<div class="col-sm-4">
		<div class="form-group">
			<label>&#160;</label>
			<div class="btn-group w-100" data-toggle="buttons">
				<label class="btn btn-block btn-warning btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_MAIN_DESC'); ?>">
					<span class="base-icon-cancel"></span>
					<input type="checkbox" name="main" id="<?php echo $APPTAG?>-main" value="1" />
					<?php echo JText::_('FIELD_LABEL_MAIN'); ?>
				</label>
			</div>
		</div>
	</div>
</div>
