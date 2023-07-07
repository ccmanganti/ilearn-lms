<x-filament::page>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="../../../../css/submissions.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    @php
        $url = request()->url(); // Get the current URL
        $segments = explode('/', $url); // Split the URL into segments
        $type = $segments[count($segments) - 2]; // Get the second-to-last segment
        $classcode = $segments[count($segments) - 3]; // Get the second-to-last segment
        $assId = basename(request()->url());
        $currentClass = \App\Models\Classes::where('code', $classcode)->first();
        $currentClassColor = \App\Models\Classes::where('code', $classcode)->first()->color;
        $classname = \App\Models\Classes::where('code', $classcode)->first()->name;
        $students = (\App\Models\Student::where('code', $classcode)->get())->sortByDesc('name');;

        if ($type == "t1") {
            $currentAss = \App\Models\Task::where('id', $assId)->first();
        } elseif ($type == "a1") {
            $currentAss = \App\Models\Assessment::where('id', $assId)->first();
        }



        if (!(\App\Models\Classes::where('code', $classcode)->where('uid', auth()->user()->id)->first())) {
            $isProf = false;
        } else{
            $isProf = true;
        }
        if(!$isProf){
            redirect('/lms/');
        }
    @endphp
    <style>:root{--class-color: {{ $currentClassColor }};}</style>


    <div class="task-details">
        <div class="task-header">
            <h2 class="class-task-title">{{ $currentAss->title }}</h2>
            <h2 class="class-task-created">Due · {{ date('F d, Y g:i:s a', strtotime($currentAss->due)) }}</h2>
        </div>
        <div class="task-description">
            <p class="task-desc">{!! $currentAss->desc !!}</p>
        </div>
    </div>


    <div class="container">
        <div class="students-list">
            <h2 class="student-list-title">Students</h2>
            <div class="student-container">
                @foreach ($students as $stud)
                    <div class="formsper-stud">
                        @php
                            if (request('type') == 't1') {
                                $submission = \App\Models\FileSubmission::where('userid', $stud->userid)->where('taskid', $assId)->get();
                            } else{
                                $submission = \App\Models\AssessSubmission::where('userid', $stud->userid)->where('assessid', $assId)->get();
                            }
                        @endphp
                        <form action="/selectstudent" method="POST" class="student" id="myForm{{$stud->id}}">
                            <input id="userid-in" type="hidden" name="userid" readonly value="{{ $stud->userid }}">
                            <input id="ass-id" type="hidden" name="ass" readonly value="{{ $assId }}">
                            <input id="type" type="hidden" name="type" readonly value="{{ $type }}">
                            <input id="type" type="hidden" name="loop" readonly value="{{ $loop->index }}">
                            <button class="username" onclick="submitForm(event, this.form)"><h2 class="studname">{{$loop->index+1}}. {{ $stud->name }}</h2><h2 class="studscore">@if($submission != "[]") Score: @if($type == 't1') <span id="changedScore">{{$submission[0]->score}}</span>/{{$currentAss->points}} @else <span id="changedScore">{{$submission[0]->score}}</span>/{{count($currentAss->item)}} @endif @endif</h2></button>
                        </form>
                        <form action="/submitscore" class="submitscore-form" method="POST" id="scoreForm{{$stud->id}}">
                            <input id="userid-in" type="hidden" name="userid" readonly value="{{ $stud->userid }}">
                            <input id="ass-id" type="hidden" name="ass" readonly value="{{ $assId }}">
                            <input id="type" type="hidden" name="type" readonly value="{{ $type }}">
                            <input id="scoreInput" type="number" name="score" placeholder="Score" required min="0" max="@if($type == 't1') {{ (int)$currentAss->points }} @else {{ count($currentAss->item) }} @endif"  oninput="validateInput(this)" step="1" @if($submission == "[]") disabled @else value="{{ (int)$submission[0]->score }}" @endif>
                            <button class="submitScore" onclick="submitScoreForm(event, this.form)" @if($submission == "[]") disabled @endif>Submit</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="view-ass">
            <h2 class="student-list-title">Submitted Files: </h2>
            <div class="submission-view" id="resultContainer">
                <h2 class="instruct-view" id="instruct">Select a student to view a submission.</h2>
            </div>
        </div>
    </div>



    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <script src="../../../../js/view-submissions.js"></script>
</x-filament::page>
