# Aplikasi Absensi & Agenda PKL (Student Internship Attendance and Logbook Application)

A comprehensive web application for managing student internship attendance, logbooks, activities, and documents. The app features a Laravel REST API backend and a modern Vue.js frontend with Tailwind CSS styling.

## Features

### Core Features
- ✅ **Attendance Tracking**: Check-in/check-out with GPS location and photo capture
- ✅ **Daily Logbook**: Students can document daily internship activities and learning outcomes
- ✅ **Activity Tracking**: Management of tasks and activities assigned to students
- ✅ **Document Management**: Upload and review internship documents
- ✅ **Reports & Statistics**: Comprehensive attendance and performance reports

### Advanced Features
- ✅ GPS Location Tracking: Verify attendance location
- ✅ Photo Evidence: Capture photos during check-in/check-out
- ✅ Role-based Access: Student, Instructor, and Admin roles
- ✅ Approval Workflow: Instructors can review and approve logbook entries
- ✅ Responsive Design: Works on desktop and mobile devices

## Technology Stack

### Backend
- **Laravel 11** - PHP web framework
- **Laravel Sanctum** - API authentication
- **MySQL** - Database
- **Vite** - Asset bundler

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Vue Router** - Client-side routing
- **Pinia** - State management
- **Tailwind CSS** - Utility-first CSS framework
- **Axios** - HTTP client

## Project Structure

```
project/
├── app/
│   ├── Http/Controllers/Api/        # API Controllers
│   │   ├── AuthController.php
│   │   ├── AttendanceController.php
│   │   ├── LogbookEntryController.php
│   │   ├── ActivityController.php
│   │   └── DocumentController.php
│   └── Models/                       # Eloquent Models
│       ├── User.php
│       ├── Student.php
│       ├── Instructor.php
│       ├── Attendance.php
│       ├── LogbookEntry.php
│       ├── Activity.php
│       ├── Document.php
│       └── NotificationLog.php
├── database/
│   ├── migrations/                   # Database migrations
│   └── seeders/                      # Database seeders
├── resources/
│   ├── js/
│   │   ├── app.js                    # Vue app entry point
│   │   ├── App.vue                   # Root Vue component
│   │   ├── router/                   # Vue Router configuration
│   │   ├── stores/                   # Pinia stores (state management)
│   │   └── views/                    # Vue page components
│   ├── css/
│   │   └── app.css                   # Global styles
│   └── views/
│       └── app.blade.php             # Blade template entry point
└── routes/
    ├── api.php                       # API routes
    └── web.php                       # Web routes
```

## Installation & Setup

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- npm or yarn
- MySQL 8.0+

### Installation Steps

1. **Clone/Setup the project**
   ```bash
   cd c:\angela\absensipklsph
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   # Edit .env file with your database credentials
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Start development servers**
   
   Terminal 1 - Backend:
   ```bash
   php artisan serve
   ```
   
   Terminal 2 - Frontend:
   ```bash
   npm run dev
   ```

7. **Access the application**
   - Open browser to `http://localhost:5173`
   - The backend API runs on `http://localhost:8000`

## API Endpoints

### Authentication
- `POST /api/v1/auth/register-student` - Register as student
- `POST /api/v1/auth/register-instructor` - Register as instructor
- `POST /api/v1/auth/login` - Login
- `POST /api/v1/auth/logout` - Logout
- `GET /api/v1/auth/me` - Get current user

### Attendance
- `GET /api/v1/attendance` - List attendance records
- `POST /api/v1/attendance/check-in` - Check in with GPS and photo
- `POST /api/v1/attendance/check-out` - Check out with GPS and photo
- `GET /api/v1/attendance/report/{studentId}` - Get attendance report

### Logbook
- `GET /api/v1/logbook` - List logbook entries
- `POST /api/v1/logbook` - Create entry
- `GET /api/v1/logbook/{id}` - Get entry details
- `PUT /api/v1/logbook/{id}` - Update entry
- `POST /api/v1/logbook/{id}/submit` - Submit for approval
- `POST /api/v1/logbook/{id}/approve` - Approve entry (instructor)
- `POST /api/v1/logbook/{id}/reject` - Reject entry (instructor)
- `DELETE /api/v1/logbook/{id}` - Delete entry

### Activities
- `GET /api/v1/activities` - List activities
- `POST /api/v1/activities` - Create activity
- `PUT /api/v1/activities/{id}` - Update activity
- `POST /api/v1/activities/{id}/complete` - Mark as completed
- `DELETE /api/v1/activities/{id}` - Delete activity

