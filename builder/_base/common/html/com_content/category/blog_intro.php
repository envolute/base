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
$this->leading = false;
$this->showTitle = $params->get('intro_show_title', 1);
$this->titlePosition = $params->get('title_position', 1);
$this->fontSize = $params->get('title_size', '1.2em');
$this->headerLength = $params->get('title_length');
$this->headerHeight = $params->get('title_height');
$this->infoPosition = $params->get('info_position', 1);
$this->showImage = $params->get('show_image');
$this->forceImageFloat = $params->get('force_image_float');
$this->imgW = ($params->get('image_width')) ? str_replace('%','',str_replace('px','',$params->get('image_width'))) : NULL;
$this->imgH = ($params->get('image_height')) ? str_replace('%','',str_replace('px','',$params->get('image_height'))) : NULL;
$this->thumbClass = $params->get('thumbnail_class', 1);
$this->imageStyle = $params->get('image_style');
$this->imgDef = $params->get('image_default');
$this->imgZoom = $params->get('image_zoom');
$this->showCategory = $params->get('show_item_category', 1);
$this->showAuthor = $params->get('intro_show_author');
$this->showDate = $params->get('intro_show_date');
$this->dateFormat = $params->get('intro_date_format');
$this->showHits = $params->get('intro_show_hits');
$this->showTags = $params->get('intro_show_tags');
$this->showPlugins = $params->get('show_plugins');
$this->showDescription = $params->get('show_item_description');
$this->descriptionPos = $params->get('item_description_position');
$this->introLength = $params->get('intro_length', 180);

echo $this->loadTemplate('item');

?>