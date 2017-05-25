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

//-- No direct access
defined('_JEXEC') || die('=;)');


JLoader::register('droppicsPluginBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsPluginBase.php');

/**
 * Content Plugin.
 *
 * @package    droppics
 * @subpackage Plugin
 */
class plgDroppicsBxslider extends droppicsPluginBase
{
    
    public $name = 'bxslider';
    protected $options;

    
    public function onShowFrontGallery($options){
        $this->options = $options;
        if($this->options['theme']!= $this->name){
            return null;
        }
        
        
        $doc = JFactory::getDocument();
        $params = JComponentHelper::getParams('com_droppics');
        
        $scripts = array();
        $stylesheets = array();
        
        $stylesheets[] = JURI::base('true').'/plugins/droppics/bxslider/style.css';
        $stylesheets[] = JURI::base('true').'/plugins/droppics/bxslider/jquery.bxslider.css';
        $scripts[] = JURI::base('true').'/plugins/droppics/bxslider/jquery.bxslider.min.js';
        $scripts[] = JURI::base('true').'/plugins/droppics/bxslider/script.js';
        
        $headLoading = '';
        if($this->options['from_plugin']===true){
            $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/droppicsHelper.js');
            if($params->get('jquerybase',true)){
                JLoader::register('DroppicsBase', JPATH_ADMINISTRATOR.'/components/com_droppics/classes/droppicsBase.php');
                if(droppicsBase::isJoomla30()){
                    JHtml::_('jquery.framework');
                }else{
                    $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/jquery-1.8.3.js');
                    $doc->addScript(JURI::base('true').'/components/com_droppics/assets/js/jquery-noconflict.js');
                }
            }
            foreach ($scripts as $script) {
                $doc->addScript($script);
            }
            foreach ($stylesheets as $stylesheet) {
                $doc->addStyleSheet($stylesheet);
            }
        }else{
            $files = array();
            foreach ($scripts as $script) {
                $files[] = '["'.$script.'","js"]';
            }
            foreach ($stylesheets as $stylesheet) {
                $files[] = '["'.$stylesheet.'","css"]';
            }
            $files = implode(',', $files);
            $headLoading .= "loadHeadFiles('[".$files."]');";
        }
        
        
        $content = "";
        $pager = "";
        $ij = 0;
        if(!empty($this->options['pictures']) || !empty($this->options['categories'])){
            $content .= '<div id="droppicsgallery'.$this->options['id_gallery'].'" class="droppicsgallerybxslider droppicsgallery" data-shownav="'.droppicsBase::loadValue($options['params'],'bxslider_show_nav','1').'" data-showbotnav="'.droppicsBase::loadValue($options['params'],'bxslider_show_bottom_nav','2').'" data-autostart="'.droppicsBase::loadValue($options['params'],'bxslider_autostart','1').'" data-pause="'.droppicsBase::loadValue($options['params'],'bxslider_pause','4000').'" data-mode="'.droppicsBase::loadValue($options['params'],'bxslider_mode','0').'" data-adaptive="'.droppicsBase::loadValue($options['params'],'bxslider_adaptive_height','0').'">';
            if(!empty($this->options['pictures'])){
                $content .= '<div class="bxSlider">';
                //$total = count($this->options['pictures']);
                foreach ($this->options['pictures'] as $picture){
                    $pparams = json_decode($picture->picture_params);
                    //$group = ($ij > 0 && $ij < $total) ? 'rel="group_'.$this->options['id_gallery'].'"' : '';
                    $group = 'rel="group_'.$this->options['id_gallery'].'"';
                    $content .= '<div class="wimg">';
                    $content .=    '<a class="set-modal" '.$group.' href="'.COM_MEDIA_BASEURL.'/com_droppics/'.$picture->id_gallery.'/'.$picture->file.'" data-modal-title="Galeria"><img src="'.COM_MEDIA_BASEURL.'/com_droppics/'.$picture->id_gallery.'/'.$picture->file.'" alt="'.$picture->alt.'" title="'.$picture->title.'" data-thumb="'.COM_MEDIA_BASEURL.'/com_droppics/'.$picture->id_gallery.'/thumbnails/'.$picture->file.'"/></a>';
                    if(droppicsBase::loadValue(@$this->options['params'],'bxslider_show_bottom_nav','2')=='2'){
                        $pager .=    '<a data-slide-index="'.$ij.'" href=""><img src="'.COM_MEDIA_BASEURL.'/com_droppics/'.$picture->id_gallery.'/thumbnails/'.$picture->file.'" alt="'.$picture->alt.'" title="'.$picture->title.'" /></a>';
                    }
                    if(trim(droppicsBase::loadValue($pparams,'bxslider_image_html',''))!==''){
                        $content .= '<div class="bxsliderhtml" style="'
                                . '             top: '.droppicsBase::loadValue($pparams,'bxslider_image_top','80').'%;'
                                . '             left: '.droppicsBase::loadValue($pparams,'bxslider_image_left','0').'%;'
                                . '             width: '.droppicsBase::loadValue($pparams,'bxslider_image_width','100').'%;'
                                . '             height: '.droppicsBase::loadValue($pparams,'bxslider_image_height','20').'%;'
                                . '             background-color: '.$this->hex2rgba(droppicsBase::loadValue($pparams,'bxslider_image_bgcolor','transparent'),droppicsBase::loadValue($pparams,'bxslider_image_transparency','90')/100).';'
                                . '                 ">'.droppicsBase::loadValue($pparams,'bxslider_image_html','').'</div>';
                    }
                    $content .= '</div>';            
                    $ij++;
                }
                $content .= '</div>';
                $content .= '<div class="clr"></div>';

                if($pager!==''){
                    $content .= '<div id="bx-pager" class="bx-pager-images">';
                    $content .= $pager;
                    $content .= '</div>';
                }
            }
            
            if((!empty($this->options['categories']) || !empty($this->options['parent'])) && droppicsBase::loadValue($options['params'],'bxslider_show_subcategories','0')){
                $content .= '<div class="droppicscats">';
                $content .= '<h2>'.JText::_('COM_DROPPICS_SUBCGALLERIES').'</h2>';
                if(!empty($this->options['parent'])){    
                    if($this->options['parent']->id_picture === null){
                        $params = JComponentHelper::getParams('com_droppics');
                        $src = JURI::base().$params->get('catimage',null);
                        $alt = $this->options['parent']->category_title;
			$title = '';
                    }else{
                        $src = COM_MEDIA_BASEURL.'/com_droppics/'.$this->options['parent']->id_category.'/thumbnails/'.$this->options['parent']->picture_file;
                        $title = $this->options['parent']->picture_title;
                        $alt = $this->options['parent']->picture_alt;
                    }
                    $content .= '<div class="wcat wimg wparent">';
                    $content .=    '<a class="droppicscatslink" data-id="'.$this->options['parent']->id_category.'" href="" >';                    
                    $content .=         '<img class="img" src="'.$src.'" alt="'.$alt.'" title="'.$title.'" />';
                    $content .=     '</a>';
                    $content .=         '<span>'.JText::_('COM_DROPPICS_BACK_TO').' '.$this->options['parent']->category_title.'</span>';
                    $content .= '</div>';
                    $content .= '<div class="clr"></div>';
                }
                foreach ($this->options['categories'] as $category){
                    $content .= '<div class="wcat wimg">';
                    $content .=    '<a class="droppicscatslink" data-id="'.$category->id_category.'" href="'.COM_MEDIA_BASEURL.'/com_droppics/'.$category->id_category.'/'.$category->picture_file.'" >';
                    $content .=         '<img class="img" src="'.COM_MEDIA_BASEURL.'/com_droppics/'.$category->id_category.'/thumbnails/'.$category->picture_file.'" alt="'.$category->picture_alt.'" title="'.$category->picture_title.'" />';
                    $content .=     '</a>';
                    $content .=         '<span>'.$category->category_title.'</span>';
                    $content .= '</div>';
                }
                $content .= '</div>';
                $content .= '<div class="clr"></div>';
            }
            
            if(empty($this->options['categories']) && empty($this->options['parent']) && $this->options['from_plugin']===true){
            //Include style in the head with joomla
                $this->addStyleDeclaration(@$this->options,false);
            }else{
                //Include style in the head dynamically
                $headLoading .= $this->addStyleDeclaration(@$this->options,true);
            }

            if($headLoading!==''){
                $headLoading = '<script type="text/javascript">'.$headLoading.'</script>';
            }
            $content .= $headLoading ;
            
            $content .= '</div>';
        }
        return $content;
    }    
    
