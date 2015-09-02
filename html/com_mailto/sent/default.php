<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_mailto
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<fieldset>
	<legend class="page-header">
		<?php echo JText::_('COM_MAILTO_EMAIL_TO_A_FRIEND'); ?>
	</legend>
	
	<h4 class="alert alert-success">
		<?php echo JText::_('COM_MAILTO_EMAIL_SENT'); ?>
	</h4>
	
	<p>&nbsp;</p>
	<a id="mailto-close" class="btn btn-xs btn-danger" href="javascript: void window.close()" title="<?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?>"><?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?></a>
</fieldset>