<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="registration-complete">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h4 class="page-header">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h4>
	<?php endif; ?>
</div>