### Documents
- `GET /api/v1/documents` - List documents
- `POST /api/v1/documents` - Upload document
- `PUT /api/v1/documents/{id}` - Update document
- `POST /api/v1/documents/{id}/approve` - Approve document
- `POST /api/v1/documents/{id}/reject` - Reject document
- `GET /api/v1/documents/{id}/download` - Download document
- `DELETE /api/v1/documents/{id}` - Delete document

## Database Schema

### Key Tables
- **users** - User accounts (Students, Instructors, Admins)
- **roles** - User roles (admin, student, instructor)
- **students** - Student profile information
- **instructors** - Instructor profile information
- **attendances** - Daily attendance records
- **logbook_entries** - Student logbook entries
- **activities** - Tasks and activities for students
- **documents** - Uploaded documents
- **notifications_log** - Notification history

## User Roles & Permissions

### Student
- View own attendance and logbook
- Create and submit logbook entries
- Check in/check out with GPS and photos
- Upload documents
- Track assigned activities
- View attendance reports

### Instructor
- Monitor student attendance
- Review and approve/reject logbook entries
- View student attendance reports
- Approve/reject documents
- Assign activities to students
- Provide feedback

### Admin
- Full system access
- User management
- System reports and analytics
- Configuration management

## Frontend Pages

- **Login** - User authentication
- **Register** - New user registration (Student/Instructor)
- **Dashboard** - Overview and quick stats
- **Attendance** - Check-in/check-out and records
- **Logbook** - View and manage logbook entries
- **Logbook Detail** - Edit and submit entries
- **Activities** - View and manage tasks
- **Documents** - Upload and manage documents
- **Reports** - View statistics and reports

## Key Features Implementation

### Check-in/Check-out Process
1. Student clicks "Check In" button
2. System captures GPS coordinates
3. Student takes a photo for verification
4. System records the attendance with location and photo

### Logbook Entry Workflow
1. Student creates daily logbook entry
2. Entry is saved as draft
3. Student submits entry for review
4. Instructor reviews and provides feedback
5. Instructor approves or rejects (can reject with feedback for revision)
6. Approved entries count towards requirements

### Activity Assignment
1. Instructor creates activity for student
2. Student views and updates activity status
3. Student marks activity as completed with deliverables
4. Instructor can monitor progress

### Document Management
1. Student uploads document (PDF, DOC, Images, etc.)
2. Document is stored securely
3. Instructor reviews and approves/rejects
4. Documents can be downloaded

## Configuration

### File Storage
- Public files: `storage/app/public`
- Create symlink: `php artisan storage:link`

### Email Configuration
1. Edit `.env` file with SMTP settings:
   ```
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_username
   MAIL_PASSWORD=your_password
   ```

## Deployment

### Production Build
```bash
# Build assets
npm run build

# Optimize Laravel
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Hosting Requirements
- PHP 8.2+ with required extensions
- MySQL 8.0+
- Node.js for build process
- Web server (Apache/Nginx)

## Troubleshooting

### CORS Issues
- Check `config/cors.php` settings
- Ensure frontend and backend URLs are properly configured

### Database Connection
- Verify `.env` database credentials
- Run `php artisan migrate` to create tables

### File Upload Issues
- Ensure `storage/app/public` is writable
- Check file size limits in PHP and Laravel config

## Development Guide

### Adding New Features
1. Create migration: `php artisan make:migration`
2. Create model: `php artisan make:model`
3. Create controller: `php artisan make:controller Api/NameController`
4. Add routes in `routes/api.php`
5. Create Vue components in `resources/js/views/`

### Running Tests
```bash
php artisan test
```

## Security Features
- ✅ CSRF protection
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ Authentication via Sanctum
- ✅ Role-based authorization
- ✅ Secure password hashing
- ✅ Photo and document verification

## Future Enhancements
- Mobile app (React Native/Flutter)
- QR code attendance scanning
- SMS notifications
- Email notifications
- Advanced analytics dashboard
- Automated report generation
- Export to Excel/PDF
- Offline attendance sync

## Support & Documentation

For issues and questions:
1. Check the troubleshooting section
2. Review API documentation
3. Check Laravel documentation: https://laravel.com/docs
4. Check Vue documentation: https://vuejs.org

## License
This project is proprietary and intended for educational use.

## Contact
For support and questions, contact the development team.

---

**Last Updated**: April 2026
**Version**: 1.0.0
