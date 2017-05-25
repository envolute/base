<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2016 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

// Contains custom fields
echo $event->displayEvent->beforeDisplayContent;

if (!$this->event->description)
{
	return;
}
?>
<div class="my-3" itemprop="description">
<?php echo $this->event->description;?>
</div>

<?php
// Joomla event
echo $event->displayEvent->afterDisplayContent;
?>
