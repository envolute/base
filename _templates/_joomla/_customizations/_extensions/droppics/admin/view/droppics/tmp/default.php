<?php
/** 
 * Droppics
 * 
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and to customize.
 * Otherwise, please feel free to contact us at contact@joomunited.com *
 * @package Droppics
 * @copyright Copyright (C) 2013 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @copyright Copyright (C) 2013 Damien Barrère (http://www.crac-design.com). All rights reserved.
 * @license GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.colorpicker');
if(droppicsBase::isJoomla30()){
    JHtml::_('formbehavior.chosen', '.droppics-chosen-select');
}
JHtml::_('behavior.modal');

//$function	= JRequest::getCmd('function', 'jInsertGallery');

JText::script('COM_DROPPICS_JS_DROP_FILES_HERE');
JText::script('COM_DROPPICS_JS_DROP_FILES_HERE');
JText::script('COM_DROPPICS_JS_USE_UPLOAD_BUTTON');
JText::script('COM_DROPPICS_JS_ARE_YOU_SURE');
JText::script('COM_DROPPICS_JS_ARE_YOU_SURE_ALL');
JText::script('COM_DROPPICS_JS_DELETE');
JText::script('COM_DROPPICS_JS_INSERT_PICTURE');
JText::script('COM_DROPPICS_JS_INSERT_PICTURE_NO_ALIGN');
JText::script('COM_DROPPICS_JS_INSERT_PICTURE_ALIGN_LEFT');
JText::script('COM_DROPPICS_JS_INSERT_PICTURE_ALIGN_RIGHT');
JText::script('COM_DROPPICS_JS_EDIT');
JText::script('COM_DROPPICS_JS_BROWSER_NOT_SUPPORT_HTML5');
JText::script('COM_DROPPICS_JS_TOO_ANY_FILES');
JText::script('COM_DROPPICS_JS_FILE_TOO_LARGE');
JText::script('COM_DROPPICS_JS_ONLY_IMAGE_ALLOWED');
JText::script('COM_DROPPICS_JS_DBLCLICK_TO_EDIT_TITLE');
JText::script('COM_DROPPICS_JS_WANT_DELETE_GALLERY');
JText::script('COM_DROPPICS_JS_SELECT_FILES');
JText::script('COM_DROPPICS_JS_IMAGE_PARAMETERS');
JText::script('COM_DROPPICS_JS_CANCEL');
JText::script('COM_DROPPICS_JS_OK');
JText::script('COM_DROPPICS_JS_CONFIRM');
JText::script('COM_DROPPICS_JS_SAVE');
JText::script('COM_DROPPICS_JS_SAVED');
$doc = JFactory::getDocument();
$doc->addScriptDeclaration('gcaninsert='.(JRequest::getBool('caninsert',false)?'true':'false').';');
$doc->addScriptDeclaration('e_name="'.JRequest::getString('e_name').'";');

$collapse = DroppicsBase::getParam('catcollapsed',0);

$declaration = 
            "   if(typeof(Droppics)=='undefined'){"
          . "     Droppics={};"
          . "}"
          . "Droppics.can = {};"
          . "Droppics.can.create=".(int)$this->canDo->get('core.create').";"
          . "Droppics.can.edit=".(int)($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')).";"
          . "Droppics.can.delete=".(int)$this->canDo->get('core.delete').";"
        
          . "Droppics.baseurl='".COM_MEDIA_BASEURL."';"
          . "Droppics.collapse=".($collapse?'true':'false').";"
          . "Droppics.version='".droppicsComponentHelper::getVersion()."';"
        . "";
$doc->addScriptDeclaration($declaration);

$ownbootstrap = '';
if(JFactory::getApplication()->isSite() || !droppicsBase::isJoomla30()){
    $ownbootstrap = 'ownbootstrap';
}
?>
<div id="mybootstrap" class="<?php if(droppicsBase::isJoomla30()) {echo 'joomla30';} else {echo 'joomla25';} ?> <?php echo $ownbootstrap; ?>">
    <div id="main_wrapper">
        <?php echo $this->loadTemplate('cats'); ?>

        <div id="rightcol" class="">
            <?php if(JRequest::getBool('caninsert')): ?>
                <a id="insertgallery" class="btn btn-success btn-block" href=""><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_INSERT_GALLERY'); ?></a>
                <a id="insertimage" class="btn btn-success btn-block" style="display: none;" href="" ><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_INSERT_PICTURE'); ?></a>
            <?php endif; ?>

            <div>
                <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                <div class="themesblock">
                    <div class="well">
                        <h4><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_THEME'); ?></h4>
                        <?php 
                        JPluginHelper::importPlugin('droppics');
                        $dispatcher = JDispatcher::getInstance();
                        $themes = $dispatcher->trigger('getThemeName');
                        foreach ($themes as $theme): ?>
                        <a class="themebtn <?php echo strtolower($theme['name']); ?>" href="" data-theme="<?php echo $theme['id']; ?>"><?php echo $theme['name']; ?></a>
                        <?php endforeach; ?>
                        <div class="clrr"></div>
                    </div>

                    <div class="well">
                        <h4><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_PARAMETERS'); ?></h4>
                        <div id="galeryparams">

                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="imageblock" style="display: none;">
                    <?php if($this->canDo->get('core.delete')): ?>
                        <a id="deleteImage" href="" class="deleteImage btn btn-block btn-large"><i class="icon-trash"></i> <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_DELETE'); ?></a>
                    <?php endif; ?>
                    <?php if(JRequest::getBool('caninsert')): ?>
                    <div id="imageblock" class="well">
                            <h4><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE'); ?></h4>
                            <fieldset>
                                <div class="center">
                                    <img id="singleimage" />
                                </div>
                                <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN'); ?></h5>
                                <div class="floating">
                                    <label id="imgp_margin_left-lbl" for="imgp_margin_left"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN_LEFT'); ?></label>
                                    <span class="paraminput">
                                        <input type="text" name="imgp_margin_left" value="4" size="3" />
                                    </span>
                                </div>
                                <div class="floating">
                                    <label id="imgp_margin_top-lbl" for="imgp_margin_top"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN_TOP'); ?></label>
                                    <span class="paraminput">
                                        <input type="text" name="imgp_margin_top" value="4" size="3" />
                                    </span>
                                </div>
                                <div class="floating">
                                    <label id="imgp_margin_right-lbl" for="imgp_margin_right"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN_RIGHT'); ?></label>
                                    <span class="paraminput">
                                        <input type="text" name="imgp_margin_right" value="4" size="3" />
                                    </span>
                                </div>
                                <div class="floating">
                                    <label id="imgp_margin_bottom-lbl" for="imgp_margin_bottom"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_MARGIN_BOTTOM'); ?></label>
                                    <span class="paraminput">
                                        <input type="text" name="imgp_margin_bottom" value="4" size="3" />
                                    </span>
                                </div>
                                <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_BORDER'); ?></h5>
                                <div class="floating">
                                    <label id="imgp_border-lbl" for="imgp_border"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_BORDER_WIDTH'); ?></label>
                                    <span class="paraminput">
                                        <input type="text" name="imgp_border" value="0" size="3" />
                                    </span>
                                </div>
                                <div class="floating">
                                    <label id="imgp_radius-lbl" for="imgp_radius"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_RADIUS'); ?></label>
                                    <span class="paraminput">
                                        <input type="text" name="imgp_radius" value="3" size="3" />
                                    </span>
                                </div>
                                <div class="floating">
                                    <label id="imgp_border_color-lbl" for="imgp_border_color"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_BORDER_COLOR'); ?></label>
                                    <span class="paraminput">
                                        <input type="text" name="imgp_border_color" id="imgp_border_color" class="minicolors minicolors-input" value="#CCC" data-position="left" data-control="hue" />
                                    </span>
                                </div>
                                <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_SHADOW'); ?></h5>
                                <div class="floating">
                                    <label id="imgp_shadow-lbl" for="imgp_shadow"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_SHADOW_WIDTH'); ?></label>
                                    <span class="paraminput">
                                        <input type="text" name="imgp_shadow" value="0" size="3" />
                                    </span>
                                </div>
                                <div class="floating">
                                    <label id="imgp_shadow_color-lbl" for="imgp_shadow_color"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_SHADOW_COLOR'); ?></label>
                                    <span class="paraminput">                                    
                                        <input type="text" name="imgp_shadow_color" id="imgp_shadow_color" class="minicolors" value="#CCC" data-position="left" />
                                    </span>
                                </div>
                                <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK'); ?></h5>
                                <div>
                                    <div class="paraminput input-append w100">
                                        <select type="text" name="imgp_click" id="imgp_click">
                                            <option value="lightbox"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_LIGHTBOX'); ?></option>
                                            <option value="article"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_ARTICLE'); ?></option>
                                            <option value="menuitem"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_MENUITEM'); ?></option>
                                            <option value="custom"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_CUSTOM'); ?></option>
                                            <option value="nothing"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_NOTHING'); ?></option>
                                        </select>
                                        <span id="click_content_article" class="click_content_block" style="display: none;">
                                            <input type="text" class="input-medium" id="click_content_article_id_name" value="" disabled="disabled" size="35" />
                                            <a class="modal btn hasTooltip" title="" href="index.php?option=com_content&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle&<?php echo JSession::getFormToken(); ?>=1" rel="{handler: 'iframe', size: {x: 800, y: 450}}" data-original-title="Select or Change article"><i class="icon-file"></i> Select</a>
                                            <input type="hidden" id="click_content_article_id" class="required modal-value" name="jform[request][id]" value="2" aria-required="true" required="required">    
                                            <input type="hidden" id="click_content_article_link" name="jform[request][link]" value="">    
                                            <script type="text/javascript">
                                                function jSelectArticle(id, title, catid, object, link, lang) {
                                                    document.getElementById("click_content_article_id").value = id;
                                                    document.getElementById("click_content_article_id_name").value = title;
                                                    document.getElementById("click_content_article_link").value = link;
                                                    SqueezeBox.close();
                                                }
                                            </script>
                                        </span>
                                        <span id="click_content_menuitem" class="click_content_block" style="display:none;">
                                            <select style="width:250px;" name="click_content_menuitem" class="droppics-chosen-select" id="click_content_menuitem_id">
                                                    <option value=""><?php echo JText::_('COM_DROPPICS_VIEW_DROPPICS_SELECT') ?></option>
                                                    <?php echo JHtml::_('select.options', JHtml::_('menu.menuitems', array('published' => $published)));?>
                                            </select>
                                        </span>
                                        <span id="click_content_custom" class="click_content_block" style="display:none;">
                                            <input type="text" class="input-medium" id="click_content_custom_id" placeholder="http://" size="35" />
                                        </span>
                                    </div>
                                    <div class="paraminput input-append w100" id="imgp_click_target_wrap" style="display: none;">
                                        <label id="imgp_click_target-lbl" for="imgp_click_target"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_TARGET'); ?></label>
                                        <select type="text" name="imgp_click_target" id="imgp_click_target">
                                            <option value="current"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_TARGET_CURRENT'); ?></option>
                                            <option value="_blank"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_TARGET_BLANK'); ?></option>
                                            <option value="lightbox"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CLICK_TARGET_LIGHTBOX'); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="wauto nomargin">
                                    <label id="click_content_custom_title-lbl" for="click_content_custom_title"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_CAPTION'); ?> :</label>
                                    <input type="text" class="input-large" id="click_content_custom_title" size="35" />
                                </div>
                                <div class="wauto nomargin">
                                    <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_WIDTH'); ?></h5>
                                    <div id="imgp_source">
                                        <label class="radio">
                                            <input type="radio" name="imgp_source" value="thumbnail" checked="checked" />
                                            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_WIDTH_THUMBNAIL') ?>
                                            <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                                            <button class="btn btn-mini hidden editImage"><i class="icon-pencil"></i><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_EDIT') ?></button>
                                            <?php endif; ?>
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="imgp_source" value="original" />
                                            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_WIDTH_ORIGINAL') ?>
                                            <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                                            <button class="btn btn-mini hidden editImage"><i class="icon-pencil"></i><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_EDIT') ?></button>
                                            <?php endif; ?>
                                        </label>
                                        <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own') || $this->canDo->get('core.delete')): ?>
                                        <label class="radio template" style="display: none">
                                            <input type="radio" name="imgp_source" value="" />
                                            <span></span>
                                            <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                                            <button class="btn btn-mini hidden editImage"><i class="icon-pencil"></i> <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_EDIT') ?></button>
                                            <?php endif; ?>
                                            <?php if($this->canDo->get('core.delete')): ?>
                                            <button id="imgp_delete" class="btn btn-mini hidden deleteImage"><i class="icon-trash"></i> <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_DELETE') ?></button>
                                            <?php endif; ?>
                                        </label>
                                        <label class="radio">
                                            <input type="radio" name="imgp_source" value="custom" />
                                            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_WIDTH_NEW') ?>
                                        </label>
                                        <?php endif; ?>
                                    </div>
                                    <?php if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')): ?>
                                    <div class="form-inline" id="newCustomSize" style="display:none;">
                                        <div class="control-group">
                                            <div class="input-append"><input type="text" class="input-mini" id="customWidth"><span class="add-on">px</span></div> x <div class="input-append"><input type="text" class="input-mini" id="customHeight"><span class="add-on">px</span></div>
                                        </div>
                                         <div class="control-group">
                                            <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_SAVE_TO_FILENAME'); ?> : <input type="text" id="customFilename" class="input-large" />
                                        </div>
                                        <button id="applyNewCustomSize" class="btn"><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_EDIT_IMAGE_RESIZE'); ?></button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="floating wauto">
                                    <h5><?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_IMAGE_PARAM_POSITION'); ?></h5>
                                    <div id="imagealign" class="btn-group" data-toggle="buttons-radio">
                                        <button class="insertleft btn active" data-align="left" type="button" ><i class="icon-align-left"></i></button>
                                        <button class="insert btn" data-align="none" type="button" ><i class="icon-align-center"></i></button>
                                        <button class="insertright btn" data-align="right" type="button" ><i class="icon-align-right"></i></button>
                                    </div>
                                </div>
                            </fieldset>
                    </div>
                    <?php endif; ?>
                    <div id="imageparameters" class="">

                    </div>
                </div>
            </div>
        </div>
        <div id="pwrapper">
            <div class="form-inline">
            	<style>
    		#id_gallery {
			width: 80px!important;
			height: auto!important;
			min-height: 18px!important;
			padding: 0!important;
			line-height: 1.4!important;
			font-weight: bold!important;
			color: #f80!important;
			border: none!important;
			background: #fff!important;
			-webkit-box-shadow: none!important;
				box-shadow: none!important;
		}
            	</style>
            	<label><strong>Diretório das imagens:</strong> com_droppics /</label> <input type="text" id="id_gallery" name="id_gallery" value="" readonly="readonly" />
            </div>
            <div id="wpreview">
                <div id="preview"></div>        
            </div>
        </div>
    </div>
    
    <div id="picture_wrapper" style="display: none;">
        <?php echo $this->loadTemplate('picture'); ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function (){
            jQuery('#galleryparams .minicolors, #galleryparams .input-colorpicker').each(function() {
                    jQuery(this).minicolors({
                            control: jQuery(this).attr('data-control') || 'hue',
                            position: jQuery(this).attr('data-position') || 'right',
                            theme: 'bootstrap'
                    });
            });
            jQuery('#imageblock .minicolors, #imageblock .input-colorpicker').minicolors('destroy');
            jQuery('#imageblock .minicolors, #imageblock .input-colorpicker').each(function() {
                    $this = this;
                    jQuery(this).minicolors({
                            control: jQuery(this).attr('data-control') || 'hue',
                            position: jQuery(this).attr('data-position') || 'right',
                            theme: 'bootstrap',
                            change : function(){jQuery($this).trigger('change')}
                    });
                    jQuery(this).attr('maxlength',7);
            });
    });
</script>