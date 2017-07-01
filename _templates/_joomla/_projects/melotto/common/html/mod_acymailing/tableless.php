<?php defined('_JEXEC') or die('Restricted access');?><div class="acymailing_module" id="acymailing_module_<?php echo $formName; ?>">
	<?php	$style = array();	if($params->get('textalign','none') != 'none') $style[] .= 'text-align:'.$params->get('textalign');	$styleString = empty($style) ? '' : 'style="'.implode(';',$style).'"';	?>
	<form id="<?php echo $formName; ?>" action="<?php echo JRoute::_('index.php'); ?>" onsubmit="return submitacymailingform('optin','<?php echo $formName;?>')" method="post" name="<?php echo $formName ?>" <?php if(!empty($fieldsClass->formoption)) echo $fieldsClass->formoption; ?> >
		<?php		if(!empty($introText)) echo '<div class="acymailing_introtext">'.$introText.'</div>';		if(!empty($visibleListsArray) && $listPosition == 'before') echo $listContent;
		$tmpCatId = array();		$tmpCatTag = array();
		foreach($fieldsToDisplay as $oneField){			if(empty($extraFields[$oneField])) echo '<p class="onefield fieldacy'.$oneField.'" id="field_'.$oneField.'_'.$formName.'">';			if($oneField == 'name' AND empty($extraFields[$oneField])){
				if($displayOutside) echo '<label for="user_name_'.$formName.'" class="acy_requiredField">'.$nameCaption.'</label>';
		?>
				<span class="acyfield_<?php echo $oneField. (!$displayOutside? ' acy_requiredField':''); ?>"><input id="user_name_<?php echo $formName; ?>" <?php if(!empty($identifiedUser->userid)) echo 'readonly="readonly" '; if(!$displayOutside){ ?> onfocus="if(this.value == '<?php echo $nameCaption;?>') this.value = '';" onblur="if(this.value=='') this.value='<?php echo $nameCaption?>';"<?php } ?> class="form-control bg-primary text-gray-lighter" type="text" name="user[name]" value="<?php if(!empty($identifiedUser->userid)) echo $identifiedUser->name; elseif(!$displayOutside) echo $nameCaption; ?>" title="<?php echo $nameCaption;?>"/></span>
		<?php			}elseif($oneField == 'email' AND empty($extraFields[$oneField])){
				if($displayOutside) echo '<label for="user_email_'.$formName.'" class="acy_requiredField">'.$emailCaption.'</label>';		?>				<span class="acyfield_<?php echo $oneField. (!$displayOutside? ' acy_requiredField':''); ?>">
					<input id="user_email_<?php echo $formName; ?>" <?php if(!empty($identifiedUser->userid)) echo 'readonly="readonly" '; if(!$displayOutside){ ?> onfocus="if(this.value == '<?php echo $emailCaption;?>') this.value = '';" onblur="if(this.value=='') this.value='<?php echo $emailCaption?>';"<?php } ?> class="form-control bg-primary text-gray-lighter" type="text" name="user[email]" value="<?php if(!empty($identifiedUser->userid)) echo $identifiedUser->email; elseif(!$displayOutside) echo $emailCaption; ?>" title="<?php echo $emailCaption;?>" />				</span>		<?php			}			if(empty($extraFields[$oneField])) echo '</p>';		}		?>
		<div class="acysubbuttons">			<?php if($params->get('showsubscribe',true)){?>
				<input class="button subbutton btn btn-primary" type="submit" value="<?php $subtext = $params->get('subscribetextreg'); if(empty($identifiedUser->userid) OR empty($subtext)){ $subtext = $params->get('subscribetext',acymailing_translation('SUBSCRIBECAPTION')); } echo $subtext;  ?>" name="Submit" onclick="try{ return submitacymailingform('optin','<?php echo $formName;?>'); }catch(err){alert('The form could not be submitted '+err);return false;}"/>
			<?php }if($params->get('showunsubscribe',false) AND (!$params->get('showsubscribe',true) OR empty($identifiedUser->userid) OR !empty($countUnsub)) ){?>
				<input class="button unsubbutton btn btn-inverse" type="button" value="<?php echo $params->get('unsubscribetext',acymailing_translation('UNSUBSCRIBECAPTION')); ?>" name="Submit" onclick="return submitacymailingform('optout','<?php echo $formName;?>')"/>
			<?php } ?>
		</div>
		<?php		if(!empty($fieldsClass->excludeValue)){			$js = "\n"."acymailing['excludeValues".$formName."'] = Array();";
			foreach($fieldsClass->excludeValue as $namekey => $value){
				$js .= "\n"."acymailing['excludeValues".$formName."']['".$namekey."'] = '".$value."';";
			}
			$js .= "\n";
			$doc = JFactory::getDocument();
			if($params->get('includejs','header') == 'header'){
				$doc->addScriptDeclaration( $js );
			}else{
				echo "
				<script type=\"text/javascript\">
					<!--
					$js
					//-->
				</script>
				";
			}
		}

		if(!empty($postText)) echo '<div class="acymailing_finaltext">'.$postText.'</div>';		$ajax = ($params->get('redirectmode') == '3') ? 1 : 0;?>
		<input type="hidden" name="ajax" value="<?php echo $ajax; ?>"/>
		<input type="hidden" name="acy_source" value="<?php echo 'module_'.$module->id ?>" />
		<input type="hidden" name="ctrl" value="sub"/>
		<input type="hidden" name="task" value="notask"/>
		<input type="hidden" name="redirect" value="<?php echo urlencode($redirectUrl); ?>"/>
		<input type="hidden" name="redirectunsub" value="<?php echo urlencode($redirectUrlUnsub); ?>"/>
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT ?>"/>
		<?php if(!empty($identifiedUser->userid)){ ?><input type="hidden" name="visiblelists" value="<?php echo $visibleLists;?>"/><?php } ?>
		<input type="hidden" name="hiddenlists" value="<?php echo $hiddenLists;?>"/>
		<input type="hidden" name="acyformname" value="<?php echo $formName; ?>" />
		<?php $myItemId = $config->get('itemid',0); if(empty($myItemId)){ global $Itemid; $myItemId = $Itemid;} if(!empty($myItemId)){ ?><input type="hidden" name="Itemid" value="<?php echo $myItemId;?>"/><?php } ?>	</form>

</div>