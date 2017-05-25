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
?>

<?php
if (!DPCalendarHelper::isFree() ) :
	echo '<div id="subscription" class="clearfix mb-2">';
	if (DPCalendarHelperBooking::openForBooking($event) && $event->params->get('access-invite') && !DPCalendarHelper::isFree() )
	{
		$button = JHtml::_('dpcalendaricon.invite', $event);
		if ($button)
		{ ?>
		<div class="float-left mr-1"><?php echo $button;?></div>
		<?php
		}
	}
	if ($event->capacity != 0 && $event->params->get('access-tickets') && !DPCalendarHelper::isFree())
	{
		$button = JHtml::_('dpcalendaricon.tickets', $event);
		if ($button)
		{ ?>
		<div class="float-left mr-1"><?php echo $button;?></div>
		<?php
		}
	}
	echo '</div>';
endif;

if ($event->params->get('access-edit'))
{
	$button = JHtml::_('dpcalendaricon.edit', $event);
	if ($button)
	{ ?>
	<div class="float-right ml-1"><?php echo $button;?></div>
	<?php
	}
}

if ($event->params->get('access-delete'))
{
	$button = JHtml::_('dpcalendaricon.delete', $event);
	if ($button)
	{ ?>
	<div class="float-right ml-1"><?php echo $button;?></div>
	<?php
	}

	$button = JHtml::_('dpcalendaricon.deleteSeries', $event->original_id);
	if ($button)
	{ ?>
	<div class="float-right ml-1"><?php echo $button;?></div>
	<?php
	}
}
?>
<h2 class="content-title" itemprop="name">
	<?php
	$title = $this->escape($event->title);
	if (JFactory::$application->input->get('tmpl') == 'component')
	{
		$title = '<a href="' . str_replace(array('?tmpl=component', 'tmpl=component'), '', DPCalendarHelperRoute::getEventRoute($event->id, $event->catid)) . '" target="_parent">' . $title . '</a>';
	}
	echo $title;
	?>
</h2>
<div class="item-actions clearfix">
	<div class="social-buttons float-left cleafix">
		<div class="float-left mr-1"><?php echo JHtml::_('share.twitter', $params);?></div>
		<div class="float-left mr-1"><?php echo JHtml::_('share.like', $params);?></div>
		<div class="float-left mr-1"><?php echo JHtml::_('share.google', $params);?></div>
		<div class="float-left mr-1"><?php echo JHtml::_('share.linkedin', $params);?></div>
		<div class="float-left mr-1"><?php echo JHtml::_('share.xing', $params);?></div>
	</div>
	<div class="actions-buttons float-right cleafix">
		<?php
		$button = JHtml::_('dpcalendaricon.printWindow', 'dpcal-event-container', false);
		if ($button) echo $button;
		$button = JHtml::_('dpcalendaricon.emailEvent', $event);
		if ($button) echo $button;
		if ($params->get('event_show_copy', '1'))
		{
			$startDate = DPCalendarHelper::getDate($event->start_date, $event->all_day);
			$endDate = DPCalendarHelper::getDate($event->end_date, $event->all_day);
			$copyDateTimeFormat = $event->all_day ? 'Ymd' : 'Ymd\THis';
			if ($event->all_day)
			{
				$endDate->modify('+1 day');
			}
			$url = 'http://www.google.com/calendar/render?action=TEMPLATE&text=' . urlencode($event->title);
			$url .= '&dates=' . $startDate->format($copyDateTimeFormat, true) . '%2F' .
					$endDate->format($copyDateTimeFormat, true);
			$url .= '&location=' . urlencode(DPCalendarHelperLocation::format($event->locations));
			$url .= '&details=' . urlencode(JHtml::_('string.truncate', $event->description, 200));
			$url .= '&hl=' . DPCalendarHelper::getFrLanguage() . '&ctz=' . $startDate->getTimezone()->getName();
			$url .= '&sf=true&output=xml';
			?>
			<div class="btn-group float-right event-button hasTooltip" id="dp-event-copy" title="<?php echo JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY');?>">
				<a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#"><span class="icon-download"></span></a>
				<div class="dropdown-menu dropdown-menu-right">
					<a class="dropdown-item" target="_blank" href="<?php echo $url;?>"><?php echo JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY_GOOGLE');?></a>
					<a class="dropdown-item" target="_blank" href="<?php echo JRoute::_("index.php?option=com_dpcalendar&view=event&format=raw&id=" . $event->id);?>"><?php echo JText::_('COM_DPCALENDAR_FIELD_CONFIG_EVENT_LABEL_COPY_OUTLOOK');?></a>
				</div>
			</div>
		<?php
		}
		?>
	</div>
</div>
