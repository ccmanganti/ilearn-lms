<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use \App\Models\Classes;
use \App\Models\User;
use \App\Models\Score;
use \App\Models\Post;
use \App\Models\FileSubmission;
use \App\Models\AssessSubmission;
use \App\Models\TemporaryFile;
use \App\Models\Task;
use \App\Models\Assessment;
use \App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    // JOIN CLASSROOM
    function joinClass(){
        if(Student::where('userid', request('userid'))->where('code', request('code'))->first() == null){
            if(Classes::where('code', request('code'))->first() == null){
                return response()->json(array("success" => "0"));
            } else{
                $uclass = new Student();
                $uclass->userid = request('userid');
                $uclass->name = request('name');
                $uclass->email = request('email');
                $uclass->code = request('code');
                $uclass->uid = Classes::where('code', (request('code')))->first()->uid;
                $uclass->save();
                return response()->json(array("success" => "2"));
            }
        } else{
            return response()->json(array("success" => "1"));
        }
    }

    function submitAssess(){
        $currentAssess = Assessment::where('id', request('assessid'))->first();
        $answers = [];
        $points = 0;
        $currentIndex = 0;
        foreach ($currentAssess["item"] as $items) {
            if ($items["type"] == "t1") {
                if(request($currentIndex) == (int)$items["answerid"]){
                    $points++;
                }
                array_push($answers, $items["answerid"]);
            } else{
                if(request($currentIndex) == $items["answermc"]){
                    $points++;
                }
                array_push($answers, $items["answermc"]);

            }
            $currentIndex++;    
        }

        $newSubmission = new AssessSubmission;
        $newSubmission->userid = request('userid');
        $newSubmission->classid = request('classid');
        $newSubmission->classcode = request('classcode');
        $newSubmission->assessid = request('assessid');
        $newSubmission->item = $answers;
        $newSubmission->score = $points;
        $newSubmission->save();

        $newScore = new Score;
        $newScore->userid = request('userid');
        $newScore->username = auth()->user()->name;
        $newScore->classid = request('classid');
        $newScore->classcode = request('classcode');
        $newScore->classname = Classes::where('code', request('classcode'))->first()->name;
        $newScore->classprof = Classes::where('code', request('classcode'))->first()->prof;
        $newScore->classprofid = 1;
        $newScore->asstype = "Assessment";
        $newScore->assid = request('assessid');
        $newScore->assname = Assessment::where('id', request('assessid'))->first()->title;
        $newScore->asspoints = count(Assessment::where('id', request('assessid'))->first()->item);
        $newScore->score = $points;
        $newScore->save();


        return redirect('/lms/assignments')->with('success', '1');

        // return response()->json(array("success" => "1"));
    }

    function submitUnenroll(){
        Student::where('userid', request('userid'))->where('code', request('classcode'))->delete();
        AssessSubmission::where('userid', request('userid'))->where('classcode', request('classcode'))->delete();
        FileSubmission::where('userid', request('userid'))->where('classcode', request('classcode'))->delete();
        Score::where('userid', request('userid'))->where('classcode', request('classcode'))->delete();
        return response()->json(array("success" => 'Deleted'));
    }

    function selectStudent(){
        $submissions = [];
        if (request('type') == 't1') {
            $submission = FileSubmission::where('userid', request('userid'))->where('taskid', request('ass'))->get();
        } else{
            $submission = AssessSubmission::where('userid', request('userid'))->where('assessid', request('ass'))->get();
        }
        foreach ($submission as $sub) {
            array_push($submissions, $sub->file);
        }
        if($submissions == []){
            $submissions = "0";
        }
        return response()->json(array("success" => $submissions, "type" => request('type')));
    }

    function submitScore(){
        if (request('type') == 't1') {
            $submission = FileSubmission::where('userid', request('userid'))->where('taskid', request('ass'))->get();
            $submissionTask = FileSubmission::where('userid', request('userid'))->where('taskid', request('ass'))->first();
            $scoreExist = Score::where('userid', request('userid'))->where('assid', request('ass'))->where('asstype', 'Task')->first();
            if($scoreExist){
                $scoreExist->score = request('score');
                $scoreExist->save();
            } else{
                $newScore = new Score;
                $newScore->userid = $submissionTask->userid;
                $newScore->username = User::where('id', request('userid'))->first()->name;
                $newScore->classid = $submissionTask->classid;
                $newScore->classcode = $submissionTask->classcode;
                $newScore->classname = Classes::where('code', $submissionTask->classcode)->first()->name;
                $newScore->classprof = Classes::where('code', $submissionTask->classcode)->first()->prof;
                $newScore->classprofid = Classes::where('code', $submissionTask->classcode)->first()->uid;
                $newScore->asstype = "Task";
                $newScore->assid = $submissionTask->taskid;
                $newScore->assname = Task::where('id', $submissionTask->taskid)->first()->title;
                $newScore->asspoints = Task::where('id', $submissionTask->taskid)->first()->points;
                $newScore->score = request('score');
                $newScore->save();
            }
        } else{
            $submission = AssessSubmission::where('userid', request('userid'))->where('assessid', request('ass'))->get();
            $submissionAss = AssessSubmission::where('userid', request('userid'))->where('assessid', request('ass'))->first();
            $newScore = Score::where('userid', request('userid'))->where('assid', request('ass'))->where('asstype', 'Assessment')->first();
            $newScore->score = request('score');
            $newScore->save();
        }
        foreach ($submission as $sub) {
            $sub->score = request('score');
            $sub->save();
        }
        

        

        return response()->json(array("success" => request('score')));
    }


    public function store(Request $request){
        $tmpFile = TemporaryFile::all();
        FileSubmission::where('taskid', $request->taskid)->where('classcode', $request->classcode)->where('userid', auth()->user()->id)->delete();
        if ($tmpFile){
            foreach($tmpFile as $tmp){
                Storage::copy('public/tmp/'.$tmp->folder.'/'.$tmp->file, 'public/'.$tmp->folder.'/'.$tmp->file);
                FileSubmission::create([
                    'userid' => auth()->user()->id,
                    'classid' => $request->classid,
                    'classcode' => $request->classcode,
                    'taskid' => $request->taskid,
                    'file' => $tmp->folder.'/'.$tmp->file
                ]);
                

                Storage::deleteDirectory('public/tmp'.'/'.$tmp->folder);
                $tmp->delete();    
            }
            // Storage::copy('submissions/tmp/'.$tmpFile->folder.'/'.$tmpFile->file, 'submissions/'.$tmpFile->folder.'/'.$tmpFile->file);
            // Sample::create([
            //     'file' => $tmpFile->folder.'/'.$tmpFile->file
            // ]);
            // Storage::deleteDirectory('submissions/tmp'.'/'.$tmpFile->folder);
            // $tmpFile->delete();
            return redirect('/lms/assignments')->with('success', 'Uploaded');
        }
        return redirect('/lms/assignments')->with('success', 'No');

    }

    public function tmpUpload(Request $request){
        if ($request->hasFile('submission')){
            $file = $request->file('submission');
            $filename = $file->getClientOriginalName();
            $folder = uniqid('file', true);
            $file->storeAs('public/tmp/'.$folder, $filename);
            TemporaryFile::create([
                'folder' => $folder,
                'file' => $filename
            ]);

            return $folder;
        }
        return '';
    }

    public function tmpDelete(){
        $tmpFile = TemporaryFile::where('folder', request()->getContent())->first();
        if($tmpFile){
            
            Storage::deleteDirectory('public/tmp'.'/'.$tmpFile->folder);
            $tmpFile->delete();

            return response('');
        }
    }
}
