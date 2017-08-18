<?php
defined('_JEXEC') or die;

// PROVIDERS
$query = '
	SELECT * FROM '. $db->quoteName('#__'.$cfg['project'].'_providers') .'
	WHERE '. $db->quoteName('group_id') .' = 2 AND '. $db->quoteName('state') .' = 1
	ORDER BY name
';
$db->setQuery($query);
$providers = $db->loadObjectList();

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_GENERAL'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-description" data-toggle="tab" href="#<?php echo $APPTAG?>TabDesc" role="tab" aria-controls="description"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="general-tab">
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_PROVIDER'); ?></label>
			<div class="input-group">
				<select name="provider_id" id="<?php echo $APPTAG?>-provider_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-name">
					<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
					<?php
						foreach ($providers as $obj) {
							echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
						}
					?>
				</select>
				<span class="input-group-btn">
					<button type="button" class="base-icon-plus btn btn-success hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-providers" data-backdrop="static" data-keyboard="false"></button>
					<button type="button" class="base-icon-cog btn btn-primary hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="providers_listReload(false)" data-toggle="modal" data-target="#modal-list-providers" data-backdrop="static" data-keyboard="false"></button>
				</span>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-8">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
					<input type="text" name="price" id="<?php echo $APPTAG?>-price" class="form-control field-price" data-convert="true" />
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabDesc" role="tabpanel" aria-labelledby="description-tab">
		<div class="form-group">
			<textarea name="description" id="<?php echo $APPTAG?>-description" rows="4" class="form-control field-html"></textarea>
		</div>
	</div>
</div>
