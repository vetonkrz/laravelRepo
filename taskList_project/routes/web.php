<?php

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function(){
    return redirect()->route('tasks.index');
});

Route::get('/tasks', function(){
    // return view('welcome');
    // return '<h1>Home page</h1> <a href="/contacts">click to go back to contact page</a>';
    return view('index',[
        'tasks' => Task::latest()->paginate(10)]);
})->name('tasks.index');

Route::view('/tasks/create', 'create')->name('tasks.create');

Route::get('/tasks/{task}/edit', function(Task $task){
  return view('edit', ['task' =>  $task]);
})->name('tasks.edit');

Route::get('/tasks/{task}', function(Task $task){
    return view('show', ['task' => $task]);
})->name('tasks.show');

Route::post('/tasks', function(TaskRequest $request)
{
  // dd($request->all());
  // $data = $request->validated();
  // $task = new Task;
  // $task->title = $data['title'];
  // $task->description = $data['description'];
  // $task->long_description = $data['long_description'];
  // $task->save();
  $task = Task::create($request->validated());

  return redirect()->route('tasks.show', ['task' => $task->id])
  ->with('success', 'Task was created successfully!');
})->name('tasks.store');

Route::put('/tasks/{task}', function(Task $task, TaskRequest $request)
{
  // $data = $request->validated();
  // $task->title = $data['title'];
  // $task->description = $data['description'];
  // $task->long_description = $data['long_description'];
  // $task->save();
  $task->update($request->validated());

  return redirect()->route('tasks.show', ['task' => $task->id])
  ->with('success', 'Task was updated successfully!');
})->name('tasks.update');

Route::delete('/tasks/{task}', function(Task $task){
  $task->delete();

  return redirect()->route('tasks.index')
    ->with('success', 'Task was successfully deleted!');
})->name('tasks.destroy');

Route::put('/tasks/{task}/toggle-complete', function(Task $task){
  $task->toggleComplete();
  return redirect()->back()->with('success', 'Task updated successfully!');
})->name('tasks.toggle-complete');


// Route::get('/contacts',function(){
//     // return '<h1>Contact page</h1> <a href="/">click to go back to homepage</a>';
//     return '<h1>Contact page!</h1>';
// });

// Route::get('/asd', function(){
//     return 'Hello!';
// })->name('hello');

// Route::get('/halo', function(){
//     return redirect()->route('hello');
// }); 


// Route::get('/greet/{name}', function($name){
//     return 'Hello ' . $name . '!';
// });

Route::fallback(function()
{
    return 'to infinity!';
});
