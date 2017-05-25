<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Create a shortcut for params.
$params = &$this->item->params;

// View Base Params

$this->layout = $params->get('lead_item_layout');
$this->introLength = $params->get('lead_intro_length');
$this->dateFormat = $params->get('lead_date_format');
$this->imgW = ($params->get('lead_image_width')) ? str_replace('%','',str_replace('px','',$params->get('lead_image_width'))) : NULL;
$this->imgH = ($params->get('lead_image_height')) ? str_replace('%','',str_replace('px','',$params->get('lead_image_height'))) : NULL;
$this->imgClass = $params->get('lead_image_class');
$this->imgProps = $params->get('lead_image_props');
$this->imgDef = $params->get('lead_image_default');

echo $this->loadTemplate('item');

?>
