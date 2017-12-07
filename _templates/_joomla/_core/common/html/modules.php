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
function modChrome_noContainer($module, &$params, &$attribs)
{
	$mClass = !empty($params->get('moduleclass_sfx')) ? htmlspecialchars($params->get('moduleclass_sfx')) : '';
	if($module->content) echo $module->content;
}

function modChrome_base($module, &$params, &$attribs)
{
	$hTag		= $params->get('header_tag', 'h4');
	$hClass		= $params->get('header_class');
	$mClass    = !empty($params->get('moduleclass_sfx')) ? htmlspecialchars($params->get('moduleclass_sfx')) : '';
	if($module->content) :
		echo '<div id="module-'.$module->id.'" class="module '.$mClass.'">';
			if ((bool) $module->showtitle) echo '<'.$hTag.' class="'.$hClass.'">'.$module->title.'</'.$hTag.'>';
			echo $module->content;
		echo '</div>';
	endif;
}

function modChrome_portlet($module, &$params, &$attribs)
{
	$hClass		= $params->get('header_class');
	$mClass		= !empty($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';

	if(!empty($module->content)) :
		echo '<div id="module-'.$module->id.'" class="module portlet '.$mClass.' clearfix">';
		if ((bool) $module->showtitle) :
			echo '
			<header class="'.$hClass.'">
				<h6 class="portlet-title">'.$module->title.'</h6>
				<span class="btn-group">
					<button type="button" href="#portlet-'.$module->id.'" class="btn btn-link toggle-state toggle-icon" data-icon-default="base-icon-down-open" data-icon-active="base-icon-up-open" data-toggle="collapse" aria-expanded="false" aria-controls="portlet'.$module->id.'"></button>
				</span>
			</header>
			';
		endif;
		echo '
			<div id="portlet-'.$module->id.'" class="portlet-body collapse show clearfix">
				<div class="mod-base-content">'.$module->content.'</div>
			</div>
		</div>
		';
	endif;
}

function modChrome_card($module, &$params, &$attribs)
{
	$hTag		= $params->get('header_tag', 'h4');
	$hClass		= 'card-header '.$params->get('header_class');
	$mClass		= !empty($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';

	if (!empty ($module->content)) :
		echo '
		<div id="module-'.$module->id.'" class="module '.$mClass.' clearfix">
			<div class="card">
		';
		if ((bool) $module->showtitle) :
			echo '
			<'.$hTag.' class="'.$hClass.'">
				'.$module->title.'
			</'.$hTag.'>
			';
		endif;
		echo '
				<div class="card-body">
					'.$module->content.'
				</div>
			</div>
		</div>
		';
	endif;
}

function modChrome_cardPrimary($module, &$params, &$attribs)
{
	$hTag		= $params->get('header_tag', 'h4');
	$hClass		= 'card-header bg-primary '.$params->get('header_class');
	$mClass		= !empty($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';

	if (!empty ($module->content)) :
		echo '
		<div id="module-'.$module->id.'" class="module '.$mClass.' clearfix">
			<div class="card card-outline-primary">
		';
		if ((bool) $module->showtitle) :
			echo '
			<'.$hTag.' class="'.$hClass.'">
				'.$module->title.'
			</'.$hTag.'>
			';
		endif;
		echo '
				<div class="card-body">
					'.$module->content.'
				</div>
			</div>
		</div>
		';
	endif;
}

function modChrome_modal($module, &$params, &$attribs)
{
	$mTag		= $params->get('module_tag', 'div');
	$hTag		= htmlspecialchars($params->get('header_tag', 'h5'));
	$mClass		= !empty($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';
	$mhead		= '';
	$close		= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';

	if(!empty ($module->content)) :
		// IMPORTANTE:
		// Quando o link para edição de módulos está disponível, são adicionadas
		// classes na primeira tag do módulo. Dessa forma, foi adicionada tag '<span>'
		// para receber essas classes e conflito com a classe 'modal'...
		// '<span>' é para possibilitar que o módulo possa ser adicionado em qualquer Local
		// sem que haja quebra do layout...
		if ((bool) $module->showtitle) :
			$mhead = '
			<div class="modal-header">
				<'.$hTag.' class="modal-title">'.$module->title.'</'.$hTag.'>
				'.$close.'
			</div>
			';
			$close = '';
		endif;
		echo '
		<span>
			<div id="modal-'.$module->id.'" class="modal fade" tabindex="-1">
				<div class="modal-dialog">
					<div class="modal-content'.$mClass.'">
						'.$mhead.'
						<div class="modal-body clearfix">'.$close.$module->content.'</div>
						<!-- div class="modal-footer">
							<a href="#" class="btn" close" data-dismiss="modal">Close</a>
						</div-->
					</div>
				</div>
			</div>
		</span>
		';
	endif;
}

function modChrome_modal_sm($module, &$params, &$attribs)
{
	$mTag		= $params->get('module_tag', 'div');
	$hTag		= htmlspecialchars($params->get('header_tag', 'h5'));
	$mClass		= !empty($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';
	$mhead		= '';
	$close		= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';

	if(!empty ($module->content)) :
		// IMPORTANTE:
		// Quando o link para edição de módulos está disponível, são adicionadas
		// classes na primeira tag do módulo. Dessa forma, foi adicionada tag '<span>'
		// para receber essas classes e conflito com a classe 'modal'...
		// '<span>' é para possibilitar que o módulo possa ser adicionado em qualquer Local
		// sem que haja quebra do layout...
		if ((bool) $module->showtitle) :
			$mhead = '
			<div class="modal-header">
				<'.$hTag.' class="modal-title">'.$module->title.'</'.$hTag.'>
				'.$close.'
			</div>
			';
			$close = '';
		endif;
		echo '
		<span>
			<div id="modal-'.$module->id.'" class="modal fade" tabindex="-1">
				<div class="modal-dialog modal-sm">
					<div class="modal-content'.$mClass.'">
						'.$mhead.'
						<div class="modal-body clearfix">'.$close.$module->content.'</div>
						<!-- div class="modal-footer">
							<a href="#" class="btn" close" data-dismiss="modal">Close</a>
						</div-->
					</div>
				</div>
			</div>
		</span>
		';
	endif;
}

function modChrome_modal_lg($module, &$params, &$attribs)
{
	$mTag		= $params->get('module_tag', 'div');
	$hTag		= htmlspecialchars($params->get('header_tag', 'h5'));
	$mClass		= !empty($params->get('moduleclass_sfx')) ? ' '.htmlspecialchars($params->get('moduleclass_sfx')) : '';
	$mhead		= '';
	$close		= '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';

	if(!empty ($module->content)) :
		// IMPORTANTE:
		// Quando o link para edição de módulos está disponível, são adicionadas
		// classes na primeira tag do módulo. Dessa forma, foi adicionada tag '<span>'
		// para receber essas classes e conflito com a classe 'modal'...
		// '<span>' é para possibilitar que o módulo possa ser adicionado em qualquer Local
		// sem que haja quebra do layout...
		if ((bool) $module->showtitle) :
			$mhead = '
			<div class="modal-header">
				<'.$hTag.' class="modal-title">'.$module->title.'</'.$hTag.'>
				'.$close.'
			</div>
			';
			$close = '';
		endif;
		echo '
		<span>
			<div id="modal-'.$module->id.'" class="modal fade" tabindex="-1">
				<div class="modal-dialog modal-lg">
					<div class="modal-content'.$mClass.'">
						'.$mhead.'
						<div class="modal-body clearfix">'.$close.$module->content.'</div>
						<!-- div class="modal-footer">
							<a href="#" class="btn" close" data-dismiss="modal">Close</a>
						</div-->
					</div>
				</div>
			</div>
		</span>
		';
	endif;
}
?>
