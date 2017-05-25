<?php
/**
 * @package     Joomla.Envolute
 * @subpackage  Templates.base
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

header('Location: '.JURI::root().'error?r='.base64_encode($this->error->getCode()));
exit;

?>
