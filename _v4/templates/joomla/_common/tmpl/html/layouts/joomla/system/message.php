<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$msgList = $displayData['msgList'];

?>
<div id="system-message-container">
	<?php if (is_array($msgList) && !empty($msgList)) : ?>
		<div id="system-message">
			<?php foreach ($msgList as $type => $msgs) : ?>
        <?php
        // Tipos de mensagens do Joomla
        $type = ($type == 'notice') ? 'info' : $type;
        $type = ($type == 'message') ? 'success' : $type;
        $type = ($type == 'warning') ? 'warning' : $type;
        $type = ($type == 'error') ? 'danger' : $type;
        ?>
				<div class="alert alert-<?php echo $type; ?>">
					<?php // This requires JS so we should add it trough JS. Progressive enhancement and stuff. ?>
					<a class="close" data-dismiss="alert">×</a>
					<?php if (!empty($msgs)) : ?>
							<?php foreach ($msgs as $msg) : ?>
								<div><?php echo $msg; ?></div>
							<?php endforeach; ?>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
