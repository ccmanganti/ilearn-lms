<x-filament::page>
    {{-- LINKS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="../../../css/classes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    @php
        $url = request()->url(); // Get the current URL
        $segments = explode('/', $url); // Split the URL into segments
        $secondToLastSegment = $segments[count($segments) - 2]; // Get the second-to-last segment

        $classcode = basename($secondToLastSegment);
        $currentClass = \App\Models\Classes::where('code', $classcode)->first();
        $currentClassColor = \App\Models\Classes::where('code', $classcode)->first()->color;
        $classname = \App\Models\Classes::where('code', $classcode)->first()->name;
        
        $assessments = \App\Models\Assessment::where('class', $classname)->get();
        $tasks = \App\Models\Task::where('class', $classname)->get();

        $assignments = $assessments->concat($tasks);
        $sortedAssignments = $assignments->sortByDesc('created_at');


        $assignedCount = 0;
        $overdueCount = 0;
        foreach ($sortedAssignments as $ass) {
            date_default_timezone_set('Asia/Manila');
            $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
            $targetDateTime = strtotime($ass->due);

            if ($targetDateTime > $currentDateTime) {
                $assignedCount++;
            } else{
                $overdueCount++;
            }
        }


        if (!(\App\Models\Classes::where('code', $classcode)->where('uid', auth()->user()->id)->first())) {
            $isProf = false;
        } else{
            $isProf = true;
        }

        if(!$isProf){
            redirect('/lms/myclass/'.$secondToLastSegment);
        }
    @endphp

    <div class="class-head-contain" style="background-color: {{ $currentClass->color }}">
        <div class="class-head"></div>
        <div class="class-head-details">
            <h2 class="class-name">{{ $currentClass->name }}</h2>
            <h2 class="class-prof">Assigned Tasks</h2>
        </div>
    </div>

    <div class="mobile-nav">
        <button class="mob-nav active" id="all-assigns-mob">All Assignments</button>
        <button class="mob-nav" id="assigned-mob">Assigned</button>
        <button class="mob-nav" id="overdue-assigns-mob">Overdue</button>
        <button class="mob-nav" id="task-assigns-mob">Tasks</button>
        <button class="mob-nav" id="assess-assigns-mob">Assessments</button>
    </div>

    <div class="assigns-container">
        <div class="assign-nav">
            <h2 class="nav-assigns nav-assigns-title">Sort by</h2>
            <button class="all-assigns nav-assign active" id="all-assigns">All Assignments</button>
            <button class="assigned nav-assign" id="assigned">Assigned</button>
            <button class="overdue-assigns nav-assign" id="overdue-assigns">Overdue</button>
            <button class="task-assigns nav-assign" id="task-assigns">Tasks</button>
            <button class="assess-assigns nav-assign" id="assess-assigns">Assessments</button>
        </div>
        <div class="assignments active" id="all-assignments-content" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
            @if ($sortedAssignments == "[]")
                <hr>
                    <h2 class="no-post">No assignment posted.</h2>
                <hr>
            @endif
            <div class="assignment-container">
                @foreach ($sortedAssignments as $item)
                        <div class="class-code">
                            <div class="class-details">
                                <h2 class="task-title">{{ $item->title }}</h2>
                                <h2 class="task-class-details">@if($item->points) Task @else Assessment @endif · Due {{(new DateTime($item->due))->format('M d')}}</h2>
                            </div>
                            <div class="post-options">
                                <a href="@if($item->points) ../../tasks/{{$item->id}}/edit @else ../../assessments/{{$item->id}}/edit @endif" class="post-option submit-task" title="Edit in Resource"><i class="fa-sharp fa-solid fa-pen"></i></a>
                                <a href="@if($item->points) t1/{{$item->id}} @else a1/{{$item->id}} @endif" class="post-option submit-task" title="View Submissions"><i class="fa-sharp fa-solid fa-folder"></i></a>
                            </div>                    
                        </div>
                @endforeach
            </div>
        </div>

        

        <div class="assignments" id="all-assigned-content" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
            @if ($assignedCount == 0)
                <hr>
                    <h2 class="no-post">No assigned assignment.</h2>
                <hr>
            @endif
            <div class="assignment-container">
                @foreach ($sortedAssignments as $item)
                    @php
                        date_default_timezone_set('Asia/Manila');
                        $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                        $targetDateTime = strtotime($item->due);
                    @endphp
                    @if ($targetDateTime > $currentDateTime)
                        <div class="class-code">
                            <div class="class-details">
                                <h2 class="task-title">{{ $item->title }}</h2>
                                <h2 class="task-class-details">@if($item->points) Task @else Assessment @endif · Due {{(new DateTime($item->due))->format('M d')}}</h2>
                            </div>
                            <div class="post-options">
                                <a href="@if($item->points) ../../tasks/{{$item->id}}/edit @else ../../assessments/{{$item->id}}/edit @endif" class="post-option submit-task" title="Edit in Resource"><i class="fa-sharp fa-solid fa-pen"></i></a>
                                <a href="@if($item->points) t1/{{$item->id}} @else a1/{{$item->id}} @endif" class="post-option submit-task" title="View Submissions"><i class="fa-sharp fa-solid fa-folder"></i></a>
                            </div>                    
                        </div>                        
                    @endif
                @endforeach
            </div>
        </div>





        <div class="assignments" id="all-overdue-content" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
            @if ($overdueCount == 0)
                <hr>
                    <h2 class="no-post">No assignments overdue.</h2>
                <hr>
            @endif
            <div class="assignment-container">
                @foreach ($sortedAssignments as $item)
                    @php
                        date_default_timezone_set('Asia/Manila');
                        $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                        $targetDateTime = strtotime($item->due);
                    @endphp
                    @if ($targetDateTime < $currentDateTime)
                        <div class="class-code">
                            <div class="class-details">
                                <h2 class="task-title">{{ $item->title }}</h2>
                                <h2 class="task-class-details">@if($item->points) Task @else Assessment @endif · Due {{(new DateTime($item->due))->format('M d')}}</h2>
                            </div>
                            <div class="post-options">
                                <a href="@if($item->points) ../../tasks/{{$item->id}}/edit @else ../../assessments/{{$item->id}}/edit @endif" class="post-option submit-task" title="Edit in Resource"><i class="fa-sharp fa-solid fa-pen"></i></a>
                                <a href="@if($item->points) t1/{{$item->id}} @else a1/{{$item->id}} @endif" class="post-option submit-task" title="View Submissions"><i class="fa-sharp fa-solid fa-folder"></i></a>
                            </div>                    
                        </div>                        
                    @endif
                @endforeach
            </div>
        </div>



        <div class="assignments" id="all-task-content" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
            @if ($tasks == "[]")
                <hr>
                    <h2 class="no-post">No task assigned.</h2>
                <hr>
            @endif
            <div class="assignment-container">
                @foreach ($sortedAssignments as $item)
                    @if ($item->points)
                        <div class="class-code">
                            <div class="class-details">
                                <h2 class="task-title">{{ $item->title }}</h2>
                                <h2 class="task-class-details">@if($item->points) Task @else Assessment @endif · Due {{(new DateTime($item->due))->format('M d')}}</h2>
                            </div>
                            <div class="post-options">
                                <a href="@if($item->points) ../../tasks/{{$item->id}}/edit @else ../../assessments/{{$item->id}}/edit @endif" class="post-option submit-task" title="Edit in Resource"><i class="fa-sharp fa-solid fa-pen"></i></a>
                                <a href="@if($item->points) t1/{{$item->id}} @else a1/{{$item->id}} @endif" class="post-option submit-task" title="View Submissions"><i class="fa-sharp fa-solid fa-folder"></i></a>
                            </div>                    
                        </div>                        
                    @endif
                @endforeach
            </div>
        </div>




        <div class="assignments" id="all-assess-content" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
            @if ($assessments == "[]")
                <hr>
                    <h2 class="no-post">No assessment assigned.</h2>
                <hr>
            @endif
            <div class="assignment-container">
                @foreach ($sortedAssignments as $item)
                    @if (!$item->points)
                        <div class="class-code">
                            <div class="class-details">
                                <h2 class="task-title">{{ $item->title }}</h2>
                                <h2 class="task-class-details">@if($item->points) Task @else Assessment @endif · Due {{(new DateTime($item->due))->format('M d')}}</h2>
                            </div>
                            <div class="post-options">
                                <a href="@if($item->points) ../../tasks/{{$item->id}}/edit @else ../../assessments/{{$item->id}}/edit @endif" class="post-option submit-task" title="Edit in Resource"><i class="fa-sharp fa-solid fa-pen"></i></a>
                                <a href="@if($item->points) t1/{{$item->id}} @else a1/{{$item->id}} @endif" class="post-option submit-task" title="View Submissions"><i class="fa-sharp fa-solid fa-folder"></i></a>
                            </div>                    
                        </div>                        
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- {{ $sortedAssignments }} --}}



    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <script src="../../../js/class-assignments.js"></script>
</x-filament::page>
