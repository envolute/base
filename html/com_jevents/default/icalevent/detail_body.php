<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	= & JEVConfig::getInstance();

if( 0 == $this->evid) {
	$Itemid = JRequest::getInt("Itemid");
	JFactory::getApplication()->redirect( JRoute::_('index.php?option=' . JEV_COM_COMPONENT. "&task=day.listevents&year=$this->year&month=$this->month&day=$this->day&Itemid=$Itemid",false));
	return;
}

if (is_null($this->data)){
	
	JFactory::getApplication()->redirect(JRoute::_("index.php?option=".JEV_COM_COMPONENT."&Itemid=$this->Itemid",false), JText::_("JEV_SORRY_UPDATED"));
}

if( array_key_exists('row',$this->data) ){
	$row=$this->data['row'];

	// Dynamic Page Title
	JFactory::getDocument()->SetTitle( $row->title() );

	$mask = $this->data['mask'];
	$page = 0;

	
	$cfg	 = & JEVConfig::getInstance();

	$dispatcher	=& JDispatcher::getInstance();
	$params =new JRegistry(null);

	if (isset($row)) {
		$customresults = $dispatcher->trigger( 'onDisplayCustomFields', array( &$row) );
		if (!$this->loadedFromTemplate('icalevent.detail_body', $row, $mask)){
        ?>
		        <div class="headingrow bottom-expand">
		                <h3><?php echo $row->title(); ?></h3>
		                <?php
				if( $cfg->get('com_byview') == '1' || $cfg->get('com_hitsview') == '1' ) :
					echo '<ul class="hlist hlist-no-space small pull-left">';
						if( $cfg->get('com_byview') == '1' ){
							echo '<li class="ev_detail contact" >'.JText::_('JEV_BY') . '&nbsp;' . $row->contactlink().'</li>';
						}
						if( $cfg->get('com_hitsview') == '1' ){
							echo '<li class="ev_detail hits" >'.JText::_('JEV_EVENT_HITS') . ' : ' . $row->hits().'</li>';
						}
					echo '</ul>';
				endif;
				
				echo '<div class="pull-right" style="margin:-3px 0 3px">';
				$jevparams = JComponentHelper::getParams(JEV_COM_COMPONENT);
		                //if ($jevparams->get("showicalicon",0) &&  !$jevparams->get("disableicalexport",0) ){
		                	JEVHelper::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
					?>
					<a class="btn btn-xs btn-primary" href="javascript:void(0)" onclick='clickIcalButton()' title="<?php echo JText::_('JEV_SAVEICAL');?>">
						<span class="glyphicon glyphicon-calendar"></span> ICAL
					</a>
					<?php
		                //}
		                if( $row->canUserEdit() && !( $mask & MASK_POPUP )) {
		                	JEVHelper::script( 'view_detail.js', 'components/'.JEV_COM_COMPONENT."/assets/js/" );
		                    	?>
		                        <a class="btn btn-xs btn-primary" href="javascript:void(0)" onclick='clickEditButton()'>
		                           	<span class="glyphicon glyphicon-pencil"></span> <?php echo JText::_('JEV_E_EDIT');?>
		                        </a>
		                        <?php
		                }
				echo '</div>';
				
				//if(($this->eventIcalDialog($row, $mask)) || ($this->eventManagementDialog($row, $mask))) {
					echo '<div class="dialogs clear">';
				?>
						<div style="position:relative;">
				                <?php
				                $this->eventIcalDialog($row, $mask);
				                ?>
				                </div>
				                <div style="position:relative;">
				                <?php
				                $this->eventManagementDialog($row, $mask);
				                ?>
				                </div>
				<?php
					echo '</div>';
				//}
				?>
			</div>
			<div class="clear"></div>
			
			<?php 
			
			if( $cfg->get('com_repeatview') == '1' ){
				echo '<div class="ev_detail repeat well well-sm text-center bottom-space clearfix">';
					
					echo '<strong><span class="glyphicon glyphicon-calendar"></span> '.$row->repeatSummary().'</strong>';
			
					if ($row->hasLocation() || $row->hasContactInfo()) {
						if( $row->hasLocation() ){
							echo '<div class="jev_address"><span class="glyphicon glyphicon-map-marker"></span> '. $row->location().'</div>';
						}
						if($row->hasContactInfo()){
							echo '<div class="jev_address">'.JText::_('JEV_EVENT_CONTACT').' : </h5>'. $row->contact_info().'</div>';
						}
					}
					
					if( $row->hasExtraInfo()){
						echo '<hr class="hr-sm" /><span class="glyphicon glyphicon-info-sign"></span> '.$row->extra_info();
					}
					
					$pager = $row->previousnextLinks();
					if($pager) { 
						echo '<hr class="hr-sm" />'.$pager; 
					}
				echo '</div>';
			}
			?>
			
			<div class="jevent-content-details"><?php echo $row->content(); ?></div>
			
			<?php
			
			if (count($customresults)>0){
				foreach ($customresults as $result) {
					if (is_string($result) && strlen($result)>0){
						echo "<div>".$result."</div>";
					}
				}
			}
	
		} // end if not loaded from template
	        
	        $results = $dispatcher->trigger( 'onAfterDisplayContent', array( &$row, &$params, $page ) );
		echo trim( implode( "\n", $results ) );
	
	} else {
		echo '<div class="alert alert-warning">'.JText::_('JEV_REP_NOEVENTSELECTED').'</div>';
	}

	if(!($mask & MASK_BACKTOLIST)) {
		echo '
		<div class="clear"></div>
		<div class="top-space">
			<a class="btn btn-default" href="javascript:window.history.go(-1);" title="'.JText::_('JEV_BACK').'">'.JText::_('JEV_BACK').'</a>
		</div>
		';
	}
}