<template>
    <div class="container">
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
                            <td>{{ task.description }}</td>
                            <td>
                                <select class="form-select form-select-sm">
                                    <option value="pending">Pending</option>
                                    <option value="in_process">In Process</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </td>
                            <td>{{ task.due_date || '-' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm me-1">View</button>
                                <button class="btn btn-danger btn-sm">Delete</button>
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
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'TaskList',
    data() {
        return {
            tasks: [],
            filter: '',
            loading: false,
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
