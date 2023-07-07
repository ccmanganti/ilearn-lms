<x-filament::page>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="../../css/assignments.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />

    @php
        $allTasks = collect();
        $allAssessments = collect();
    
        $taskNotDoneArray = [];
        $taskDoneArray = [];
        $assessDoneArray = [];
        $assessNotDoneArray = [];
        $overdue = [];

        // GET ALL CLASS FOR THIS USER
        $classesEnrolled = [];
        $classes = \App\Models\Student::where('userid', auth()->user()->id)->get();
        foreach ($classes as $class) {
            $classEnrolled = \App\Models\Classes::where('code', $class->code)->first();
            array_push($classesEnrolled, $classEnrolled);
        }

        foreach ($classesEnrolled as $class) {
            $tasks = \App\Models\Task::where('class', $class->name)->get();
            $assessments = \App\Models\Assessment::where('class', $class->name)->get();

            if ($tasks) {
                $tasks->each(function ($task) use ($class, $allTasks){
                    $task->code = \App\Models\Classes::where('name', $class->name)->first()->code; // Assign the new data to the "code" property
                    $allTasks->push($task);
                });
            }
            if ($assessments) {
                $assessments->each(function ($assess) use ($class, $allAssessments){
                    $assess->code = \App\Models\Classes::where('name', $class->name)->first()->code; // Assign the new data to the "code" property
                    $allAssessments->push($assess);
                });
            }
        }

        foreach ($allTasks as $task) {
            if (!\App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $task->code)->where('taskid', $task->id)->first()) {
                array_push($taskNotDoneArray, $task);
            } else{
                array_push($taskDoneArray, $task);
            }
        }

        foreach ($allAssessments as $assess) {
            if (!\App\Models\AssessSubmission::where('userid', auth()->user()->id)->where('classcode', $assess->code)->where('assessid', $assess->id)->first()) {
                array_push($assessNotDoneArray, $assess);
            } else{
                array_push($assessDoneArray, $assess);
            }
        }
        
        $allDone = array_merge($assessDoneArray, $taskDoneArray);
        $allNotDone = array_merge($assessNotDoneArray, $taskNotDoneArray);

        usort($allNotDone, function ($a, $b) {
            return strtotime($a['due']) - strtotime($b['due']);
        });
        usort($allDone, function ($a, $b) {
            return strtotime($a['due']) - strtotime($b['due']);
        });



        foreach ($allNotDone as $notDone) {
            date_default_timezone_set('Asia/Manila');
            $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
            $targetDateTime = strtotime($notDone->due);

            if ($targetDateTime < $currentDateTime) {
                array_push($overdue, $notDone);
            }
        }
    @endphp


    {{-- HEADER --}}
    <div class="class-head-contain">
        <div class="class-head"></div>
        <div class="class-head-details">
            <h2 class="class-name">My Assignments</h2>
            <h2 class="class-prof">To-do List</h2>
        </div>
    </div>

    <div class="mobile-nav">
        <button class="mob-nav active" id="all-assigns-mob">Assigned</button>
        <button class="mob-nav" id="overdue-assigns-mob">Overdue</button>
        <button class="mob-nav" id="submitted-assigns-mob">Done</button>
    </div>

    {{-- assigns --}}
    <div class="assigns-container">
        <div class="assign-nav">
            <h2 class="nav-assigns nav-assigns-title">Sort by</h2>
            <button class="all-assigns nav-assign active" id="all-assigns">Assigned</button>
            <button class="assigns-assigns nav-assign" id="overdue-assigns">Overdue</button>
            <button class="tasks-assigns nav-assign" id="submitted-assigns">Done</button>
        </div>
        {{-- ALL assigns --}}
        <div class="assignments active" id="all-assignments" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
            @if (!$allNotDone)
                <hr>
                    <h2 class="no-post">No assignments for any class yet.</h2>
                <hr>
            @endif
            @foreach ($allNotDone as $item)
                <div class="assignment-container">
                    <a class="class-code" href="@if($item->item) /lms/assess/{{ $item->code }}/{{ $item->id }} @else /lms/assignments/{{ $item->code }}/{{ $item->id }} @endif">
                        <div class="class-details">
                            <h2 class="task-title">{{ $item->title }}</h2>
                            <h2 class="task-class-details">{{ $item->class }}</h2>
                        </div>
                        @php
                            date_default_timezone_set('Asia/Manila');
                            $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                            $targetDateTime = strtotime($item->due); // The target datetime in 24-hour format
                        @endphp
                        <div class="task-date" style="color: @if ($targetDateTime < $currentDateTime) rgb(202, 60, 60); @else #d97706; @endif">
                            <h2 class="task-due">{{ date('F d, Y g:i:s a', strtotime($item->due)) }}</h2>
                            <h2 class="task-state">
                                @if ($targetDateTime < $currentDateTime)
                                    Overdue
                                @else
                                    Accepting                                    
                                @endif
                            </h2>
                        </div>                    
                    </a>
                </div>
            @endforeach
        </div>
        <div class="assignments" id="assignments-overdue" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
            @if (!$overdue)
                <hr>
                    <h2 class="no-post">No assignment overdue.</h2>
                <hr>
            @endif
            @foreach ($allNotDone as $item)
                @php
                    date_default_timezone_set('Asia/Manila');
                    $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                    $targetDateTime = strtotime($item->due); // The target datetime in 24-hour format
                @endphp
                @if ($targetDateTime < $currentDateTime)
                    <div class="assignment-container assignment-overdue-container" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
                        <a class="class-code" href="@if($item->item) /lms/assess/{{ $item->code }}/{{ $item->id }} @else /lms/assignments/{{ $item->code }}/{{ $item->id }} @endif">
                            <div class="class-details">
                                <h2 class="task-title">{{ $item->title }}</h2>
                                <h2 class="task-class-details">{{ $item->class }}</h2>
                            </div>

                            <div class="task-date" style="color: @if ($targetDateTime < $currentDateTime) rgb(202, 60, 60); @else #d97706; @endif">
                                <h2 class="task-due">{{ date('F d, Y g:i:s a', strtotime($item->due)) }}</h2>
                                <h2 class="task-state">
                                    @if ($targetDateTime < $currentDateTime)
                                        Overdue
                                    @else
                                        Accepting                                    
                                    @endif
                                </h2>
                            </div>                    
                        </a>
                    </div>    
                @endif
                
            @endforeach
        </div>
        <div class="assignments" id="assignments-submitted" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
            @if (!$allDone)
                <hr>
                    <h2 class="no-post">No submitted assignment</h2>
                <hr>
            @endif
            @foreach ($allDone as $item)
                <div class="assignment-container-submitted" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
                    @php
                        if ($item->points && !$item->item) {
                            $submitted = \App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $item->code)->where('taskid', $item->id)->first();
                        } else{
                            $submitted = \App\Models\AssessSubmission::where('userid', auth()->user()->id)->where('classcode', $item->code)->where('assessid', $item->id)->first();
                        }
                        date_default_timezone_set('Asia/Manila');
                        $currentDateTime = strtotime($submitted->created_at); // Get the current timestamp in 24-hour format
                        $targetDateTime = strtotime($item->due); // The target datetime in 24-hour format
                        
                        if($submitted && $item->points){
                            if($submitted->score){
                                $score = '· Score: '.$submitted->score.'/'.$item->points;
                            } else{
                                $score = '';
                            }
                        } else if($submitted && $item->item){
                            $score = '· Score: '.$submitted->score.'/'.count($item->item);
                        }
                    @endphp
                    
                    <a class="class-code" href="@if($item->item) /lms/assess/{{ $item->code }}/{{ $item->id }} @else /lms/assignments/{{ $item->code }}/{{ $item->id }} @endif">
                        <div class="class-details-submitted">
                            <h2 class="task-title">{{ $item->title }}</h2>
                            <h2 class="task-class-details">{{ $item->class }} {{$score}}</h2>
                        </div>

                        @if ($targetDateTime > $currentDateTime)
                            <div class="task-date" style="color: rgb(0, 158, 0)">
                                <h2 class="task-due">{{ date('F d, Y g:i:s a', strtotime($item->due)) }}</h2>
                                <h2 class="task-state">Submitted</h2>
                            </div>
                        @else
                            <div class="task-date" style="color: #d97706;">
                                <h2 class="task-due">{{ date('F d, Y g:i:s a', strtotime($item->due)) }}</h2>
                                <h2 class="task-state">Submitted Late</h2>
                            </div>
                        @endif
                                            
                    </a>
                </div>
            @endforeach
        </div>
    </div>


    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
      AOS.init();
    </script>
    <script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
    <script>
        // Get a reference to the file input element
        const inputElement = document.querySelector('input[type="file"]');
    
        // Create a FilePond instance
        const pond = FilePond.create(inputElement);

        FilePond.setOptions({
            server: {
                process: './tmp-upload',
                revert: './tmp-delete',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            },
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <script src="../js/assignments.js"></script>
</x-filament::page>