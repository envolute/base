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

// Your custom code here
?>

<script type="text/javascript">
function checkAll(bx) {
	var cbs = document.getElementsByTagName('input');
	for(var i=0; i < cbs.length; i++) {
		if(cbs[i].type == 'checkbox') {
			cbs[i].checked = bx.checked;
			Joomla.isChecked(cbs[i].checked);
		}
	}
}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="5">
					<?php echo JText::_( 'ID' ); ?>
				</th>
				<th width="20">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this);" />
				</th>
					<th>
					<?php echo JText::_( 'COM_SIMPLECHATSUPPORT_TITLE' ); ?>
				</th>
					<th>
					<?php echo JText::_( 'COM_SIMPLECHATSUPPORT_MESSAGE' ); ?>
				</th>
					<th>
					<?php echo JText::_( 'COM_SIMPLECHATSUPPORT_CREATED' ); ?>
				</th>
			</tr>
		</thead>
	<?php
$k = 0;
for ($i=0, $n=count( $this->items ); $i < $n; $i++)
{
	$row = $this->items[$i];
	$checked 	= JHTML::_('grid.id',   $i, $row->id );
	$link 		= JRoute::_( 'index.php?option=com_simplechatsupport&controller=messages&task=edit&cid='. $row->id );

	?>
		<tr class="<?php echo "row$k"; ?>">
	<td>
		<?php echo $row->id; ?>
	</td>
		<td>
		<?php echo $checked; ?>
	</td>
		<td>
		<a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
	</td>
		<td>
		<?php echo $row->message; ?>
	</td>   
		<td>				
		<?php echo $row->created_on; ?>
	</td>
		</tr>
		<?php
	$k = 1 - $k;
}
?>
	</table>
	
	<input type="hidden" name="option" value="com_simplechatsupport" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="messages" />
</form>