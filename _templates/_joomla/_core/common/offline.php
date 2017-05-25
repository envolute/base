<?php
/**
 * @package     Joomla.Site
 * @subpackage  Template.system
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$app = JFactory::getApplication();
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>

	<!-- load jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script type="text/javascript">
		jQuery.noConflict();
	</script>

	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo 'templates/'.$this->template.'/css/' ?>style.css" type="text/css" />
</head>
<body class="site offline" onload="document.getElementById('modlgn-username').focus()">
<jdoc:include type="message" />
	<div id="screen">
		<div id="frame" class="outline text-center">
			<?php
			if ($app->getCfg('offline_image')) :
				echo '<img class="logo-off p-3" src="'.$app->getCfg('offline_image').'" alt="'.htmlspecialchars($app->getCfg('sitename')).'" />';
			else :
				echo '<h1 class="title-off p-3">'.htmlspecialchars($app->getCfg('sitename')).'</h1>';
			endif;
			?>
			<div class="text-muted lh-1-2">
				<?php
				if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != '') :
					echo $app->getCfg('offline_message');
				elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != '') :
					echo JText::_('JOFFLINE_MESSAGE');
				endif;
				?>
			</div>
		</div>
		<div class="login" style="max-width:300px; margin:30px auto">
			<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
				<fieldset class="fieldset-embed p-4">
					<div id="form-login-username" class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<span class="base-icon-user hasTooltip" data-animation="false" title="<?php echo JText::_('JGLOBAL_USERNAME') ?>"></span>
								<label for="modlgn-username" hidden><?php echo JText::_('JGLOBAL_USERNAME') ?></label>
							</span>
							<input id="modlgn-username" type="text" name="username" class="form-control" tabindex="1" size="18" placeholder="<?php echo JText::_('JGLOBAL_USERNAME') ?>">
						</div>
					</div>
					<div id="form-login-password" class="form-group">
						<div class="input-group">
							<span class="input-group-addon">
								<i class="base-icon-lock hasTooltip" data-animation="false" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>"></i>
								<label for="modlgn-passwd" hidden><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
							</span>
							<input id="modlgn-passwd" type="password" name="password" class="form-control" tabindex="2" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>">
						</div>
					</div>
					<div class="form-check mb-3">
						<label for="modlgn-remember" class="form-check-label">
							<input id="modlgn-remember" type="checkbox" name="remember" class="form-check-input" value="yes">
							<?php echo JText::_('JGLOBAL_REMEMBER_ME') ?>
						</label>
					</div>
					<div id="form-login-submit">
						<button type="submit" tabindex="3" name="Submit" class="btn btn-success btn-block">
							<i class="base-icon-lock"></i> <?php echo JText::_('JLOGIN') ?>
						</button>
					</div>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.login" />
					<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
					<?php echo JHtml::_('form.token'); ?>
				</fieldset>
			</form>
		</div>
	</div>
</body>
</html>
