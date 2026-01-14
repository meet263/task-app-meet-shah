<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Task;
use App\Services\TaskService;
use App\Http\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Helpers\PaginationHelper;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    use ApiResponse;

    public function __construct(private TaskService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tasks = $this->service->list(request()->only('status'));
            return $this->successResponse(
                [
                    'tasks' => TaskResource::collection($tasks),
                    'pagination' => PaginationHelper::format($tasks),
                ],
                'Tasks retrieved successfully'
            );
        } catch (Exception $ex) {
            Log::error('Failed to retrieve tasks: ' . $ex->getMessage());
            return $this->errorResponse('Failed to retrieve tasks');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        try {
            $task = $this->service->create($request->validated());
            return $this->successResponse(
                new TaskResource($task),
                'Task created successfully',
                201
            );
        } catch (Exception $ex) {
            Log::error('Failed to create task: ' . $ex->getMessage());
            return $this->errorResponse('Failed to create task');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            return $this->successResponse(
                new TaskResource($task),
                'Task retrieved successfully'
            );
        } catch (ModelNotFoundException $ex) {
            return $this->errorResponse('Requested task not found', 404);
        } catch (Exception $ex) {
            Log::error('Failed to retrieve task: ' . $ex->getMessage());
            return $this->errorResponse('Failed to retrieve task');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task = $this->service->update($task, $request->validated());
            return $this->successResponse(
                new TaskResource($task),
                'Task updated successfully'
            );
        } catch (ModelNotFoundException $ex) {
            return $this->errorResponse('Requested task not found', 404);
        } catch (Exception $ex) {
            Log::error('Failed to update task: ' . $ex->getMessage());
            return $this->errorResponse('Failed to update task');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);
            $this->service->delete($task);
            return $this->successResponse(
                null,
                'Task deleted successfully'
            );
        } catch (ModelNotFoundException $ex) {
            return $this->errorResponse('Requested task not found', 404);
        } catch (Exception $ex) {
            Log::error('Failed to delete task: ' . $ex->getMessage());
            return $this->errorResponse('Failed to delete task');
        }
    }
}
