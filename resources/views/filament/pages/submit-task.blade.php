<x-filament::page>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="../../../css/submittask.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />

    @php
        $url = request()->url(); // Get the current URL
        $segments = explode('/', $url); // Split the URL into segments
        $secondToLastSegment = $segments[count($segments) - 2]; // Get the second-to-last segment

        $classcode = basename($secondToLastSegment);
        $taskid = basename(request()->url());
        $currentClass = \App\Models\Classes::where('code', $classcode)->first();
        $currentTask = \App\Models\Task::where('id', $taskid)->first();

        if (!$currentClass || !$currentTask) {
            echo '<script>window.location.href = "/lms/";</script>';
            exit;
        }

        $currentClassColor = $currentClass->color;

        $submissionIn = \App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $currentClass->code)->where('taskid', $taskid)->first();
        $submissions = \App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $currentClass->code)->where('taskid', $taskid)->get();

        if ($submissionIn) {
            date_default_timezone_set('Asia/Manila');
            $currentDateTime = strtotime($submissionIn->created_at); // Get the current timestamp in 24-hour format
            $targetDateTime = strtotime($currentTask->due); // The target datetime in 24-hour format

            if ($targetDateTime < $currentDateTime) {
                $currentTaskState = "Submitted Late";
                $color = "#d97706";
            } else {
                $currentTaskState = "Submitted";
                $color = "green";
            }
        } else {
            date_default_timezone_set('Asia/Manila');
            $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
            $targetDateTime = strtotime($currentTask->due); // The target datetime in 24-hour format

            if ($targetDateTime < $currentDateTime) {
                $currentTaskState = "Overdue";
                $color = "rgb(202, 60, 60)";
            } else {
                $currentTaskState = "Assigned";
                $color = "green";
            }
        }
    @endphp
    <style>:root{--class-color: {{ $currentClassColor }};}</style>


    <div class="class-head-contain" style="background-color: {{ $currentClass->color }};">
        <div class="class-head"></div>
        <div class="class-head-details">
            <h2 class="class-name">{{ $currentClass->name }}</h2>
            <h2 class="class-prof">Task Submission</h2>
        </div>
    </div>

    <div class="task-container">
        <div class="task-details">
            <div class="task-header">
                <h2 class="class-task-title">{{ $currentTask->title }}</h2>
                <h2 class="class-task-created">Posted · {{ date('F d, Y g:i:s a', strtotime($currentTask->created_at)) }}</h2>
                <h2 class="class-task-created" style="color: {{ $color }};">{{ $currentTaskState }}</h2>
            </div>
            <div class="task-description">
                <p class="task-desc">{!! $currentTask->desc !!}</p>
            </div>
        </div>
        <div class="task-submission">
                <div class="task-submission-details">
                    <h2 class="submission-title">Task Submission</h2>
                    <h2 class="task-due">Due: {{ date('F d, Y g:i:s a', strtotime($currentTask->due)) }}</h2>    
                </div>
                @if ($submissionIn)
                    <div class="submitted-files">
                        <div class="submission-description">
                            <h2 class="submitted-desc">Note: Uploading another file will overwrite the submission.</h2>
                            <h2 class="submitted-title">Submitted Files:</h2>
                        </div>
                        @foreach ($submissions as $submit)
                            <a href="../../../storage/{!! $submit->file !!}" target="_blank" class="files-submitted">{!! $submit->file !!}</a>
                        @endforeach
                    </div>                    
                @endif

                <form action="/submit" method="POST" enctype="multipart/form-data" class="submission-form">
                    @csrf
                    <input id="name-in" type="hidden" name="classid" readonly value="{{ $currentClass->id }}">
                    <input id="name-in" type="hidden" name="classcode" readonly value="{{ $currentClass->code }}">
                    <input id="name-in" type="hidden" name="taskid" readonly value="{{ $currentTask->id }}">
                    <input type="file" name="submission" class="submission" id="submission" multiple required>
                    <button type="submit" class="submit-btn">Submit Task</button>
                </form>
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
    
</x-filament::page>
