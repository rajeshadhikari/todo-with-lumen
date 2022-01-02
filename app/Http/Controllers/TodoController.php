<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\File\File;

class TodoController extends Controller
{

    /**
     * get all todo items with filter option
     * Url GET : /api/v1/todo
     * @headerParam api_token       string
     * @queryParam is_complete optional value 0 or 1 e.g GET : /api/v1/todo?is_complete=1
     */
    public function index()
    {
        $complete = request()->query('is_complete');

        $todo = Todo::user()->when($complete != null, function($q)use($complete){
            $q->isComplete($complete);
        })
        ->orderByRaw('case when due_date is null then 1 else 0 end')
        ->orderBy('due_date')
        ->paginate()->withQueryString();
        return response()->json($todo, 200);
    }

    /**
     * Get single todo item
     * GET : /api/v1/todo/{id}
     * @param id
     * @headerParam api_token       string
     */     
    public function show($id)
    {
        $todo = Todo::user()->findOrFail($id);
        return response()->json($todo, 200);
    }

    /**
     * crete new todo item
     * POST : /api/v1/todo
     * @headerParam api_token       string
     * @bodyParam title             string
     * @bodyParam body              string
     * @bodyParam due_date          datetime Y-m-d H:i:s
     * @bodyParam attachment_file   file
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title'=>'required|string|max:100',
            'body'=>'nullable|string',
            'due_date'=>'nullable|date|after_or_equal:now',
            'attachment_file'=>'file|mimes:png,jpg,jpeg,csv,txt,xlx,xls,pdf|max:2048',
        ]);

    
        if($request->file()) {
            $name = time().'_'.$request->attachment_file->getClientOriginalName();
            $filePath = $request->file('attachment_file')->move('uploads', $name);
            $request->merge(['attachment' => $filePath->getPathname()]);
        }
        $todo = Todo::create($request->all());
        return response()->json($todo, 201);
    }

    /**
     * Edit todo item
     * POST : /api/v1/todo/{id}
     * @param id
     * @headerParam api_token       string
     * @bodyParam title             string
     * @bodyParam body              string
     * @bodyParam due_date          datetime Y-m-d H:i:s
     * @bodyParam attachment_file   file
     */    
    public function update(Request $request, $id)
    {
        $todo =Todo::user()->findOrFail($id);
        $this->validate($request, [
            'title'=>'required|string|max:100',
            'body'=>'nullable|string',
            'due_date'=>'nullable|date|after_or_equal:now',
            'attachment_file'=>'file|mimes:png,jpg,jpeg,csv,txt,xlx,xls,pdf|max:2048',
        ]);        
        if($request->file()) {
            $name = time().'_'.$request->attachment_file->getClientOriginalName();
            $filePath = $request->file('attachment_file')->move('uploads', $name);
            $request->merge(['attachment' => $filePath->getPathname()]);
            if(!empty($todo->attachment) && file_exists($todo->attachment)){
                unlink(realpath($todo->attachment));
            }
        }
        $todo->update($request->all());
        return response()->json($todo, 200);
    }

    /**
     * delete todo item
     * DELETE : /api/v1/todo/{id}
     * @headerParam api_token       string
     * @param id
     */
    public function delete(Request $request, $id)
    {
        $todo = Todo::user()->findOrFail($id);
        $todo->delete();
        return response()->json(null, 204);
    }    

    /**
     * Update only status of todo item
     * PUT : /api/v1/todo-update-status/{id}
     * @param id
     * @headerParam api_token       string
     * @body json {"is_complete":"1"}
     */
    public function updateStatus(Request $request, $id)
    {
        $todo =Todo::user()->findOrFail($id);
        $this->validate($request, [
            'is_complete'=>'required|numeric|in:0,1',
        ]);
        $todo->is_complete = $request->is_complete;
        $todo->save();
        return response()->json($todo, 200);
    } 
    
    /**
     * Add reminder to todo item
     * @headerParam api_token       string
     * @bodyParam todo_id           integer
     * @bodyParam reminder_date     datetime Y-m-d H:i:s      
     */
    public function addReminder(Request $request)
    {
        $this->validate($request, [
            'todo_id' => [
                'required',
                'numeric',
                Rule::exists('todo','id')->where(function ($query)use($request) {
                    return $query->where('is_complete', 0)
                                ->where('user_id', Auth::user()->id);
                }),
            ],            
            'reminder_date'=>[
                'required',
                'date',
                'after_or_equal:now',
                function ($attribute, $value, $fail)use($request) {
                    if($due_date = DB::table('todo')->where('id', '=', $request->todo_id)->where('due_date', '<' , $value)->value('due_date')){
                        $fail('The '.$attribute.' must be a date before '.$due_date);
                    }
                }
            ]
        ]);
        $reminder = Reminder::create($request->all());
        return response()->json($reminder, 201);        
    }

    /**
     * Change reminder state to off
     */
}
