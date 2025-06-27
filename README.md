# 📋 Task Management System (TMS)

A comprehensive, modern task management system built with Laravel 11, featuring role-based access control, real-time notifications, and advanced project management capabilities.

## 🌟 Features

### 🔐 **Authentication & Authorization**
- **Multi-role system**: Admin, Creator, Assignee, Member
- **Email domain validation**: Company-specific registration
- **Session-based authentication** with remember me functionality
- **Policy-based permissions** for granular access control

### 📊 **Project Management**
- **Create and manage projects** with detailed descriptions
- **Team member assignment** and role management
- **Project status tracking** (Active, Completed, Archived)
- **Automatic project archiving** when all tasks are completed
- **Project statistics** and progress tracking

### ✅ **Task Management**
- **Create, edit, and delete tasks** within projects
- **Task assignment** to team members
- **Priority levels**: Low, Medium, High (with color-coded badges)
- **Status workflow**: Pending → In Progress → Completed
- **Due date management** with overdue tracking
- **Rich task descriptions** and comments system

### 💬 **Comments & Collaboration**
- **Task commenting system** with real-time updates
- **Collapsible comment sections** for clean UI
- **User identification** in comments
- **Permission-based commenting** (members and admins only)

### 📧 **Email Notifications**
- **Beautiful HTML email templates** for task assignments
- **Automatic notifications** when tasks are assigned
- **Queue-based email processing** with retry logic
- **Email logging** for development and testing
- **Telescope integration** for email monitoring

### 📈 **Dashboard & Analytics**
- **Project overview dashboard** with statistics
- **Task filtering** by status (All, Pending, In Progress, Completed)
- **Progress tracking** with visual progress bars
- **Overdue task highlighting**
- **Team member overview**

### 🔧 **Advanced Features**
- **Artisan commands** for automated tasks
- **Queue job processing** for background tasks
- **Database seeding** with sample data
- **Responsive design** with dark mode support
- **Modern UI** with Tailwind CSS

## 🏗️ System Architecture

### **User Roles & Permissions**

| Role | Permissions |
|------|-------------|
| **Admin** | Full system access, can view/edit all projects and tasks, manage all users |
| **Creator** | Create projects, manage own projects, assign tasks, manage team members |
| **Assignee** | View assigned projects, update task status, comment on tasks |
| **Member** | View project details, comment on tasks, basic project participation |

### **Database Schema**

```
Users
├── id, name, email, role, email_verified_at
├── HasMany: Projects (created)
├── BelongsToMany: Projects (assigned)
└── HasMany: TaskComments

Projects
├── id, name, description, status, created_by
├── BelongsTo: User (creator)
├── BelongsToMany: Users (members)
└── HasMany: Tasks

Tasks
├── id, title, description, status, priority, due_date
├── project_id, assigned_to, created_by
├── BelongsTo: Project, User (assignee), User (creator)
└── HasMany: TaskComments

TaskComments
├── id, task_id, created_by, comment
├── BelongsTo: Task
└── BelongsTo: User (creator)
```

## 🚀 Installation & Setup

### **Prerequisites**
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL/PostgreSQL/SQLite

### **Installation Steps**

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd TMS
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database** (in `.env`)
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tms
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Configure email** (in `.env`)
   ```env
   MAIL_MAILER=log
   MAIL_FROM_ADDRESS=noreply@yourcompany.com
   MAIL_FROM_NAME="Task Management System"
   ```

7. **Configure queue** (in `.env`)
   ```env
   QUEUE_CONNECTION=sync
   ```

8. **Run migrations and seed data**
   ```bash
   php artisan migrate --seed
   ```

9. **Build assets**
   ```bash
   npm run build
   ```

10. **Start the development server**
    ```bash
    php artisan serve
    ```

## 🎯 Usage Guide

### **Getting Started**

1. **Access the application** at `http://localhost:8000`
2. **Register** with a company email (domain validation applies)
3. **Login** and explore the dashboard

### **Default Accounts** (after seeding)

| Email | Role | Password |
|-------|------|----------|
| admin@company.com | Admin | password |
| emma.creator@company.com | Creator | password |
| john.assignee@company.com | Assignee | password |
| sarah.member@company.com | Member | password |

### **Creating Your First Project**

1. **Navigate** to the Projects page
2. **Click "Create Project"**
3. **Fill in** project details (name, description)
4. **Add team members** using the "Manage Team" button
5. **Start creating tasks** within the project

### **Task Management Workflow**

1. **Create Task**: Click "Add Task" in a project
2. **Assign Task**: Select a team member from the dropdown
3. **Set Priority**: Choose Low, Medium, or High priority
4. **Set Due Date**: Optional deadline for the task
5. **Track Progress**: Tasks flow through Pending → In Progress → Completed
6. **Add Comments**: Collaborate using the comment system

### **Email Notifications**

- **Automatic emails** are sent when tasks are assigned
- **View emails** in Telescope at `/telescope` → Mail section
- **Beautiful HTML templates** with all task details
- **Queue processing** ensures reliable delivery

## 🛠️ Advanced Features

### **Artisan Commands**

#### **Send Due Task Reminders**
```bash
php artisan tasks:send-due-reminders
```
- Sends email reminders for tasks due today
- Processes only pending and in-progress tasks
- Logs all email activities

