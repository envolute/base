<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// REDIRECIONAMENTO:
// -> Essa tela não precisa ser visualizada se o usuário estiver logado.
// -> Nesse caso, ele é redirecionado para o seu perfil
$app = JFactory::getApplication();
$user = JFactory::getUser();
if(!$user->guest) :
	$app->redirect(JURI::base().'profile', false);
	return;
endif;

?>
<div class="logout<?php echo $this->pageclass_sfx?>">
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h5 class="page-header">
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h5>
	<?php endif; ?>

	<?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description')) != '')|| $this->params->get('logout_image') != '') : ?>
	<div class="logout-description pb-1">
	<?php endif; ?>

		<?php if (($this->params->get('logout_image') != '')) :?>
			<img src="<?php echo $this->escape($this->params->get('logout_image')); ?>" class="obj-to-left img-responsive logout-image" alt="<?php echo JTEXT::_('COM_USER_LOGOUT_IMAGE_ALT')?>"/>
		<?php endif; ?>

		<?php if ($this->params->get('logoutdescription_show') == 1) : ?>
			<?php echo $this->params->get('logout_description'); ?>
		<?php endif; ?>

	<?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description')) != '')|| $this->params->get('logout_image') != '') : ?>
	</div>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.logout'); ?>" method="post">
		<button type="submit" class="btn btn-danger"><span class="base-icon-cancel"></span> <?php echo JText::_('JLOGOUT'); ?> </button>
		<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('logout_redirect_url', $this->form->getValue('return'))); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
