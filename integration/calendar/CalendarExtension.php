<?php

namespace humhub\modules\external_calendar\integration\calendar;

use humhub\modules\external_calendar\models\ExternalCalendar;
use humhub\modules\external_calendar\models\ExternalCalendarEntry;
use Yii;
use yii\base\Object;
use humhub\modules\external_calendar\models\ExternalCalendarEntryQuery;

/**
 * CalendarExtension implements functions for the Events.php file
 *
 * @author davidborn
 */
class CalendarExtension extends Object
{
    /**
     * Default color of external calendar type items.
     */
    const DEFAULT_COLOR = '#DC0E25';

    const ITEM_TYPE_KEY = 'external_calendar';

    /**
     * @param $event \humhub\modules\calendar\interfaces\CalendarItemTypesEvent
     * @return mixed
     */
    public static function addItemTypes($event)
    {
        $calendars = self::getCalendarsForEvent($event);

        foreach ($calendars as $calendar) {
            $event->addType($calendar->getItemTypeKey(), $calendar->getFullCalendarArray());
        }
    }

    /**
     * @param $event \humhub\modules\calendar\interfaces\CalendarItemsEvent
     */
    public static function addItems($event)
    {
        /* @var $entries ExternalCalendarEntry[] */
        $entries = ExternalCalendarEntryQuery::findForEvent($event);
        $calendars = self::getCalendarsForEvent($event);

        foreach ($calendars as $calendar) {
            $items = [];
            foreach ($entries as $entry) {
                if ($entry->calendar->id !== $calendar->id) {
                    continue;
                }
                $items[] = $entry->getFullCalendarArray();
            }
            if (!empty($items)) {
                $event->addItems($calendar->getItemTypeKey(), $items);
            }

        }


        // old
//        $event->addItems(static::ITEM_TYPE_KEY, $items);
    }

    private static function getCalendarsForEvent($event)
    {
        if (isset($event->contentContainer) && !empty($event->contentContainer)) {
            $calendars = ExternalCalendar::find()->contentContainer($event->contentContainer)->all();
        }
        else {
            $calendars = ExternalCalendar::find()->all();
        }
        return $calendars;
    }

}