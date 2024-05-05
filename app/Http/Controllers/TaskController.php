<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        $projectId = $request->input('project');

        if ($projectId) {
            $all_tasks = Task::orderByRaw('priority_level DESC, updated_at DESC')
                ->where('project', $projectId)
                ->get();
        } else {
            $all_tasks = Task::orderByRaw('priority_level DESC, updated_at DESC')
                ->get();
        }

        return view("home.index", ['all_tasks' => $all_tasks]);
    }

    public function addTask(Request $request)
    {
        $data = (object) $request->all();

        if (!$data->task_name) {
            return Redirect::back()->with('error', 'Enter The Task Name!');
        } elseif (!$data->project) {
            return Redirect::back()->with('error', 'Select The Project Name!');
        } else {
            $validated = $request->validate([
                "task_name" => ["required"],
                "priority_level" => ["required"],
                "project" => ["required"],
            ]);

            $result = Task::create([
                "task_name" => $data->task_name,
                "priority_level" => $data->priority_level,
                "project" => $data->project,
            ]);

            if (!empty($result)) {
                return Redirect::back()->with('success', 'Task Added successfully!');
            } else {
                return Redirect::back()->with('error', 'Adding  Task Failed!');
            }
        }

    }

    public function filterTasks(Request $request)
    {
        $projectId = $request->input('project');
        $tasks = Task::where('project_id', $projectId)->get();
        return response()->json($tasks);
    }
    

    public function editTask(Request $request)
    {

        $data = (object) $request->all();
        $validatedData = $request->validate([
            'task_name' => 'required|string',
            'priority_level' => 'required|integer',
        ]);

        $taskId = $request->input('task_id');

        $result = Task::where("id", "=", $taskId)->update([

            "task_name" => $data->task_name,
            "priority_level" => $data->priority_level,
        ]);

        if (!empty($result)) {
            return Redirect::back()->with('success', 'Task Editing Was successfully!');
        } else {
            return Redirect::back()->with('error', 'Task Editing failed!');
        }
    }

    public function updateTaskOrder(Request $request)
    {
        $taskId = $request->input('taskId');
        if (!is_null($taskId)) {
            $task = Task::find($taskId);
            if ($task) {
                $task->update([
                    'priority_level' => 2,
                    'updated_at' => now(),
                ]);
                return Redirect::back()->with('success', 'Task Update Was successfully!');
            } else {
                return Redirect::back()->with('success', 'Task Update Failed!');
            }
        }
        return response()->json(['error' => 'Invalid task ID'], 404);
    }

    public function deleteTask($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json(['error' => 'Task not found.'], 404);
        }

        $deleted = $task->delete();
        if (!empty($deleted)) {
            return Redirect::back()->with('success', 'Task Deleted successfully!');
        } else {
            return Redirect::back()->with('error', 'Task failed! To Delete');
        }
    }

    public function deleteAllTasks()
    {
        Task::truncate();
        return response()->json(['success' => true, 'message' => 'All tasks deleted successfully.']);
    }
}
