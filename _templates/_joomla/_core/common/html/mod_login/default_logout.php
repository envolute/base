<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

// CUSTOM
// Carrega o arquivo de tradução do Template BASE
$lang = JFactory::getLanguage();
$lang->load('tpl_base');
?>
<form action="<?php echo JRoute::_(JUri::getInstance()->toString(), true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-vertical">
<?php if ($params->get('greeting')) : ?>
	<h5 class="text-center mb-3">
		<?php echo JText::_('TPL_BASE_LOGOUT_DESC'); ?>
	</h5>
<?php endif; ?>
	<div class="logout-button">
		<button type="submit" name="Submit" class="btn btn-danger btn-block base-icon-cancel-circled"> <?php echo JText::_('JLOGOUT') ?> </button>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