#### **Archive Completed Projects**
```bash
php artisan projects:archive-completed
```
- Automatically archives projects with all tasks completed
- Provides detailed status reporting
- Maintains audit logs

### **Queue Jobs**

#### **SendTaskAssignmentNotification**
- **Triggered**: When a task is assigned to a user
- **Features**: Retry logic, failure handling, duplicate prevention
- **Email**: Beautiful HTML template with task details

### **Telescope Integration**

Access Telescope at `/telescope` to monitor:
- **Mail**: View all sent emails with full HTML content
- **Queries**: Database query performance
- **Jobs**: Queue job execution and failures
- **Logs**: Application logs and errors

## 🎨 User Interface

### **Design System**
- **Framework**: Tailwind CSS
- **Components**: Blade components for reusability
- **Responsive**: Mobile-first design approach
- **Dark Mode**: Full dark mode support
- **Icons**: Heroicons for consistent iconography

### **Key UI Components**
- **Project Cards**: Visual project overview with statistics
- **Task Cards**: Collapsible task details with comments
- **Status Badges**: Color-coded priority and status indicators
- **Modal Dialogs**: Clean, accessible modal interactions
- **Notifications**: Toast-style success/error messages

### **Interactive Features**
- **Real-time filtering**: Filter tasks by status
- **Collapsible sections**: Expandable comment areas
- **Drag interactions**: Smooth modal and dropdown behaviors
- **Form validation**: Client and server-side validation
- **Progress indicators**: Visual progress tracking

## 🔧 Configuration

### **Email Configuration**
```php
// config/mail.php
'default' => env('MAIL_MAILER', 'log'),
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
    'name' => env('MAIL_FROM_NAME', 'Example'),
],
```

### **Queue Configuration**
```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'sync'),
```

### **Company Email Domain Validation**
```php
// app/Rules/CompanyEmailDomain.php
// Customize allowed email domains for registration
```

## 🧪 Testing

### **Run Tests**
```bash
php artisan test
```

### **Available Test Suites**
- **Authentication Tests**: Login, registration, email verification
- **Profile Tests**: User profile management
- **Feature Tests**: Core application functionality

### **Test Data**
- **Database Seeding**: Comprehensive sample data
- **Factory Classes**: Generate test data on demand

## 📊 Monitoring & Debugging

### **Logging**
- **Application Logs**: `storage/logs/laravel.log`
- **Email Logs**: Captured via log mail driver
- **Queue Logs**: Job execution and failure tracking

### **Telescope Dashboard**
- **URL**: `/telescope`
- **Features**: Mail, Queries, Jobs, Logs, Exceptions
- **Authentication**: Admin access required

### **Performance Monitoring**
- **Database Queries**: Optimized with eager loading
- **Caching**: Configuration and view caching
- **Queue Processing**: Background job handling

## 🔒 Security Features

### **Authentication Security**
- **Password Hashing**: Bcrypt encryption
- **Session Security**: CSRF protection
- **Email Verification**: Required for new accounts
- **Domain Validation**: Company email enforcement

### **Authorization Security**
- **Policy Classes**: Granular permission control
- **Role-based Access**: Multi-level user roles
- **Route Protection**: Middleware-based security
- **Data Isolation**: Users see only authorized data

## 🚀 Deployment

### **Production Checklist**

1. **Environment Configuration**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   ```

2. **Database Setup**
   ```bash
   php artisan migrate --force
   ```

3. **Cache Optimization**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Queue Workers**
   ```bash
   php artisan queue:work --daemon
   ```

5. **Scheduler Setup** (crontab)
   ```bash
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

### **Server Requirements**
- **Web Server**: Apache/Nginx
- **PHP**: 8.2+ with required extensions
- **Database**: MySQL 8.0+/PostgreSQL 13+
- **Queue Worker**: Supervisor for queue processing
- **SSL Certificate**: HTTPS for production

## 📚 API Documentation

### **Task Assignment Workflow**
```php
// Create task with assignment
$task = Task::create([
    'title' => 'New Task',
    'assigned_to' => $userId,
    // ... other fields
]);

// Automatic email notification triggered via job
SendTaskAssignmentNotification::dispatch($task, $assignedUser, $creator);
```

### **Policy Usage Examples**
```php
// Check if user can create tasks in project
$user->can('createInProject', [Task::class, $project]);

// Check if user can update task
$user->can('update', $task);

// Check if user can manage project team
$user->can('manageMembers', $project);
```

## 🤝 Contributing

### **Development Workflow**
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write/update tests
5. Submit a pull request

### **Code Standards**
- **PSR-12**: PHP coding standards
- **Laravel Conventions**: Follow Laravel best practices
- **Documentation**: Update README for new features

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

### **Common Issues**

**Email not sending?**
- Check mail configuration in `.env`
- Verify Telescope mail logs
- Ensure queue workers are running

**Permission denied errors?**
- Check user roles and policies
- Verify project membership
- Review authorization logic

**Database errors?**
- Run `php artisan migrate:fresh --seed`
- Check database connection
- Verify table permissions

### **Getting Help**
- Check the logs in `storage/logs/laravel.log`
- Use Telescope for debugging at `/telescope`
- Review the codebase documentation

---

**Built with ❤️ using Laravel 12, Tailwind CSS, and modern web technologies.**
