<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{
    public function list($filters = [])
    {
        $query = Task::query();
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        return $query->latest()->paginate(config('app.default_page_length', 10));
    }

    public function create(array $data)
    {
        //
    }

    public function update(Task $task, $data)
    {
        //
    }

    public function delete(Task $task)
    {
        //
    }
}
