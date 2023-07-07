<?php

namespace App\Filament\Widgets;

// use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Task;
use App\Models\Assessment;

class CalendarWidget extends FullCalendarWidget
{
    /**
     * Return events that should be rendered statically on calendar.
     */
    public function getViewData(): array
    {
        $userId = auth()->user()->id;

        $classCode = Student::where('userid', $userId)->get();
        $tasks = [];
        foreach ($classCode as $code) {
            $className = Classes::where('code', $code->code)->first()->name;
            $taskList = Task::where('class', $className)->get();
            $assList = Assessment::where('class', $className)->get();

            foreach ($taskList as $task) {
                array_push($tasks, $task);
            }
            foreach ($assList as $ass) {
                array_push($tasks, $ass);
            }
        }
        $result = [];
        foreach ($tasks as $task) {
            if($task->points){
                $type = "Task";
                $url = "/lms/assignments/".Classes::where('name',$task->class)->first()->code.'/'.$task->id;
            } else{
                $type = "Assessment";
                $url = "/lms/assess/".Classes::where('name',$task->class)->first()->code.'/'.$task->id;
            }

            $result[] = [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due,
                'allDay' => true,
                'url' => $url,
                'backgroundColor' => Classes::where('name',$task->class)->first()->color,
            ];
        }
        return $result;
        // return [
        //     [
        //         'id' => 1,
        //         'title' => $tasks[0]->due,
        //         'start' => now()
        //     ],
        //     [
        //         'id' => 2,
        //         'title' => 'Meeting with Pamela',
        //         'start' => now()->addDay(),
        //         'url' => 'https://some-url.com',
        //         'shouldOpenInNewTab' => true,
        //     ]
        // ];
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        // You can use $fetchInfo to filter events by date.
        return [];
    }
    public static function canCreate(): bool
    {
        // Returning 'false' will remove the 'Create' button on the calendar.
        return false;
    }

    public static function canEdit(?array $event = null): bool
    {
        // Returning 'false' will disable the edit modal when clicking on a event.
        return false;
    }
}
