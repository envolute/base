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

	<h5 class="alert alert-success">
		<span class="base-icon-ok"></span>
		<?php echo JText::_('COM_MAILTO_EMAIL_SENT'); ?>
	</h5>
	<a id="mailto-close" class="btn btn-default" href="javascript: void window.close()" title="<?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?>">
		<span class="base-icon-cancel"></span> 
		<?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?>
	</a>
</fieldset>
