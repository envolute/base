<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2016 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

$event = $this->event;
$params = $this->params;

if (!$event->locations || $params->get('event_show_location', '2') != '2')
{
	return;
}
?>
<h2 class="dpcal-event-header"><?php echo JText::_('COM_DPCALENDAR_VIEW_EVENT_LOCATION_INFORMATION');?></h2>
<div class="dplocations">
<?php
$counter = 0;
foreach ($event->locations as $location) {
	if($counter > 0) echo '<hr />';
	$counter++;
?>
	<h5 id="<?php echo $this->escape($location->alias)?>" class="mb-0">
		<span class="base-icon-location"></span>
		<a href="http://maps.google.com/?q=<?php echo urlencode(DPCalendarHelperLocation::format($location));?>" rel="nofollow" target="_blank">
			<?php
			echo $this->escape($location->title) .' ';
			?>
		</a>
		<?php
		if (JFactory::getUser()->authorise('core.edit', 'com_dpcalendar'))
		{
			echo JHtmlDPCalendaricon::editLocation($location);
		}
		?>
	</h5>
	<address>
		<?php
		if ($location->street) echo $this->escape($location->street);
		if ($location->street && $location->number) echo ', ' . $this->escape($location->number);
		if ($location->street && $location->room) echo ', ' . $this->escape($location->room);
		echo '<br />';
		if ($location->zip) echo $this->escape($location->zip);
		if ($location->zip && ($location->city || $location->province || $location->country)) echo ' - ';
		if ($location->city) echo $this->escape($location->city);
		if ($location->city && ($location->province || $location->country)) echo ' - ';
		if ($location->province) echo $this->escape($location->province);
		if ($location->province && $location->country) echo ' - ';
		if ($location->country) echo $this->escape($location->country);
		if ($location->url) { ?>
		<div class="text-sm">
			<a href="<?php echo $location->url;?>" target="_blank"><?php echo $location->url?></a>
		</div>
		<?php } ?>
	</address>
	<?php
	if ($params->get('event_show_map', '1') == '1')
	{?>
		<div class="dp-event-details-map-single dpcalendar-fixed-map" id="dp-event-details-map-single<?php echo (int)$location->id?>"
				data-zoom="<?php echo $params->get('event_map_zoom', 4);?>"
				data-lat="<?php echo $location->latitude;?>"
				data-long="<?php echo $location->longitude;?>"
				data-color="<?php echo $event->color;?>">
		</div>
	<?php
	}
	?>
</div>
<?php
	$output = JEventDispatcher::getInstance()->trigger('onContentBeforeDisplay', array(
				'com_dpcalendar.location',
				&$location,
				&$event->params,
				0
		));
	echo trim(implode("\n", $output));
	if ($location->description)
	{
		echo JHTML::_('content.prepare', $location->description);
	}
}
?>
</div>
