<?php
/**
 * @package     Joomla.Envolute
 * @subpackage  Templates.base
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// corrige o problema com instalações em subdiretório
$urlBase = (JURI::base(true)) ? JURI::base(true).'/' : '';

header('Location: '.$urlBase.'error?r='.base64_encode($this->error->getCode()));
exit;

?>