    protected function addStyleDeclaration($options,$dynamic = false){
            $params = $options['params'];
                        
            $style = '';
            
            $style .= '#droppicsgallery'.$this->options['id_gallery'].' .bx-wrapper {';
            if(droppicsBase::loadValue($params,'bxslider_align_center',0)==1) {
                $style .= 'margin-left: auto;';
                $style .= 'margin-right: auto;';
            }
            if(droppicsBase::loadValue($params,'bxslider_width',0)>0) {
                $style .= '     width : '.droppicsBase::loadValue($params,'bxslider_width').'px;';
            }
                
            $style .= '}';
	    if(droppicsBase::loadValue($params,'bxslider_align_center',0)==1) {
		$style .= '#bx-pager {margin: 0 auto;}';
	    }
	    
            
            if(droppicsBase::loadValue($params,'bxslider_width',0)>0) {
                $style .= '#bx-pager {';
                $style .= '     width : '.droppicsBase::loadValue($params,'bxslider_width').'px;';
                $style .= '}';
            }            
            
            $style .= '#droppicsgallery'.$this->options['id_gallery'].' .bx-wrapper .bx-viewport {';
            if(droppicsBase::loadValue($params,'bxslider_height',0)>0) {
                $style .= '     height : '.droppicsBase::loadValue($params,'bxslider_height').'px !important;';
            }
            if(droppicsBase::loadValue($params,'bxslider_background','')!=='') {
                $style .= '     background-color : '.droppicsBase::loadValue($params,'bxslider_background').';';
            }else{
                $style .= '     background-color : transparent;';
            }
            if(droppicsBase::loadValue($params,'bxslider_show_shadow','1')!='1') {
                    $style .= '     -webkit-box-shadow: none;';
                    $style .= '                   -moz-box-shadow: none;';
                    $style .= '                        box-shadow: none;';
                    $style .= '                border: none;';
                    $style .= '                left: 0px;';
            }
            $style .= '}';
            
            if(droppicsBase::loadValue($params,'bxslider_fitwidth','0')!=='0') {
                $style .= '#droppicsgallery'.$this->options['id_gallery'].'.droppicsgallerybxslider .wimg img {';
                $style .= '     width : 100%;';
                $style .= '}';
            }
            
            $style .= '#droppicsgallery'.$this->options['id_gallery'].'.droppicsgallerybxslider .droppicscats .wimg {';
            $height  = droppicsBase::getParam('thumbnail_height','0');
            $style .= 'height : '.$height.'px;';
            $width   = droppicsBase::getParam('thumbnail_width','0');
            $style .= 'width : '.$width.'px;';
            $style .= '}';
            
            if($dynamic===false){
                $doc = JFactory::getDocument();
                $doc->addStyleDeclaration($style);
            }else{
                return 'loadHeadStyle("'.$style.'","droppicsgalleryStyle'.$options['id_gallery'].'");';
            }
            return '';
    }
    
