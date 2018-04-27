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
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php
	// INITIALIZE JS FILES
	$jsInit	= $this->params->get('jsInit');
	require_once('templates/'.$this->template.'/_js.init.php');
	?>

	<jdoc:include type="head" />
	<link rel="stylesheet" href="<?php echo 'templates/'.$this->template.'/css/' ?>style.css" type="text/css" />
</head>
<body class="site offline" onload="document.getElementById('modlgn-username').focus()">
<jdoc:include type="message" />
	<div id="screen">
		<div class="text-center">
			<?php
			// LOGO
			if ($app->getCfg('offline_image')) :
				echo '<img class="logo-off img-retina m-3" src="'.$app->getCfg('offline_image').'" alt="'.htmlspecialchars($app->getCfg('sitename')).'" />';
			else :
				echo '<h1 class="title-off m-3">'.htmlspecialchars($app->getCfg('sitename')).'</h1>';
			endif;
			// MENSAGEM
			if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != '') :
				echo $app->getCfg('offline_message');
			elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != '') :
				echo JText::_('JOFFLINE_MESSAGE');
			endif;
			?>
		</div>
		<div class="login my-4 mx-auto" style="max-width:300px;">
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
		<div class="text-center">
			<a href="http://www.envolute.com" target="_blank" class="font-condensed text-xs text-muted new-window">
				ENVOLUTE
			</a>
		</div>
	</div>
</body>
</html>
