<x-filament::page>
    {{-- LINKS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />


    {{-- CLASS TOOLS --}}
    <div class="tools">
        <div class="class-tools">
            <h2 class="warning-unenroll" id="warning-unenroll">Warning: Unenrolling will permanently erase all associated <br> resources from your account for the enrolled class.</h2>
            <a href="/lms/assignments" class="to-do standard-btns" title="To-do List"><i class="fa-solid fa-list-check fa"></i></a>
            <a href="/lms/calendar" class="calendar standard-btns" title="Class Calendar"><i class="fa-regular fa-calendar"></i></a>
            <button class="calendar standard-btns" id="unenroll-classes" title="Unenroll Classes"><i class="fa-solid fa-xmark"></i></button>
            <button class="joinclass-btn standard-btns" id="joinclass-btn" title="Join Class">Join Class</button>
        </div>
        <h2 class="warning-unenroll" id="warning-unenroll-mob">Warning: Unenrolling will permanently erase all associated <br> resources from your account for the enrolled class.</h2>

    </div>



    {{-- JOIN CLASS FUNCTIONS --}}
    <form action="/joinclass" method="post" id="join-form">
        @csrf
        <div class="form-join">
            <a id="remove-join"><i class="fa-sharp fa-solid fa-xmark fa-xl"></i></a>
            <h2 class="join-title">Join an ILearn Class</h2>
            <div class="join-inputs">
                <input id="userid-in" type="hidden" name="userid" readonly value="{{ auth()->user()->id }}">
                <input id="name-in" type="hidden" name="name" readonly value="{{ auth()->user()->name }}">
                <input id="email-in" type="hidden" name="email" readonly value="{{ auth()->user()->email }}">
                <input id="code-in" type="text" name="code" placeholder="Class Code" required>
                <button id="submit-in" type="submit" class="standard-btns">Join Class</button>
            </div>
            <h2 id="no-class" class="response-class" style="display: none;">No such class exist!</h2>
            <h2 id="exist-class" class="response-class" style="display: none;">Class already joined!</h2>
            <h2 id="with-class" class="response-class" style="display: none;">Successfully Joined Class!</h2>
        </div>    
    </form>



    {{-- CLASSES DISPLAY --}}
    @php
        $studclasses = \App\Models\Student::where('userid', auth()->user()->id)->get();
        $profclasses = \App\Models\Classes::where('uid', auth()->user()->id)->get();
        

    @endphp
    @if (count($studclasses) == 0)
        <div class="join-class-field">
            <button class="join-class-field-btn" id="join-class-box">
                <i class="fa-solid fa-plus fa-xl"></i>
                <h2>No enrolled classes. Join a Class.</h2>
            </button>
        </div>
    @endif
    <div class="class-container">
        @foreach ($studclasses as $classes)
            @php
                $class = \App\Models\Classes::where('code', $classes->code)->first()
            @endphp
            <div class="class-contain" style="background-color: {{ $class->color }};" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="0">
                <form action="/unenrollclass" method="POST" class="unenroll" id="unenroll-form">
                    <input id="id-in" type="hidden" name="userid" readonly value="{{ auth()->user()->id }}">
                    <input id="classcode-in" type="hidden" name="classcode" readonly value="{{ $class->code }}">
                    <input id="classname-in" type="hidden" name="classname" readonly value="{{ $class->name }}">
                    <button class="unenroll" onclick="submitUnenroll(event, this.form)"><i class="fa-sharp fa-solid fa-circle-xmark"></i></button>
                </form>
                <a class="class-link" href="/lms/myclass/{{ $class->code }}">
                    <div class="class-title">
                        <h2 class="class-name">{{ $class->name }}</h2>
                    </div>
                    <div class="class-desc">
                        <h2 class="class-prof" title="Class Instructor">{{ \App\Models\User::where('id', $class->uid)->first()->name }} <i class="fa-solid fa-chalkboard-user"></i></h2>
                        <h2 class="class-code" title="Class Code">{{ $class->code." " }} <i class="fa-sharp fa-solid fa-share-nodes fa-lg"></i></h2>                        
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- CREATED CLASSES --}}
    @if (auth()->user()->hasRole("Instructor") || auth()->user()->hasRole("Superadmin"))
        <h2 class="created-classes">Created Classes</h2>
        <div class="class-tools">
            <a href="/lms/posts" class="to-do standard-btns" title="Create New Post"><i class="fa-regular fa-pen-to-square"></i></a>
            <a href="/lms/posts" class="to-do standard-btns" title="Create New Task"><i class="fa-solid fa-clipboard-check"></i></a>
            <a href="/lms/assessments" class="to-do standard-btns" title="Create New Assessment"><i class="fa-sharp fa-solid fa-file-pen"></i></a>
            <a href="/lms/classes/create"><button class="createclass-btn standard-btns" id="createclass-btn" title="Create Class">Create Class</button></a>        
        </div>
        @if (count($profclasses) == 0)
            <div class="join-class-field">
                <a class="join-class-field-btn" href="lms/classes/create">
                    <i class="fa-solid fa-plus fa-xl"></i>
                    <h2>No enrolled classes. Join a Class.</h2>
                </a>
            </div>
        @endif
        <div class="class-container">
            @foreach ($profclasses as $classes)
                @php
                    $class = \App\Models\Classes::where('code', $classes->code)->first()
                @endphp
                <div class="class-contain" style="background-color: {{ $class->color }};" data-aos="fade-left" data-aos-delay="50"  data-aos-offset="0">
                    <a class="class-link" href="/lms/myclass/{{ $class->code }}">
                        <div class="class-title">
                            <h2 class="class-name">{{ $class->name }}</h2>
                        </div>
                        <div class="class-desc">
                            <h2 class="class-prof" title="Class Instructor">{{ \App\Models\User::where('id', $class->uid)->first()->name }} <i class="fa-solid fa-chalkboard-user"></i></h2>
                            <h2 class="class-code" title="Class Code">{{ $class->code." " }} <i class="fa-sharp fa-solid fa-share-nodes fa-lg"></i></h2>                        
                        </div>
                    </a>
                </div>
            @endforeach
        </div>    
    @endif
    
    



    {{-- SCRIPTS --}}
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
      AOS.init();
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <script src="../js/op.js"></script>
</x-filament::page>