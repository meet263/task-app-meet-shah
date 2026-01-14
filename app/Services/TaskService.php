<?php

namespace App\Services;

use App\Models\Task;
use App\Enums\TaskStatus;

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
        $data['status'] ??= TaskStatus::PENDING->value;
        return Task::create($data);
    }

    public function update(Task $task, $data)
    {
        $task->update($data);
        return $task;
    }

    public function delete(Task $task)
    {
        return $task->delete();
    }
}
