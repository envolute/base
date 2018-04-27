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
<body class="component-modal <?php echo $option.$view.$layout.$task.$itemid.$pageclass.$screen.' '. $access; ?>">

	<div class="wrapper-modal">
		<div class="container-fluid">
			<div class="row">
				<div class="col">
					<jdoc:include type="message" />
					<jdoc:include type="component" />
				</div>
			</div>
		</div>
	</div>

	<?php
	// set URL base to javascript files
	echo '<input type="hidden" id="baseurl" name="baseurl" value="'.$this->baseurl.'" />';
	?>

</body>
</html>
