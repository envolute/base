<?php
/**
 * @copyright  Copyright (C) 2012 Open Source Matters. All rights reserved.
 * @license    GNU/GPL, see LICENSE.php
 * Developed by Ivo Junior.
 */

// no direct access
defined('_JEXEC') or die;

class statusChat {
	
	// REMOVE ACENTOS DAS PALAVRAS
	public static function getStatus() {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT status FROM #__simplechatsupport_status');
		$status = $db->loadResult();
		return $status;
	}
}

?>