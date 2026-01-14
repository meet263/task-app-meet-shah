<template>
    <div class="container">
        <!-- Success Alert -->
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
            {{ successMessage }}
            <button type="button" class="btn-close" @click="successMessage = ''" aria-label="Close"></button>
        </div>

        <!-- Store Task Form -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add New Task</h5>
                <form @submit.prevent="addTask">
                    <div class="mb-3">
                        <input v-model="newTask.title" type="text" class="form-control" placeholder="Task title"
                            required>
                    </div>
                    <div class="mb-3">
                        <textarea v-model="newTask.description" class="form-control" placeholder="Description"
                            rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <input v-model="newTask.due_date" type="date" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Task</button>
                </form>
            </div>
        </div>

        <!-- Listing -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <strong>Total Tasks: {{ pagination.total || 0 }}</strong>
                <span v-if="pagination.total" class="text-muted ms-2">
                    (Showing {{ pagination.from }}-{{ pagination.to }})
                </span>
            </div>
            <select v-model="filter" @change="loadTasks" class="form-select w-auto">
                <option value="">All Tasks</option>
                <option value="pending">Pending</option>
                <option value="in_process">In Process</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <div v-if="loading" class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div v-else>
            <div v-if="tasks.length === 0" class="alert alert-info">
                No tasks found
            </div>
            <div v-else class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(task, index) in tasks" :key="task.id">
                            <td>{{ pagination.from + index }}</td>
                            <td>{{ task.title }}</td>
                            <td>{{ truncateText(task.description) }}</td>
                            <td>
                                <select v-model="task.status" @change="confirmStatusChange(task, $event)"
                                    class="form-select form-select-sm">
                                    <option value="pending">Pending</option>
                                    <option value="in_process">In Process</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </td>
                            <td>{{ formatDate(task.due_date) }}</td>
                            <td>
                                <button class="btn btn-info btn-sm me-1" @click="viewTask(task.id)">View</button>
                                <button class="btn btn-danger btn-sm" @click="deleteTask(task.id)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <nav v-if="pagination.last_page > 1" aria-label="Task pagination">
                <ul class="pagination">
                    <li class="page-item" :class="{ disabled: !pagination.links.prev }">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page - 1)">
                            Previous
                        </a>
                    </li>
                    <li class="page-item" v-for="page in pagination.last_page" :key="page"
                        :class="{ active: page === pagination.current_page }">
                        <a class="page-link" href="#" @click.prevent="changePage(page)">
                            {{ page }}
                        </a>
                    </li>
                    <li class="page-item" :class="{ disabled: !pagination.links.next }">
                        <a class="page-link" href="#" @click.prevent="changePage(pagination.current_page + 1)">
                            Next
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- View Task Modal -->
        <div v-if="viewingTask" class="modal show d-block" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Task Details</h5>
                        <button type="button" class="btn-close" @click="viewingTask = null"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <strong>Title:</strong>
                            <p>{{ viewingTask.title }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Description:</strong>
                            <p>{{ viewingTask.description || 'No description' }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <p><span class="badge bg-secondary">{{ getStatusLabel(viewingTask.status) }}</span></p>
                        </div>
                        <div class="mb-3">
                            <strong>Due Date:</strong>
                            <p>{{ viewingTask.due_date ? formatDate(viewingTask.due_date) : 'No due date' }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Created:</strong>
                            <p>{{ formatDate(viewingTask.created_at) }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Updated:</strong>
                            <p>{{ formatDate(viewingTask.updated_at) }}</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="viewingTask = null">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TaskList',
    data() {
        return {
            tasks: [],
            newTask: {
                title: '',
                description: '',
                due_date: ''
            },
            filter: '',
            loading: false,
            successMessage: '',
            viewingTask: null,
            pagination: {
                current_page: 1,
                last_page: 1,
                total: 0,
                from: 0,
                to: 0,
                links: {}
            },
            apiUrl: '/api/tasks'
        };
    },
    methods: {
        truncateText(text, limit = 50) {
            if (!text) return '-';
            return text.length > limit ? text.substring(0, limit) + '...' : text;
        },
        showSuccess(message) {
            this.successMessage = message;
            setTimeout(() => {
                this.successMessage = '';
            }, 3000);
        },
        getStatusLabel(status) {
            const labels = {
                'pending': 'Pending',
                'in_process': 'In Process',
                'completed': 'Completed'
            };
            return labels[status] || status;
        },
        formatDate(dateString) {
            if (!dateString) return '-';
            return new Date(dateString).toLocaleDateString('en-GB');
        },
        async loadTasks(page = 1) {
            this.loading = true;
            try {
                let url = `${this.apiUrl}?page=${page}`;
                if (this.filter) {
                    url += `&status=${this.filter}`;
                }
                const response = await axios.get(url);
                if (response.data && response.data.data) {
                    this.tasks = response.data.data.tasks;
                    this.pagination = response.data.data.pagination;
                }
            } catch (error) {
                console.error('Failed to load tasks:', error);
                alert('Failed to load tasks. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        async addTask() {
            if (!this.newTask.title.trim()) {
                alert('Task title is required');
                return;
            }
            try {
                const response = await axios.post(this.apiUrl, this.newTask);
                this.newTask = {
                    title: '',
                    description: '',
                    due_date: ''
                };
                this.showSuccess(response.data.message || 'Task created successfully!');
                this.loadTasks();
            } catch (error) {
                console.error('Failed to add task:', error);
                alert('Failed to add task. Please try again.');
            }
        },
        async confirmStatusChange(task, event) {
            const newStatus = event.target.value;
            const oldStatus = task.status;
            if (!confirm(`Are you sure you want to change status to "${this.getStatusLabel(newStatus)}"?`)) {
                task.status = oldStatus;
                event.target.value = oldStatus;
                return;
            }
            await this.updateStatus(task);
        },
        async updateStatus(task) {
            try {
                const response = await axios.put(`${this.apiUrl}/${task.id}`, {
                    status: task.status
                });
                this.showSuccess(response.data.message || 'Task status updated successfully!');
            } catch (error) {
                console.error('Failed to update task:', error);
                alert('Failed to update task. Please try again.');
                this.loadTasks();
            }
        },
        async viewTask(id) {
            try {
                const response = await axios.get(`${this.apiUrl}/${id}`);
                this.viewingTask = response.data.data;
            } catch (error) {
                console.error('Failed to load task:', error);
                alert('Failed to load task details.');
            }
        },
        async deleteTask(id) {
            if (!confirm('Are you sure you want to delete this task?')) {
                return;
            }
            try {
                await axios.delete(`${this.apiUrl}/${id}`);
                this.showSuccess('Task deleted successfully!');
                this.loadTasks();
            } catch (error) {
                console.error('Failed to delete task:', error);
                alert('Failed to delete task. Please try again.');
            }
        },
        changePage(page) {
            if (page >= 1 && page <= this.pagination.last_page) {
                this.loadTasks(page);
            }
        }
    },
    mounted() {
        this.loadTasks();
    }
};
</script>

<style scoped>
.table-responsive {
    margin-bottom: 1rem;
}
</style>
