<?php
defined('_JEXEC') or die;

$query = 'SELECT * FROM '. $db->quoteName('#__usergroups') .' WHERE '. $db->quoteName('parent_id') .' = 10 ORDER BY id';
$db->setQuery($query);
$userGrps = $db->loadObjectList();
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
							<input type="hidden" name="user_id" id="<?php echo $APPTAG?>-user_id" value="0" />
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
				<div class="col-sm-7">
					<div class="row">
						<div class="col-sm-8">
							<div class="form-group field-required">
								<label class="field-required"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
								<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label class="field-required"><?php echo JText::_('FIELD_LABEL_CLIENT_CODE'); ?></label>
								<input type="text" name="code" id="<?php echo $APPTAG?>-code" class="form-control" disabled="disabled" />
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group field-required">
								<label>E-mail</label>
								<input type="text" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
								<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group field-required">
								<label>CPF</label>
								<input type="text" name="cpf" id="<?php echo $APPTAG?>-cpf" class="form-control field-cpf" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group field-required">
								<label>RG</label>
								<input type="text" name="rg" id="<?php echo $APPTAG?>-rg" class="form-control" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group field-required">
								<label>RG Orgão</label>
								<input type="text" name="rg_orgao" id="<?php echo $APPTAG?>-rg_orgao" class="form-control upper" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_GENDER'); ?></label>
								<span class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="gender" id="<?php echo $APPTAG?>-male" class="auto-tab" data-target="<?php echo $APPTAG?>-birthday" value="1" />
										<?php echo JText::_('FIELD_LABEL_GENDER_MALE_ABBR'); ?>
									</label>
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="gender" id="<?php echo $APPTAG?>-female" class="auto-tab" data-target="<?php echo $APPTAG?>-birthday" value="2" />
										<?php echo JText::_('FIELD_LABEL_GENDER_FEMALE_ABBR'); ?>
									</label>
								</span>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_BIRTHDAY'); ?></label>
								<input type="text" name="birthday" id="<?php echo $APPTAG?>-birthday" class="form-control field-date birthday" data-convert="true" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_PLACE_BIRTH'); ?></label>
								<input type="text" name="place_birth" id="<?php echo $APPTAG?>-place_birth" class="form-control upper" />
							</div>
						</div>
						<div class="col-sm-4">
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
						<span id="<?php echo $APPTAG?>-group-partner" class="hide">
							<div class="col-sm-8">
								<div class="form-group">
									<label><?php echo JText::_('FIELD_LABEL_PARTNER'); ?></label>
									<input type="text" name="partner" id="<?php echo $APPTAG?>-partner" class="form-control upper" />
								</div>
							</div>
						</span>
						<div class="col-sm-4 pull-right">
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
						<div class="col-sm-8">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_MOTHER'); ?></label>
								<input type="text" name="mother_name" id="<?php echo $APPTAG?>-mother_name" class="form-control upper" />
							</div>
						</div>
						<div class="col-sm-8">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_FATHER'); ?></label>
								<input type="text" name="father_name" id="<?php echo $APPTAG?>-father_name" class="form-control upper" />
							</div>
						</div>
					</div>
					<div class="well well-sm top-space-lg">
						<div class="row">
							<div class="col-sm-8">
								<h4 class="no-margin-top bottom-space-xs font-featured"><span class="base-icon-award"></span> <?php echo JText::_('MAG_MEMBERSHIP_BENEFITS'); ?></h4>
								<div class="small text-muted font-condensed">
									<?php echo JText::_('MAG_MEMBERSHIP_BENEFITS_DESC'); ?>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group field-required">
									<label><?php echo JText::_('FIELD_LABEL_CARD_LIMIT'); ?></label>
									<div class="input-group">
										<span class="input-group-addon">R$</span>
										<input type="text" name="card_limit" id="<?php echo $APPTAG?>-card_limit" class="form-control field-price" data-convert="true" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-5">
					<div id="<?php echo $APPTAG?>-group-relation" class="hide">
						<div class="form-group top-expand-xs">
							<span class="btn-group btn-group-justified">
								<a href="#" class="base-icon-location btn btn-warning hasTooltip" onclick="<?php echo $APPTAG?>_viewAddresses()" data-toggle="modal" data-target="#modal-list-addresses" title="<?php echo JText::_('MSG_VIEW_ADDRESS')?>"></a>
								<a href="#" class="base-icon-phone-squared btn btn-warning hasTooltip" onclick="<?php echo $APPTAG?>_viewPhones()" data-toggle="modal" data-target="#modal-list-phones" title="<?php echo JText::_('MSG_VIEW_PHONE')?>"></a>
								<a href="#" class="base-icon-bank btn btn-warning hasTooltip" onclick="<?php echo $APPTAG?>_viewBanks()" data-toggle="modal" data-target="#modal-list-banksAccounts" title="<?php echo JText::_('MSG_VIEW_BANKS_ACCOUNTS')?>"></a>
							</span>
						</div>
					</div>
					<div class="well well-sm">
						<div class="row">
							<div class="col-sm-8">
								<div class="form-group field-required">
									<label><?php echo JText::_('FIELD_LABEL_USERGROUP'); ?></label>
									<select name="usergroup" id="<?php echo $APPTAG?>-usergroup" class="form-control" onchange="<?php echo $APPTAG?>_setType(this.value)">
										<?php
											foreach ($userGrps as $obj) {
												echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->title).'</option>';
											}
										?>
									</select>
								</div>
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-group-caixa" class="row">
							<div class="col-sm-12">
								<hr class="hr-label" />
								<span class="label label-warning">Dados da Caixa</span>
							</div>
							<div id="<?php echo $APPTAG?>-group-emailCaixa" class="col-sm-12">
								<div class="form-group">
									<label>E-mail Caixa</label>
									<div class="input-group">
										<input type="text" name="cx_email" id="<?php echo $APPTAG?>-cx_email" class="form-control" />
										<span class="input-group-addon">@caixa.gov.br</span>
									</div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Matrícula Caixa</label>
									<input type="text" name="cx_matricula" id="<?php echo $APPTAG?>-cx_matricula" class="form-control upper" />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label><span class="base-icon-calendar"></span> Admissão</label>
									<input type="text" name="cx_admissao" id="<?php echo $APPTAG?>-cx_admissao" class="form-control field-date" data-convert="true" />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Lotação (Agência)</label>
									<input type="text" name="cx_lotacao" id="<?php echo $APPTAG?>-cx_lotacao" class="form-control upper" />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Cargo</label>
									<input type="text" name="cx_cargo" id="<?php echo $APPTAG?>-cx_cargo" class="form-control upper" />
								</div>
							</div>
						</div>
						<hr class="hr-label" />
						<span class="label label-warning">Carteira do Associado</span>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label class="iconTip hasTooltip" title="Devido ao espaço limitado para impressão, pode ser necessário utilizar um nome abreviado.<br />Obs: Caso o nome abreviado não seja informado, o nome completo será utilizado.">Nome Abreviado</label><br />
									<input type="text" name="name_card" id="<?php echo $APPTAG?>-name_card" class="form-control upper" maxlength="30" />
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Foto</label><br />
									<span id="<?php echo $APPTAG?>-display-img"></span>
									<span class="btn-group">
										<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
									</span>
									<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control field-image element-invisible" />
								</div>
							</div>
							<div class="col-sm-6">
								<div id="<?php echo $APPTAG?>-btnPrint-img" class="form-group hide">
									<div class="hidden-xs hidden-sm"><label>&#160;</label></div>
								</div>
							</div>
							<div class="col-sm-12">
								<hr class="hr-sm" />
								<div class="checkbox no-margin">
									<label>
										<input type="checkbox" name="emailConfirm" id="<?php echo $APPTAG?>-emailConfirm" />
										<?php echo JText::_('FIELD_LABEL_REGISTRATION_EMAIL_CONFIRM'); ?>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
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
