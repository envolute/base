<?php
/**
 * @version     1.9.3
 * @package     com_quicklogout
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 * Version 1.8 includes an addition by Chad Myers to allow the user to specify the logout redirect
 */

defined('_JEXEC') or die;

$url = base64_encode(JURI::root());
header('Location: ' . JURI::root(true) . '/index.php?option=com_users&task=user.logout&' .JSession::getFormToken(). '=1&return='.$url);
?>