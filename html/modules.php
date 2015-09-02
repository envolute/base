<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_no($module, &$params, &$attribs)
{
	if ($module->content) {
		echo $module->content;
	}
}

function modChrome_base($module, &$params, &$attribs)
{
	$bootstrapSize  = (int) $params->get('bootstrap_size', 0);
	$moduleClass    = $bootstrapSize != 0 ? ' col-sm' . $bootstrapSize : '';
	if ($module->content) {
		echo '<div id="module-'.$module->id.'" class="'.htmlspecialchars($params->get('moduleclass_sfx')).$moduleClass.'">'.$module->content.'</div>';
	}
}

function modChrome_mod($module, &$params, &$attribs) {
	$mTag		= $params->get('module_tag', 'div');
	$hTag		= $params->get('header_tag', 'h3');
	$hClass		= 'page-header clearfix '.$params->get('header_class');
	$bootSize	= (int) $params->get('bootstrap_size', 0);
	$mClass		= $bootSize != 0 ? ' col-sm' . $bootSize : '';
	$mClass		= ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')).$mClass : $mClass;

	if (!empty ($module->content)) :
		echo '<'.$mTag.' id="module-'.$module->id.'" class="module '.$mClass.' clearfix">';
			if ((bool) $module->showtitle) :
				echo '
				<'.$hTag.' class="'.$hClass.'">
					<span class="head-container">'.$module->title.'</span>
					<span class="head-tag"></span>
				</'.$hTag.'>
				';
			endif;
			echo '
			<div class="module-body">
				<div class="module-content">'.$module->content.'</div>
			</div>		
			';
		echo '</'.$mTag.'>';

	endif;
}

function modChrome_well($module, &$params, &$attribs) {
	$mTag		= $params->get('module_tag', 'div');
	$hTag		= $params->get('header_tag', 'h3');
	$hClass		= 'page-header '.$params->get('header_class');
	$bootSize	= (int) $params->get('bootstrap_size', 0);
	$mClass		= $bootSize != 0 ? ' col-sm' . $bootSize : '';
	$mClass		= ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')).$mClass : $mClass;

	if (!empty ($module->content)) :
		echo '<'.$mTag.' id="module-'.$module->id.'" class="module '.$mClass.'">';
		echo '	<div class="well clearfix">';
			if ((bool) $module->showtitle)
			echo '	<'.$hTag.' class="'.$hClass.'">'.$module->title.'<span class="head-tag"></span></'.$hTag.'>';
			echo '	<div class="module-content">'.$module->content.'</div>';
		echo '	</div>';
		echo '</'.$mTag.'>';

	endif;
}

function modChrome_well_sm($module, &$params, &$attribs) {
	$mTag		= $params->get('module_tag', 'div');
	$hTag		= $params->get('header_tag', 'h3');
	$hClass		= 'page-header '.$params->get('header_class');
	$bootSize	= (int) $params->get('bootstrap_size', 0);
	$mClass		= $bootSize != 0 ? ' col-sm' . $bootSize : '';
	$mClass		= ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')).$mClass : $mClass;

	if (!empty ($module->content)) :
		echo '<'.$mTag.' id="module-'.$module->id.'" class="module '.$mClass.'">';
		echo '	<div class="well well-sm clearfix">';
			if ((bool) $module->showtitle)
			echo '	<'.$hTag.' class="'.$hClass.'">'.$module->title.'<span class="head-tag"></span></'.$hTag.'>';
			echo '	<div class="module-content">'.$module->content.'</div>';
		echo '	</div>';
		echo '</'.$mTag.'>';

	endif;
}

function modChrome_panel($module, &$params, &$attribs) {
	$mTag		= $params->get('module_tag', 'div');
	$hTag		= $params->get('header_tag', 'h3');
	$hClass		= 'page-header '.$params->get('header_class');
	$bootSize	= (int) $params->get('bootstrap_size', 0);
	$mClass		= $bootSize != 0 ? ' col-sm' . $bootSize : '';
	$mClass		= ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')).$mClass : $mClass;

	if (!empty ($module->content)) :
		echo '<'.$mTag.' id="module-'.$module->id.'" class="module '.$mClass.'">';
		echo '	<div class="panel panel-default clearfix">';
			if ((bool) $module->showtitle)
			echo '	<div class="panel-heading">'.$module->title.'</div>';
			echo '	<div class="panel-body module-content">'.$module->content.'</div>';
		echo '	</div>';
		echo '</'.$mTag.'>';

	endif;
}

function modChrome_collapse($module, &$params, &$attribs) {
	$bootSize	= (int) $params->get('bootstrap_size', 0);
	$mClass		= $bootSize != 0 ? ' col-sm' . $bootSize : '';
	$mClass		= ($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')).$mClass : $mClass;

	if (!empty ($module->content)) :
		echo '<div id="module-'.$module->id.'" class="module panel-group collapse-indicator '.$mClass.'">';
		echo '	<div class="panel panel-default clearfix">';
			if ((bool) $module->showtitle)
			echo '
				<a href="#panel-body-'.$module->id.'" class="panel-heading display-block" role="button" data-toggle="collapse" aria-expanded="false" aria-controls="panel-body-'.$module->id.'">
					<div class="panel-title">'.$module->title.'</div>
				</a>
			';
			echo '
				<div id="panel-body-'.$module->id.'" class="collapse">
					<div class="panel-body module-content">'.$module->content.'</div>
				</div>
			';
		echo '	</div>';
		echo '</div>';
	endif;
}

function modChrome_modal($module, &$params, &$attribs)
{
	$mTitle    = htmlspecialchars(str_replace(' ','',strtolower($module->title)));
	$mTag      = $params->get('module_tag', 'div');
	$hTag      = htmlspecialchars($params->get('header_tag', 'h3'));

	if (!empty ($module->content)){
	
		echo '<div id="'.$mTitle.'" class="modal fade '.htmlspecialchars($params->get('moduleclass_sfx')).'" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title">'.$module->title.'</h4>
					</div>
					<div class="modal-body clearfix">'.$module->content.'</div>
					<!-- div class="modal-footer">
						<a href="#" class="btn" close" data-dismiss="modal">Close</a>
					</div-->
				</div>
			</div>
		</div>';

	}
}
?>