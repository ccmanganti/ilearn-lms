<x-filament::page>
    {{-- LINKS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="../../css/classes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    

    {{-- REDIRECT USERS IF CLASS IS INVALID --}}
    @php
        $classcode = basename(request()->url());
        $class = \App\Models\Classes::where('code', $classcode)->first();

        if (empty($class)) {
            echo '<script>window.location.href = "/lms/";</script>';
            exit;
        }

        $isEnrolled = auth()->user()->student || $class;
        $isProf = \App\Models\Classes::where('code', $classcode)->where('uid', auth()->user()->id)->exists();

        if (!$isEnrolled && !$isProf) {
            echo '<script>window.location.href = "/lms/";</script>';
            exit;
        }

        $posts = \App\Models\Post::where('class', $class->name)->get();
        $assessments = \App\Models\Assessment::where('class', $class->name)->get();
        $tasks = \App\Models\Task::where('class', $class->name)->get();

        $allPosts = $posts->concat($assessments)->concat($tasks);
        $sortedPosts = $allPosts->sortByDesc('created_at');
        $sortedPost = $posts->sortByDesc('created_at');
        $sortedAssessments = $assessments->sortByDesc('created_at');
        $sortedTasks = $tasks->sortByDesc('created_at');
        $currPosts = $sortedPosts;
    @endphp





    {{-- HEADER --}}
    <div class="class-head-contain" style="background-color: {{ $class->color }}">
        <div class="class-head"></div>
        <div class="class-head-details">
            <h2 class="class-name">{{ $class->name }}</h2>
            <h2 class="class-prof">{{ \App\Models\User::where('id', $class->uid)->first()->name }}</h2>
        </div>
    </div>


    <div class="mobile-nav">
        <button class="mob-nav active" id="all-nav">All</button>
        <button class="mob-nav" id="class-nav">Class Posts</button>
        <button class="mob-nav" id="tasks-nav">Tasks</button>
        <button class="mob-nav" id="assess-nav">Assessments</button>
    </div>

    {{-- POSTS --}}
    <div class="posts-container">
        <div class="post-navigations">
            
            <div class="post-nav">
                <h2 class="nav-posts nav-posts-title">Sort by</h2>
                <button class="all-posts nav-post active" id="all-posts">All</button>
                <button class="posts-posts nav-post" id="class-posts">Class Posts</button>
                <button class="tasks-posts nav-post" id="tasks-posts">Tasks</button>
                <button class="assess-posts nav-post" id="assess-posts">Assessments</button>
            </div>
            @if ($isProf)
                <div class="post-nav class-tools-nav">
                    <div class="manage-class">
                        <h2 class="nav-posts nav-posts-title">Manage Class</h2>
                    </div>
                    <div class="nav-items">
                        <a href="./{{ $classcode }}/class-assignments" class="all-posts nav-post">Class Assginments</a>
                        <a href="../attendances" class="posts-posts nav-post">Attendance</a>                        
                    </div>

                </div>
            @endif
        </div>

        {{-- ALL POSTS --}}
        <div class="posts" id="posts">
            @if ($allPosts == "[]")
                <hr>
                <h2 class="no-post">No posts for this class yet. @if($isProf) Click <a href="/lms/posts/create" style="text-decoration: underline">here</a> to create new post.@endif</h2>
                <hr>
            @endif
            @foreach ($currPosts as $post)
                @php
                    if(!$post->points && !$post->item){
                        $postType = "Class Post";
                        $postColor = "#cc7127";
                        $postIcon = "fa-solid fa-credit-card";

                    } else if($post->points && !$post->item){
                        $postType = "Task";
                        $postColor = "#cc274e";
                        $postIcon = "fa-sharp fa-solid fa-calendar-check fa-xl";
                    } else {
                        $postType = "Assessment";
                        $postColor = "#cc7127";
                        $postIcon = "fa-sharp fa-solid fa-box-archive fa-xl";
                    }
                @endphp
                <div class="post" style="border: 1px solid {{ $class->color }};" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="0">
                    <div class="post-descriptions">
                        <div class="post-desc-profile" style="background-color: {{ $postColor }};">
                            <i class="{{ $postIcon }}"></i>
                        </div>
                        <div class="post-desc-contain">
                            <div class="post-title">
                                <h2 class="title">{!! $post->title !!}</h2>
                            </div>
                            <div class="post-details">
                                <h2 class="post-type" style="color: {{ $postColor }};">{{ $postType }} <span style="color: gray; font-weight: normal;">· Posted: {{ date('F d, Y g:i a', strtotime($post->created_at)) }}</span></h2>
                            </div>
                            @if(!$isProf)
                                @if ($post->points && !$post->item)
                                    <div class="post-state-container">
                                        @if (\App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('taskid', $post->id)->get() != "[]")
                                            <h2 class="post-state-green">Submitted</h2>
                                        @else
                                            @php
                                                date_default_timezone_set('Asia/Manila');
                                                $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                                                $targetDateTime = strtotime($post->due);
                                            @endphp
                                            @if ($targetDateTime < $currentDateTime)
                                                <h2 class="post-state-overdue">Overdue</h2>                                    
                                            @endif
                                        @endif
                                    </div>
                                @endif
                                @if (!$post->points && $post->item)
                                    <div class="post-state-container">
                                        @if (\App\Models\AssessSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('assessid', $post->id)->get() != "[]")
                                            <h2 class="post-state-green">Answered</h2>
                                        @else
                                            @php
                                                date_default_timezone_set('Asia/Manila');
                                                $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                                                $targetDateTime = strtotime($post->due);
                                            @endphp
                                            @if ($targetDateTime < $currentDateTime)
                                                <h2 class="post-state-overdue">Overdue</h2>                                    
                                            @endif
                                        @endif
                                    </div>    
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="post-desc">
                        <p class="post-desc">{!! $post->desc !!}</p>
                    </div>
                    @if(!$isProf)
                        @if ($post->points && !$post->item && \App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('taskid', $post->id)->get() != "[]")
                            <a href="../assignments/{{ $classcode }}/{{ $post->id }}" class="submit-task">Resubmit</a>
                        @endif
                        @if ($post->points && !$post->item && \App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('taskid', $post->id)->get() == "[]")
                            <a href="../assignments/{{ $classcode }}/{{ $post->id }}" class="submit-task">Submit Task</a>
                        @endif
                        @if (!$post->points && $post->item && \App\Models\AssessSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('assessid', $post->id)->get() == "[]")
                            <a href="../assess/{{ $classcode }}/{{ $post->id }}" class="submit-task">Take Assessment</a>
                        @endif
                    @else
                        @if ($post->points && !$post->item)
                            <a href="../tasks/{{ $post->id }}/edit" class="submit-task">Manage Task</a>
                        @endif
                        @if (!$post->points && $post->item)
                            <a href="../assessments/{{ $post->id }}/edit" class="submit-task">Manage Assessment</a>
                        @endif
                        @if (!$post->points && !$post->item)
                            <a href="../posts/{{ $post->id }}/edit" class="submit-task">Manage Post</a>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>


        {{-- POSTS --}}
        <div class="posts-only" id="posts-only">
            @if ($posts == "[]")
                <hr>
                <h2 class="no-post">No posts for this class yet. @if($isProf) Click <a href="/lms/posts/create" style="text-decoration: underline">here</a> to create new post.@endif</h2>
                <hr>
            @endif
            @foreach ($sortedPost as $post)
                @php
                    if(!$post->points && !$post->item){
                        $postType = "Class Post";
                        $postColor = "#cc7127";
                        $postIcon = "fa-solid fa-credit-card";

                    } else if($post->points && !$post->item){
                        $postType = "Task";
                        $postColor = "#cc274e";
                        $postIcon = "fa-sharp fa-solid fa-calendar-check fa-xl";
                    } else {
                        $postType = "Assessment";
                        $postColor = "#cc7127";
                        $postIcon = "fa-sharp fa-solid fa-box-archive fa-xl";
                    }
                @endphp
                <div class="post" style="border: 1px solid {{ $class->color }};" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
                    <div class="post-descriptions">
                        <div class="post-desc-profile" style="background-color: {{ $postColor }};">
                            <i class="{{ $postIcon }}"></i>
                        </div>
                        <div class="post-desc-contain">
                            <div class="post-title">
                                <h2 class="title">{!! $post->title !!}</h2>
                            </div>
                            <div class="post-details">
                                <h2 class="post-type" style="color: {{ $postColor }};">{{ $postType }} <span style="color: gray; font-weight: normal;">· Posted: {{ date('F d, Y g:i a', strtotime($post->created_at)) }}</span></h2>
                            </div>
                        </div>
                    </div>
                    <div class="post-desc">
                        <p class="post-desc">{!! $post->desc !!}</p>
                    </div>
                    @if ($isProf)
                        <a href="../posts/{{ $post->id }}/edit" class="submit-task">Manage Post</a>
                    @endif
                </div>
            @endforeach
        </div>


        {{-- TASKS --}}
        <div class="tasks-only" id="tasks-only">
            @if ($tasks == "[]")
                <hr>
                <h2 class="no-post">No tasks for this class yet. @if($isProf) Click <a href="/lms/tasks/create" style="text-decoration: underline">here</a> to create new task.@endif</h2>
                <hr>
            @endif
            @foreach ($sortedTasks as $post)
                @php
                    if(!$post->points && !$post->item){
                        $postType = "Class Post";
                        $postColor = "#cc7127";
                        $postIcon = "fa-solid fa-credit-card";

                    } else if($post->points && !$post->item){
                        $postType = "Task";
                        $postColor = "#cc274e";
                        $postIcon = "fa-sharp fa-solid fa-calendar-check fa-xl";
                    } else {
                        $postType = "Assessment";
                        $postColor = "#cc7127";
                        $postIcon = "fa-sharp fa-solid fa-box-archive fa-xl";
                    }
                @endphp
                <div class="post" style="border: 1px solid {{ $class->color }};" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
                    <div class="post-descriptions">
                        <div class="post-desc-profile" style="background-color: {{ $postColor }};">
                            <i class="{{ $postIcon }}"></i>
                        </div>
                        <div class="post-desc-contain">
                            <div class="post-title">
                                <h2 class="title">{!! $post->title !!}</h2>
                            </div>
                            <div class="post-details">
                                <h2 class="post-type" style="color: {{ $postColor }};">{{ $postType }} <span style="color: gray; font-weight: normal;">· Posted: {{ date('F d, Y g:i a', strtotime($post->created_at)) }}</span></h2>
                            </div>
                            @if(!$isProf)
                                <div class="post-state-container">
                                    @if (\App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('taskid', $post->id)->get() != "[]")
                                        <h2 class="post-state-green">Submitted</h2>
                                    @else
                                        @php
                                            date_default_timezone_set('Asia/Manila');
                                            $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                                            $targetDateTime = strtotime($post->due);
                                        @endphp
                                        @if ($targetDateTime < $currentDateTime)
                                            <h2 class="post-state-overdue">Overdue</h2>                                    
                                        @endif
                                    @endif
                                </div>   
                            @endif
                        </div>
                            
                    </div>
                    <div class="post-desc">
                        <p class="post-desc">{!! $post->desc !!}</p>
                    </div>
                    @if(!$isProf)
                        @if ($post->points && !$post->item && \App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('taskid', $post->id)->get() != "[]")
                            <a href="../assignments/{{ $classcode }}/{{ $post->id }}" class="submit-task">Resubmit</a>
                        @endif
                        @if ($post->points && !$post->item && \App\Models\FileSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('taskid', $post->id)->get() == "[]")
                            <a href="../assignments/{{ $classcode }}/{{ $post->id }}" class="submit-task">Submit Task</a>
                        @endif
                    @else
                        <a href="../tasks/{{ $post->id }}/edit" class="submit-task">Manage Task</a>
                    @endif
                    
                </div>
            @endforeach
        </div>


        {{-- ASSESSMENTS --}}
        <div class="assess-only" id="assess-only">
            @if ($assessments == "[]")
                <hr>
                <h2 class="no-post">No assessments for this class yet. @if($isProf) Click <a href="/lms/assessments/create" style="text-decoration: underline">here</a> to create new assessment.@endif</h2>
                <hr>
            @endif
            @foreach ($sortedAssessments as $post)
                @php
                    if(!$post->points && !$post->item){
                        $postType = "Class Post";
                        $postColor = "#cc7127";
                        $postIcon = "fa-solid fa-credit-card";

                    } else if($post->points && !$post->item){
                        $postType = "Task";
                        $postColor = "#cc274e";
                        $postIcon = "fa-sharp fa-solid fa-calendar-check fa-xl";
                    } else {
                        $postType = "Assessment";
                        $postColor = "#cc7127";
                        $postIcon = "fa-sharp fa-solid fa-box-archive fa-xl";
                    }
                @endphp
                <div class="post" style="border: 1px solid {{ $class->color }};" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="80">
                    <div class="post-descriptions">
                        <div class="post-desc-profile" style="background-color: {{ $postColor }};">
                            <i class="{{ $postIcon }}"></i>
                        </div>
                        <div class="post-desc-contain">
                            <div class="post-title">
                                <h2 class="title">{!! $post->title !!}</h2>
                            </div>
                            <div class="post-details">
                                <h2 class="post-type" style="color: {{ $postColor }};">{{ $postType }} <span style="color: gray; font-weight: normal;">· Posted: {{ date('F d, Y g:i a', strtotime($post->created_at)) }}</span></h2>
                            </div>
                            @if(!$isProf)
                                <div class="post-state-container">
                                    @if (\App\Models\AssessSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('assessid', $post->id)->get() != "[]")
                                        <h2 class="post-state-green">Answered</h2>
                                    @else
                                        @php
                                            date_default_timezone_set('Asia/Manila');
                                            $currentDateTime = strtotime(date('Y-m-d H:i:s')); // Get the current timestamp in 24-hour format
                                            $targetDateTime = strtotime($post->due);
                                        @endphp
                                        @if ($targetDateTime < $currentDateTime)
                                            <h2 class="post-state-overdue">Overdue</h2>                                    
                                        @endif
                                    @endif
                                </div>   
                            @endif
                        </div>
                            
                    </div>
                    <div class="post-desc">
                        <p class="post-desc">{!! $post->desc !!}</p>
                    </div>
                    @if(!$isProf)
                        @if (!$post->points && $post->item && \App\Models\AssessSubmission::where('userid', auth()->user()->id)->where('classcode', $classcode)->where('assessid', $post->id)->get() == "[]")
                            <a href="../assess/{{ $classcode }}/{{ $post->id }}" class="submit-task">Take Assessment</a>
                        @endif
                    @else
                        <a href="../assessments/{{ $post->id }}/edit" class="submit-task">Manage Assessment</a>
                    @endif
                    
                </div>
            @endforeach
        </div>
    </div>



    
    
    
    
    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <script src="../../js/op.js"></script>
</x-filament::page>
