<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\TaskModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class TaskController extends Controller
{
    public function validId(int $id)
    {
        if (!$id || $id <= 0) {
            abort(400, 'Id is required and must be positive');
        }
    }

    public function isTaskNull($task)
    {
        if ($task === null) {
            abort(404, 'Task not found');
        }
    }

    public function index()
    {
        try 
        {
            $tasks = TaskModel::all();
            return response()->json([
                'success' => true,
                'data' => $tasks
            ], 200);
        } 
        catch (\Throwable $th) 
        {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(CreateTaskRequest $request)
    {
        try 
        {
            DB::beginTransaction();

            $data = $request->validated();
            $task = TaskModel::create($data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);
        } 
        catch (\Throwable $th) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function show(int $id)
    {
        $this->validId($id);

        $task = TaskModel::find($id);
        $this->isTaskNull($task);

        return response()->json([
            'success' => true,
            'data' => $task
        ], 200);
    }

    public function edit(int $id)
    {
        $this->validId($id);
    }

    public function update(UpdateTaskRequest $request, int $id)
    {
        try 
        {
            DB::beginTransaction();

            $this->validId($id);

            $task = TaskModel::find($id);
            $this->isTaskNull($task);

            $task->update($request->validated());

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => $task
            ], 200);
        } 
        catch (\Throwable $th) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id)
    {
        try 
        {
            DB::beginTransaction();

            $this->validId($id);

            $task = TaskModel::findOrFail($id);
            $this->isTaskNull($task);

            $task->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Task deleted successfully'
            ], 200);
        } 
        catch (ModelNotFoundException $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'Task not found'
            ], 404);
        } 
        catch (Exception $e) 
        {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while deleting the task',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}