<?php
/**
 * @copyright	Copyright ? 2014 - All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @author		Joompolitan -> Envolute
 * @author mail	dev@envolute.com
 * @website		http://www.envolute.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<script type="text/javascript">
<!--
	function validateForm( frm ) {
		var valid = document.formvalidator.isValid(frm);
		if (valid == false) {
			// do field validation
			// your custom code here
			return false;
		} else {
			frm.submit();
		}
	}
// -->
</script>

<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<fieldset class="adminform">
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_SIMPLECHATSUPPORT_TITLE' ); ?>
			</div>
			<div class="controls">
				<input class="text_area" type="text" name="title" id="title" size="32" maxlength="250" value="<?php echo $this->item->title;?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_( 'COM_SIMPLECHATSUPPORT_MESSAGE' ); ?>
			</div>
			<div class="controls">
				<textarea class="text_area" name="message" id="message" cols="60" rows="12" maxlength="250" /><?php echo $this->item->message;?></textarea>
			</div>
		</div>
	</fieldset>
	<div class="clr"></div>
	
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="cat_id" value="<?php echo $this->item->cat_id; ?>"/>
	<input type="hidden" name="created_on" value="<?php echo date("y/m/d : H:i:s", Time()); ?>" />
	<input type="hidden" name="task" value="submit" />
	<input type="hidden" name="controller" value="messages" />
	<input type="hidden" name="option" value="com_simplechatsupport" />
	<?php echo JHTML::_( 'form.token' ); ?>

</form>