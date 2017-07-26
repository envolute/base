<?php
/*
 * Fonticons: Fancy, Retinaized Icons for Joomla
 * @version	$Id: fonticons.php 1.0
 * @date 10/23/2012
 * @sikumbang
 * @site http://www.templateplazza.com
 * @package Joomla 2.5.x
 * @license GNU General Public License version 2 or later; see LICENSE.txt
*/

// no direct access
defined('_JEXEC') or die;

class plgButtonBaseicons extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	public function onDisplay($name)
	{
		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();
		$path = ($app->isAdmin()) ? '../' : '';
		
		JPluginHelper::importPlugin('content');
		$plugin = JPluginHelper::getPlugin('content', 'baseicons');
		$pluginParams = new JRegistry();
		if($plugin && isset($plugin->params)) $pluginParams->loadString($plugin->params);
		
		JHtml::_('behavior.modal');
		$button = new JObject;
		$button->set('modal', true);
		$button->set('link', $path.'plugins/editors-xtd/baseicons/icons_display.php');
		$button->set('text', JText::_('Icones'));		
		$button->set('name', 'copy'); 
		$button->set('options', "{handler: 'iframe', size: {x: 790, y: 500}}");
		return $button;
	}
}
?>