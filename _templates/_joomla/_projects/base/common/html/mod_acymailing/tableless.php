<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.8.1
 * @author	acyba.com
 * @copyright	(C) 2009-2017 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="acymailing_module_<?php echo $formName; ?>">
	<div id="acymailing_fulldiv_<?php echo $formName; ?>">
		<form id="<?php echo $formName; ?>" action="<?php echo acymailing_route('index.php'); ?>" onsubmit="return submitacymailingform('optin','<?php echo $formName;?>')" method="post" name="<?php echo $formName ?>" <?php if(!empty($fieldsClass->formoption)) echo $fieldsClass->formoption; ?> >
			<?php
			if(!empty($introText)) echo '<div class="acymailing_intro">'.$introText.'</div>';
			?>
			<div class="acymailing_form">
				<?php
				$tmpCatId = array();
				$tmpCatTag = array();
				foreach($fieldsToDisplay as $oneField){
					if($oneField == 'name' AND empty($extraFields[$oneField])) { ?>
						<div id="<?php echo 'field_'.$oneField.'_'.$formName?>" class="form-group">
							<input id="user_name_<?php echo $formName; ?>" <?php if(!empty($identifiedUser->userid)) echo 'readonly="readonly" ';?> onfocus="if(this.value == '<?php echo $nameCaption;?>') this.value = '';" onblur="if(this.value=='') this.value='<?php echo $nameCaption?>';" class="form-control form-control-lg b-warning" type="text" name="user[name]" value="<?php if(!empty($identifiedUser->userid)) echo $identifiedUser->name; else echo $nameCaption; ?>" title="<?php echo $nameCaption;?>"/>
						</div>
						<?php
					} elseif($oneField == 'email' AND empty($extraFields[$oneField])) { ?>
						<div id="<?php echo 'field_'.$oneField.'_'.$formName?>" class="form-group">
							<div class="input-group input-group-lg">
								<input id="user_email_<?php echo $formName; ?>" <?php if(!empty($identifiedUser->userid)) echo 'readonly="readonly" ';?> onfocus="if(this.value == '<?php echo $emailCaption;?>') this.value = '';" onblur="if(this.value=='') this.value='<?php echo $emailCaption?>';" class="form-control field-email b-warning" type="email" name="user[email]" value="<?php if(!empty($identifiedUser->userid)) echo $identifiedUser->email; else echo $emailCaption; ?>" title="<?php echo $emailCaption;?>" />
								<div class="input-group-btn">
									<?php if($params->get('showsubscribe',true) AND empty($identifiedUser->userid) AND empty($countUnsub)){?>
										<input class="subbutton btn btn-warning bg-live" type="submit" value="<?php $subtext = $params->get('subscribetextreg'); if(empty($identifiedUser->userid) OR empty($subtext)){ $subtext = $params->get('subscribetext',acymailing_translation('SUBSCRIBECAPTION')); } echo $subtext;  ?>" name="Submit" onclick="try{ return submitacymailingform('optin','<?php echo $formName;?>'); }catch(err){alert('The form could not be submitted '+err);return false;}"/>
									<?php } if($params->get('showunsubscribe',false) AND (!$params->get('showsubscribe',true) OR !empty($identifiedUser->userid) OR !empty($countUnsub)) ) {?>
										<button type="button" class="unsubbutton btn btn-danger base-icon-cancel hasTooltip" type="button" title="<?php echo $params->get('unsubscribetext',acymailing_translation('UNSUBSCRIBECAPTION')); ?>" name="Submit" onclick="return submitacymailingform('optout','<?php echo $formName;?>')"></button>
									<?php } ?>
								</div>
							</div>
						</div>
					<?php
					}
				}
				?>
			</div>
			<?php
			if(!empty($fieldsClass->excludeValue)){
				$js = "\n"."acymailing['excludeValues".$formName."'] = Array();";
				foreach($fieldsClass->excludeValue as $namekey => $value){
					$js .= "\n"."acymailing['excludeValues".$formName."']['".$namekey."'] = '".$value."';";
				}
				$js .= "\n";
				if($params->get('includejs','header') == 'header'){
					acymailing_addScript(true, $js);
				}else{
					echo "<script type=\"text/javascript\">
							<!--
							$js
							//-->
							</script>";
				}
			}

			$ajax = ($params->get('redirectmode') == '3') ? 1 : 0;?>
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
			<?php if(acymailing_getVar('cmd', 'tmpl') == 'component'){ ?>
				<input type="hidden" name="tmpl" value="component" />
				<?php if($params->get('effect','normal') == 'mootools-box' AND !empty($redirectUrl)){ ?>
					<input type="hidden" name="closepop" value="1" />
				<?php } } ?>
			<?php $myItemId = $config->get('itemid',0); if(empty($myItemId)){ global $Itemid; $myItemId = $Itemid;} if(!empty($myItemId)){ ?><input type="hidden" name="Itemid" value="<?php echo $myItemId;?>"/><?php } ?>
		</form>
	</div>
</div>
