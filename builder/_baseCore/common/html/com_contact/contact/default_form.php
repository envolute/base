<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

$user =& JFactory::getUser();

// preenche os campos caso essas variaveis sejam enviadas
$name	= ($_REQUEST['name'])?$_REQUEST['name']:$user->name;
$email	= ($_REQUEST['email'])?$_REQUEST['email']:$user->email;
$subj	= $_REQUEST['subj'];
$msg	= $_REQUEST['msg'];

// adicionando a url de um artigo na mensagem através da variável "$_REQUEST['aID']"
if($_REQUEST['aID'] != '') {
	$textLink	= $_REQUEST['label'];
	$lk		= JURI::root().'index.php?option=com_content&view=article&id='.$_REQUEST['aID'];
	$txt		= ($textLink) ? $textLink : 'Acesse';
	$msg		.= "\n\n".$txt.":\n".$lk."\n";
}

if (isset($this->error)) : ?>
	<div class="contact-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>

<div class="contact-form">
	<form id="contact-form" action="<?php echo JRoute::_('index.php'); ?>" method="post" class="form-validate form-horizontal">
		<fieldset>

			<div class="row">
				<div class="col-sm-10 col-sm-offset-2">
					<p class="list-info small top-space bottom-space"><?php echo JText::_('COM_CONTACT_FORM_LABEL'); ?></p>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-2"><?php echo $this->form->getLabel('contact_name'); ?></div>
				<div class="col-sm-10"><?php echo $this->form->getInput('contact_name',null,$name); ?></div>
			</div>
			<div class="form-group">
				<div class="col-sm-2"><?php echo $this->form->getLabel('contact_email'); ?></div>
				<div class="col-sm-10"><?php echo $this->form->getInput('contact_email',null,$email); ?></div>
			</div>
			<div class="form-group">
				<div class="col-sm-2"><?php echo $this->form->getLabel('contact_subject'); ?></div>
				<div class="col-sm-10"><?php echo $this->form->getInput('contact_subject',null,$subj); ?></div>
			</div>
			<div class="form-group">
				<div class="col-sm-2"><?php echo $this->form->getLabel('contact_message'); ?></div>
				<div class="col-sm-10"><?php echo $this->form->getInput('contact_message',null,$msg); ?>

					<?php if ($this->params->get('show_email_copy')) { ?>
						<div>
							<span class="checkbox-inline">
								<?php echo $this->form->getInput('contact_email_copy'); ?>
								<?php echo $this->form->getLabel('contact_email_copy'); ?>
							</span>
						</div>
					<?php } ?>
				</div>
			</div>
			<?php //Dynamically load any additional fields from plugins. ?>
			<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
				<?php if ($fieldset->name != 'contact'):?>
					<?php $fields = $this->form->getFieldset($fieldset->name);?>
					<?php foreach($fields as $field): ?>
							<?php if ($field->hidden): ?>
								<div>
									<?php echo $field->input;?>
								</div>
							<?php else:?>
								<div class="form-group">
									<div class="col-sm-2">
										<?php echo $field->label; ?>
										<?php
										if (!$field->required && $field->type != "Spacer")
										echo '('.JText::_('COM_CONTACT_OPTIONAL').')';
										?>
									</div>
									<div class="col-sm-10"><?php echo $field->input;?></div>
								</div>
							<?php endif;?>
					<?php endforeach;?>
				<?php endif ?>
			<?php endforeach;?>
			<div class="form-group top-expand-lg">
				<div class="col-sm-10 col-sm-offset-2">
					<button class="btn btn-lg btn-primary validate" type="submit"><?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></button>
					<input type="hidden" name="option" value="com_contact" />
					<input type="hidden" name="task" value="contact.submit" />
					<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
					<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
					<?php echo JHtml::_('form.token'); ?>
				</div>
			</div>
		</fieldset>
	</form>
</div>
