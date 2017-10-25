<?php
defined('_JEXEC') or die;

// FORM
?>
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
		<div class="form-group field-required">
			<label><?php echo JText::_('FIELD_LABEL_TITLE'); ?></label>
			<div class="input-group">
				<input type="text" name="title" id="<?php echo $APPTAG?>-title" class="form-control upper" maxlength="30" placeholder="<?php echo JText::_('FIELD_LABEL_TITLE_PLACEHOLDER'); ?>" />
				<span class="input-group-btn btn-group" data-toggle="buttons">
					<label class="btn btn-danger btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_VISIBLE_DESC'); ?>">
						<input type="checkbox" name="showTitle" id="<?php echo $APPTAG?>-showTitle" class="no-validate" value="1" />
						<span class="base-icon-cancel"></span>
					</label>
				</span>
			</div>
		</div>
	</div>
</div>
<ul class="nav nav-tabs" id="<?php echo $APPTAG?>Tab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" id="<?php echo $APPTAG?>Tab-general" data-toggle="tab" href="#<?php echo $APPTAG?>TabGeneral" role="tab" aria-controls="general" aria-expanded="true"><?php echo JText::_('TEXT_PHONES'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-web" data-toggle="tab" href="#<?php echo $APPTAG?>TabWeb" role="tab" aria-controls="web"><?php echo JText::_('TEXT_INTERNET'); ?></a>
	</li>
	<li class="nav-item">
		<a class="nav-link" id="<?php echo $APPTAG?>Tab-extra" data-toggle="tab" href="#<?php echo $APPTAG?>TabExtra" role="tab" aria-controls="extra"><?php echo JText::_('FIELD_LABEL_EXTRA_INFO'); ?></a>
	</li>
</ul>
<div class="tab-content" id="<?php echo $APPTAG?>TabContent">
	<div class="tab-pane fade show active" id="<?php echo $APPTAG?>TabGeneral" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-general">
		<div class="row">
			<div class="col-8 col-sm-6 col-lg-4">
				<div class="form-group">
					<label><?php echo JText::_('FIELD_LABEL_PHONE'); ?></label>
					<input type="text" name="phone[]" id="<?php echo $APPTAG?>-phone" class="form-control field-phone" data-toggle-mask="true" />
				</div>
			</div>
			<div class="col-4 col-sm-2 col-lg-1">
				<div class="form-group">
					<label class="d-block">&#160;</label>
					<span class="btn-group" data-toggle="buttons">
						<label class="btn btn-outline-success btn-active-success hasTooltip" title="<?php echo JText::_('TEXT_HAS_WHATSAPP'); ?>">
							<input type="checkbox" name="wapp[]" id="<?php echo $APPTAG?>-wapp" value="1" class="auto-tab" data-target="#<?php echo $APPTAG?>-whatsapp" data-target-value="1" data-target-value-reset="" data-tab-disabled="true" />
							<span class="base-icon-whatsapp icon-default"></span>
							<input type="hidden" name="whatsapp[]" id="<?php echo $APPTAG?>-whatsapp" />
						</label>
					</span>
				</div>
			</div>
			<div class="col-sm-6 col-lg-7">
				<div class="form-group">
					<label class="d-none d-lg-block">&#160;</label>
					<input type="text" name="phone_desc[]" id="<?php echo $APPTAG?>-phone_desc" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_DESCRIPTION'); ?>" maxlength="50" />
				</div>
			</div>
		</div>
		<div id="<?php echo $APPTAG?>-phoneGroups" class="newFieldsGroup"></div>
		<button type="button" class="btn btn-sm btn-success base-icon-plus" onclick="<?php echo $APPTAG?>_phoneAdd()"> <?php echo JText::_('TEXT_PHONES_ADD')?></button>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabWeb" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-web">
		<div class="row">
			<div class="col-lg-8">
				<div class="form-group">
					<label>E-mail</label>
					<div class="input-group">
						<input type="text" name="email[]" id="<?php echo $APPTAG?>-email" class="form-control field-email" />
						<span class="input-group-btn">
							<button type="button" class="btn btn-success base-icon-plus hasTooltip" title="<?php echo JText::_('TEXT_ADD_EMAIL')?>" onclick="<?php echo $APPTAG?>_emailAdd()"></button>
						</span>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-emailGroups" class="newFieldsGroup"></div>
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_CHAT_DESC')?>"><?php echo JText::_('FIELD_LABEL_CHAT')?></label>
					<div class="row no-gutters">
						<div class="col-sm-4 pr-1">
							<input type="text" name="chat_name[]" id="<?php echo $APPTAG?>-chat_name" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_CHAT_NAME'); ?>" />
						</div>
						<div class="col-sm-8">
							<div class="input-group">
								<input type="text" name="chat_user[]" id="<?php echo $APPTAG?>-chat_user" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_CHAT_USER'); ?>" />
								<span class="input-group-btn">
									<button type="button" class="btn btn-success base-icon-plus hasTooltip" title="<?php echo JText::_('TEXT_ADD_CHAT')?>" onclick="<?php echo $APPTAG?>_chatAdd()"></button>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-chatGroups" class="newFieldsGroup"></div>
				<div class="form-group">
					<label class="iconTip hasTooltip" title="<?php echo JText::_('FIELD_LABEL_WEBLINK_DESC')?>">Weblink</label>
					<div class="row no-gutters">
						<div class="col-sm-4 pr-1">
							<input type="text" name="weblink_text[]" id="<?php echo $APPTAG?>-weblink_text" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_WEBLINK_TEXT'); ?>" />
						</div>
						<div class="col-sm-8">
							<div class="input-group">
								<input type="text" name="weblink_url[]" id="<?php echo $APPTAG?>-weblink_url" class="form-control" placeholder="<?php echo JText::_('FIELD_LABEL_WEBLINK_URL'); ?>" />
								<span class="input-group-btn">
									<button type="button" class="btn btn-success base-icon-plus hasTooltip" title="<?php echo JText::_('TEXT_ADD_WEBLINK')?>" onclick="<?php echo $APPTAG?>_linkAdd()"></button>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div id="<?php echo $APPTAG?>-linkGroups" class="newFieldsGroup"></div>
			</div>
			<div class="col-lg-4 b-left b-default">
				<!--
					TODO: Mensagem explicativa sobre as opções
					<div class="row">
						<div class="col-sm-6 col-lg-12">
							<div class="form-group">
								<label>Skype</label>
								<input type="text" name="hangouts" id="<?php echo $APPTAG?>-hangouts" class="form-control" placeholder="<?php echo JText::_('TEXT_USER'); ?>" />
							</div>
						</div>
						<div class="col-sm-6 col-lg-12">
							<div class="form-group">
								<label>Gtalk/Hangouts</label>
								<input type="text" name="hangouts" id="<?php echo $APPTAG?>-hangouts" class="form-control" placeholder="<?php echo JText::_('TEXT_USER'); ?>" />
							</div>
						</div>
					</div>
				-->
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="<?php echo $APPTAG?>TabExtra" role="tabpanel" aria-labelledby="<?php echo $APPTAG?>Tab-extra">
		<div class="form-group">
			<textarea name="extra_info" id="<?php echo $APPTAG?>-extra_info" rows="4" class="form-control field-html" data-editor-full="true"></textarea>
		</div>
	</div>
</div>
