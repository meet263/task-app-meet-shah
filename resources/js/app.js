import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import 'bootstrap/dist/css/bootstrap.min.css';
import App from './App.vue';
import TaskList from './components/TaskList.vue';

const routes = [
    {
        path: '/',
        name: 'tasks',
        component: TaskList
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

const app = createApp(App);
app.use(router);
app.mount('#app');
