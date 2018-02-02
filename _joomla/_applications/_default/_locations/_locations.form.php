<?php
defined('_JEXEC') or die;

// FORM
?>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_LOCATION'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-map" data-toggle="tab" href="#<?php echo $APPTAG?>TabMap" role="tab" aria-controls="map"><?php echo JText::_('TEXT_MAP'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-extra" data-toggle="tab" href="#<?php echo $APPTAG?>TabExtra" role="tab" aria-controls="extra"><?php echo JText::_('FIELD_LABEL_EXTRA_INFO'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-lg-8">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_IS_PUBLIC_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_IS_PUBLIC'); ?></label>
							<div class="btn-group w-100" data-toggle="buttons">
								<span class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="isPublic" id="<?php echo $APPTAG?>-isPublic1" value="1" />
										<?php echo JText::_('TEXT_YES'); ?>
									</label>
									<label class="btn btn-default btn-active-danger">
										<input type="radio" name="isPublic" id="<?php echo $APPTAG?>-isPublic0" value="0" />
										<?php echo JText::_('TEXT_NO'); ?>
									</label>
								</span>
							</div>
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_TITLE'); ?></label>
							<input type="text" name="title" id="<?php echo $APPTAG?>-title" class="form-control upper" maxlength="30" placeholder="<?php echo JText::_('FIELD_LABEL_TITLE_PLACEHOLDER'); ?>" />
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_ZIP_CODE'); ?></label>
							<input type="text" name="zip_code" id="<?php echo $APPTAG?>-zip_code" class="form-control field-cep" />
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-group field-required">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STREET'); ?></label>
							<input type="text" name="address" id="<?php echo $APPTAG?>-address" class="form-control upper" />
						</div>
					</div>
					<div class="col-sm-4">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_NUMBER'); ?></label>
							<input type="text" name="address_number" id="<?php echo $APPTAG?>-address_number" class="form-control upper" />
						</div>
					</div>
					<div class="col-sm-8">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_INFO'); ?></label>
							<input type="text" name="address_info" id="<?php echo $APPTAG?>-address_info" class="form-control" />
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4 b-left b-primary">
				<div class="row">
					<?php
					$col = 6;
					if(!$cfg['onlyBR']) :
					?>
						<div class="col-sm-6 col-lg-12">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_ADDRESS_COUNTRY'); ?></label>
								<input type="text" name="address_country" id="<?php echo $APPTAG?>-address_country" class="form-control upper" />
								<input type="hidden" name="onlyBR" id="<?php echo $APPTAG?>-onlyBR" value="0" />
							</div>
						</div>
						<div class="col-sm-6 col-lg-12">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STATE'); ?></label>
								<input type="text" name="address_state" id="<?php echo $APPTAG?>-address_state" class="form-control upper" />
							</div>
						</div>
					<?php
					else :
						$col = 5;
					?>
						<div class="col-sm-2">
							<div class="form-group">
								<label>UF</label>
								<input type="text" name="address_state" id="<?php echo $APPTAG?>-address_state" class="form-control w-auto upper" size="2" maxlength="2" />
								<input type="hidden" name="address_country" id="<?php echo $APPTAG?>-address_country" />
								<input type="hidden" name="onlyBR" id="<?php echo $APPTAG?>-onlyBR" value="1" />
							</div>
						</div>
					<?php endif; ?>
					<div class="col-sm-<?php echo $col?> col-lg-12">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
							<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
						</div>
					</div>
					<div class="col-sm-<?php echo $col?> col-lg-12">
						<div class="form-group">
							<label><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
							<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabMap" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-map">
		<div class="row">
			<div class="col-sm-6">
				<div class="form-group">
					<label>Latitude</label>
					<input type="text" name="latitude" id="<?php echo $APPTAG?>-latitude" class="form-control" />
				</div>
			</div>
			<div class="col-sm-6">
				<div class="form-group">
					<label>Longitude</label>
					<input type="text" name="longitude" id="<?php echo $APPTAG?>-longitude" class="form-control" />
				</div>
			</div>
			<div class="col-12">
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_MAP_INFO_DESC'); ?>">
						<?php echo JText::_('FIELD_LABEL_MAP_INFO'); ?>
					</label>
					<textarea type="text" name="map_info" id="<?php echo $APPTAG?>-map_info" rows="8" class="form-control"></textarea>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabExtra" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-extra">
		<div class="form-group">
			<label><?php echo JText::_('FIELD_LABEL_EXTRA_INFO_DESC')?></label>
			<textarea name="extra_info" id="<?php echo $APPTAG?>-extra_info" rows="4" class="form-control field-html" data-editor-full="true"></textarea>
		</div>
	</div>
</div>
