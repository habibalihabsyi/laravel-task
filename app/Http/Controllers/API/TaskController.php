<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskShowResource;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:Super Admin|Admin']);
    }
    public function index(){
        try {
            $tasks = Task::all();
            return $this->successResponse(TaskResource::collection($tasks), 'All Tasks', 200);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'description' => 'nullable',
                'start_at' => 'nullable',
                'end_at' =>'nullable',
                'status' => 'required',
                'priority' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 400, ['errors' => $validator->errors()]);
            }
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'status' => $request->status,
                'priority' => $request->priority,
            ]);
            return $this->successResponse(TaskResource::make($task), 'Successfully add task', 201);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function show(Task $task){
        try {
            return $this->successResponse(new TaskShowResource($task), 'Get Task', 200);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function update(Request $request, Task $task){
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|max:255',
                'description' => 'nullable',
                'start_at' => 'nullable',
                'end_at' =>'nullable',
                'status' => 'required',
                'priority' => 'required'
            ]);
            if ($validator->fails()) {
                return $this->errorResponse('Validation failed', 400, ['errors' => $validator->errors()]);
            }
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'status' => $request->status,
                'priority' => $request->priority,
            ]);
            return $this->successResponse(new TaskResource($task), 'Successfully edit task', 200);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
        
    }
    public function destroy(Task $task){
        try {
            $task->delete();
            return $this->successResponse([], 'Succesfully delete Task', 200);
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function assignTask(Task $task, Request $request){
        try {
            $task->users()->sync($request->user_id);
            return $this->successResponse([], 'Task assigned successfully');
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
    public function commentTask(Task $task, Request $request){
        try {
            $comment = new Comment();
            $comment->body = $request->body;
            $task->comments()->save($comment);
            if($request->has('image')){
                $image = $request->file('image');
                $image_name = time().'_'.$image->getClientOriginalName();
                $path = Storage::putFileAs('public/images', $image, $image_name);
                $imagePath = new Image();
                $imagePath->path = $path;
                $comment->images()->save($imagePath);
            }
            return $this->successResponse([], 'Comment successfully');
        } catch (\Throwable $th) {
            return $this->unknownResponse($th);
        }
    }
}
