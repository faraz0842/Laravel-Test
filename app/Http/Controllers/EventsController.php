<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class EventsController extends BaseController
{
    public function getWarmupEvents()
    {
        return Event::all();
    }

    /* TODO: complete getEventsWithWorkshops so that it returns all events including the workshops
    Requirements:
    - maximum 2 sql queries
    - Don't post process query result in PHP
    - verify your solution with `php artisan test`
    - do a `git commit && git push` after you are done or when the time limit is over

    Hints:
    - partial or not working answers also get graded so make sure you commit what you have

    Sample response on GET /events:
    ```json
    [
    {
    "id": 1,
    "name": "Laravel convention 2020",
    "created_at": "2021-04-25T09:32:27.000000Z",
    "updated_at": "2021-04-25T09:32:27.000000Z",
    "workshops": [
    {
    "id": 1,
    "start": "2020-02-21 10:00:00",
    "end": "2020-02-21 16:00:00",
    "event_id": 1,
    "name": "Illuminate your knowledge of the laravel code base",
    "created_at": "2021-04-25T09:32:27.000000Z",
    "updated_at": "2021-04-25T09:32:27.000000Z"
    }
    ]
    },
    {
    "id": 2,
    "name": "Laravel convention 2021",
    "created_at": "2021-04-25T09:32:27.000000Z",
    "updated_at": "2021-04-25T09:32:27.000000Z",
    "workshops": [
    {
    "id": 2,
    "start": "2021-10-21 10:00:00",
    "end": "2021-10-21 18:00:00",
    "event_id": 2,
    "name": "The new Eloquent - load more with less",
    "created_at": "2021-04-25T09:32:27.000000Z",
    "updated_at": "2021-04-25T09:32:27.000000Z"
    },
    {
    "id": 3,
    "start": "2021-11-21 09:00:00",
    "end": "2021-11-21 17:00:00",
    "event_id": 2,
    "name": "AutoEx - handles exceptions 100% automatic",
    "created_at": "2021-04-25T09:32:27.000000Z",
    "updated_at": "2021-04-25T09:32:27.000000Z"
    }
    ]
    },
    {
    "id": 3,
    "name": "React convention 2021",
    "created_at": "2021-04-25T09:32:27.000000Z",
    "updated_at": "2021-04-25T09:32:27.000000Z",
    "workshops": [
    {
    "id": 4,
    "start": "2021-08-21 10:00:00",
    "end": "2021-08-21 18:00:00",
    "event_id": 3,
    "name": "#NoClass pure functional programming",
    "created_at": "2021-04-25T09:32:27.000000Z",
    "updated_at": "2021-04-25T09:32:27.000000Z"
    },
    {
    "id": 5,
    "start": "2021-08-21 09:00:00",
    "end": "2021-08-21 17:00:00",
    "event_id": 3,
    "name": "Navigating the function jungle",
    "created_at": "2021-04-25T09:32:27.000000Z",
    "updated_at": "2021-04-25T09:32:27.000000Z"
    }
    ]
    }
    ]
     */

    public function getEventsWithWorkshops()
    {
        $events = DB::table('events')
            ->leftJoin('workshops', 'events.id', '=', 'workshops.event_id')
            ->select('events.id', 'events.name', 'events.created_at', 'events.updated_at',
                'workshops.id as workshop_id', 'workshops.start', 'workshops.end', 'workshops.name as workshop_name', 'workshops.created_at as workshop_created_at', 'workshops.updated_at as workshop_updated_at')
            ->orderBy('events.id')
            ->get();

        $result = [];
        $currentEvent = null;
        foreach ($events as $row) {
            if ($row->id != $currentEvent) {
                $currentEvent = $row->id;
                $result[] = [
                    'id' => $row->id,
                    'name' => $row->name,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                    'workshops' => [],
                ];
            }

            if ($row->workshop_id) {
                $result[count($result) - 1]['workshops'][] = [
                    'id' => $row->workshop_id,
                    'start' => $row->start,
                    'end' => $row->end,
                    'name' => $row->workshop_name,
                    'created_at' => $row->workshop_created_at,
                    'updated_at' => $row->workshop_updated_at,
                ];
            }
        }

        return $result;
    }

    /* TODO: complete getFutureEventWithWorkshops so that it returns events with workshops, that have not yet started
    Requirements:
    - only events that have not yet started should be included
    - the event starting time is determined by the first workshop of the event
    - the eloquent expressions should result in maximum 3 SQL queries, no matter the amount of events
    - Don't post process query result in PHP
    - verify your solution with `php artisan test`
    - do a `git commit && git push` after you are done or when the time limit is over

    Hints:
    - partial or not working answers also get graded so make sure you commit what you have
    - join, whereIn, min, groupBy, havingRaw might be helpful
    - in the sample data set  the event with id 1 is already in the past and should therefore be excluded

    Sample response on GET /futureevents:
    ```json
    [
    {
    "id": 2,
    "name": "Laravel convention 2021",
    "created_at": "2021-04-20T07:01:14.000000Z",
    "updated_at": "2021-04-20T07:01:14.000000Z",
    "workshops": [
    {
    "id": 2,
    "start": "2021-10-21 10:00:00",
    "end": "2021-10-21 18:00:00",
    "event_id": 2,
    "name": "The new Eloquent - load more with less",
    "created_at": "2021-04-20T07:01:14.000000Z",
    "updated_at": "2021-04-20T07:01:14.000000Z"
    },
    {
    "id": 3,
    "start": "2021-11-21 09:00:00",
    "end": "2021-11-21 17:00:00",
    "event_id": 2,
    "name": "AutoEx - handles exceptions 100% automatic",
    "created_at": "2021-04-20T07:01:14.000000Z",
    "updated_at": "2021-04-20T07:01:14.000000Z"
    }
    ]
    },
    {
    "id": 3,
    "name": "React convention 2021",
    "created_at": "2021-04-20T07:01:14.000000Z",
    "updated_at": "2021-04-20T07:01:14.000000Z",
    "workshops": [
    {
    "id": 4,
    "start": "2021-08-21 10:00:00",
    "end": "2021-08-21 18:00:00",
    "event_id": 3,
    "name": "#NoClass pure functional programming",
    "created_at": "2021-04-20T07:01:14.000000Z",
    "updated_at": "2021-04-20T07:01:14.000000Z"
    },
    {
    "id": 5,
    "start": "2021-08-21 09:00:00",
    "end": "2021-08-21 17:00:00",
    "event_id": 3,
    "name": "Navigating the function jungle",
    "created_at": "2021-04-20T07:01:14.000000Z",
    "updated_at": "2021-04-20T07:01:14.000000Z"
    }
    ]
    }
    ]
    ```
     */

    public function getFutureEventWithWorkshops()
    {
        return Event::select('events.*', 'workshops.id as workshop_id', 'workshops.name as workshop_name', 'workshops.start as workshop_start', 'workshops.end as workshop_end')
            ->join('workshops', 'workshops.event_id', '=', 'events.id')
            ->groupBy('events.id')
            ->havingRaw('MIN(workshops.start) > NOW()')
            ->get()
            ->groupBy('id')
            ->map(function ($event) {
                $event = $event->first();
                $event->workshops = $event->map(function ($item) {
                    return collect($item)->only(['workshop_id', 'workshop_name', 'workshop_start', 'workshop_end']);
                })->toArray();
                unset($event['workshop_id'], $event['workshop_name'], $event['workshop_start'], $event['workshop_end']);
                return $event;
            })
            ->values();
    }

}
