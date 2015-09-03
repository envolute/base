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

// view gallery params
$this->leading = true;
$this->showTitle = $params->get('lead_show_title', 1);
$this->titlePosition = $params->get('lead_title_position');
$this->fontSize = $params->get('lead_title_size', '1.6em');
$this->infoPosition = $params->get('lead_info_position', 1);
$this->showImage = $params->get('lead_show_image');
$this->forceImageFloat = $params->get('lead_force_image_float');
$this->imgW = ($params->get('lead_image_width')) ? str_replace('%','',str_replace('px','',$params->get('lead_image_width'))) : NULL;
$this->imgH = ($params->get('lead_image_height')) ? str_replace('%','',str_replace('px','',$params->get('lead_image_height'))) : NULL;
$this->thumbClass = $params->get('lead_thumbnail_class');
$this->imageStyle = $params->get('lead_image_style');
$this->imgDef = $params->get('lead_image_default');
$this->imgZoom = $params->get('lead_image_zoom');
$this->showCategory = $params->get('lead_show_item_category');
$this->showAuthor = $params->get('lead_show_author');
$this->showDate = $params->get('lead_show_date', 1);
$this->dateFormat = $params->get('lead_date_format');
$this->showHits = $params->get('lead_show_hits');
$this->showTags = $params->get('lead_show_tags');
$this->showPlugins = $params->get('lead_show_plugins');
$this->showDescription = $params->get('lead_show_item_description');
$this->introLength = $params->get('lead_intro_length');

echo $this->loadTemplate('item');

?>