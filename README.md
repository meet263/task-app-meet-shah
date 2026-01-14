# Task App - Meet Shah
A simple task management application built with Laravel 12 and Vue.js 3. Create, update, delete, and view your tasks with ease.

## What You Need

- PHP 8.2 or newer
- Composer (PHP package manager)
- Node.js and npm (JavaScript package manager)
- MySQL database

## Getting Started

### Step 1: Install Everything

First, install all the required packages:

```bash
composer install
npm install
```

### Step 2: Set Up Your Environment

Copy the example environment file and generate an application key:

```bash
copy .env.example .env
php artisan key:generate
```

### Step 3: Configure Your Database

Open the `.env` file and update these lines for MySQL:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_app_meet_shah
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Create Database Tables

Run this command to create all necessary tables:

```bash
php artisan migrate
```

### Step 5: Add Sample Data

```bash
php artisan db:seed
```

### Step 6: Start the Application

You need TWO terminal windows open:

**Terminal 1 - Start the frontend:**
```bash
npm run dev
```

**Terminal 2 - Start the backend:**
```bash
php artisan serve
```

### Step 7: Open Your Browser

Go to: `http://localhost:8000`

That's it! You're ready to manage tasks.

## How to Use

### Adding a Task
1. Fill in the task title (required)
2. Add a description (optional)
3. Set a due date (optional)
4. Click "Add Task"
5. You'll see a success message

### Viewing Tasks
- See all tasks in a table format
- Total count shown at the top
- Latest tasks appear first
- 10 tasks per page

### Filtering Tasks
Use the dropdown to filter by:
- All Tasks
- Pending
- In Process
- Completed

### Updating Task Status
1. Click the status dropdown for any task
2. Select new status
3. Confirm the change
4. Success message appears

### Deleting a Task
1. Click the "Delete" button
2. Confirm deletion
3. Task is removed (soft delete - stays in database)

## Features

- Create new tasks with title, description, and due date
- View all tasks in a clean table layout
- Filter tasks by status
- Update task status with confirmation
- Delete tasks with confirmation
- Pagination for large task lists
- Success alerts for all actions
- Latest tasks shown first
- Total task count displayed
- Responsive design (works on mobile)

## API Endpoints

The application provides a RESTful API:

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks` | Get all tasks (with pagination) |
| POST | `/api/tasks` | Create a new task |
| GET | `/api/tasks/{id}` | Get a specific task |
| PUT | `/api/tasks/{id}` | Update a task |
| DELETE | `/api/tasks/{id}` | Delete a task |

### Example API Request

Get all pending tasks:
```bash
curl http://localhost:8000/api/tasks?status=pending
```

Create a new task:
```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{"title":"My Task","description":"Task details","due_date":"2024-12-31"}'
```

## Running Tests

```bash
php artisan test --filter TaskApiTest
```

## Project Structure

```
meet-task-manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/TaskController.php  (Handles requests)
│   │   ├── Requests/                           (Validates input)
│   │   ├── Resources/                          (Formats output)
│   │   ├── Traits/ApiResponse.php              (Standard responses)
│   │   └── Helpers/PaginationHelper.php        (Pagination format)
│   ├── Models/Task.php                         (Database model)
│   ├── Services/TaskService.php                (Business logic)
│   └── TaskStatus.php                          (Status enum)
├── resources/
│   ├── js/
│   │   ├── App.vue                             (Main Vue component)
│   │   ├── app.js                              (Vue setup)
│   │   └── components/TaskList.vue             (Task list component)
│   └── views/app.blade.php                     (HTML template)
├── routes/
│   ├── api.php                                 (API routes)
│   └── web.php                                 (Web routes)
└── tests/
    └── Feature/TaskApiTest.php                 (API tests)
```

## Task Status Options

- **Pending** - Task is waiting to be started
- **In Process** - Task is currently being worked on
- **Completed** - Task is finished

## Common Issues

### Issue: "npm run dev" not working
**Solution:** Make sure Node.js is installed. Run `node -v` to check.

### Issue: Database connection error
**Solution:** Check your `.env` file has correct database credentials.

### Issue: Page shows blank
**Solution:** Make sure both `npm run dev` AND `php artisan serve` are running.

### Issue: Tasks not loading
**Solution:** Check browser console (F12) for errors. Verify API is working at `http://localhost:8000/api/tasks`

### Issue: Port 8000 already in use
**Solution:** Use a different port: `php artisan serve --port=8001`

## Architecture

- **Controllers** - Handle HTTP requests and responses
- **Services** - Contain business logic
- **Requests** - Validate incoming data
- **Resources** - Transform data for API responses
- **Models** - Represent database tables
- **Traits** - Reusable functionality
- **Helpers** - Utility functions

**Commands Reference:**

# Install dependencies
composer install && npm install

# Setup environment
copy .env.example .env && php artisan key:generate

# Setup database
php artisan migrate && php artisan db:seed

# Run application
npm run dev # Terminal 1
php artisan serve # Terminal 2

# Run tests
php artisan test --filter TaskApiTest
