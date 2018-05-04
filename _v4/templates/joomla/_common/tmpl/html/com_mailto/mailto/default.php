<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mailto
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.core');
JHtml::_('behavior.keepalive');

$data = $this->get('data');

JFactory::getDocument()->addScriptDeclaration("
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.getElementById('mailtoForm');

		// do field validation
		if (form.mailto.value == '' || form.from.value == '')
		{
			alert('" . JText::_('COM_MAILTO_EMAIL_ERR_NOINFO') . "');
			return false;
		}
		form.submit();
	}
");
?>

<form action="<?php echo JURI::base() ?>index.php" name="mailtoForm" id="mailtoForm" method="post">

	<fieldset id="mailto-window" class="fieldset-embed">

		<legend>
			<span class="base-icon-paper-plane"></span> <?php echo JText::_('COM_MAILTO_EMAIL_TO_A_FRIEND'); ?>
		</legend>

		<div class="form-group">
			<input type="text" id="mailto_field" name="mailto" class="form-control" size="25" value="<?php echo $this->escape($data->mailto); ?>" placeholder="<?php echo JText::_('COM_MAILTO_EMAIL_TO'); ?>" />
		</div>

		<?php // se o usuário estiver logado, esconde os dois campos abaixo ?>
		<?php $type = (!$this->escape($data->sender)) ? 'text' : 'hidden'; ?>
		<?php //$type = 'text'; ?>

		<div class="form-group">
			<input type="<?php echo $type?>" id="sender_field" name="sender" class="form-control" size="25" value="<?php echo $this->escape($data->sender); ?>" placeholder="<?php echo JText::_('COM_MAILTO_SENDER'); ?>" />
		</div>
		<div class="form-group">
			<input type="<?php echo $type?>" id="from_field" name="from" class="form-control" size="25" value="<?php echo $this->escape($data->from); ?>" placeholder="<?php echo JText::_('COM_MAILTO_YOUR_EMAIL'); ?>" />
		</div>
		<div class="form-group">
			<input type="text" id="subject_field" name="subject" class="form-control" size="25" value="<?php echo $this->escape($data->subject); ?>" placeholder="<?php echo JText::_('COM_MAILTO_SUBJECT'); ?>" />
		</div>

		<div class="form-actions">
			<button class="btn btn-primary" onclick="return Joomla.submitbutton('send');">
				<?php echo JText::_('COM_MAILTO_SEND'); ?>
			</button>
			<button class="btn btn-default" onclick="window.close();return false;">
				<?php echo JText::_('COM_MAILTO_CANCEL'); ?>
			</button>
		</div>
		<input type="hidden" name="layout" value="<?php echo htmlspecialchars($this->getLayout(), ENT_COMPAT, 'UTF-8'); ?>" />
		<input type="hidden" name="option" value="com_mailto" />
		<input type="hidden" name="task" value="send" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="link" value="<?php echo $data->link; ?>" />
		<?php echo JHtml::_('form.token'); ?>

	</fieldset>

</form>
