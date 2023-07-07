<x-filament::page>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="stylesheet" href="../../../css/assessment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    @php
        $url = request()->url(); // Get the current URL
        $segments = explode('/', $url); // Split the URL into segments
        $secondToLastSegment = $segments[count($segments) - 2]; // Get the second-to-last segment

        $classcode = basename($secondToLastSegment);
        $assessid = basename(request()->url());
        $currentClass = \App\Models\Classes::where('code', $classcode)->first();
        $currentClassColor = \App\Models\Classes::where('code', $classcode)->first()->color;
        $currentAssess = \App\Models\Assessment::where('id', $assessid)->first();

        $submissionIn = \App\Models\AssessSubmission::where('userid', auth()->user()->id)->where('classcode', $currentClass->code)->where('assessid', $assessid)->first();
        if ($submissionIn) {
            redirect('/lms/assignments');
        }
    @endphp
    <style>:root{--class-color: {{ $currentClassColor }};}</style>


    <div class="class-head-contain">
        <div class="class-head"></div>
        <div class="class-head-details">
            <h2 class="class-name">{{ $currentAssess->title }}</h2>
            <h2 class="class-prof">{{ $currentClass->name }}</h2>
        </div>
    </div>


    {{-- 
        {
            "id":1,
            "class":"Chemistry 3A",
            "title":"Sample Assessment for 3A",
            "desc":"<p>This is the instructions for the exam. The author of this exam can include images and files inside of it to be utilized by the examinee. This particular exam is made for testing purposes only. Treat this description as an instruction.<\/p>",
            "due":"2023-06-21 00:00:00",
            "item":[
                {
                    "type":"t1",
                    "choicenum":"4",
                    "question":"What is th name of this website?",
                    "choices":[
                        {
                            "choice":"iLearn"
                        },
                        {
                            "choice":"iKnow"
                        },
                        {
                            "choice":"iDontKnow"
                        },
                        {
                            "choice":"iMaybe"
                        }
                    ],
                    "answermc":null,
                    "answerid":"0"
                }
            ],
            "uid":"2",
            "created_at":
            "2023-06-19T02:30:40.000000Z",
            "updated_at":"2023-06-20T07:28:46.000000Z"}
        --}}
        
    {{-- {{ $currentAssess }} --}}
    @if (!$submissionIn)
        <div class="assessment-container">
            <div class="assess-contain">
                <div class="title-total">
                    <h2 class="class-instruct-title">Assessment Description</h2>
                    <h2 class="class-instruct-total">{{ count($currentAssess->item) }} Points</h2>
                </div>
                <h2 class="class-instruct" target=”_blank”>{!! $currentAssess->desc !!}</h2>
            </div>
            <form action="/submitassess" id="assessment-form" method="POST">
                <input id="userid-in" type="hidden" name="userid" readonly value="{{ auth()->user()->id }}">
                <input id="classid-in" type="hidden" name="classid" readonly value="{{ $currentClass->id }}">
                <input id="classcode-in" type="hidden" name="classcode" readonly value="{{ $currentClass->code }}">
                <input id="name-in" type="hidden" name="assessid" readonly value="{{ $currentAssess->id }}">
                
                @php $itemNum = 0; @endphp
                @foreach ($currentAssess["item"] as $items)
                    <div class="assess-contain">

                        <label for={{ $itemNum }} class="question">{{ $itemNum }}. {!! $items["question"] !!}</label>                        
                        <div class="choices-list">
                            
                            @if ($items["type"] == "t1")
                                @php $choiceNum = 0; @endphp
                                @foreach ($items["choices"] as $choices)
                                    <div class="choice">
                                        <input type="radio" name="{{ $itemNum }}" value="{{ $choiceNum }}" id="{{ $itemNum.$choiceNum }}" required/><label for="{{ $itemNum.$choiceNum }}" class="empty-choice"> {!! $choices["choice"] !!} </label>
                                    </div>
                                    @php $choiceNum++; @endphp
                                @endforeach                            
                            @else
                                <input class="choice-input" type="text" placeholder="Answer" name="{{ $itemNum }}" id="{{ $itemNum.$choiceNum }}" required/>
                            @endif

                        </div>

                        @php $itemNum++ @endphp
                    </div>                    
                @endforeach
                <button class="submit-answers" type="submit" id="">Submit</button>
                        {{-- {{ $currentAssess["item"][0]["choices"][0]["choice"] }}                     --}}
            </form>
        </div>
    @endif
        


    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.js" integrity="sha256-a9jBBRygX1Bh5lt8GZjXDzyOB+bWve9EiO7tROUtj/E=" crossorigin="anonymous"></script>
    <script src="../../../js/assessment.js"></script>
</x-filament::page>
