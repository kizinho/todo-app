<?php

namespace App\Http\Controllers\Api\Task;

use App\Http\Controllers\Controller;
use App\Models\Task\Task;
use Illuminate\Http\Request;
use \App\Http\Controllers\Api\Traits\HasError;

class TaskController extends Controller {

    use HasError;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data['tasks'] = Task::orderBy('created_at')->with('todos')->get();
        return [
            'status' => 200,
            'message' => 'ok',
            'body' => $data,
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        return static::createTask($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function show($slug) {
        $data['task'] = Task::whereSlug($slug)->firstOrFail();
        return [
            'status' => 200,
            'message' => 'ok',
            'body' => $data,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug) {
        return static::updateTask($request, $slug);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug) {
        $task = Task::whereSlug($slug)->firstOrFail();
        $task->delete();
        return [
            'status' => 201,
            'message' => 'ok',
            'body' => 'Task Soft deleted',
        ];
    }

    public static function createTask(Request $request) {
        $input = $request->all();
        $rules = ([
            'title' => ['required', 'unique:tasks'],
            'todo_id' => ['required'],
            'due_date' => ['required', 'date_format:Y-m-d']
        ]);
        $error = static::getErrorMessage($input, $rules);
        if ($error) {
            return $error;
        }
        $input['slug'] = str_slug($request->title);

        $data['task'] = Task::create($input);

        return [
            'status' => 201,
            'message' => 'ok',
            'body' => $data,
        ];
    }

    public static function updateTask($request, $slug) {
        $input = $request->all();
        $rules = ([
            'title' => ['required'],
            'todo_id' => ['required'],
            'status' => ['required'],
            'due_date' => ['required', 'date_format:Y-m-d']
        ]);
        $error = static::getErrorMessage($input, $rules);
        if ($error) {
            return $error;
        }
        $input['slug'] = str_slug($request->title);
        $check_title_unique = Task::whereSlug($slug)->firstOrFail();
        if ($check_title_unique->title == $request->title) {
            $check_title_unique->update([
                'title' => $request->title,
                'todo_id' => $request->todo_id,
                'status' => $request->status,
                'due_date' => $request->due_date,
            ]);
        } else {
            $check_title_unique->update($input);
        }
        $data['todo'] = $check_title_unique;
        return [
            'status' => 201,
            'message' => 'ok',
            'body' => $data,
        ];
    }

}
