<?php
defined('_JEXEC') or die;
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
					<div class="form-group">
						<label class="field-required"><?php echo JText::_('FIELD_LABEL_NAME'); ?></label>
						<div class="input-group">
							<input type="text" name="name" id="<?php echo $APPTAG?>-name" class="form-control upper input-required" />
							<span class="input-group-addon strong">
								<input type="checkbox" name="proposal" id="<?php echo $APPTAG?>-proposal" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-email" />
								<?php echo JText::_('TEXT_PROPOSAL'); ?>
							</span>
						</div>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-group-relation" class="col-sm-4 hide">
					<div class="form-group">
						<div class="hidden-xs hidden-sm"><label>&#160;</label></div>
						<span class="btn-group btn-group-justified">
							<a href="#" class="base-icon-location btn btn-warning hasTooltip" onclick="<?php echo $APPTAG?>_viewAddresses()" data-toggle="modal" data-target="#modal-list-addresses" title="<?php echo JText::_('MSG_VIEW_ADDRESS')?>"></a>
							<a href="#" class="base-icon-phone-squared btn btn-warning hasTooltip" onclick="<?php echo $APPTAG?>_viewPhones()" data-toggle="modal" data-target="#modal-list-phones" title="<?php echo JText::_('MSG_VIEW_PHONE')?>"></a>
							<a href="#" class="base-icon-chat-empty btn btn-warning hasTooltip" onclick="<?php echo $APPTAG?>_viewSocials()" data-toggle="modal" data-target="#modal-list-webSocials" title="<?php echo JText::_('MSG_VIEW_SOCIAL')?>"></a>
							<a href="#" class="base-icon-bank btn btn-warning hasTooltip" onclick="<?php echo $APPTAG?>_viewBanks()" data-toggle="modal" data-target="#modal-list-banksAccounts" title="<?php echo JText::_('MSG_VIEW_BANKS_ACCOUNTS')?>"></a>
						</span>
					</div>
				</div>
				<div class="col-sm-8">
					<div class="form-group">
						<label>E-mail</label>
						<div class="input-group">
							<input type="text" name="email" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
							<input type="hidden" name="cmail" id="<?php echo $APPTAG?>-cmail" />
							<span class="input-group-btn">
								<button id="setEmailOptional" type="button" class="base-icon-plus btn btn-success hasTooltip" title="<?php echo JText::_('MSG_ADD_EMAIL_OPTIONAL')?>"></button>
							</span>
						</div>
					</div>
					<div id="group-email_optional" class="form-group hide">
						<label>E-mail <?php echo JText::_('TEXT_OPTIONAL'); ?></label>
						<input type="text" name="email_optional" id="<?php echo $APPTAG?>-email_optional" class="form-control field-email" />
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_TYPE'); ?></label>
						<span id="<?php echo $APPTAG?>-type-group" class="btn-group btn-group-justified" data-toggle="buttons">
							<label class="btn btn-default btn-active-success" onclick="<?php echo $APPTAG?>_setType(1)">
								<input type="radio" name="type" id="<?php echo $APPTAG?>-type-1" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-name_company" />
								Jurídica
							</label>
							<label class="btn btn-default btn-active-success" onclick="<?php echo $APPTAG?>_setType(0)">
								<input type="radio" name="type" id="<?php echo $APPTAG?>-type-0" class="auto-tab" data-target="<?php echo $APPTAG?>-doc_number" value="0" />
								Física
							</label>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-company" class="col-sm-8">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_NAME_COMPANY'); ?></label>
						<input type="text" name="name_company" id="<?php echo $APPTAG?>-name_company" class="form-control upper" />
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-numDoc" class="col-sm-4">
					<div class="form-group">
						<label>CNPJ</label>
						<input type="text" name="doc_number" id="<?php echo $APPTAG?>-doc_number" class="form-control field-cnpj" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label>Cliente Desde</label>
						<input type="text" name="date" id="<?php echo $APPTAG?>-date" class="form-control field-date" data-convert="true" />
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DUE_DATE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DUE_DATE'); ?></label>
						<select name="due_date" id="<?php echo $APPTAG?>-due_date" class="form-control">
							<?php
							for($i = 0; $i < count($cfg['dueDates']); $i++) {
								$pre = ($cfg['dueDates'][$i] < 10) ? '0' : '';
								echo '<option value="'.$cfg['dueDates'][$i].'"'.($cfg['dueDates'][$i] == 1 ? ' selected' : '').'>'.$pre.$cfg['dueDates'][$i].'</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label>Logo</label><br />
						<span class="btn-group">
							<button type="button" class="base-icon-search btn btn-default set-file-action hasTooltip" title="<?php echo JText::_('TEXT_FILE_SELECT'); ?>"></button>
						</span>
						<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control element-invisible" />
					</div>
				</div>
			</div>
			<hr class="hr-sm" />
			<div class="row">
				<div class="col-sm-8">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
						<textarea name="description" rows="3" id="<?php echo $APPTAG?>-description" class="form-control"></textarea>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
						<textarea name="note" rows="3" id="<?php echo $APPTAG?>-note" class="form-control"></textarea>
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
