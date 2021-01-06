<?php

namespace App\Http\Controllers\Api\Todo;

use App\Http\Controllers\Controller;
use App\Models\Todo\Todo;
use Illuminate\Http\Request;
use \App\Http\Controllers\Api\Traits\HasError;
use Illuminate\Support\Str;

class TodoController extends Controller {

    use HasError;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $data['todos'] = Todo::orderBy('created_at','desc')->with('tasks')->get();
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
        return static::createTodo($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Todo\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show($slug) {
        $data['todo'] = Todo::whereSlug($slug)->firstOrFail();
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
     * @param  \App\Models\Todo\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug) {
        return static::updateTodo($request, $slug);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Todo\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug) {
        $todo = Todo::whereSlug($slug)->firstOrFail();
        $todo->delete();
        return [
            'status' => 201,
            'message' => 'ok',
            'body' => 'Todo Soft deleted',
        ];
    }

    public static function createTodo(Request $request) {
        $input = $request->all();
        $rules = ([
            'name' => ['required', 'unique:todos'],
            'description' => ['required']
        ]);
        $error = static::getErrorMessage($input, $rules);
        if ($error) {
            return $error;
        }
        $input['slug'] = str_slug($request->name);

        $data['todo'] = Todo::create($input);

        return [
            'status' => 201,
            'message' => 'ok',
            'body' => $data,
        ];
    }

    public static function updateTodo($request, $slug) {
        $input = $request->all();
        $rules = ([
            'name' => ['required'],
            'description' => ['required'],
            'status' => ['required']
        ]);
        $error = static::getErrorMessage($input, $rules);
        if ($error) {
            return $error;
        }
        $input['slug'] = str_slug($request->name);
        $check_name_unique = Todo::whereSlug($slug)->firstOrFail();
        if ($check_name_unique->name == $request->name) {
            $check_name_unique->update([
                'description' => $request->description,
                'status' => $request->status
            ]);
        } else {
            $check_name_unique->update($input);
        }
        $data['todo'] = $check_name_unique;
        return [
            'status' => 201,
            'message' => 'ok',
            'body' => $data,
        ];
    }

}
