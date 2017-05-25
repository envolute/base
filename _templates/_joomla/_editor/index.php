<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app             = JFactory::getApplication();
$doc             = JFactory::getDocument();
$user            = JFactory::getUser();
$this->language  = $doc->language;
$this->direction = $doc->direction;

// Output as HTML5
$doc->setHtml5(true);

// Getting params from template
$params = $app->getTemplate(true)->params;

// Detecting Active Variables
$option   = $app->input->getCmd('option', '');
$view     = $app->input->getCmd('view', '');
$layout   = $app->input->getCmd('layout', '');
$task     = $app->input->getCmd('task', '');
$itemid   = $app->input->getCmd('Itemid', '');
$sitename = $app->get('sitename');

if($task == "edit" || $layout == "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');

$doc->addScriptVersion($this->baseurl . '/templates/' . $this->template . '/js/template.js');

// Add Stylesheets
$doc->addStyleSheetVersion($this->baseurl . '/templates/' . $this->template . '/css/template.css');
$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/custom.css');

// Load optional RTL Bootstrap CSS
JHtml::_('bootstrap.loadCss', false, $this->direction);
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<jdoc:include type="head" />
	<!--[if lt IE 9]><script src="<?php echo JUri::root(true); ?>/media/jui/js/html5.js"></script><![endif]-->
</head>
<body class="site fluid <?php echo $option
	. ' view-' . $view
	. ($layout ? ' layout-' . $layout : ' no-layout')
	. ($task ? ' task-' . $task : ' no-task')
	. ($itemid ? ' itemid-' . $itemid : '');
	echo ($this->direction == 'rtl' ? ' rtl' : '');
?>">
	<!-- Body -->
	<div class="body">
		<div class="container-fluid">
			<div>
				<a href="javascript:history.back()" class="btn btn-small btn-default hasTooltip" data-placement="right" title="<?php echo JText::_('TPL_EDITOR_BACK')?>">
					<span class="icon-chevron-left"></span>
				</a>
				<a href="<?php echo JURI::root()?>" class="btn btn-small btn-default pull-right hasTooltip" data-placement="left" title="<?php echo JText::_('TPL_EDITOR_HOME')?>">
					<span class="icon-home"></span>
				</a>
			</div>
			<hr class="hr-sm" />
			<div class="row-fluid">
				<main id="content" role="main">
					<!-- Begin Content -->
					<jdoc:include type="message" />
					<jdoc:include type="component" />
					<!-- End Content -->
				</main>
			</div>
		</div>
	</div>
	<jdoc:include type="modules" name="debug" style="none" />
</body>
</html>
