<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2016 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

DPCalendarHelper::loadLibrary(array(
		'jquery' => true,
		'bootstrap' => true,
		'dpcalendar' => true
));

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base() . 'templates/base/html/com_dpcalendar/event/default.css');

$params = $this->params;
if ($params->get('event_show_map', '1'))
{
	DPCalendarHelper::loadLibrary(array(
			'maps' => true
	));
	$document->addScript(JURI::base() . 'components/com_dpcalendar/views/event/tmpl/event.js');
}

if (JFactory::getApplication()->input->getCmd('tmpl', '') == 'component')
{
	$document->addStyleSheet(JURI::base() . 'components/com_dpcalendar/views/event/tmpl/none-responsive.css');
}

$event = $this->event;

JPluginHelper::importPlugin('dpcalendar');

// User timezone when available
echo JLayoutHelper::render('user.timezone');
?>
<div id="dpcal-event-container" class="dp-container item-page" itemscope
	itemtype="http://schema.org/Event">
<?php
echo JHtml::_('content.prepare', $params->get('event_textbefore'));

echo implode(' ', JDispatcher::getInstance()->trigger('onEventBeforeDisplay', array(
		&$event
)));

// Header with buttons and title
echo $this->loadTemplate('header');

// Joomla event
echo $event->displayEvent->afterDisplayTitle;

// Tags
echo JLayoutHelper::render('joomla.content.tags', $event->tags->itemTags);
?>

<div class="row">
	<div class="col-lg-8 dp-content-main">
		<?php
		// Informations like date calendar
		echo $this->loadTemplate('information');

		// Description
		echo $this->loadTemplate('description');
		?>
	</div>
	<div class="col-lg-4 dp-content-sidebar">
		<?php
		// Booking details when available
		echo $this->loadTemplate('bookings');

		// Attendees
		echo $this->loadTemplate('tickets');

		if ($event->locations && $params->get('event_show_map', '1') == '1' && $params->get('event_show_location', '2') == '1')
		{?>
			<div id="dp-event-details-map" class="dpcalendar-fixed-map"
				data-zoom="<?php echo $params->get('event_map_zoom', 4);?>"
				data-lat="<?php echo $params->get('event_map_lat', 47);?>"
				data-long="<?php echo $params->get('event_map_long', 4);?>"
				data-color="<?php echo $event->color;?>"></div>
		<?php
		}

		// Locations detail information
		echo $this->loadTemplate('locations');
		?>
	</div>
</div>

<?php

// Load the comments
echo $this->loadTemplate('comments');

// After event trigger
echo implode(' ', JDispatcher::getInstance()->trigger('onEventAfterDisplay', array(
		&$event
)));

echo JHtml::_('content.prepare', $params->get('event_textafter'));
?>
</div>