    protected function addAdminScriptDeclaration(){
            $script  = '';
            $script .= '#preview .placeholder img, #preview .wimg {';
            $script .= 'height:'.(droppicsBase::getParam('thumbnail_height','0')+2*10).'px;';
            $script .= 'width:'.(droppicsBase::getParam('thumbnail_width','0')+2*10).'px;';
            $script .= '}';
            $script .= '#preview .highlight {';
            $script .= 'height:'.(droppicsBase::getParam('thumbnail_height','0')+2*10-2).'px;';
            $script .= 'width:'.(droppicsBase::getParam('thumbnail_width','0')+2*10-2).'px;';
            $script .= '}';

            return '<style type="text/css">'.$script.'</style>';
    }

        
    /* Convert hexdec color string to rgb(a) string 
     * From http://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/ 
     */
    private function hex2rgba($color, $opacity = false) {
            $default = 'rgb(0,0,0)';

            //Return default if no color provided
            if(empty($color)){
              return $default;
            }

            //Sanitize $color if "#" is provided 
            if ($color[0] == '#' ) {
                    $color = substr( $color, 1 );
            }

            //Check if color has 6 or 3 characters and get values
            if (strlen($color) == 6) {
                    $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
            } elseif ( strlen( $color ) == 3 ) {
                    $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
            } else {
                    return $default;
            }

            //Convert hexadec to rgb
            $rgb =  array_map('hexdec', $hex);

            //Check if opacity is set(rgba or rgb)
            if($opacity && $opacity!=='transparent'){
                    if(abs($opacity) > 1){
                            $opacity = 1.0;
                    }
                    $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
            } else {
                    $output = 'rgb('.implode(",",$rgb).')';
            }

            //Return rgb(a) color string
            return $output;
        }
}