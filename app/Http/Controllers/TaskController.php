<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\TaskModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;

class TaskController extends Controller
{
    public function validId(int $id)
    {
        if (!$id || $id <= 0) {
            throw new HttpResponseException(response()->json([
                'message' => 'Id is required'
            ], 400));
        }
    }

    public function changeStatus(string $id) 
    {
        try 
        {
            $id = (int) $id;
            DB::beginTransaction();

            $this->validId($id);

            $task = TaskModel::find($id);
            $this->isTaskNull($task);

            $task->done = !$task->done;
            $task->save();

            DB::commit();
            return response()->json('Task updated successfully', 200);
        } 
        catch (\Throwable $th) 
        {
            DB::rollBack();
            throw new HttpResponseException(response()->json(
                $th
            , 500));
        }

    }

    public function findByTitle(string $title)
    {
        $tasks = TaskModel::where('title', 'like', "%$title%")->get();

        return response()->json(
            $tasks, 200
        );
    }

    public function isTaskNull($task)
    {
        if ($task === null) {
            throw new HttpResponseException(response()->json('Task not found', 404));
        }
    }

    public function index()
    {
        try 
        {
            $tasks = TaskModel::all();
            return response()->json(
                $tasks
            , 200);
        } 
        catch (\Throwable $th) 
        {
            throw new HttpResponseException(response()->json(
                $th
            , 500));
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
            return response()->json('Task created successfully', 201);
        } 
        catch (\Throwable $th) 
        {
            DB::rollBack();
            throw new HttpResponseException(response()->json(
                $th
            , 500));
        }
    }

    public function show(string $id)
    {
        $id = (int) $id;
        $this->validId($id);

        $task = TaskModel::find($id);
        $this->isTaskNull($task);

        return response()->json(
            $task
        , 200);
    }

    public function edit(int $id)
    {
        $this->validId($id);
    }

    public function update(UpdateTaskRequest $request, string $id)
    {
        try 
        {
            $id = (int) $id;
            DB::beginTransaction();

            $this->validId($id);

            $task = TaskModel::find($id);
            $this->isTaskNull($task);

            $task->update($request->validated());

            DB::commit();
            return response()->json('Task updated successfully', 200);
        } 
        catch (\Throwable $th) 
        {
            DB::rollBack();
            throw new HttpResponseException(response()->json(
                $th
            , 500));
        }
    }

    public function destroy(string $id)
    {
        try 
        {
            $id = (int) $id;
            DB::beginTransaction();

            $this->validId($id);

            $task = TaskModel::find($id);
            $this->isTaskNull($task);

            $task->forceDelete();

            DB::commit();

            return response()->json('Task deleted successfully', 200);
        } 
        catch (ModelNotFoundException $e) 
        {
            DB::rollBack();
            throw new HttpResponseException(response()->json($e, 500));
        } 
        catch (Exception $e) 
        {
            DB::rollBack();
            throw new HttpResponseException(response()->json($e, 500));
        }
    }
}