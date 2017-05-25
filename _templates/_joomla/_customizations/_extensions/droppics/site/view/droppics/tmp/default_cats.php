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
?>
<div id="mygalleries">
    
    <div id="updateGroup" class="btn-group btn-block hide">
        <a id="updateBtn" class="btn btn-warning" href="http://www.joomunited.com/my-account" target="_blank" style="width: 60%;"><i class="icon-info-sign"></i> <?php echo JText::_('COM_DROPPICS_CTRL_UPDATE_AVAILABLE'); ?> <span class="versionNumber"></span></a>
	<a id="hideUpdateBtn" class="btn btn-warning" href=""><i class="icon-remove"></i> <?php echo JText::_('COM_DROPPICS_CTRL_UPDATE_HIDE'); ?></a>
    </div>
    
    <?php if($this->canDo->get('core.create')): ?>
    <a id="newgallery" class="btn btn-block" href=""><i class="icon-plus"></i> <?php echo JText::_('COM_DROPPICS_LAYOUT_DROPPICS_NEW_GALLERY'); ?></a>
    <?php endif; ?>
    <div class="nested dd">
        <ol id="gallerieslist" class="dd-list nav bs-docs-sidenav2 ">
            <?php
            $content = '';
            if(!empty($this->categories)){
                $previouslevel = 1;
                for ($index = 0; $index < count($this->categories); $index++) {
                    if($index+1!=count($this->categories)){
                        $nextlevel = $this->categories[$index+1]->level;
                    }else{
                        $nextlevel = 0;
                    }
                    $content .= '<li class="dd-item dd3-item '.($index?'':'active').'" data-id-gallery="'.$this->categories[$index]->id.'">
                        <div class="dd-handle dd3-handle"></div>
                        <div class="dd-content dd3-content">';
                    if($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')){
                        $content .= '<a class="edit"><i class="icon-edit"></i></a>';
                    }
                    if($this->canDo->get('core.delete')){
                        $content .= '<a class="trash"><i class="icon-trash"></i></a>';
                    }
                    $content .= '<a href="" class="t">
                                <span class="title">'.$this->categories[$index]->title.'</span>
                            </a>
                        </div>';
                    if($nextlevel>$this->categories[$index]->level){
                        $content .= '<ol class="dd-list">';
                    }elseif($nextlevel==$this->categories[$index]->level){
                        $content .= '</li>';
                    }else{
                        $c = '';
                        $c .= '</li>';
                        $c .= '</ol>';
                        $content .= str_repeat($c,$this->categories[$index]->level-$nextlevel);
                    }
                    $previouslevel = $this->categories[$index]->level;                    
                }
            } 
            echo $content;
            ?>
        </ol>
        <input type="hidden" id="galleryToken" name="<?php echo JSession::getFormToken(); ?>" /> 
    </div>
</div>