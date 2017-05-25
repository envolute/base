<?php 
defined('_JEXEC') or die('Restricted access');
?>

<div id="jevents-search">
	<form action="<?php echo JRoute::_("index.php?option=".JEV_COM_COMPONENT."&task=search.results&Itemid=".$this->Itemid);?>" method="post" >
		<input type="text" name="keyword" size="30" maxlength="50" class="inputbox" value="<?php echo $this->keyword;?>" />
		<input class="btn btn-primary" type="submit" name="push" value="<?php echo JText::_('JEV_SEARCH_TITLE'); ?>" />
		<label for="showpast">
			<input type="checkbox" id="showpast" name="showpast" value="1" <?php echo JRequest::getInt('showpast',0)?'checked="checked"':''?> />
			<?php echo JText::_("JEV_SHOW_PAST");?>
		</label>
		<hr />
	</form>
</div>