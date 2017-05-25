<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidator');
?>
<div class="login">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h4 class="page-header">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h4>
	<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	<div class="login-description">
	<?php endif; ?>

		<?php if ($this->params->get('logindescription_show') == 1) : ?>
			<?php echo $this->params->get('login_description'); ?>
		<?php endif; ?>

		<?php if (($this->params->get('login_image') != '')) :?>
			<img src="<?php echo $this->escape($this->params->get('login_image')); ?>" class="login-image" alt="<?php echo JTEXT::_('COM_USERS_LOGIN_IMAGE_ALT')?>"/>
		<?php endif; ?>

	<?php if (($this->params->get('logindescription_show') == 1 && str_replace(' ', '', $this->params->get('login_description')) != '') || $this->params->get('login_image') != '') : ?>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.login'); ?>" method="post" class="form-validate">
		<fieldset class="fieldset-embed float-sm-left m-0">
			<legend><span class="base-icon-lock"></span> Login</legend>
			<?php foreach ($this->form->getFieldset('credentials') as $field) : ?>
				<?php if (!$field->hidden) : ?>
					<div class="form-group">
						<?php echo $field->label; ?><br />
						<?php echo $field->input; ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php if ($this->tfa): ?>
				<div class="form-group">
					<?php echo $this->form->getField('secretkey')->label; ?><br />
					<?php echo $this->form->getField('secretkey')->input; ?>
				</div>
			<?php endif; ?>

			<div class="form-inline mb-1">
				<button type="submit" class="btn btn-primary base-icon-login mr-1"> <?php echo JText::_('JLOGIN'); ?> </button>

				<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
				<div class="form-check">
					<label class="form-check-label">
						<input id="remember" type="checkbox" name="remember" class="form-check-input" value="yes"/> <?php echo JText::_('COM_USERS_LOGIN_REMEMBER_ME') ?>
					</label>
				</div>
				<?php endif; ?>
			</div>

			<?php if ($this->params->get('login_redirect_url')) : ?>
				<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_url', $this->form->getValue('return'))); ?>" />
			<?php else : ?>
				<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('login_redirect_menuitem', $this->form->getValue('return'))); ?>" />
			<?php endif; ?>
			<?php echo JHtml::_('form.token'); ?>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset&Itemid=' . UsersHelperRoute::getResetRoute()); ?>">
				<?php echo JText::_('COM_USERS_LOGIN_RESET'); ?>
			</a>
			<?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) :
			?>
				<div class="form-actions">
					<a class="btn btn-default btn-block" href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid=' . UsersHelperRoute::getRegistrationRoute()); ?>">
						<span class="base-icon-user-add"></span> <?php echo JText::_('COM_USERS_LOGIN_REGISTER'); ?>
					</a>
				</div>
			<?php endif; ?>
		</fieldset>
	</form>
</div>
