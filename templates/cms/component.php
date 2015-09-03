<?php
/**
 * Modals subtemplate
 *
 * @package         Modals
 * @version         5.1.0
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2014 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;// TEMPLATE PARAMS
include_once('_init.tpl.php');

?>
<body class="contentpane component <?php echo $option.$view.$layout.$task.$itemid.$pageclass.$screen.' '. $access; ?>">

	<div class="wrapper-modal">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</div>

	<?php
	// set URL base to javascript files
	echo '<input type="hidden" id="baseurl" name="baseurl" value="'.$this->baseurl.'" />';
	// call base javascript files
	require_once('templates/'.$this->template.'/_js.tpl.php');
	?>
</body>
</html>