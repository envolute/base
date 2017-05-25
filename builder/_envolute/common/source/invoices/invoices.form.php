<?php
defined('_JEXEC') or die;

$query = '
SELECT
	'. $db->quoteName('T1.id') .',
	CONCAT('.$db->quoteName('T2.name').', " - ", '.$db->quoteName('T1.name').') project
FROM
	'. $db->quoteName('#__envolute_projects') .' T1
	JOIN '. $db->quoteName('#__envolute_clients').' T2
	ON T2.id = T1.client_id
WHERE T1.state = 1
ORDER BY T2.name, T1.name';
$db->setQuery($query);
$projects = $db->loadObjectList();
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

			<!-- Nav tabs -->
		  <ul class="nav nav-tabs" role="tablist">
		    <li role="presentation" class="active"><a href="#<?php echo $APPTAG?>tabMain" aria-controls="<?php echo $APPTAG?>tabMain" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_MAIN'); ?></a></li>
		    <li role="presentation"><a href="#<?php echo $APPTAG?>tabInfo" aria-controls="<?php echo $APPTAG?>tabInfo" role="tab" data-toggle="tab"><?php echo JText::_('FIELD_LABEL_BOLETO')?></a></li>
		    <li role="presentation"><a href="#<?php echo $APPTAG?>tabAccount" aria-controls="<?php echo $APPTAG?>tabAccount" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_BANK_ACCOUNT')?></a></li>
		    <li role="presentation"><a href="#<?php echo $APPTAG?>tabSender" aria-controls="<?php echo $APPTAG?>tabSender" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_SEND_MAIL')?></a></li>
		    <li role="presentation"><a href="#<?php echo $APPTAG?>tabReSender" aria-controls="<?php echo $APPTAG?>tabReSender" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_REMIND_MAIL')?></a></li>
		    <li role="presentation"><a href="#<?php echo $APPTAG?>tabAttach" aria-controls="<?php echo $APPTAG?>tabAttach" role="tab" data-toggle="tab"><?php echo JText::_('TEXT_ATTACHMENT')?>s</a></li>
		  </ul>
			<!-- Tab panes -->
		  <div class="tab-content">
		    <div role="tabpanel" class="tab-pane active" id="<?php echo $APPTAG?>tabMain">
					<div class="row">
						<div id="<?php echo $APPTAG?>-type-group" class="col-sm-5">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_INVOICE_TYPE'); ?></label>
								<div class="btn-group btn-group-justified" data-toggle="buttons">
									<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_ACTIVITIES_DESC'); ?>">
										<input type="radio" name="type" id="<?php echo $APPTAG?>-type-0" value="0" class="auto-tab" data-target="<?php echo $APPTAG?>-price-group" data-target-group="#<?php echo $APPTAG?>-hosting-group" data-target-display="false" />
										<?php echo JText::_('FIELD_LABEL_ACTIVITIES'); ?>
									</label>
									<label class="btn btn-default btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_DIRECT_DESC'); ?>">
										<input type="radio" name="type" id="<?php echo $APPTAG?>-type-1" value="1" class="auto-tab" data-target="<?php echo $APPTAG?>-price-group" data-target-group="#<?php echo $APPTAG?>-hosting-group" data-target-display="true" />
										<?php echo JText::_('FIELD_LABEL_DIRECT'); ?>
									</label>
								</div>
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-hosting-group" class="col-xs-6 col-sm-4">
							<div class="form-group">
								<label class="display-block">&#160;</label>
								<div class="btn-group width-full" data-toggle="buttons">
									<label class="btn btn-warning btn-block btn-active-success hasTooltip" title="<?php echo JText::_('FIELD_LABEL_HOSTING_DESC'); ?>">
										<span class="base-icon-cancel btn-icon"></span>
										<input type="checkbox" name="hosting" id="<?php echo $APPTAG?>-hosting" value="1" /><?php echo JText::_('FIELD_LABEL_HOSTING'); ?>
									</label>
								</div>
							</div>
						</div>
						<div id="<?php echo $APPTAG?>-price-group" class="col-xs-6 col-sm-4 hide">
							<div class="form-group">
								<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_PRICE_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_PRICE'); ?></label>
								<div class="input-group">
									<span class="input-group-addon">R$</span>
									<input type="text" name="price" id="<?php echo $APPTAG?>-price" size="6" class="form-control field-price" data-convert="true" />
								</div>
							</div>
						</div>
						<div class="col-sm-9">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_PROJECT'); ?></label>
								<select name="project_id" id="<?php echo $APPTAG?>-project_id" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-due_date">
									<option value="0">- <?php echo JText::_('TEXT_SELECT'); ?> -</option>
									<?php
										foreach ($projects as $obj) {
											echo '<option value="'.$obj->id.'">'.baseHelper::nameFormat($obj->project).'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_DUE_DATE'); ?></label>
								<input type="text" name="due_date" id="<?php echo $APPTAG?>-due_date" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_MONTH'); ?></label>
								<select name="month" id="<?php echo $APPTAG?>-month" class="form-control field-id auto-tab" data-target="<?php echo $APPTAG?>-year">
									<option value="0"><?php echo JText::_('TEXT_SELECT'); ?></option>
									<option value="1">Janeiro</option>
									<option value="2">Fevereiro</option>
									<option value="3">Março</option>
									<option value="4">Abril</option>
									<option value="5">Maio</option>
									<option value="6">Junho</option>
									<option value="7">Julho</option>
									<option value="8">Agosto</option>
									<option value="9">Setembro</option>
									<option value="10">Outubro</option>
									<option value="11">Novembro</option>
									<option value="12">Dezembro</option>
								</select>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group field-required">
								<label><?php echo JText::_('FIELD_LABEL_YEAR'); ?></label>
								<input type="text" name="year" id="<?php echo $APPTAG?>-year" class="form-control field-number upper" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group field-required">
								<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_DESCRIPTION_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?></label>
								<input type="text" name="description" id="<?php echo $APPTAG?>-description" class="form-control" maxlength="80" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_DISCOUNT'); ?></label>
								<span class="input-group">
									<span class="input-group-addon">R$</span>
									<input type="text" name="discount" id="<?php echo $APPTAG?>-discount" size="6" class="form-control field-price" data-convert="true" />
								</span>
							</div>
						</div>
						<div class="col-sm-9">
							<div class="form-group">
								<div class="hidden-xs"><label>&#160;</label></div>
								<input type="text" name="discount_note" id="<?php echo $APPTAG?>-discount_note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_REASON'); ?>" />
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_TAX'); ?></label>
								<span class="input-group">
									<span class="input-group-addon">R$</span>
									<input type="text" name="tax" id="<?php echo $APPTAG?>-tax" size="6" class="form-control field-price" data-convert="true" />
								</span>
							</div>
						</div>
						<div class="col-sm-9">
							<div class="form-group">
								<div class="hidden-xs"><label>&#160;</label></div>
								<input type="text" name="tax_note" id="<?php echo $APPTAG?>-tax_note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_REASON'); ?>" />
							</div>
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_ASSESSMENT'); ?></label>
								<span class="input-group">
									<span class="input-group-addon">R$</span>
									<input type="text" name="assessment" id="<?php echo $APPTAG?>-assessment" size="6" class="form-control field-price" data-convert="true" />
								</span>
							</div>
						</div>
						<div class="col-sm-9">
							<div class="form-group">
								<div class="hidden-xs"><label>&#160;</label></div>
								<input type="text" name="assessment_note" id="<?php echo $APPTAG?>-assessment_note" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_REASON'); ?>" />
							</div>
						</div>
					</div>
					<hr class="hr-sm hr-label" />
					<span class="label label-warning"><?php echo JText::_('FIELD_LABEL_PAID_REGISTRATION'); ?></span>
					<div class="small font-featured text-muted"><?php echo JText::_('FIELD_LABEL_PAID_DATE_DESC'); ?></div>
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_DATE'); ?></label>
								<input type="text" name="paid_date" id="<?php echo $APPTAG?>-paid_date" class="form-control field-date" data-convert="true" />
							</div>
						</div>
						<div class="col-sm-9">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_NOTE'); ?></label>
								<input type="text" name="note" id="<?php echo $APPTAG?>-note" class="form-control" />
							</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="<?php echo $APPTAG?>tabInfo">
					<div class="row">
						<div class="col-sm-4 col-lg-3">
							<div class="form-group">
								<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_CREATE_BOLETO_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_CREATE_BOLETO'); ?></label>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-default btn-active-success">
										<input type="radio" name="create_boleto" id="<?php echo $APPTAG?>-create_boleto-1" value="1" />
										<?php echo JText::_('TEXT_YES'); ?>
									</label>
									<label class="btn btn-default btn-active-danger">
										<input type="radio" name="create_boleto" id="<?php echo $APPTAG?>-create_boleto-0" value="0" />
										<?php echo JText::_('TEXT_NO'); ?>
									</label>
								</div>
							</div>
						</div>
						<div class="col-sm-8 col-lg-9">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_URL_BOLETO'); ?></label>
								<input type="text" name="url_boleto" id="<?php echo $APPTAG?>-url_boleto" class="form-control field-url" />
							</div>
						</div>
						<div class="col-sm-12">
							<hr class="hr-sm" />
							<div class="form-group">
								<label class="iconTip hasPopover" data-content="<?php echo JText::_('FIELD_LABEL_LINES_DESC'); ?>"><?php echo JText::_('FIELD_LABEL_LINES'); ?></label>
								<input type="text" name="description1" id="<?php echo $APPTAG?>-description1" class="form-control" maxlength="80" placeholder="<?php echo JText::_('TEXT_LINE'); ?> 1" />
							</div>
							<div class="form-group">
								<input type="text" name="description2" id="<?php echo $APPTAG?>-description2" class="form-control" maxlength="80" placeholder="<?php echo JText::_('TEXT_LINE'); ?> 2" />
							</div>
							<div class="form-group">
								<input type="text" name="description3" id="<?php echo $APPTAG?>-description3" class="form-control" maxlength="80" placeholder="<?php echo JText::_('TEXT_LINE'); ?> 3" />
							</div>
							<div class="form-group">
								<input type="text" name="description4" id="<?php echo $APPTAG?>-description4" class="form-control" maxlength="80" placeholder="<?php echo JText::_('TEXT_LINE'); ?> 4" />
							</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="<?php echo $APPTAG?>tabAccount">
					<div class="form-group">
						<label><?php echo JText::_('FIELD_LABEL_BANK_ACCOUNT_INFO'); ?></label>
						<textarea rows="8" name="bankAccount_info" id="<?php echo $APPTAG?>-bankAccount_info" class="field-html"></textarea>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="<?php echo $APPTAG?>tabSender">
					<div class="row">
						<div class="col-sm-9">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_EMAIL_SUBJECT'); ?></label>
								<input type="text" name="email_subject" id="<?php echo $APPTAG?>-email_subject" class="form-control" />
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_EMAIL_CONTENT'); ?></label>
								<textarea rows="8" name="email_content" id="<?php echo $APPTAG?>-email_content" class="field-html"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="<?php echo $APPTAG?>tabReSender">
					<div class="row">
						<div class="col-sm-9">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_EMAIL_SUBJECT'); ?></label>
								<input type="text" name="email_subject_resend" id="<?php echo $APPTAG?>-email_subject_resend" class="form-control" />
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group">
								<label><?php echo JText::_('FIELD_LABEL_EMAIL_CONTENT'); ?></label>
								<textarea rows="8" name="email_content_resend" id="<?php echo $APPTAG?>-email_content_resend" class="field-html"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div role="tabpanel" class="tab-pane" id="<?php echo $APPTAG?>tabAttach">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label><span class="base-icon-attach"></span> Nota Fiscal</label><br />
								<span class="btn-group">
									<button type="button" class="base-icon-search btn btn-default set-file-action"> <?php echo JText::_('TEXT_FILE_SELECT'); ?></button>
								</span>
								<input type="file" name="file[0]" id="<?php echo $APPTAG?>-file0" class="form-control element-invisible" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label><span class="base-icon-attach"></span> Boleto</label><br />
								<span class="btn-group">
									<button type="button" class="base-icon-search btn btn-default set-file-action"> <?php echo JText::_('TEXT_FILE_SELECT'); ?></button>
								</span>
								<input type="file" name="file[1]" id="<?php echo $APPTAG?>-file1" class="form-control element-invisible" />
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
								<input type="file" name="file[2]" id="<?php echo $APPTAG?>-file2" class="form-control element-invisible" />
							</div>
						</div>
						<div class="col-sm-4">
							<button type="button" class="base-icon-plus btn btn-block btn-success" onclick="<?php echo $APPTAG?>_setNewFile()">
								<?php echo JText::_('TEXT_ADD'); ?>
							</button>
						</div>
						<div id="<?php echo $APPTAG?>-files-group" class="col-sm-8"></div>
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
