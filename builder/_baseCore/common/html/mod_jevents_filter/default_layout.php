<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: default_layout.php 3323 2012-03-08 13:37:46Z geraintedwards $
 * @package     JEvents
 * @subpackage  Module JEvents Filter
 * @copyright   Copyright (C) 2008 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.gwesystems.com
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
if (count($filterHTML)>0){
	JEVHelper::script("mod_jevents_filter.js","modules/mod_jevents_filter/",true);
	?>
	<form action="<?php echo $form_link;?>" id="jeventspost" name="jeventspost" method="post">
	<?php
		// This forces category settings in URL to reset too since they could be set by SEF 
		$script = "try {JeventsFilters.filters.push({id:'catidsfv',value:0});} catch (e) {}\n";
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($script);
	?>
	<input type='hidden' name='catids' id='catidsfv' value='<?php echo trim($datamodel->catidsOut);?>' />
	<?php	
	
	foreach ($filterHTML as $filter){
		if (!isset($filter["title"])) continue;
		if (strlen($filter["title"])>0) echo $filter["title"];
		echo "<div>".$filter["html"]."</div>";
	}
	
	echo '
	<div class="form-actions">
		<input class="btn btn-primary btn-sm" type="submit" value="'.JText::_('JSEARCH_FILTER_SUBMIT').'" />&nbsp;
		<input class="btn btn-danger btn-sm" type="button" onclick="JeventsFilters.reset(this.form)" value="'.JText::_('JSEARCH_FILTER_CLEAR').'" />
	</div>
	';
	?>
	</form>
	<?php 
}