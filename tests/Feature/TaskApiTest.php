<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Enums\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_task_successfully(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => 'Test Task',
            'description' => 'Test Description',
        ]);
        $response->assertStatus(201)
            ->assertJson([
                'status' => 201,
                'message' => 'Task created successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => ['id', 'title', 'description', 'status', 'due_date', 'created_at', 'updated_at'],
            ]);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'status' => TaskStatus::PENDING->value,
        ]);
    }

    public function test_create_task_validation_fails_without_title(): void
    {
        $response = $this->postJson('/api/tasks', [
            'description' => 'Test Description',
        ]);
        $response->assertStatus(422)
            ->assertJson([
                'status' => 422,
                'message' => 'Validation failed',
                'data' => null,
            ])
            ->assertJsonStructure(['status', 'message', 'data', 'errors'])
            ->assertJsonValidationErrors(['title']);
    }

    public function test_can_list_tasks_with_pagination(): void
    {
        Task::factory()->count(10)->create();
        $response = $this->getJson('/api/tasks');
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Tasks retrieved successfully',
            ])
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'tasks',
                    'pagination' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                        'from',
                        'to',
                        'links' => ['first', 'last', 'prev', 'next'],
                    ],
                ],
            ]);
    }

    public function test_can_update_task(): void
    {
        $task = Task::factory()->create(['title' => 'Old Title']);
        $response = $this->putJson("/api/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'status' => TaskStatus::COMPLETED->value,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Task updated successfully',
                'data' => [
                    'status' => TaskStatus::COMPLETED->value,
                ],
            ]);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create();
        $response = $this->deleteJson("/api/tasks/{$task->id}");
        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Task deleted successfully',
                'data' => null,
            ]);
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}
