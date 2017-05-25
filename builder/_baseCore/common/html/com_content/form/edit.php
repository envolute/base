<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.modal', 'a.modal_jform_contenthistory');

// Create shortcut to parameters.
$params = $this->state->get('params');
//$images = json_decode($this->item->images);
//$urls = json_decode($this->item->urls);

// This checks if the editor config options have ever been saved. If they haven't they will fall back to the original settings.
$editoroptions = isset($params->show_publishing_options);
if (!$editoroptions)
{
	$params->show_urls_images_frontend = '0';
}
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'article.cancel' || document.formvalidator.isValid(document.getElementById('adminForm')))
		{
			<?php echo $this->form->getField('articletext')->save(); ?>
			Joomla.submitform(task);
		}
	}
</script>
<div class="edit item-page<?php echo $this->pageclass_sfx; ?>">
	<?php if ($params->get('show_page_heading', 1)) : ?>
	<h4 class="page-header">
		<?php echo $this->escape($params->get('page_heading')); ?>
	</h4>
	<?php endif; ?>

	<form action="<?php echo JRoute::_('index.php?option=com_content&a_id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
		<div class="btn-toolbar">
			<div class="btn-group">
				<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('article.save')">
					<span class="icon-ok"></span> <?php echo JText::_('JSAVE') ?>
				</button>
			</div>
			<div class="btn-group">
				<button type="button" class="btn" onclick="Joomla.submitbutton('article.cancel')">
					<span class="icon-cancel"></span> <?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
			<?php if ($params->get('save_history', 0)) : ?>
			<div class="btn-group">
				<?php echo $this->form->getInput('contenthistory'); ?>
			</div>
			<?php endif; ?>
		</div>
		<fieldset>
			<ul class="nav nav-tabs">
				<li class="active"><a href="#editor" data-toggle="tab"><?php echo JText::_('COM_CONTENT_ARTICLE_CONTENT') ?></a></li>
				<?php if ($params->get('show_urls_images_frontend') ) : ?>
				<li><a href="#images" data-toggle="tab"><?php echo JText::_('COM_CONTENT_IMAGES_AND_URLS') ?></a></li>
				<?php endif; ?>
				<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('COM_CONTENT_PUBLISHING') ?></a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="editor">
					<div class="row">
						<div class="col-sm-8">
							<?php echo $this->form->renderField('title'); ?>
						</div>
						<div class="col-sm-4">
							<?php echo $this->form->renderField('catid'); ?>
						</div>
					</div>
					<div class="clear"></div>
					<?php echo $this->form->getInput('articletext'); ?>
				</div>
				<?php if ($params->get('show_urls_images_frontend')): ?>
				<div class="tab-pane" id="images">
					<div class="row">
						<div class="col-sm-6">
							<?php echo $this->form->renderField('image_intro', 'images'); ?>
							<?php if ($params->get('show_urls_images_frontend')): ?>
								<div class="pull-left right-space">
									<?php echo $this->form->renderField('image_intro_caption', 'images'); ?>
								</div>
								<div class="pull-left right-space">
									<?php echo $this->form->renderField('float_intro', 'images'); ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="col-sm-6">
							<?php echo $this->form->renderField('image_fulltext', 'images'); ?>
							<?php if ($params->get('show_urls_images_frontend')): ?>
								<div class="pull-left right-space">
									<?php echo $this->form->renderField('image_fulltext_caption', 'images'); ?>
								</div>
								<div class="pull-left right-space bottom-space">
									<?php echo $this->form->renderField('float_fulltext', 'images'); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php endif; ?>
				<div class="tab-pane" id="publishing">
					<div class="row">
						<div class="col-sm-6">
							<div class="well well-sm pull-left">
								<?php if ($this->item->params->get('access-change')) : ?>
									<div class="pull-left right-space">
										<?php echo $this->form->renderField('publish_up'); ?>
									</div>
									<div class="pull-left">
										<?php echo $this->form->renderField('publish_down'); ?>
									</div>
								<?php endif; ?>
							</div>
							<div class="clear"></div>
							<div class="row">
								<div class="col-md-4">
									<?php echo $this->form->renderField('access'); ?>
								</div>
								<?php if ($this->item->params->get('access-change')) : ?>
									<div class="col-md-4">
										<?php echo $this->form->renderField('state'); ?>
									</div>
									<div class="col-md-4">
										<?php echo $this->form->renderField('featured'); ?>
									</div>
								<?php endif; ?>
								<div class="col-md-4">
									<?php echo $this->form->renderField('alias'); ?>
								</div>
								<div class="col-md-8">
									<?php echo $this->form->renderField('tags'); ?>
								</div>
							</div>
							<div class="clear"></div>
						</div>
						<div class="col-sm-6">
							<div class="well well-sm clearfix no-margin-bottom">

								<?php if (is_null($this->item->id)):?>
									<div class="control-group">
										<div class="control-label">
										</div>
										<div class="controls">
											<?php echo JText::_('COM_CONTENT_ORDERING'); ?>
										</div>
									</div>
								<?php endif; ?>
								<?php echo $this->form->renderField('created_by_alias'); ?>
								<?php echo $this->form->renderField('language'); ?>
								<?php echo $this->form->renderField('metadesc'); ?>
								<?php echo $this->form->renderField('metakey'); ?>

								<input type="hidden" name="task" value="" />
								<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
								<?php if ($this->params->get('enable_category', 0) == 1) :?>
								<input type="hidden" name="jform[catid]" value="<?php echo $this->params->get('catid', 1); ?>" />
								<?php endif; ?>
								<?php if ($params->get('save_history', 0)) : ?>
									<?php echo $this->form->renderField('version_note'); ?>
								<?php endif; ?>

							</div>
						</div>
					</div>
				</div>
			</div>
			<?php echo JHtml::_('form.token'); ?>
		</fieldset>
	</form>
</div>
