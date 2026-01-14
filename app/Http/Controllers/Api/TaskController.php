<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Services\TaskService;
use App\Http\Traits\ApiResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Http\Helpers\PaginationHelper;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
