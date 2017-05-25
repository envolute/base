<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_users/helpers/route.php';

JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');

?>
<form action="<?php echo JRoute::_(JUri::getInstance()->toString(), true, $params->get('usesecure')); ?>" method="post" id="login-form">
	<?php if ($params->get('pretext')) : ?>
		<div class="pretext">
			<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; ?>
	<div class="userdata">
		<div id="form-login-username" class="form-group">
			<?php if (!$params->get('usetext')) : ?>
				<div class="input-group">
					<span class="input-group-addon">
						<span class="base-icon-user hasTooltip" title="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>"></span>
						<label for="modlgn-username" class="element-invisible"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME'); ?></label>
					</span>
					<input id="modlgn-username" type="text" name="username" class="form-control" tabindex="0" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
				</div>
			<?php else: ?>
				<label for="modlgn-username"><?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?></label>
				<input id="modlgn-username" type="text" name="username" class="form-control" tabindex="0" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
			<?php endif; ?>
		</div>
		<div id="form-login-password" class="form-group">
			<?php if (!$params->get('usetext')) : ?>
				<div class="input-group">
					<span class="input-group-addon">
						<span class="base-icon-lock hasTooltip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>">
						</span>
							<label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('JGLOBAL_PASSWORD'); ?>
						</label>
					</span>
					<input id="modlgn-passwd" type="password" name="password" class="form-control" tabindex="0" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
				</div>
			<?php else: ?>
				<label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
				<input id="modlgn-passwd" type="password" name="password" class="form-control" tabindex="0" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
			<?php endif; ?>
		</div>
		<?php if (count($twofactormethods) > 1): ?>
		<div id="form-login-secretkey" class="form-group">
			<?php if (!$params->get('usetext')) : ?>
				<div class="input-group">
					<span class="input-group-addon">
						<span class="base-icon-star hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>">
						</span>
							<label for="modlgn-secretkey" class="element-invisible"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?>
						</label>
					</span>
					<input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="form-control" tabindex="0" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>" />
					<span class="input-group-addon hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?>">
						<span class="base-icon-help"></span>
					</span>
			</div>
			<?php else: ?>
				<label for="modlgn-secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY') ?></label>
				<input id="modlgn-secretkey" autocomplete="off" type="text" name="secretkey" class="form-control" tabindex="0" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY') ?>" />
				<span class="btn btn-default hasTooltip" title="<?php echo JText::_('JGLOBAL_SECRETKEY_HELP'); ?>">
					<span class="base-icon-help"></span>
				</span>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div id="form-login-remember" class="checkbox pull-left">
			<label for="modlgn-remember" class="control-label"><input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/> <?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label>
		</div>
		<?php endif; ?>
		<div id="form-login-submit" class="pull-right">
				<button type="submit" tabindex="0" name="Submit" class="btn btn-primary base-icon-login"> <?php echo JText::_('JLOGIN') ?> </button>
		</div>
		<?php
			$usersConfig = JComponentHelper::getParams('com_users'); ?>
			<div class="clear"></div>
			<hr class="hr-sm" />
			<?php if ($usersConfig->get('allowUserRegistration')) : ?>
				<a class="btn btn-default btn-block right-space-sm bottom-space-sm" href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid=' . UsersHelperRoute::getRegistrationRoute()); ?>">
					<span class="base-icon-user-add"></span> <?php echo JText::_('MOD_LOGIN_REGISTER'); ?>
				</a>
			<?php endif; ?>
			<ul class="list-unstyled text-sm no-margin">
				<li class="text-sm">
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset&Itemid=' . UsersHelperRoute::getResetRoute()); ?>">
					<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
				</li>
				<!-- <li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind&Itemid=' . UsersHelperRoute::getRemindRoute()); ?>">
					<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_USERNAME'); ?></a>
				</li> -->
			</ul>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<?php if ($params->get('posttext')) : ?>
		<div class="posttext">
			<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form>
