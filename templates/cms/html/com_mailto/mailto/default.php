<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mailto
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.getElementById('mailtoForm');

		// do field validation
		if (form.mailto.value == "" || form.from.value == "")
		{
			alert('<?php echo JText::_('COM_MAILTO_EMAIL_ERR_NOINFO'); ?>');
			return false;
		}
		form.submit();
	}
	
	// seta o focus no primeiro campo
	setTimeout(function() { document.getElementById('mailto_field').focus(); }, 10);
	
</script>
<?php
$data	= $this->get('data');
?>

<form action="<?php echo JURI::base() ?>index.php" name="mailtoForm" id="mailtoForm" method="post">
	
	<fieldset id="mailto-window">
		
		<legend>
			<?php echo JText::_('COM_MAILTO_EMAIL_TO_A_FRIEND'); ?>
			<a id="mailto-close" class="btn btn-xs btn-danger pull-right" href="javascript: void window.close()" title="<?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?>">
				<?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?>
			</a>
		</legend>
	
		<p><input type="text" id="mailto_field" name="mailto" class="inputbox" size="25" value="<?php echo $this->escape($data->mailto); ?>" placeholder="<?php echo JText::_('COM_MAILTO_EMAIL_TO'); ?>" /></p>
		
		<?php // se o usuário estiver logado, esconde os dois campos abaixo ?>
		<?php $type = (!$this->escape($data->sender)) ? 'text' : 'hidden'; ?>
		<?php //$type = 'text'; ?>
		
		<p><input type="<?php echo $type?>" id="sender_field" name="sender" class="inputbox" size="25" value="<?php echo $this->escape($data->sender); ?>" placeholder="<?php echo JText::_('COM_MAILTO_SENDER'); ?>" /></p>
		
		<p><input type="<?php echo $type?>" id="from_field" name="from" class="inputbox" size="25" value="<?php echo $this->escape($data->from); ?>" placeholder="<?php echo JText::_('COM_MAILTO_YOUR_EMAIL'); ?>" /></p>
		
		
		<p><input type="text" id="subject_field" name="subject" class="inputbox" size="25" value="<?php echo $this->escape($data->subject); ?>" placeholder="<?php echo JText::_('COM_MAILTO_SUBJECT'); ?>" /></p>
		
		<hr />
		
		<p>
			<button class="btn btn-primary" onclick="return Joomla.submitbutton('send');">
				<?php echo JText::_('COM_MAILTO_SEND'); ?>
			</button>
			<button class="btn btn-danger" onclick="window.close();return false;">
				<?php echo JText::_('COM_MAILTO_CANCEL'); ?>
			</button>
		</p>
		<input type="hidden" name="layout" value="<?php echo $this->getLayout();?>" />
		<input type="hidden" name="option" value="com_mailto" />
		<input type="hidden" name="task" value="send" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="link" value="<?php echo $data->link; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	
	</fieldset>

</form>