<?php
defined('_JEXEC') or die;

// TYPE
$query = 'SELECT * FROM '. $db->quoteName($cfg['mainTable'].'_types') .' ORDER BY name';
$db->setQuery($query);
$types = $db->loadObjectList();

// FORM
?>

<!-- Nav tabs -->
<ul class="nav nav-tabs">
	<li class="nav-item">
		<a class="nav-link active" href="#tab-main" data-toggle="tab" role="tab">Geral</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#tab-address" data-toggle="tab" role="tab">Endereço</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#tab-files" data-toggle="tab" role="tab">Arquivos</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#tab-images" data-toggle="tab" role="tab">Imagens</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" href="#tab-extra-info" data-toggle="tab" role="tab">Informações extra</a>
	</li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active" id="tab-main" role="tabpanel">
		<div class="row">
			<div class="col-lg-9">
				<div class="form-group field-required">
					<label class="iconTip hasTooltip" data-animation="false" title="<?php echo JText::_('FIELD_LABEL_TYPE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label>
					<div class="input-group">
						<select name="type_id" id="<?php echo $APPTAG?>-type_id" class="form-control field-id auto-tab" data-target="#<?php echo $APPTAG?>-name">
							<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
							<?php
								foreach ($types as $obj) {
									echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->name).'</option>';
								}
							?>
						</select>
						<span class="input-group-btn">
							<button type="button" class="base-icon-plus btn btn-success hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_ADD')?>" data-toggle="modal" data-target="#modal-<?php echo $APPTAG?>Types" data-backdrop="static" data-keyboard="false"></button>
							<button type="button" class="base-icon-cog btn btn-primary hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_EDIT')?>" onclick="<?php echo $APPTAG?>Types_listReload(false)" data-toggle="modal" data-target="#modal-list-<?php echo $APPTAG?>Types" data-backdrop="static" data-keyboard="false"></button>
						</span>
					</div>
				</div>
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
					<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
				</div>
				<div class="form-group field-required">
					<label>E-mail</label>
					<input type="email" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
					<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
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
			<div class="col-lg-3">
				<div class="form-group field-required">
					<label>CPF</label>
					<input type="text" name="cpf" id="<?php echo $APPTAG?>-cpf" class="form-control field-cpf" />
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group field-required">
					<label>RG</label>
					<input type="text" name="rg" id="<?php echo $APPTAG?>-rg" class="form-control" />
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group field-required">
					<label>RG Orgão</label>
					<input type="text" name="rg_orgao" id="<?php echo $APPTAG?>-rg_orgao" class="form-control upper" />
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
					<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" class="auto-tab" data-target="<?php echo $APPTAG?>-marital_status" value="1" />
							<?php echo JText::_('TEXT_MALE_ABBR'); ?>
						</label>
						<label class="btn btn-default btn-active-success">
							<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" class="auto-tab" data-target="<?php echo $APPTAG?>-marital_status" value="2" />
							<?php echo JText::_('TEXT_FEMALE_ABBR'); ?>
						</label>
					</span>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_MARITAL_STATUS'); ?></label>
					<select name="marital_status" id="<?php echo $APPTAG?>-marital_status" class="form-control auto-tab" data-target="<?php echo $APPTAG?>-group-partner">
						<option value="" data-target-display="false"><?php echo JText::_('TEXT_SELECT'); ?></option>
						<option value="SOLTEIRO" data-target-display="false">Solteiro</option>
						<option value="CASADO" data-target-display="true">Casado</option>
						<option value="UNIÃO ESTÁVEL" data-target-display="true">União Estável</option>
						<option value="DIVORCIADO" data-target-display="false">Divorciado</option>
						<option value="VIÚVO" data-target-display="false">Viúvo</option>
					</select>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_PLACE_BIRTH'); ?></label>
					<input type="text" name="place_birth" id="<?php echo $APPTAG?>-place_birth" class="form-control upper" />
				</div>
			</div>
		</div>
		<hr class="hr-xs" />
		<div class="row">
			<div class="col-lg-9">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_STATUS'); ?></label><br />
					<span class="btn-group btn-group-justified" data-toggle="buttons">
						<label class="base-icon-clock text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_WAITING'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-0" value="0" class="auto-tab" data-target="status_desc-group" data-target-display="true" />
						</label>
						<label class="base-icon-off text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_ACTIVE'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-1" value="1" class="auto-tab" data-target="status_desc-group" data-target-display="false" data-target-value="" />
						</label>
						<label class="base-icon-pause text-live btn btn-default btn-active-warning hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_PAUSED'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-2" value="2" class="auto-tab" data-target="status_desc-group" data-target-display="true" />
						</label>
						<label class="base-icon-ok text-success btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_COMPLETED'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-3" value="3" class="auto-tab" data-target="status_desc-group" data-target-display="false" data-target-value="" />
						</label>
						<label class="base-icon-cancel text-danger btn btn-default btn-active-danger hasTooltip" title="<?php echo JText::_('FIELD_LABEL_STATUS_CANCELED'); ?>">
							<input type="radio" name="status" id="<?php echo $APPTAG?>-status-4" value="4" class="auto-tab" data-target="status_desc-group" data-target-display="true" />
						</label>
					</span>
				</div>
				<div id="status_desc-group" class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_STATUS_DESC'); ?></label>
					<input type="text" name="status_desc" id="<?php echo $APPTAG?>-status_desc" class="form-control" />
				</div>
			</div>
			<div class="col-xs-6 col-lg-3">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
					<input type="text" name="price" id="<?php echo $APPTAG?>-price" size="6" class="form-control field-price" data-convert="true" />
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane" id="tab-address" role="tabpanel">
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
			<div class="col-lg-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_ADDRESS_DISTRICT'); ?></label>
					<input type="text" name="address_district" id="<?php echo $APPTAG?>-address_district" class="form-control upper" />
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_ADDRESS_CITY'); ?></label>
					<input type="text" name="address_city" id="<?php echo $APPTAG?>-address_city" class="form-control upper" />
				</div>
			</div>
			<div class="col-lg-4">
				<div class="form-group field-required">
					<label><?php echo JText::_('FIELD_LABEL_ADDRESS_STATE'); ?></label>
					<input type="text" name="address_state" id="<?php echo $APPTAG?>-address_state" class="form-control upper" />
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane" id="tab-files" role="tabpanel">
		<div class="row">
			<div class="col-lg-3">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_FILE'); ?></label>
					<div class="btn-file">
						<span class="btn-group w-100">
							<button type="button" class="col base-icon-search btn btn-default btn-active-success file-action text-truncate hasTooltip" data-animation="false" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"> <span><?php echo JText::_('TEXT_FILE_SELECT'); ?></span></button>
						</span>
						<input type="file" name="file[1]" id="<?php echo $APPTAG?>-file1" class="form-control" hidden />
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="form-group">
					<label>&#160;</label>
					<button type="button" class="base-icon-plus btn btn-block btn-success" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-files-group', 'file', 'col-md-6 col-lg-3')">
						<?php echo JText::_('TEXT_ADD'); ?>
					</button>
				</div>
			</div>
		</div>
		<hr />
		<div id="<?php echo $APPTAG?>-files-group" class="row"></div>
	</div>
	<div class="tab-pane" id="tab-images" role="tabpanel">
		<button type="button" class="base-icon-plus btn btn-success" onclick="<?php echo $APPTAG?>_setNewFile('#<?php echo $APPTAG?>-images-group', 'image', 'col-md-6 col-lg-3')">
			<?php echo JText::_('TEXT_ADD'); ?>
		</button>
		<hr />
		<div id="<?php echo $APPTAG?>-images-group" class="row"></div>
	</div>
	<div class="tab-pane" id="tab-extra-info" role="tabpanel">
		<div class="row">
			<div class="col-12">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
					<textarea name="description" rows="10" id="<?php echo $APPTAG?>-description" class="form-control field-html"></textarea>
				</div>
			</div>
		</div>
	</div>
</div>
